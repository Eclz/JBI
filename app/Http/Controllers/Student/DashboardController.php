<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\CourseEnrollment;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Grade;
use App\Models\Attendance;
use App\Models\FeeRecord;
use App\Models\Notification;
use App\Models\Semester;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class DashboardController extends Controller
{
    public function index()
    {
        $student = Auth::user();

        // Get student profile with department
        $studentProfile = $student->studentProfile()->with('department')->first();
        $currentSemester = Semester::where('is_current', true)->first();

        if ($studentProfile) {
            $this->enforcePaymentDeadlines($student, $studentProfile, $currentSemester);
        }

        // Get enrolled courses with relationships
        $enrolledCourses = $student->courseEnrollments()
            ->with(['course.department', 'course.instructor', 'course.semester'])
            ->where('status', 'enrolled')
            ->get();

        // Get upcoming assignments (due in next 7 days)
        $upcomingAssignments = Assignment::whereIn('course_id', $enrolledCourses->pluck('course.id'))
            ->where('is_published', true)
            ->where('due_date', '>=', now())
            ->where('due_date', '<=', now()->addDays(7))
            ->with(['course'])
            ->orderBy('due_date', 'asc')
            ->limit(5)
            ->get();

        // Get pending assignments (not submitted yet)
        $submittedAssignmentIds = AssignmentSubmission::where('user_id', $student->id)
            ->pluck('assignment_id')
            ->toArray();

        $pendingAssignments = Assignment::whereIn('course_id', $enrolledCourses->pluck('course.id'))
            ->where('is_published', true)
            ->where('due_date', '>=', now())
            ->whereNotIn('id', $submittedAssignmentIds)
            ->count();

        $courseIds = $enrolledCourses->pluck('course.id')->toArray();
        $totalClasses = \Illuminate\Support\Facades\DB::table(function ($query) use ($courseIds) {
            $query->select('course_id', 'attendance_date')
                ->from('attendance')
                ->whereIn('course_id', $courseIds)
                ->distinct();
        }, 'unique_classes')->count();

        $attendedClasses = Attendance::where('user_id', $student->id)
            ->whereIn('status', ['present', 'late'])
            ->count();

        $attendanceRate = $totalClasses > 0 ? round(($attendedClasses / $totalClasses) * 100, 1) : 0;

        // Get recent grades
        $recentGrades = Grade::where('user_id', $student->id)
            ->with(['course', 'assignment'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Calculate GPA (based on completed courses)
        $completedEnrollments = $student->courseEnrollments()
            ->where('status', 'completed')
            ->whereNotNull('grade_points')
            ->get();

        $currentGPA = $completedEnrollments->count() > 0
            ? round($completedEnrollments->avg('grade_points'), 2)
            : ($studentProfile->current_gpa ?? 0);

        // Get fee statistics
        $totalFees = FeeRecord::where('user_id', $student->id)->sum('amount');
        $paidFees = FeeRecord::where('user_id', $student->id)->sum('paid_amount');
        $pendingFees = $totalFees - $paidFees;

        // Get unread notifications
        $unreadNotifications = Notification::where('user_id', $student->id)
            ->where('is_read', false)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Calculate course statistics
        $courseStats = [
            'total_courses' => $enrolledCourses->count(),
            'completed_courses' => $student->courseEnrollments()
                ->where('status', 'completed')
                ->count(),
            'total_credits' => $enrolledCourses->sum('course.credits'),
            'credits_earned' => $studentProfile->total_credits_earned ?? 0,
        ];

        $registrationOpen = $currentSemester?->is_registration_open ?? false;
        $hasCurrentEnrollment = $currentSemester
            ? $student->courseEnrollments()->whereHas('course', function ($query) use ($currentSemester) {
                $query->where('semester_id', $currentSemester->id);
            })->where('status', 'enrolled')->exists()
            : false;

        // Get attendance by course
        $attendanceByCourse = $enrolledCourses->map(function($enrollment) use ($student) {
            $courseId = $enrollment->course_id;
            $total = Attendance::where('course_id', $courseId)
                ->distinct('attendance_date')
                ->count();
            $present = Attendance::where('course_id', $courseId)
                ->where('user_id', $student->id)
                ->whereIn('status', ['present', 'late'])
                ->count();

            return [
                'course' => $enrollment->course->name,
                'percentage' => $total > 0 ? round(($present / $total) * 100, 1) : 0,
                'present' => $present,
                'total' => $total,
            ];
        });

        return view('student.dashboard', compact(
            'student',
            'studentProfile',
            'enrolledCourses',
            'upcomingAssignments',
            'pendingAssignments',
            'attendanceRate',
            'recentGrades',
            'currentGPA',
            'totalFees',
            'paidFees',
            'pendingFees',
            'unreadNotifications',
            'courseStats',
            'attendanceByCourse',
            'currentSemester',
            'registrationOpen',
            'hasCurrentEnrollment'
        ));
    }

    private function enforcePaymentDeadlines($student, $studentProfile, $currentSemester): void
    {
        if ($studentProfile->status === 'inactive') {
            return;
        }

        if ($studentProfile->registration_deadline_at && !$studentProfile->registration_fee_paid_at) {
            if (now()->greaterThan($studentProfile->registration_deadline_at)) {
                $student->update(['is_active' => false]);
                $studentProfile->update(['status' => 'inactive']);

                Notification::create([
                    'user_id' => $student->id,
                    'type' => 'warning',
                    'title' => 'Account Deactivated',
                    'message' => 'Your registration fee was not paid before the deadline. Please contact administration.',
                    'priority' => 'high',
                ]);

                Mail::raw('Your account has been deactivated due to missing the registration payment deadline. Please contact administration.', function ($message) use ($student) {
                    $message->to($student->email)->subject('Registration Payment Deadline Missed');
                });
            }
            return;
        }

        $minPercent = (float) SystemSetting::getSetting('tuition_min_percent', 0);
        if ($minPercent <= 0 || !$currentSemester) {
            return;
        }

        $tuitionRecords = FeeRecord::where('user_id', $student->id)
            ->whereHas('feeStructure', function ($query) use ($currentSemester) {
                $query->where('type', 'tuition')
                    ->where('semester_id', $currentSemester->id);
            })
            ->get();

        $total = $tuitionRecords->sum('total_amount');
        $paid = $tuitionRecords->sum('paid_amount');
        $percent = $total > 0 ? round(($paid / $total) * 100, 2) : 100;

        $deadline = $studentProfile->tuition_deadline_at;
        if ($deadline && now()->greaterThan($deadline) && $percent < $minPercent) {
            $student->update(['is_active' => false]);
            $studentProfile->update(['status' => 'inactive']);

            Notification::create([
                'user_id' => $student->id,
                'type' => 'warning',
                'title' => 'Account Deactivated',
                'message' => 'You missed the tuition payment deadline and your account was deactivated.',
                'priority' => 'high',
            ]);

            Mail::raw('Your account has been deactivated due to missing the tuition payment deadline. Please contact administration.', function ($message) use ($student) {
                $message->to($student->email)->subject('Tuition Payment Deadline Missed');
            });
            return;
        }

        if ($currentSemester->start_date && now()->greaterThanOrEqualTo($currentSemester->start_date) && $percent < $minPercent) {
            $student->update(['is_active' => false]);
            $studentProfile->update(['status' => 'inactive']);

            Notification::create([
                'user_id' => $student->id,
                'type' => 'warning',
                'title' => 'Account Deactivated',
                'message' => 'You did not meet the tuition payment requirement before the semester started.',
                'priority' => 'high',
            ]);

            Mail::raw('Your account has been deactivated because you did not meet the tuition payment requirement before the semester started. Please contact administration.', function ($message) use ($student) {
                $message->to($student->email)->subject('Tuition Requirement Not Met');
            });
        }
    }
}
