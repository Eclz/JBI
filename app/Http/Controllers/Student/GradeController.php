<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GradeController extends Controller
{
    public function index()
    {
        $grades = Grade::where('user_id', Auth::id())
            ->with(['course', 'assignment'])
            ->published()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $allGrades = Grade::where('user_id', Auth::id())
            ->with(['course'])
            ->published()
            ->get();

        $gpa = $this->calculateGPAFromGrades($allGrades);
        $totalCredits = $allGrades->pluck('course')->unique('id')->sum('credits');
        $completedCourses = $allGrades->pluck('course_id')->unique()->count();
        $averageScore = $allGrades->avg('final_grade') ?? 0;

        return view('student.grades.index', compact('grades', 'gpa', 'totalCredits', 'completedCourses', 'averageScore'));
    }

    public function show(Course $course)
    {
        // Verify student is enrolled in the course
        $enrollment = $course->enrollments()
            ->where('user_id', Auth::id())
            ->where('status', 'enrolled')
            ->firstOrFail();

        $grades = Grade::where('user_id', Auth::id())
            ->where('course_id', $course->id)
            ->with(['assignment'])
            ->published()
            ->orderBy('created_at', 'desc')
            ->get();

        $totalPoints = $grades->sum('points_earned');
        $maxPoints = $grades->sum('points_possible');
        $percentage = $maxPoints > 0 ? ($totalPoints / $maxPoints) * 100 : 0;

        return view('student.grades.show', compact('course', 'grades', 'totalPoints', 'maxPoints', 'percentage'));
    }

    public function transcript()
    {
        $grades = Grade::where('user_id', Auth::id())
            ->with(['course.semester', 'course.department'])
            ->published()
            ->get()
            ->groupBy('course_id');

        $gpa = $this->calculateGPA($grades);

        return view('student.grades.transcript', compact('grades', 'gpa'));
    }

    private function calculateGPAFromGrades($grades)
    {
        $courseGrades = $grades->groupBy('course_id');

        $totalGradePoints = 0;
        $totalCredits = 0;

        foreach ($courseGrades as $courseGrades) {
            $course = $courseGrades->first()->course;
            $courseAverage = $courseGrades->avg('final_grade') ?? $courseGrades->avg('percentage') ?? 0;

            // Convert percentage to grade points (4.0 scale)
            $gradePoints = $this->percentageToGradePoints($courseAverage);

            $totalGradePoints += $gradePoints * $course->credits;
            $totalCredits += $course->credits;
        }

        return $totalCredits > 0 ? $totalGradePoints / $totalCredits : 0;
    }

    private function calculateGPA($grades)
    {
        $totalGradePoints = 0;
        $totalCredits = 0;

        foreach ($grades as $courseGrades) {
            $course = $courseGrades->first()->course;
            $courseAverage = $courseGrades->avg('percentage');

            // Convert percentage to grade points (4.0 scale)
            $gradePoints = $this->percentageToGradePoints($courseAverage);

            $totalGradePoints += $gradePoints * $course->credits;
            $totalCredits += $course->credits;
        }

        return $totalCredits > 0 ? $totalGradePoints / $totalCredits : 0;
    }

    private function percentageToGradePoints($percentage)
    {
        if ($percentage >= 90) return 4.0;
        if ($percentage >= 80) return 3.0;
        if ($percentage >= 70) return 2.0;
        if ($percentage >= 60) return 1.0;
        return 0.0;
    }
}
