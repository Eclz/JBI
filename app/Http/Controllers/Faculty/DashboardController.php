<?php

namespace App\Http\Controllers\Faculty;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Assignment;
use App\Models\Attendance;
use App\Models\AssignmentSubmission;
use App\Models\CourseEnrollment;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $faculty = Auth::user();
        $courseIds = Course::where('instructor_id', $faculty->id)->pluck('id');

        // Get faculty courses
        $courses = Course::whereIn('id', $courseIds)
            ->with('semester')
            ->withCount([
                'enrollments as enrolled_students_count' => function ($query) {
                    $query->where('status', 'enrolled');
                },
            ])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $totalCourses = $courseIds->count();

        // Unique currently-enrolled students across all faculty courses.
        $totalStudents = CourseEnrollment::whereIn('course_id', $courseIds)
            ->where('status', 'enrolled')
            ->distinct('user_id')
            ->count('user_id');

        // Pending grading should count ungraded submitted submissions, not assignment rows.
        $pendingAssignments = AssignmentSubmission::whereHas('assignment', function ($query) use ($courseIds) {
                $query->whereIn('course_id', $courseIds);
            })
            ->where(function ($query) {
                $query->where('status', 'submitted')
                    ->orWhereNotNull('submitted_at');
            })
            ->whereNull('score')
            ->count();

        // Get upcoming assignments
        $upcomingAssignments = Assignment::whereIn('course_id', $courseIds)
            ->where('is_published', true)
            ->whereNotNull('due_date')
            ->where('due_date', '>=', now())
            ->with('course')
            ->orderBy('due_date')
            ->limit(5)
            ->get();

        // Recent submitted items only.
        $recentSubmissions = AssignmentSubmission::whereHas('assignment', function ($query) use ($courseIds) {
                $query->whereIn('course_id', $courseIds);
            })
            ->where(function ($query) {
                $query->where('status', 'submitted')
                    ->orWhereNotNull('submitted_at');
            })
            ->with(['assignment', 'student'])
            ->orderBy('submitted_at', 'desc')
            ->limit(10)
            ->get();

        // Total attendance records marked today for faculty-owned courses.
        $todayAttendance = Attendance::whereHas('course', function ($query) use ($faculty) {
                $query->where('instructor_id', $faculty->id);
            })
            ->whereDate('attendance_date', today())
            ->count();

        return view('faculty.dashboard', compact(
            'courses',
            'totalCourses',
            'totalStudents',
            'pendingAssignments',
            'upcomingAssignments',
            'recentSubmissions',
            'todayAttendance'
        ));
    }
}
