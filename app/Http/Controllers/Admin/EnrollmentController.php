<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CourseEnrollment;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EnrollmentController extends Controller
{
    public function index(Request $request)
    {
        $query = CourseEnrollment::with(['student', 'course', 'semester']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by course
        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        // Filter by semester
        if ($request->filled('semester_id')) {
            $query->where('semester_id', $request->semester_id);
        }

        // Search by student name or admission number
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('student', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('admission_number', 'like', "%{$search}%");
            });
        }

        $enrollments = $query->latest()->paginate(20);

        // Get filter options
        $courses = Course::orderBy('name')->get();
        $semesters = \App\Models\Semester::orderBy('name')->get();

        return view('admin.enrollments.index', compact('enrollments', 'courses', 'semesters'));
    }

    public function create()
    {
        $students = User::where('role', 'student')
                        ->orderBy('name')
                        ->get();
        $courses = Course::with('semester')
                        ->where('status', 'active')
                        ->orderBy('name')
                        ->get();

        return view('admin.enrollments.create', compact('students', 'courses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:users,id',
            'course_id' => 'required|exists:courses,id',
            'enrollment_date' => 'required|date',
            'status' => 'required|in:enrolled,dropped,completed,pending',
        ]);

        // Check if enrollment already exists
        $exists = CourseEnrollment::where('student_id', $request->student_id)
                                  ->where('course_id', $request->course_id)
                                  ->exists();

        if ($exists) {
            return back()->with('error', 'Student is already enrolled in this course.');
        }

        // Get course details for semester
        $course = Course::findOrFail($request->course_id);

        CourseEnrollment::create([
            'student_id' => $request->student_id,
            'course_id' => $request->course_id,
            'semester_id' => $course->semester_id,
            'enrollment_date' => $request->enrollment_date,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.enrollments.index')
                        ->with('success', 'Student enrolled successfully.');
    }

    public function show(CourseEnrollment $enrollment)
    {
        $enrollment->load(['student', 'course', 'semester', 'grades', 'attendances']);

        return view('admin.enrollments.show', compact('enrollment'));
    }

    public function edit(CourseEnrollment $enrollment)
    {
        $students = User::where('role', 'student')
                        ->orderBy('name')
                        ->get();
        $courses = Course::with('semester')
                        ->orderBy('name')
                        ->get();

        return view('admin.enrollments.edit', compact('enrollment', 'students', 'courses'));
    }

    public function update(Request $request, CourseEnrollment $enrollment)
    {
        $request->validate([
            'status' => 'required|in:enrolled,dropped,completed,pending',
            'enrollment_date' => 'required|date',
            'completion_date' => 'nullable|date|after:enrollment_date',
        ]);

        $enrollment->update($request->only(['status', 'enrollment_date', 'completion_date']));

        return redirect()->route('admin.enrollments.index')
                        ->with('success', 'Enrollment updated successfully.');
    }

    public function destroy(CourseEnrollment $enrollment)
    {
        $enrollment->delete();

        return redirect()->route('admin.enrollments.index')
                        ->with('success', 'Enrollment deleted successfully.');
    }

    public function approve(CourseEnrollment $enrollment)
    {
        $enrollment->update(['status' => 'enrolled']);

        return back()->with('success', 'Enrollment approved successfully.');
    }

    public function reject(CourseEnrollment $enrollment)
    {
        $enrollment->update(['status' => 'dropped']);

        return back()->with('success', 'Enrollment rejected.');
    }

    public function bulkEnroll(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:users,id',
        ]);

        $course = Course::findOrFail($request->course_id);
        $enrolled = 0;
        $skipped = 0;

        foreach ($request->student_ids as $studentId) {
            $exists = CourseEnrollment::where('student_id', $studentId)
                                      ->where('course_id', $request->course_id)
                                      ->exists();

            if (!$exists) {
                CourseEnrollment::create([
                    'student_id' => $studentId,
                    'course_id' => $request->course_id,
                    'semester_id' => $course->semester_id,
                    'enrollment_date' => now(),
                    'status' => 'enrolled',
                ]);
                $enrolled++;
            } else {
                $skipped++;
            }
        }

        return back()->with('success', "Enrolled {$enrolled} students. Skipped {$skipped} (already enrolled).");
    }
}
