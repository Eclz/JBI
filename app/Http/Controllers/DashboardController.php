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
                $data['recentUsers'] = User::latest()->take(5)->get();
                $data['pendingFees'] = FeeRecord::where('status', 'pending')->count();
                return view('dashboard.admin', $data);

            case 'faculty':
                $facultyId = $user->id;
                $data['myCourses'] = Course::where('instructor_id', $facultyId)->get();
                $data['pendingAssignments'] = Assignment::where('course_id', $facultyId)->count();
                $data['upcomingAssignments'] = Assignment::where('course_id', $facultyId)->where('due_date', '>', now())->orderBy('due_date')->take(5)->get();
                return view('dashboard.faculty', $data);

            case 'student':
                $studentId = $user->id;
                $data['enrolledCourses'] = Course::whereHas('enrollments', function($query) use ($studentId) {
                    $query->where('user_id', $studentId);
                })->get();
                $data['pendingAssignments'] = Assignment::whereHas('course.enrollments', function($query) use ($studentId) {
                    $query->where('user_id', $studentId);
                })->where('due_date', '>', now())->count();
                $data['upcomingAssignments'] = Assignment::whereHas('course.enrollments', function($query) use ($studentId) {
                    $query->where('user_id', $studentId);
                })->where('due_date', '>', now())->orderBy('due_date')->take(5)->get();
                $data['recentGrades'] = Grade::where('user_id', $studentId)->latest()->take(5)->get();
                $data['attendanceRate'] = Attendance::where('user_id', $studentId)->where('status', 'present')->count() / max(1, Attendance::where('user_id', $studentId)->count()) * 100;
                return view('dashboard.student', $data);

            default:
                return view('dashboard', $data);
        }
    }
}
