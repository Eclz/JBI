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
}
