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
            'student_view' => 'nullable|in:all,active,applicants',
        ]);

        $studentView = $request->query('student_view', 'all');
        $totalStudents = Application::students()->where('status', 'admitted')->count();
        $totalFaculties = Faculty::count();
        $totalDepartments = Department::count();
        $totalPrograms = Program::count();
        $activeEnrollments = CourseEnrollment::where('status', 'enrolled')->count();

        if ($studentView === 'applicants') {
            $applicants = Application::students()
                ->whereIn('status', ['pending', 'under_review', 'approved'])
                ->with('programRecord')
                ->latest()
                ->take(10)
                ->get();
            $recentStudents = collect();
        } else {
            $admittedStudentEmails = Application::students()
                ->where('status', 'admitted')
                ->select('email');
            $studentsQuery = StudentProfile::with('user', 'program')
                ->whereHas('user', fn ($query) => $query->whereIn('email', $admittedStudentEmails));

            if ($studentView === 'active') {
                $studentsQuery->where('status', 'active');
            }

            $recentStudents = $studentsQuery->latest()->take(10)->get();
            $applicants = collect();
        }

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
