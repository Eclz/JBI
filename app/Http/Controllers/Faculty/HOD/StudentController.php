<?php

namespace App\Http\Controllers\Faculty\HOD;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\User;
use App\Models\StudentProfile;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    /**
     * Get the department where the current user is HOD.
     */
    private function getDepartment()
    {
        return Department::where('head_of_department_id', auth()->id())->firstOrFail();
    }

    public function index(Request $request)
    {
        $department = $this->getDepartment();

        $query = StudentProfile::with(['user', 'program'])
            ->where('department_id', $department->id);

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->whereHas('user', function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })->orWhere('admission_number', 'like', "%{$search}%");
        }

        $students = $query->paginate(15);

        return view('faculty.hod.students.index', compact('students', 'department'));
    }

    public function show($id)
    {
        $department = $this->getDepartment();

        $studentProfile = StudentProfile::with(['user', 'program'])
            ->where('department_id', $department->id)
            ->where('id', $id)
            ->firstOrFail();

        return view('faculty.hod.students.show', compact('studentProfile', 'department'));
    }
}
