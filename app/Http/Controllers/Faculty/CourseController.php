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

        // Get available students for enrollment (not already enrolled)
        $availableStudents = \App\Models\User::where('role', 'student')
            ->where('is_active', true)
            ->whereNotIn('id', function($query) use ($course) {
                $query->select('user_id')
                      ->from('course_enrollments')
                      ->where('course_id', $course->id)
                      ->where('status', '!=', 'dropped');
            })
            ->with('studentProfile')
            ->orderBy('name')
            ->get();

        // Get active programs to filter students
        $programs = \App\Models\Program::where('is_active', true)->orderBy('name')->get();

        return view('faculty.courses.show', compact('course', 'availableStudents', 'programs'));
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

    public function enrollStudent(Request $request, Course $course)
    {
        $this->authorize('view', $course);

        $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:users,id',
        ]);

        $enrolledCount = 0;
        foreach ($request->student_ids as $studentId) {
            // Check if student is already enrolled
            if ($course->enrollments()->where('user_id', $studentId)->where('status', '!=', 'dropped')->exists()) {
                continue;
            }

            \App\Models\CourseEnrollment::create([
                'user_id' => $studentId,
                'course_id' => $course->id,
                'enrollment_date' => now(),
                'status' => 'enrolled',
            ]);
            $enrolledCount++;
        }

        if ($enrolledCount === 0) {
            return back()->with('info', 'No new students were enrolled (they may already be enrolled).');
        }

        return back()->with('success', "{$enrolledCount} student(s) enrolled successfully.");
    }

    public function dropStudent(Course $course, \App\Models\CourseEnrollment $enrollment)
    {
        $this->authorize('view', $course);

        // Ensure the enrollment belongs to this course
        if ($enrollment->course_id !== $course->id) {
            return back()->withErrors(['error' => 'Invalid enrollment for this course.']);
        }

        $enrollment->update(['status' => 'dropped']);

        return back()->with('success', 'Student dropped from course successfully.');
    }
}
