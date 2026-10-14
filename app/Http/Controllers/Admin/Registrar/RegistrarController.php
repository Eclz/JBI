<?php

namespace App\Http\Controllers\Admin\Registrar;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StudentProfile;
use App\Models\Faculty;
use App\Models\Department;
use App\Models\CourseEnrollment;
use App\Models\Program;

class RegistrarController extends Controller
{
    public function dashboard()
    {
        $totalStudents = StudentProfile::count();
        $totalFaculties = Faculty::count();
        $totalDepartments = Department::count();
        $totalPrograms = Program::count();
        $activeEnrollments = CourseEnrollment::where('status', 'enrolled')->count();
        
        $recentStudents = StudentProfile::with('user', 'program')->latest()->take(10)->get();

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
