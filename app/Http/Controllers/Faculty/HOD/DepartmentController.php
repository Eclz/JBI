<?php

namespace App\Http\Controllers\Faculty\HOD;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\FacultyProfile;
use App\Models\Program;
use App\Models\Course;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    /**
     * Get the department where the current user is HOD.
     */
    private function getDepartment()
    {
        return Department::with(['faculty', 'programs', 'courses', 'facultyMembers.user'])
            ->where('head_of_department_id', auth()->id())
            ->firstOrFail();
    }

    public function index()
    {
        $department = $this->getDepartment();

        // Get basic stats for HOD Dashboard/Department view
        $totalStudents = \App\Models\StudentProfile::where('department_id', $department->id)->count();
        $totalLecturers = $department->facultyMembers->count();
        $totalPrograms = $department->programs->count();
        $totalCourses = $department->courses->count();

        return view('faculty.hod.department.index', compact(
            'department', 'totalStudents', 'totalLecturers', 'totalPrograms', 'totalCourses'
        ));
    }

    public function programs()
    {
        $department = $this->getDepartment();
        $programs = $department->programs()->paginate(15);
        return view('faculty.hod.department.programs', compact('department', 'programs'));
    }

    public function lecturers()
    {
        $department = $this->getDepartment();
        $lecturers = FacultyProfile::with('user')->where('department_id', $department->id)->paginate(15);
        return view('faculty.hod.department.lecturers', compact('department', 'lecturers'));
    }

    public function courses()
    {
        $department = $this->getDepartment();
        $courses = Course::where('department_id', $department->id)->paginate(15);
        return view('faculty.hod.department.courses', compact('department', 'courses'));
    }
}
