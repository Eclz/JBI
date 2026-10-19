<?php

namespace App\Http\Controllers\Admin\Registrar;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Application;
use App\Models\StudentProfile;
use App\Models\Faculty;
use App\Models\Department;
use App\Models\CourseEnrollment;
use App\Models\Program;

class RegistrarController extends Controller
{
    public function dashboard(Request $request)
    {
        $request->validate([
            'student_view' => 'nullable|in:all,active,inactive',
        ]);

        $studentView = $request->query('student_view', 'all');
        $totalStudents = Application::students()->where('status', 'admitted')->count();
        $totalFaculties = Faculty::count();
        $totalDepartments = Department::count();
        $totalPrograms = Program::count();
        $activeEnrollments = CourseEnrollment::where('status', 'enrolled')->count();

        $admittedStudentEmails = Application::students()
            ->where('status', 'admitted')
            ->select('email');
        $studentsQuery = StudentProfile::with('user', 'program')
            ->whereHas('user', fn ($query) => $query->whereIn('email', $admittedStudentEmails));
        if ($studentView === 'active') {
            $studentsQuery->where('status', 'active');
        } elseif ($studentView === 'inactive') {
            $studentsQuery->where('status', 'inactive');
        }
        $recentStudents = $studentsQuery->latest()->take(10)->get();

        return view('admin.registrar.dashboard', compact(
            'totalStudents', 
            'totalFaculties', 
            'totalDepartments', 
            'totalPrograms',
            'activeEnrollments',
            'recentStudents'
        ));
    }

    public function gradingCriteria()
    {
        $gradingCriteria = CourseEnrollment::getAcademicRegistrarGradingCriteria();
        return view('admin.registrar.grading-criteria', compact('gradingCriteria'));
    }

    public function updateGradingCriteria(Request $request)
    {
        $request->validate([
            'criteria' => 'required|array|min:1',
            'criteria.*.min' => 'required|numeric|min:0|max:100',
            'criteria.*.max' => 'required|numeric|min:0|max:100',
            'criteria.*.letter' => 'required|string|max:5',
            'criteria.*.grade_points' => 'required|numeric|min:0|max:5',
            'criteria.*.remark' => 'nullable|string|max:100',
        ]);

        $criteria = array_values($request->criteria);
        usort($criteria, fn($a, $b) => (float)$b['min'] <=> (float)$a['min']);

        \App\Models\SystemSetting::setSetting('academic_registrar_grading_criteria', $criteria, 'json', 'academic', true);

        return back()->with('success', 'Academic Registrar grading criteria updated successfully.');
    }
}
