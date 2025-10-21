<?php

namespace App\Http\Controllers\Faculty;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::with(['semester', 'department', 'enrollments'])
            ->where('instructor_id', Auth::id())
            ->when(request('semester'), function ($query, $semester) {
                $query->where('semester_id', $semester);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('faculty.courses.index', compact('courses'));
    }

    public function show(Course $course)
    {
        $this->authorize('view', $course);
        
        $course->load([
            'semester',
            'department',
            'enrollments.student',
            'assignments',
            'materials'
        ]);

        return view('faculty.courses.show', compact('course'));
    }

    public function students(Course $course)
    {
        $this->authorize('view', $course);
        
        $students = $course->enrollments()
            ->with('student')
            ->where('status', 'enrolled')
            ->paginate(20);

        return view('faculty.courses.students', compact('course', 'students'));
    }
}
