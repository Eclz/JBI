<?php

namespace App\Http\Controllers\Faculty;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Assignment;
use App\Models\Grade;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GradingController extends Controller
{
    public function index()
    {
        $courses = Course::where('instructor_id', Auth::id())
            ->with(['semester', 'department'])
            ->orderBy('name')
            ->get();

        return view('faculty.grading.index', compact('courses'));
    }

    public function course(Course $course)
    {
        // Verify faculty teaches this course
        if ($course->instructor_id !== Auth::id()) {
            abort(403, 'Unauthorized access to course grading.');
        }

        $assignments = $course->assignments()
            ->orderBy('due_date', 'desc')
            ->get();

        $students = $course->enrollments()
            ->with('student')
            ->where('status', 'enrolled')
            ->get()
            ->pluck('student');

        return view('faculty.grading.course', compact('course', 'assignments', 'students'));
    }

    public function assignment(Assignment $assignment)
    {
        $course = $assignment->course;

        // Verify faculty teaches this course
        if ($course->instructor_id !== Auth::id()) {
            abort(403, 'Unauthorized access to assignment grading.');
        }

        $submissions = $assignment->submissions()
            ->with(['student', 'grade'])
            ->get();

        $students = $course->enrollments()
            ->with('student')
            ->where('status', 'enrolled')
            ->get()
            ->pluck('student');

        return view('faculty.grading.assignment', compact('assignment', 'submissions', 'students'));
    }

    public function storeGrade(Request $request, Assignment $assignment)
    {
        $course = $assignment->course;

        // Verify faculty teaches this course
        if ($course->instructor_id !== Auth::id()) {
            abort(403, 'Unauthorized access to grade assignment.');
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'points_earned' => 'required|numeric|min:0|max:' . $assignment->points,
            'comments' => 'nullable|string|max:1000',
            'is_published' => 'boolean'
        ]);

        $percentage = ($request->points_earned / $assignment->points) * 100;
        $letterGrade = $this->calculateLetterGrade($percentage);
        $gradePoints = $this->calculateGradePoints($percentage);

        Grade::updateOrCreate(
            [
                'user_id' => $request->user_id,
                'course_id' => $course->id,
                'assignment_id' => $assignment->id,
            ],
            [
                'grade_type' => 'assignment',
                'points_earned' => $request->points_earned,
                'points_possible' => $assignment->points,
                'percentage' => $percentage,
                'letter_grade' => $letterGrade,
                'grade_points' => $gradePoints,
                'comments' => $request->comments,
                'is_published' => $request->boolean('is_published', false),
                'graded_at' => now(),
                'graded_by' => Auth::id(),
            ]
        );

        return back()->with('success', 'Grade saved successfully.');
    }

    public function bulkGrade(Request $request, Assignment $assignment)
    {
        $course = $assignment->course;

        // Verify faculty teaches this course
        if ($course->instructor_id !== Auth::id()) {
            abort(403, 'Unauthorized access to bulk grading.');
        }

        $request->validate([
            'grades' => 'required|array',
            'grades.*.user_id' => 'required|exists:users,id',
            'grades.*.points_earned' => 'required|numeric|min:0|max:' . $assignment->points,
            'grades.*.comments' => 'nullable|string|max:1000',
        ]);

        DB::transaction(function () use ($request, $assignment, $course) {
            foreach ($request->grades as $gradeData) {
                $percentage = ($gradeData['points_earned'] / $assignment->points) * 100;
                $letterGrade = $this->calculateLetterGrade($percentage);
                $gradePoints = $this->calculateGradePoints($percentage);

                Grade::updateOrCreate(
                    [
                        'user_id' => $gradeData['user_id'],
                        'course_id' => $course->id,
                        'assignment_id' => $assignment->id,
                    ],
                    [
                        'grade_type' => 'assignment',
                        'points_earned' => $gradeData['points_earned'],
                        'points_possible' => $assignment->points,
                        'percentage' => $percentage,
                        'letter_grade' => $letterGrade,
                        'grade_points' => $gradePoints,
                        'comments' => $gradeData['comments'] ?? null,
                        'is_published' => $request->boolean('publish_all', false),
                        'graded_at' => now(),
                        'graded_by' => Auth::id(),
                    ]
                );
            }
        });

        return back()->with('success', 'Bulk grades saved successfully.');
    }

    public function publishGrades(Request $request, Assignment $assignment)
    {
        $course = $assignment->course;

        // Verify faculty teaches this course
        if ($course->instructor_id !== Auth::id()) {
            abort(403, 'Unauthorized access to publish grades.');
        }

        Grade::where('assignment_id', $assignment->id)
            ->where('course_id', $course->id)
            ->update(['is_published' => true]);

        return back()->with('success', 'All grades published successfully.');
    }

    public function gradebook(Course $course)
    {
        // Verify faculty teaches this course
        if ($course->instructor_id !== Auth::id()) {
            abort(403, 'Unauthorized access to gradebook.');
        }

        $students = $course->enrollments()
            ->with(['student', 'student.grades' => function ($query) use ($course) {
                $query->where('course_id', $course->id)->published();
            }])
            ->where('status', 'enrolled')
            ->get()
            ->pluck('student');

        $assignments = $course->assignments()
            ->orderBy('due_date')
            ->get();

        return view('faculty.grading.gradebook', compact('course', 'students', 'assignments'));
    }

    private function calculateLetterGrade($percentage)
    {
        if ($percentage >= 90) return 'A';
        if ($percentage >= 80) return 'B';
        if ($percentage >= 70) return 'C';
        if ($percentage >= 60) return 'D';
        return 'F';
    }

    private function calculateGradePoints($percentage)
    {
        if ($percentage >= 90) return 4.0;
        if ($percentage >= 80) return 3.0;
        if ($percentage >= 70) return 2.0;
        if ($percentage >= 60) return 1.0;
        return 0.0;
    }
}
