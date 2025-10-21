<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseEnrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    public function index()
    {
        $enrollments = CourseEnrollment::where('user_id', Auth::id())
            ->with(['course.semester', 'course.instructor', 'course.department'])
            ->where('status', 'enrolled')
            ->orderBy('enrollment_date', 'desc')
            ->paginate(20);

        return view('student.courses.index', compact('enrollments'));
    }

    public function show(Course $course)
    {
        $enrollment = $course->enrollments()
            ->where('user_id', Auth::id())
            ->where('status', 'enrolled')
            ->firstOrFail();

        $course->load([
            'semester',
            'instructor',
            'department',
            'assignments' => function ($query) {
                $query->orderBy('due_date', 'asc');
            }
        ]);

        // Get student's grades for this course
        $grades = $course->grades()->where('user_id', Auth::id())->get();

        // Get student's attendance for this course
        $attendanceRecords = $course->attendance()->where('user_id', Auth::id())->get();
        // $totalClasses = $course->attendance()->distinct('date')->count();
        $totalClasses = 0;
        $attendedClasses = $attendanceRecords->where('status', 'present')->count();
        $attendancePercentage = $totalClasses > 0 ? round(($attendedClasses / $totalClasses) * 100, 2) : 0;

        return view('student.courses.show', compact('course', 'enrollment', 'grades', 'attendanceRecords', 'attendancePercentage'));
    }

    public function materials(Course $course)
    {
        $enrollment = $course->enrollments()
            ->where('user_id', Auth::id())
            ->where('status', 'enrolled')
            ->firstOrFail();

        $materials = $course->materials()
            ->where('is_published', true)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('student.courses.materials', compact('course', 'materials'));
    }

    public function enroll(Request $request, Course $course)
    {
        // Check if student is already enrolled
        if ($course->enrollments()->where('user_id', Auth::id())->where('status', '!=', 'dropped')->exists()) {
            return back()->withErrors(['error' => 'You are already enrolled in this course.']);
        }

        // Check course capacity
        if ($course->capacity && $course->enrollments()->where('status', 'enrolled')->count() >= $course->capacity) {
            return back()->withErrors(['error' => 'This course is at full capacity.']);
        }

        CourseEnrollment::create([
            'user_id' => Auth::id(),
            'course_id' => $course->id,
            'enrollment_date' => now(),
            'status' => 'enrolled',
        ]);

        return back()->with('success', 'Successfully enrolled in the course.');
    }

    public function unenroll(Course $course)
    {
        $enrollment = $course->enrollments()
            ->where('user_id', Auth::id())
            ->where('status', 'enrolled')
            ->first();

        if (!$enrollment) {
            return back()->withErrors(['error' => 'You are not enrolled in this course.']);
        }

        $enrollment->update(['status' => 'dropped']);

        return back()->with('success', 'Successfully unenrolled from the course.');
    }
}
