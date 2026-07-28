<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\StudentProfile;
use App\Models\FacultyProfile;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\FeeRecord;
use App\Models\FeeStructure;
use App\Models\Grade;
use App\Models\Attendance;
use App\Models\Department;
use App\Models\AcademicYear;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Display reports dashboard
     */
    public function index()
    {
        $stats = [
            'total_students' => User::where('role', 'student')->count(),
            'active_students' => User::where('role', 'student')->where('is_active', true)->count(),
            'total_faculty' => User::where('role', 'faculty')->count(),
            'active_faculty' => User::where('role', 'faculty')->where('is_active', true)->count(),
            'total_courses' => Course::count(),
            'active_courses' => Course::where('status', 'active')->count(),
            'total_enrollments' => CourseEnrollment::where('status', 'enrolled')->count(),
            'total_revenue' => FeeRecord::sum('paid_amount'),
            'pending_fees' => FeeRecord::whereIn('status', ['pending', 'partial'])->sum('balance_amount'),
            'average_attendance' => $this->calculateAverageAttendance(),
        ];

        // Recent enrollments
        $recentEnrollments = CourseEnrollment::with(['student', 'course'])
            ->latest()
            ->take(5)
            ->get();

        // Recent payments
        $recentPayments = FeeRecord::with(['student', 'feeStructure'])
            ->where('paid_amount', '>', 0)
            ->latest('updated_at')
            ->take(5)
            ->get();

        // Enrollment trend (last 7 days)
        $enrollmentTrend = CourseEnrollment::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Revenue trend (last 30 days)
        $revenueTrend = FeeRecord::selectRaw('DATE(updated_at) as date, SUM(paid_amount) as revenue')
            ->where('updated_at', '>=', now()->subDays(30))
            ->where('paid_amount', '>', 0)
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('admin.reports.index', compact(
            'stats',
            'recentEnrollments',
            'recentPayments',
            'enrollmentTrend',
            'revenueTrend'
        ));
    }

    /**
     * Student Reports
     */
    public function students(Request $request)
    {
        $query = User::where('role', 'student')
            ->with(['studentProfile.department']);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        if ($request->filled('department_id')) {
            $query->whereHas('studentProfile', function ($q) use ($request) {
                $q->where('department_id', $request->department_id);
            });
        }

        if ($request->filled('admission_year')) {
            $query->whereHas('studentProfile', function ($q) use ($request) {
                $q->whereYear('admission_date', $request->admission_year);
            });
        }

        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhereHas('studentProfile', function ($q) use ($search) {
                        $q->where('admission_number', 'like', "%{$search}%");
                    });
            });
        }

        $students = $query->paginate(50);

        // Statistics
        $stats = [
            'total_students' => User::where('role', 'student')->count(),
            'active_students' => User::where('role', 'student')->where('is_active', true)->count(),
            'inactive_students' => User::where('role', 'student')->where('is_active', false)->count(),
            'male_students' => User::where('role', 'student')->where('gender', 'male')->count(),
            'female_students' => User::where('role', 'student')->where('gender', 'female')->count(),
        ];

        $studentsByStatus = User::where('role', 'student')
            ->selectRaw("CASE WHEN is_active = 1 THEN 'active' ELSE 'inactive' END as status, COUNT(*) as count")
            ->groupBy('status')
            ->get();

        $recentAdmissions = User::where('role', 'student')
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Students by department
        $studentsByDepartment = StudentProfile::select('department_id', DB::raw('COUNT(*) as count'))
            ->with('department')
            ->groupBy('department_id')
            ->orderBy('count', 'desc')
            ->get();

        // Students by admission year
        $studentsByYear = StudentProfile::selectRaw('YEAR(admission_date) as year, COUNT(*) as count')
            ->whereNotNull('admission_date')
            ->groupBy('year')
            ->orderBy('year', 'desc')
            ->get();

        // Get filter options
        $departments = Department::orderBy('name')->get();
        $years = range(date('Y'), date('Y') - 10);

        return view('admin.reports.students', compact(
            'students',
            'stats',
            'studentsByStatus',
            'recentAdmissions',
            'studentsByDepartment',
            'studentsByYear',
            'departments',
            'years'
        ));
    }

    /**
     * Faculty Reports
     */
    public function faculty(Request $request)
    {
        $query = User::where('role', 'faculty')
            ->with(['facultyProfile.department']);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        if ($request->filled('department_id')) {
            $query->whereHas('facultyProfile', function ($q) use ($request) {
                $q->where('department_id', $request->department_id);
            });
        }

        if ($request->filled('employment_status')) {
            $query->whereHas('facultyProfile', function ($q) use ($request) {
                $q->where('employment_status', $request->employment_status);
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhereHas('facultyProfile', function ($q) use ($search) {
                        $q->where('employee_id', 'like', "%{$search}%");
                    });
            });
        }

        $faculty = $query->paginate(50);

        // Statistics
        $stats = [
            'total_faculty' => User::where('role', 'faculty')->count(),
            'active_faculty' => User::where('role', 'faculty')->where('is_active', true)->count(),
            'inactive_faculty' => User::where('role', 'faculty')->where('is_active', false)->count(),
        ];

        // Faculty by department
        $facultyByDepartment = FacultyProfile::select('department_id', DB::raw('COUNT(*) as count'))
            ->with('department')
            ->groupBy('department_id')
            ->orderBy('count', 'desc')
            ->get();

        // Faculty by employment status
        $facultyByStatus = FacultyProfile::select('employment_status', DB::raw('COUNT(*) as count'))
            ->whereNotNull('employment_status')
            ->groupBy('employment_status')
            ->get();

        // Course load per faculty
        $facultyLoad = User::where('role', 'faculty')
            ->withCount(['taughtCourses as active_courses' => function ($query) {
                $query->where('status', 'active');
            }])
            ->with('facultyProfile.department')
            ->orderBy('active_courses', 'desc')
            ->take(10)
            ->get();

        // Get filter options
        $departments = Department::orderBy('name')->get();
        $employmentStatuses = ['full-time', 'part-time', 'contract', 'adjunct'];

        return view('admin.reports.faculty', compact(
            'faculty',
            'stats',
            'facultyByDepartment',
            'facultyByStatus',
            'facultyLoad',
            'departments',
            'employmentStatuses'
        ));
    }

    /**
     * Course Reports
     */
    public function courses(Request $request)
    {
        $query = Course::with(['department', 'instructor', 'semester']);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status === 'active' ? 'active' : 'inactive');
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('semester_id')) {
            $query->where('semester_id', $request->semester_id);
        }

        if ($request->filled('instructor_id')) {
            $query->where('instructor_id', $request->instructor_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $courses = $query->withCount([
            'enrollments',
            'enrollments as active_enrollments' => function ($query) {
                $query->where('status', 'enrolled');
            }
        ])->paginate(50);

        // Statistics
        $stats = [
            'total_courses' => Course::count(),
            'active_courses' => Course::where('status', 'active')->count(),
            'inactive_courses' => Course::where('status', 'inactive')->count(),
            'total_enrollments' => CourseEnrollment::where('status', 'enrolled')->count(),
        ];

        // Courses by department
        $coursesByDepartment = Course::select('department_id', DB::raw('COUNT(*) as count'))
            ->with('department')
            ->groupBy('department_id')
            ->orderBy('count', 'desc')
            ->get();

        // Most enrolled courses
        $popularCourses = Course::withCount([
            'enrollments as enrollment_count' => function ($query) {
                $query->where('status', 'enrolled');
            }
        ])
            ->orderBy('enrollment_count', 'desc')
            ->take(10)
            ->get();

        // Get filter options
        $departments = Department::orderBy('name')->get();
        $semesters = Semester::orderBy('start_date', 'desc')->get();
        $instructors = User::where('role', 'faculty')
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        return view('admin.reports.courses', compact(
            'courses',
            'stats',
            'coursesByDepartment',
            'popularCourses',
            'departments',
            'semesters',
            'instructors'
        ));
    }

    /**
     * Fee Reports
     */
    public function fees(Request $request)
    {
        $query = FeeRecord::with(['student', 'feeStructure.academicYear', 'processor']);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('academic_year_id')) {
            $query->whereHas('feeStructure', function ($q) use ($request) {
                $q->where('academic_year_id', $request->academic_year_id);
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                    ->orWhereHas('student', function ($studentQuery) use ($search) {
                        $studentQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        $feeRecords = $query->latest()->paginate(50);

        // Financial summary
        $summary = [
            'total_billed' => FeeRecord::sum('total_amount'),
            'total_paid' => FeeRecord::sum('paid_amount'),
            'total_balance' => FeeRecord::sum('balance_amount'),
            'total_discounts' => FeeRecord::sum('discount_amount'),
            'total_late_fees' => FeeRecord::sum('late_fee'),
        ];

        // Status breakdown
        $statusBreakdown = FeeRecord::select('status', DB::raw('COUNT(*) as count'), DB::raw('SUM(balance_amount) as total_balance'))
            ->groupBy('status')
            ->get();

        // Payment trends (last 12 months)
        $paymentTrends = FeeRecord::selectRaw('DATE_FORMAT(updated_at, "%Y-%m") as month, SUM(paid_amount) as total')
            ->where('updated_at', '>=', now()->subMonths(12))
            ->where('paid_amount', '>', 0)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Top paying students
        $topPayers = FeeRecord::select('user_id', DB::raw('SUM(paid_amount) as total_paid'))
            ->with('student')
            ->groupBy('user_id')
            ->orderBy('total_paid', 'desc')
            ->take(10)
            ->get();

        $academicYears = AcademicYear::whereHas('feeStructures')
            ->orderBy('year', 'desc')
            ->get();

        return view('admin.reports.fees', compact(
            'feeRecords',
            'summary',
            'statusBreakdown',
            'paymentTrends',
            'topPayers',
            'academicYears'
        ));
    }

    /**
     * Enrollment Report
     */
    public function enrollment(Request $request)
    {
        $semesterId = $request->get('semester_id');
        $departmentId = $request->get('department_id');

        $query = CourseEnrollment::with(['student', 'course', 'semester']);

        if ($semesterId) {
            $query->where('semester_id', $semesterId);
        }

        if ($departmentId) {
            $query->whereHas('course', function ($q) use ($departmentId) {
                $q->where('department_id', $departmentId);
            });
        }

        if ($request->filled('academic_year_id')) {
            $academicYearId = $request->get('academic_year_id');
            $query->whereHas('course.semester', function ($q) use ($academicYearId) {
                $q->where('academic_year_id', $academicYearId);
            });
        }

        $enrollments = $query->latest()->paginate(50);

        // Statistics
        $stats = [
            'total_enrollments' => CourseEnrollment::count(),
            'active_enrollments' => CourseEnrollment::where('status', 'enrolled')->count(),
            'dropped_enrollments' => CourseEnrollment::where('status', 'dropped')->count(),
            'completed_enrollments' => CourseEnrollment::where('status', 'completed')->count(),
        ];

        // Enrollment trends (last 30 days)
        $enrollmentTrends = CourseEnrollment::selectRaw('DATE(enrollment_date) as date, COUNT(*) as count')
            ->where('enrollment_date', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Enrollments by department
        $enrollmentsByDepartment = CourseEnrollment::select('courses.department_id', DB::raw('COUNT(*) as count'))
            ->join('courses', 'course_enrollments.course_id', '=', 'courses.id')
            ->leftJoin('departments', 'courses.department_id', '=', 'departments.id')
            ->selectRaw('departments.name as department_name')
            ->where('course_enrollments.status', 'enrolled')
            ->groupBy('courses.department_id', 'departments.name')
            ->orderBy('count', 'desc')
            ->get();

        // Enrollments by semester
        $enrollmentsBySemester = CourseEnrollment::select('semester_id', DB::raw('COUNT(*) as count'))
            ->with('semester')
            ->groupBy('semester_id')
            ->orderBy('semester_id', 'desc')
            ->get();

        // Get filter options
        $semesters = Semester::orderBy('start_date', 'desc')->get();
        $departments = Department::orderBy('name')->get();
        $academicYears = AcademicYear::orderBy('start_date', 'desc')->get();

        return view('admin.reports.enrollment', compact(
            'enrollments',
            'stats',
            'enrollmentTrends',
            'enrollmentsByDepartment',
            'enrollmentsBySemester',
            'semesters',
            'departments',
            'academicYears'
        ));
    }

    /**
     * Financial Report
     */
    public function financial(Request $request)
    {
        $dateFrom = $request->input('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->input('date_to', now()->format('Y-m-d'));
        $status = $request->input('status');
        $semesterId = $request->input('semester_id');
        $academicYearId = $request->input('academic_year_id');

        // Revenue summary
        $revenue = [
            'total_billed' => FeeRecord::whereBetween('created_at', [$dateFrom, $dateTo])->sum('total_amount'),
            'total_collected' => FeeRecord::whereBetween('updated_at', [$dateFrom, $dateTo])->sum('paid_amount'),
            'total_pending' => FeeRecord::whereBetween('created_at', [$dateFrom, $dateTo])->where('status', '!=', 'paid')->sum('balance_amount'),
            'collection_rate' => 0,
        ];

        if ($revenue['total_billed'] > 0) {
            $revenue['collection_rate'] = round(($revenue['total_collected'] / $revenue['total_billed']) * 100, 2);
        }

        // Daily collection trends
        $collectionTrends = FeeRecord::selectRaw('DATE(updated_at) as date, SUM(paid_amount) as total')
            ->whereBetween('updated_at', [$dateFrom, $dateTo])
            ->where('paid_amount', '>', 0)
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Payment method breakdown
        $paymentMethods = FeeRecord::select('payment_method', DB::raw('COUNT(*) as count'), DB::raw('SUM(paid_amount) as total'))
            ->whereBetween('updated_at', [$dateFrom, $dateTo])
            ->whereNotNull('payment_method')
            ->groupBy('payment_method')
            ->get();

        $paymentBreakdown = FeeRecord::select('status', DB::raw('COUNT(*) as count'), DB::raw('SUM(total_amount) as total'))
            ->groupBy('status')
            ->get();

        // Fee structure breakdown
        $feeStructureRevenue = FeeRecord::select('fee_structure_id', DB::raw('COUNT(*) as records'), DB::raw('SUM(paid_amount) as collected'), DB::raw('SUM(total_amount) as total'))
            ->with('feeStructure')
            ->whereBetween('updated_at', [$dateFrom, $dateTo])
            ->groupBy('fee_structure_id')
            ->orderBy('collected', 'desc')
            ->get();

        // Outstanding payments
        $outstandingPayments = FeeRecord::with(['student', 'feeStructure'])
            ->whereIn('status', ['pending', 'partial', 'overdue'])
            ->orderBy('due_date')
            ->take(20)
            ->get();

        $feeRecords = FeeRecord::with(['student', 'feeStructure.academicYear', 'feeStructure.semester'])
            ->when($status, function ($query, $statusValue) {
                $query->where('status', $statusValue);
            })
            ->when($semesterId, function ($query, $semesterValue) {
                $query->whereHas('feeStructure', function ($feeQuery) use ($semesterValue) {
                    $feeQuery->where('semester_id', $semesterValue);
                });
            })
            ->when($academicYearId, function ($query, $academicYearValue) {
                $query->whereHas('feeStructure', function ($feeQuery) use ($academicYearValue) {
                    $feeQuery->where('academic_year_id', $academicYearValue);
                });
            })
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->latest()
            ->paginate(50)
            ->appends($request->query());

        $semesters = Semester::orderBy('start_date', 'desc')->get();
        $academicYears = AcademicYear::orderBy('start_date', 'desc')->get();

        return view('admin.reports.financial', compact(
            'revenue',
            'paymentBreakdown',
            'collectionTrends',
            'paymentMethods',
            'feeStructureRevenue',
            'outstandingPayments',
            'feeRecords',
            'semesters',
            'academicYears',
            'dateFrom',
            'dateTo'
        ));
    }

    /**
     * Academic Performance Report
     */
    public function academic(Request $request)
    {
        $semesterId = $request->get('semester_id');
        $departmentId = $request->get('department_id');
        $academicYearId = $request->get('academic_year_id');

        $query = Grade::with(['student', 'course', 'assignment']);

        if ($semesterId) {
            $query->whereHas('course', function ($q) use ($semesterId) {
                $q->where('semester_id', $semesterId);
            });
        }

        if ($departmentId) {
            $query->whereHas('course', function ($q) use ($departmentId) {
                $q->where('department_id', $departmentId);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($academicYearId) {
            $query->whereHas('course.semester', function ($q) use ($academicYearId) {
                $q->where('academic_year_id', $academicYearId);
            });
        }

        $grades = $query->latest()->paginate(50);

        $allGrades = (clone $query)->get();
        $totalGrades = $allGrades->count();
        $averageGPA = $totalGrades > 0 ? round($allGrades->avg('percentage'), 2) : 0;

        // Calculate pass/fail rates from filtered grades
        $passCount = $allGrades->where('percentage', '>=', 50)->count();
        $failCount = $allGrades->where('percentage', '<', 50)->count();
        $passRate = $totalGrades > 0 ? ($passCount / $totalGrades) * 100 : 0;
        $failRate = $totalGrades > 0 ? ($failCount / $totalGrades) * 100 : 0;

        $stats = [
            'total_grades' => $totalGrades,
            'average_gpa' => $averageGPA,
            'pass_rate' => round($passRate, 1),
            'fail_rate' => round($failRate, 1),
        ];

        // Grade distribution
        $gradeDistribution = DB::table('grades')
            ->selectRaw('
                CASE
                    WHEN percentage >= 90 THEN "A"
                    WHEN percentage >= 80 THEN "B"
                    WHEN percentage >= 70 THEN "C"
                    WHEN percentage >= 50 THEN "D"
                    ELSE "F"
                END as grade,
                COUNT(*) as count
            ')
            ->groupBy('grade')
            ->orderBy('grade')
            ->get();

        // Top performers
        $topPerformers = User::where('role', 'student')
            ->with('studentProfile.department')
            ->withAvg('grades as avg_gpa', 'percentage')
            ->orderBy('avg_gpa', 'desc')
            ->take(10)
            ->get();

        // Course performance
        $coursePerformance = DB::table('courses')
            ->select(
                'courses.code as course_code',
                'courses.name as course_name',
                DB::raw('COUNT(DISTINCT course_enrollments.id) as enrolled_count'),
                DB::raw('COUNT(grades.id) as graded_count'),
                DB::raw('AVG(grades.percentage) as avg_grade'),
                DB::raw('ROUND((SUM(CASE WHEN grades.percentage >= 50 THEN 1 ELSE 0 END) / NULLIF(COUNT(grades.id), 0)) * 100, 2) as pass_rate')
            )
            ->leftJoin('course_enrollments', 'courses.id', '=', 'course_enrollments.course_id')
            ->leftJoin('grades', function($join) {
                $join->on('course_enrollments.user_id', '=', 'grades.user_id')
                     ->on('course_enrollments.course_id', '=', 'grades.course_id');
            })
            ->groupBy('courses.id', 'courses.code', 'courses.name')
            ->get();

        // Get filter options
        $academicYears = AcademicYear::orderBy('start_date', 'desc')->get();
        $semesters = Semester::orderBy('start_date', 'desc')->get();
        $departments = Department::orderBy('name')->get();

        return view('admin.reports.academic', compact(
            'grades',
            'stats',
            'gradeDistribution',
            'topPerformers',
            'coursePerformance',
            'academicYears',
            'semesters',
            'departments'
        ));
    }

    /**
     * Attendance Report
     */
    public function attendance(Request $request)
    {
        $dateFrom = $request->input('date_from', now()->subDays(30)->format('Y-m-d'));
        $dateTo = $request->input('date_to', now()->format('Y-m-d'));
        $courseId = $request->input('course_id');
        $departmentId = $request->input('department_id');

        $query = Attendance::with(['student', 'course']);

        if ($dateFrom && $dateTo) {
            $query->whereBetween('attendance_date', [$dateFrom, $dateTo]);
        }

        if ($courseId) {
            $query->where('course_id', $courseId);
        }

        if ($departmentId) {
            $query->whereHas('course', function ($q) use ($departmentId) {
                $q->where('department_id', $departmentId);
            });
        }

        $attendanceRecords = $query->latest('attendance_date')->paginate(50);

        // Overall statistics
        $totalRecords = $query->count();
        $stats = [
            'total_records' => $totalRecords,
            'present' => (clone $query)->where('status', 'present')->count(),
            'absent' => (clone $query)->where('status', 'absent')->count(),
            'late' => (clone $query)->where('status', 'late')->count(),
            'excused' => (clone $query)->where('status', 'excused')->count(),
            'attendance_rate' => $totalRecords > 0 ? round(((clone $query)->where('status', 'present')->count() / $totalRecords) * 100, 2) : 0,
        ];

        // Daily attendance trends
        $attendanceTrends = Attendance::selectRaw('DATE(attendance_date) as date, COUNT(*) as total, SUM(CASE WHEN status = "present" THEN 1 ELSE 0 END) as present')
            ->whereBetween('attendance_date', [$dateFrom, $dateTo])
            ->groupBy('attendance_date')
            ->orderBy('attendance_date')
            ->get()
            ->map(function ($item) {
                $item->rate = $item->total > 0 ? round(($item->present / $item->total) * 100, 2) : 0;
                return $item;
            });

        // Course-wise attendance
        $courseAttendance = Attendance::select('course_id', DB::raw('COUNT(*) as total'), DB::raw('SUM(CASE WHEN status = "present" THEN 1 ELSE 0 END) as present'))
            ->with('course')
            ->whereBetween('attendance_date', [$dateFrom, $dateTo])
            ->groupBy('course_id')
            ->get()
            ->map(function ($item) {
                $item->rate = $item->total > 0 ? round(($item->present / $item->total) * 100, 2) : 0;
                return $item;
            })
            ->sortByDesc('rate');

        // Students with low attendance
        $lowAttendanceStudents = Attendance::select('user_id', DB::raw('COUNT(*) as total'), DB::raw('SUM(CASE WHEN status = "present" THEN 1 ELSE 0 END) as present'))
            ->with('student')
            ->whereBetween('attendance_date', [$dateFrom, $dateTo])
            ->groupBy('user_id')
            ->get()
            ->map(function ($item) {
                $item->rate = $item->total > 0 ? round(($item->present / $item->total) * 100, 2) : 0;
                return $item;
            })
            ->filter(function ($item) {
                return $item->rate < 75;
            })
            ->sortBy('rate')
            ->take(20);

        // Get filter options
        $courses = Course::where('status', true)->orderBy('name')->get();
        $departments = Department::orderBy('name')->get();

        return view('admin.reports.attendance', compact(
            'attendanceRecords',
            'stats',
            'attendanceTrends',
            'courseAttendance',
            'lowAttendanceStudents',
            'courses',
            'departments',
            'dateFrom',
            'dateTo'
        ));
    }

    /**
     * Export Students Report
     */
    public function exportStudents(Request $request)
    {
        $students = User::where('role', 'student')->with('studentProfile.department')->get();

        $csv = "Name,Email,Admission Number,Department,Status,Date of Admission\n";

        foreach ($students as $student) {
            $csv .= sprintf(
                '"%s","%s","%s","%s","%s","%s"' . "\n",
                $student->full_name ?: trim(($student->first_name ?? '') . ' ' . ($student->last_name ?? '')),
                $student->email,
                $student->studentProfile->admission_number ?? 'N/A',
                $student->studentProfile->department->name ?? 'N/A',
                $student->is_active ? 'Active' : 'Inactive',
                $student->studentProfile->admission_date ?? 'N/A'
            );
        }

        return Response::make($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="students-report-' . now()->format('Y-m-d') . '.csv"',
        ]);
    }

    /**
     * Export Faculty Report
     */
    public function exportFaculty(Request $request)
    {
        $faculty = User::where('role', 'faculty')->with('facultyProfile')->get();

        $csv = "Name,Email,Employee ID,Department,Employment Status,Qualification\n";

        foreach ($faculty as $member) {
            $csv .= sprintf(
                '"%s","%s","%s","%s","%s","%s"' . "\n",
                $member->full_name ?: trim(($member->first_name ?? '') . ' ' . ($member->last_name ?? '')),
                $member->email,
                $member->facultyProfile->employee_id ?? 'N/A',
                $member->facultyProfile->department->name ?? 'N/A',
                $member->facultyProfile->employment_status ?? 'N/A',
                $member->facultyProfile->qualification ?? 'N/A'
            );
        }

        return Response::make($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="faculty-report-' . now()->format('Y-m-d') . '.csv"',
        ]);
    }

    /**
     * Export Courses Report
     */
    public function exportCourses(Request $request)
    {
        $courses = Course::with(['department', 'instructor'])->get();

        $csv = "Course Code,Course Name,Department,Instructor,Credits,Capacity,Status\n";

        foreach ($courses as $course) {
            $csv .= sprintf(
                '"%s","%s","%s","%s","%s","%s","%s"' . "\n",
                $course->code,
                $course->name,
                $course->department->name ?? 'N/A',
                $course->instructor->name ?? 'N/A',
                $course->credits,
                $course->capacity,
                $course->is_active ? 'Active' : 'Inactive'
            );
        }

        return Response::make($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="courses-report-' . now()->format('Y-m-d') . '.csv"',
        ]);
    }

    /**
     * Export Fees Report
     */
    public function exportFees(Request $request)
    {
        $feeRecords = FeeRecord::with(['student', 'feeStructure'])->get();

        $csv = "Date,Student Name,Fee Structure,Total Amount,Amount Paid,Balance,Status,Due Date\n";

        foreach ($feeRecords as $record) {
            $csv .= sprintf(
                '"%s","%s","%s","%s","%s","%s","%s","%s"' . "\n",
                $record->created_at->format('Y-m-d'),
                $record->student?->full_name ?: trim(($record->student?->first_name ?? '') . ' ' . ($record->student?->last_name ?? '')),
                $record->feeStructure->name ?? 'N/A',
                $record->total_amount,
                $record->paid_amount,
                $record->balance_amount,
                $record->status,
                $record->due_date
            );
        }

        return Response::make($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="fees-report-' . now()->format('Y-m-d') . '.csv"',
        ]);
    }

    /**
     * Export Enrollment Report
     */
    public function exportEnrollment(Request $request)
    {
        $enrollments = CourseEnrollment::with(['student', 'course', 'semester'])->get();

        $csv = "Student Name,Course Code,Course Name,Semester,Enrollment Date,Status,Grade\n";

        foreach ($enrollments as $enrollment) {
            $csv .= sprintf(
                '"%s","%s","%s","%s","%s","%s","%s"' . "\n",
                $enrollment->student?->full_name ?: trim(($enrollment->student?->first_name ?? '') . ' ' . ($enrollment->student?->last_name ?? '')),
                $enrollment->course->code ?? $enrollment->course->course_code,
                $enrollment->course->name,
                $enrollment->semester->name ?? 'N/A',
                $enrollment->enrollment_date,
                $enrollment->status,
                $enrollment->final_grade ?? 'N/A'
            );
        }

        return Response::make($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="enrollment-report-' . now()->format('Y-m-d') . '.csv"',
        ]);
    }

    /**
     * Export Financial Report
     */
    public function exportFinancial(Request $request)
    {
        $dateFrom = $request->input('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->input('date_to', now()->format('Y-m-d'));

        $feeRecords = FeeRecord::with(['student', 'feeStructure'])
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->get();

        $csv = "Date,Student Name,Fee Structure,Total Amount,Amount Paid,Payment Method,Transaction ID,Status\n";

        foreach ($feeRecords as $record) {
            $csv .= sprintf(
                '"%s","%s","%s","%s","%s","%s","%s","%s"' . "\n",
                $record->created_at->format('Y-m-d'),
                $record->student?->full_name ?: trim(($record->student?->first_name ?? '') . ' ' . ($record->student?->last_name ?? '')),
                $record->feeStructure->name ?? 'N/A',
                $record->total_amount,
                $record->paid_amount,
                $record->payment_method ?? 'N/A',
                $record->transaction_id ?? 'N/A',
                $record->status
            );
        }

        return Response::make($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="financial-report-' . $dateFrom . '-to-' . $dateTo . '.csv"',
        ]);
    }

    /**
     * Export Academic Report
     */
    public function exportAcademic(Request $request)
    {
        $grades = Grade::with(['student', 'course', 'assignment'])->get();

        $csv = "Student Name,Course Code,Course Name,Assignment,Points Earned,Points Possible,Percentage,Letter Grade\n";

        foreach ($grades as $grade) {
            $csv .= sprintf(
                '"%s","%s","%s","%s","%s","%s","%s","%s"' . "\n",
                $grade->student?->full_name ?: trim(($grade->student?->first_name ?? '') . ' ' . ($grade->student?->last_name ?? '')),
                $grade->course->code ?? $grade->course->course_code ?? 'N/A',
                $grade->course->name ?? 'N/A',
                $grade->assignment->title ?? 'N/A',
                $grade->points_earned,
                $grade->points_possible,
                $grade->percentage,
                $grade->letter_grade ?? 'N/A'
            );
        }

        return Response::make($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="academic-report-' . now()->format('Y-m-d') . '.csv"',
        ]);
    }

    /**
     * Export Attendance Report
     */
    public function exportAttendance(Request $request)
    {
        $dateFrom = $request->input('date_from', now()->subDays(30)->format('Y-m-d'));
        $dateTo = $request->input('date_to', now()->format('Y-m-d'));

        $attendanceRecords = Attendance::with(['student', 'course'])
            ->whereBetween('attendance_date', [$dateFrom, $dateTo])
            ->get();

        $csv = "Date,Student Name,Course Code,Course Name,Status,Remarks\n";

        foreach ($attendanceRecords as $record) {
            $csv .= sprintf(
                '"%s","%s","%s","%s","%s","%s"' . "\n",
                $record->attendance_date,
                $record->student?->full_name ?: trim(($record->student?->first_name ?? '') . ' ' . ($record->student?->last_name ?? '')),
                $record->course->code ?? $record->course->course_code,
                $record->course->name,
                $record->status,
                $record->notes ?? ''
            );
        }

        return Response::make($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="attendance-report-' . $dateFrom . '-to-' . $dateTo . '.csv"',
        ]);
    }

    /**
     * Helper method to calculate average attendance
     */
    private function calculateAverageAttendance()
    {
        $totalRecords = Attendance::count();
        if ($totalRecords === 0) {
            return 0;
        }

        $presentRecords = Attendance::where('status', 'present')->count();
        return round(($presentRecords / $totalRecords) * 100, 2);
    }

    /**
     * Helper method to calculate average GPA
     */
    private function calculateAverageGPA($query)
    {
        $grades = $query->get();
        if ($grades->isEmpty()) {
            return 0;
        }

        $totalMarks = $grades->sum('total_marks');
        $count = $grades->count();

        return $count > 0 ? round($totalMarks / $count, 2) : 0;
    }
}
