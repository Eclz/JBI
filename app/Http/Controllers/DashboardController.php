<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Course;
use App\Models\Assignment;
use App\Models\Announcement;
use App\Models\FeeRecord;
use App\Models\Attendance;
use App\Models\Grade;
use App\Models\Department;
use App\Models\AcademicYear;
use App\Models\Semester;
use App\Models\CourseEnrollment;
use App\Models\Payment;
use App\Models\Application;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = Auth::user();
        $role = $user->role;

        // Common data for all dashboards
        $data = [
            'user' => $user,
            'announcements' => Announcement::latest()->take(5)->get(),
            'currentSemester' => Semester::where('is_current', true)->first(),
            'currentAcademicYear' => AcademicYear::where('is_current', true)->first(),
        ];

        // Role-specific data
        switch ($role) {
            case 'admin':
                $data['totalStudents'] = User::where('role', 'student')->count();
                $data['totalFaculty'] = User::where('role', 'faculty')->count();
                $data['totalCourses'] = Course::count();
                $data['totalDepartments'] = Department::count();
                $data['activeStudents'] = User::where('role', 'student')->where('is_active', 1)->count();
                $data['activeFaculty'] = User::where('role', 'faculty')->where('is_active', 1)->count();

                // Financial data
                $data['totalRevenue'] = FeeRecord::sum('total_amount');
                $data['collectedRevenue'] = FeeRecord::sum('paid_amount');
                $data['pendingRevenue'] = FeeRecord::sum('balance_amount');
                $data['pendingFeesCount'] = FeeRecord::where('status', 'pending')->count();
                $data['overdueFeesCount'] = FeeRecord::where('status', 'overdue')->count();

                // Enrollment data
                $data['totalEnrollments'] = CourseEnrollment::count();
                $data['activeEnrollments'] = CourseEnrollment::where('status', 'active')->count();

                // Recent activity
                $data['recentUsers'] = User::latest()->take(5)->get();
                $data['recentEnrollments'] = CourseEnrollment::with(['student', 'course'])
                    ->latest()
                    ->take(5)
                    ->get();
                $data['recentPayments'] = Payment::with(['feeRecord.student'])
                    ->latest()
                    ->take(5)
                    ->get();

                // Monthly statistics
                $currentMonth = now()->startOfMonth();
                $data['monthlyEnrollments'] = CourseEnrollment::where('created_at', '>=', $currentMonth)->count();
                $data['monthlyRevenue'] = Payment::where('created_at', '>=', $currentMonth)->sum('amount');
                $data['monthlyNewStudents'] = User::where('role', 'student')
                    ->where('created_at', '>=', $currentMonth)
                    ->count();

                // Attendance overview
                $data['averageAttendance'] = Attendance::where('status', 'present')->count() /
                    max(1, Attendance::count()) * 100;

                // Academic performance
                $data['averageGPA'] = Grade::avg('points_earned') ?? 0;

                // Application statistics
                $data['pendingApplications'] = Application::where('status', 'pending')->count();
                $data['approvedApplications'] = Application::where('status', 'approved')->count();

                return view('dashboard.admin', $data);

            case 'faculty':
                $facultyId = $user->id;
                $data['myCourses'] = Course::where('instructor_id', $facultyId)->get();
                $data['pendingAssignments'] = Assignment::where('course_id', $facultyId)->count();
                $data['upcomingAssignments'] = Assignment::where('course_id', $facultyId)->where('due_date', '>', now())->orderBy('due_date')->take(5)->get();
                return view('dashboard.faculty', $data);

            case 'student':
                return redirect()->route('student.dashboard');

            default:
                return view('dashboard', $data);
        }
    }
}
