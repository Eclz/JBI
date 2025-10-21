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
            'active_courses' => Course::where('is_active', true)->count(),
            'total_enrollments' => CourseEnrollment::where('status', 'enrolled')->count(),
            'total_revenue' => FeeRecord::sum('amount_paid'),
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
            ->where('amount_paid', '>', 0)
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
        $revenueTrend = FeeRecord::selectRaw('DATE(updated_at) as date, SUM(amount_paid) as revenue')
            ->where('updated_at', '>=', now()->subDays(30))
            ->where('amount_paid', '>', 0)
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
            ->with(['studentProfile']);

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
                $q->whereYear('date_of_admission', $request->admission_year);
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
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
        ];

        // Students by department
        $studentsByDepartment = StudentProfile::select('department_id', DB::raw('COUNT(*) as count'))
            ->with('department')
            ->groupBy('department_id')
            ->orderBy('count', 'desc')
            ->get();

        // Students by admission year
        $studentsByYear = StudentProfile::selectRaw('YEAR(date_of_admission) as year, COUNT(*) as count')
            ->whereNotNull('date_of_admission')
            ->groupBy('year')
            ->orderBy('year', 'desc')
            ->get();

        // Get filter options
        $departments = Department::orderBy('name')->get();
        $admissionYears = StudentProfile::selectRaw('DISTINCT YEAR(date_of_admission) as year')
            ->whereNotNull('date_of_admission')
            ->orderBy('year', 'desc')
            ->pluck('year');

        return view('admin.reports.students', compact(
            'students',
            'stats',
            'studentsByDepartment',
            'studentsByYear',
            'departments',
            'admissionYears'
        ));
    }

    /**
     * Faculty Reports
     */
    public function faculty(Request $request)
    {
        $query = User::where('role', 'faculty')
            ->with(['facultyProfile']);

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
                $query->where('is_active', true);
            }])
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
            $query->where('is_active', $request->status === 'active');
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
            'active_courses' => Course::where('is_active', true)->count(),
            'inactive_courses' => Course::where('is_active', false)->count(),
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
        $instructors = User::where('role', 'faculty')->orderBy('name')->get();

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
        $query = FeeRecord::with(['student', 'feeStructure', 'processor']);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('academic_year')) {
            $query->where('academic_year', $request->academic_year);
        }

        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('created_at', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('student', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $feeRecords = $query->latest()->paginate(50);

        // Financial summary
        $summary = [
            'total_billed' => FeeRecord::sum('total_amount'),
            'total_paid' => FeeRecord::sum('amount_paid'),
            'total_balance' => FeeRecord::sum('balance_amount'),
            'total_discounts' => FeeRecord::sum('discount_amount'),
            'total_late_fees' => FeeRecord::sum('late_fee'),
        ];

        // Status breakdown
        $statusBreakdown = FeeRecord::select('status', DB::raw('COUNT(*) as count'), DB::raw('SUM(balance_amount) as total_balance'))
            ->groupBy('status')
            ->get();

        // Payment trends (last 12 months)
        $paymentTrends = FeeRecord::selectRaw('DATE_FORMAT(updated_at, "%Y-%m") as month, SUM(amount_paid) as total')
            ->where('updated_at', '>=', now()->subMonths(12))
            ->where('amount_paid', '>', 0)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Top paying students
        $topPayers = FeeRecord::select('user_id', DB::raw('SUM(amount_paid) as total_paid'))
            ->with('student')
            ->groupBy('user_id')
            ->orderBy('total_paid', 'desc')
            ->take(10)
            ->get();

        // Get filter options
        $academicYears = FeeRecord::selectRaw('DISTINCT academic_year')
            ->whereNotNull('academic_year')
            ->orderBy('academic_year', 'desc')
            ->pluck('academic_year');

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
            ->with('course.department')
            ->where('course_enrollments.status', 'enrolled')
            ->groupBy('courses.department_id')
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

        return view('admin.reports.enrollment', compact(
            'enrollments',
            'stats',
            'enrollmentTrends',
            'enrollmentsByDepartment',
            'enrollmentsBySemester',
            'semesters',
            'departments'
        ));
    }

    /**
     * Financial Report
     */
    public function financial(Request $request)
    {
        $dateFrom = $request->input('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->input('date_to', now()->format('Y-m-d'));

        // Revenue summary
        $revenue = [
            'total_billed' => FeeRecord::whereBetween('created_at', [$dateFrom, $dateTo])->sum('total_amount'),
            'total_collected' => FeeRecord::whereBetween('updated_at', [$dateFrom, $dateTo])->sum('amount_paid'),
            'total_pending' => FeeRecord::whereBetween('created_at', [$dateFrom, $dateTo])->where('status', '!=', 'paid')->sum('balance_amount'),
            'collection_rate' => 0,
        ];

        if ($revenue['total_billed'] > 0) {
            $revenue['collection_rate'] = round(($revenue['total_collected'] / $revenue['total_billed']) * 100, 2);
        }

        // Daily collection trends
        $collectionTrends = FeeRecord::selectRaw('DATE(updated_at) as date, SUM(amount_paid) as total')
            ->whereBetween('updated_at', [$dateFrom, $dateTo])
            ->where('amount_paid', '>', 0)
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Payment method breakdown
        $paymentMethods = FeeRecord::select('payment_method', DB::raw('COUNT(*) as count'), DB::raw('SUM(amount_paid) as total'))
            ->whereBetween('updated_at', [$dateFrom, $dateTo])
            ->whereNotNull('payment_method')
            ->groupBy('payment_method')
            ->get();

        // Fee structure breakdown
        $feeStructureRevenue = FeeRecord::select('fee_structure_id', DB::raw('COUNT(*) as records'), DB::raw('SUM(amount_paid) as collected'), DB::raw('SUM(total_amount) as total'))
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

        return view('admin.reports.financial', compact(
            'revenue',
            'collectionTrends',
            'paymentMethods',
            'feeStructureRevenue',
            'outstandingPayments',
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

        $query = Grade::with(['student', 'course', 'assignment']);

        if ($semesterId) {
            $query->where('semester_id', $semesterId);
        }

        if ($departmentId) {
            $query->whereHas('course', function ($q) use ($departmentId) {
                $q->where('department_id', $departmentId);
            });
        }

        $grades = $query->latest()->paginate(50);

        // Calculate average GPA
        $averageGPA = $this->calculateAverageGPA($query);

        // Grade distribution
        $gradeDistribution = DB::table('grades')
            ->selectRaw('
                CASE
                    WHEN total_marks >= 90 THEN "A"
                    WHEN total_marks >= 80 THEN "B"
                    WHEN total_marks >= 70 THEN "C"
                    WHEN total_marks >= 60 THEN "D"
                    ELSE "F"
                END as grade_letter,
                COUNT(*) as count
            ')
            ->groupBy('grade_letter')
            ->orderBy('grade_letter')
            ->get();

        // Top performers
        $topPerformers = Grade::select('user_id', DB::raw('AVG(total_marks) as avg_grade'), DB::raw('COUNT(*) as course_count'))
            ->with('student')
            ->groupBy('user_id')
            ->having('course_count', '>=', 3)
            ->orderBy('avg_grade', 'desc')
            ->take(10)
            ->get();

        // Course performance
        $coursePerformance = Grade::select('course_id', DB::raw('AVG(total_marks) as avg_grade'), DB::raw('COUNT(*) as student_count'))
            ->with('course')
            ->groupBy('course_id')
            ->orderBy('avg_grade', 'desc')
            ->take(10)
            ->get();

        // Department performance
        $departmentPerformance = Grade::select('courses.department_id', DB::raw('AVG(grades.total_marks) as avg_grade'), DB::raw('COUNT(*) as student_count'))
            ->join('courses', 'grades.course_id', '=', 'courses.id')
            ->with('course.department')
            ->groupBy('courses.department_id')
            ->orderBy('avg_grade', 'desc')
            ->get();

        // Get filter options
        $semesters = Semester::orderBy('start_date', 'desc')->get();
        $departments = Department::orderBy('name')->get();

        return view('admin.reports.academic', compact(
            'grades',
            'averageGPA',
            'gradeDistribution',
            'topPerformers',
            'coursePerformance',
            'departmentPerformance',
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
            $query->whereBetween('date', [$dateFrom, $dateTo]);
        }

        if ($courseId) {
            $query->where('course_id', $courseId);
        }

        if ($departmentId) {
            $query->whereHas('course', function ($q) use ($departmentId) {
                $q->where('department_id', $departmentId);
            });
        }

        $attendanceRecords = $query->latest('date')->paginate(50);

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
        $attendanceTrends = Attendance::selectRaw('DATE(date) as date, COUNT(*) as total, SUM(CASE WHEN status = "present" THEN 1 ELSE 0 END) as present')
            ->whereBetween('date', [$dateFrom, $dateTo])
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(function ($item) {
                $item->rate = $item->total > 0 ? round(($item->present / $item->total) * 100, 2) : 0;
                return $item;
            });

        // Course-wise attendance
        $courseAttendance = Attendance::select('course_id', DB::raw('COUNT(*) as total'), DB::raw('SUM(CASE WHEN status = "present" THEN 1 ELSE 0 END) as present'))
            ->with('course')
            ->whereBetween('date', [$dateFrom, $dateTo])
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
            ->whereBetween('date', [$dateFrom, $dateTo])
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
        $courses = Course::where('is_active', true)->orderBy('name')->get();
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
        $students = User::where('role', 'student')->with('studentProfile')->get();

        $csv = "Name,Email,Admission Number,Department,Status,Date of Admission\n";

        foreach ($students as $student) {
            $csv .= sprintf(
                '"%s","%s","%s","%s","%s","%s"' . "\n",
                $student->name,
                $student->email,
                $student->studentProfile->admission_number ?? 'N/A',
                $student->studentProfile->department->name ?? 'N/A',
                $student->is_active ? 'Active' : 'Inactive',
                $student->studentProfile->date_of_admission ?? 'N/A'
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
                $member->name,
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

        $csv = "Student Name,Admission Number,Fee Structure,Total Amount,Amount Paid,Balance,Status,Due Date\n";

        foreach ($feeRecords as $record) {
            $csv .= sprintf(
                '"%s","%s","%s","%s","%s","%s","%s","%s"' . "\n",
                $record->student->name,
                $record->student->studentProfile->admission_number ?? 'N/A',
                $record->feeStructure->name ?? 'N/A',
                $record->total_amount,
                $record->amount_paid,
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
                $enrollment->student->name,
                $enrollment->course->code,
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
                $record->student->name,
                $record->feeStructure->name ?? 'N/A',
                $record->total_amount,
                $record->amount_paid,
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

        $csv = "Student Name,Course Code,Course Name,Assignment,Total Marks,Obtained Marks,Grade,Semester\n";

        foreach ($grades as $grade) {
            $csv .= sprintf(
                '"%s","%s","%s","%s","%s","%s","%s","%s"' . "\n",
                $grade->student->name,
                $grade->course->code ?? 'N/A',
                $grade->course->name ?? 'N/A',
                $grade->assignment->title ?? 'N/A',
                $grade->total_marks,
                $grade->obtained_marks,
                $grade->grade,
                $grade->semester->name ?? 'N/A'
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
            ->whereBetween('date', [$dateFrom, $dateTo])
            ->get();

        $csv = "Date,Student Name,Course Code,Course Name,Status,Remarks\n";

        foreach ($attendanceRecords as $record) {
            $csv .= sprintf(
                '"%s","%s","%s","%s","%s","%s"' . "\n",
                $record->date,
                $record->student->name,
                $record->course->code,
                $record->course->name,
                $record->status,
                $record->remarks ?? ''
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
