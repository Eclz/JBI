<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\PasswordChangeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\ForumController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\HelpController;
use App\Http\Controllers\SupportController;
use App\Http\Controllers\SystemController;

// Admin Controllers
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\StudentController as AdminStudentController;
use App\Http\Controllers\Admin\FacultyController as AdminFacultyController;
use App\Http\Controllers\Admin\FacultyStaffController as AdminFacultyStaffController;
use App\Http\Controllers\Admin\CourseController as AdminCourseController;
use App\Http\Controllers\Admin\FeeController as AdminFeeController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Admin\ApplicationController as AdminApplicationController;

// Faculty Controllers
use App\Http\Controllers\Faculty\CourseController as FacultyCourseController;
use App\Http\Controllers\Faculty\AttendanceController as FacultyAttendanceController;
use App\Http\Controllers\Faculty\GradingController as FacultyGradingController;
use App\Http\Controllers\Faculty\MaterialController as FacultyMaterialController;

// Student Controllers
use App\Http\Controllers\Student\CourseController as StudentCourseController;
use App\Http\Controllers\Student\AssignmentController as StudentAssignmentController;
use App\Http\Controllers\Student\GradeController as StudentGradeController;
use App\Http\Controllers\Student\FeeController as StudentFeeController;
use App\Http\Controllers\Student\AttendanceController as StudentAttendanceController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);

    // Password Reset Routes
    Route::get('/password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');
});

// Authenticated routes
Route::middleware(['auth'])->group(function () {
    // Logout
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile Management
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/mark-read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');

    // Help & Support
    Route::get('/help', [HelpController::class, 'index'])->name('help.index');
    Route::get('/support', [SupportController::class, 'index'])->name('support.index');
    Route::post('/support', [SupportController::class, 'store'])->name('support.store');

    // Forums (All users)
    Route::resource('forums', ForumController::class);
    Route::post('/forums/{forum}/topics', [ForumController::class, 'storeTopic'])->name('forums.topics.store');
    Route::post('/forums/{forum}/topics/{topic}/replies', [ForumController::class, 'storeReply'])->name('forums.topics.replies.store');
});

// Password change routes (bypass password.change middleware)
Route::middleware('auth')->group(function () {
    Route::get('/change-password', [PasswordChangeController::class, 'showChangeForm'])->name('password.change.form');
    Route::post('/change-password', [PasswordChangeController::class, 'changePassword'])->name('password.change');
});

// Admin Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // User Management
    Route::resource('users', AdminUserController::class);
    Route::post('/users/{user}/toggle-status', [AdminUserController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::post('/users/{user}/reset-password', [AdminUserController::class, 'resetPassword'])->name('users.reset-password');

    // Student Management
    Route::resource('students', AdminStudentController::class);
    Route::get('/students/{student}/academic-record', [AdminStudentController::class, 'academicRecord'])->name('students.academic-record');
    Route::get('/students/{student}/attendance', [AdminStudentController::class, 'attendance'])->name('students.attendance');
    Route::get('/students/{student}/fees', [AdminStudentController::class, 'fees'])->name('students.fees');
    Route::get('/students/{student}/enroll-course', [AdminStudentController::class, 'showEnrollCourse'])->name('students.enroll-course');
    Route::post('/students/{student}/enroll-course', [AdminStudentController::class, 'enrollCourse'])->name('students.enroll-course.store');
    Route::delete('/students/{student}/enrollments/{enrollment}', [AdminStudentController::class, 'removeEnrollment'])->name('students.remove-enrollment');
    Route::post('/students/{student}/notes', [AdminStudentController::class, 'addNote'])->name('students.notes.add');
    Route::post('/students/{student}/toggle-status', [AdminStudentController::class, 'toggleStatus'])->name('students.toggle-status');
    Route::post('/students/bulk-import', [AdminStudentController::class, 'bulkImport'])->name('students.bulk-import');
    Route::get('/students/export', [AdminStudentController::class, 'export'])->name('students.export');

    // Faculty Management (Academic Divisions)
    Route::get('/faculties', [AdminFacultyController::class, 'index'])->name('faculties.index');
    Route::get('/faculties/create', [AdminFacultyController::class, 'create'])->name('faculties.create');
    Route::post('/faculties', [AdminFacultyController::class, 'store'])->name('faculties.store');
    Route::get('/faculties/{faculty}', [AdminFacultyController::class, 'show'])->name('faculties.show');
    Route::get('/faculties/{faculty}/edit', [AdminFacultyController::class, 'edit'])->name('faculties.edit');
    Route::put('/faculties/{faculty}', [AdminFacultyController::class, 'update'])->name('faculties.update');
    Route::delete('/faculties/{faculty}', [AdminFacultyController::class, 'destroy'])->name('faculties.destroy');
    Route::post('/faculties/{faculty}/toggle-status', [AdminFacultyController::class, 'toggleStatus'])->name('faculties.toggle-status');

    // Faculty Staff Management (Individual Faculty Members)
    Route::get('/faculty-staff', [AdminFacultyStaffController::class, 'index'])->name('faculty-staff.index');
    Route::get('/faculty-staff/create', [AdminFacultyStaffController::class, 'create'])->name('faculty-staff.create');
    Route::post('/faculty-staff', [AdminFacultyStaffController::class, 'store'])->name('faculty-staff.store');
    Route::get('/faculty-staff/{faculty}', [AdminFacultyStaffController::class, 'show'])->name('faculty-staff.show');
    Route::get('/faculty-staff/{faculty}/edit', [AdminFacultyStaffController::class, 'edit'])->name('faculty-staff.edit');
    Route::put('/faculty-staff/{faculty}', [AdminFacultyStaffController::class, 'update'])->name('faculty-staff.update');
    Route::delete('/faculty-staff/{faculty}', [AdminFacultyStaffController::class, 'destroy'])->name('faculty-staff.destroy');
    Route::post('/faculty-staff/{faculty}/toggle-status', [AdminFacultyStaffController::class, 'toggleStatus'])->name('faculty-staff.toggle-status');
    Route::get('/faculty-staff/{faculty}/courses', [AdminFacultyStaffController::class, 'courses'])->name('faculty-staff.courses');
    Route::post('/faculty-staff/{faculty}/assign-course', [AdminFacultyStaffController::class, 'assignCourse'])->name('faculty-staff.assign-course');

    // Course Management
    Route::resource('courses', AdminCourseController::class);
    Route::get('/courses/{course}/enrollments', [AdminCourseController::class, 'enrollments'])->name('courses.enrollments');
    Route::get('/courses/{course}/materials', [AdminCourseController::class, 'materials'])->name('courses.materials');
    Route::post('/courses/{course}/materials', [AdminCourseController::class, 'storeMaterial'])->name('courses.materials.store');
    Route::delete('/courses/{course}/materials/{material}', [AdminCourseController::class, 'destroyMaterial'])->name('courses.materials.destroy');
    Route::get('/courses/{course}/assignments', [AdminCourseController::class, 'assignments'])->name('courses.assignments');
    Route::get('/courses/{course}/grades', [AdminCourseController::class, 'grades'])->name('courses.grades');
    Route::post('/courses/{course}/toggle-status', [AdminCourseController::class, 'toggleStatus'])->name('courses.toggle-status');
    Route::post('/courses/{course}/enroll-student', [AdminCourseController::class, 'enrollStudent'])->name('courses.enroll-student');
    Route::delete('/courses/{course}/enrollments/{enrollment}/drop', [AdminCourseController::class, 'dropStudent'])->name('courses.drop-student');

    // Fee Management
    Route::prefix('fees')->name('fees.')->group(function () {
        // Fee Records
        Route::get('/', [AdminFeeController::class, 'index'])->name('index');
        Route::get('/create', [AdminFeeController::class, 'create'])->name('create');
        Route::post('/', [AdminFeeController::class, 'store'])->name('store');
        Route::get('/{fee}', [AdminFeeController::class, 'show'])->name('show');
        Route::get('/{fee}/edit', [AdminFeeController::class, 'edit'])->name('edit');
        Route::put('/{fee}', [AdminFeeController::class, 'update'])->name('update');
        Route::delete('/{fee}', [AdminFeeController::class, 'destroy'])->name('destroy');
        Route::get('/{fee}/payment', [AdminFeeController::class, 'showPayment'])->name('payment');
        Route::post('/{fee}/payment', [AdminFeeController::class, 'processPayment'])->name('payment.process');
        Route::post('/generate-invoices', [AdminFeeController::class, 'generateInvoices'])->name('generate-invoices');
        Route::post('/send-reminders', [AdminFeeController::class, 'sendReminders'])->name('send-reminders');
        Route::get('/export/records', [AdminFeeController::class, 'export'])->name('export');

        // Fee Structures
        Route::prefix('structures')->name('structures.')->group(function () {
            Route::get('/', [AdminFeeController::class, 'structures'])->name('index');
            Route::get('/create', [AdminFeeController::class, 'createStructure'])->name('create');
            Route::post('/', [AdminFeeController::class, 'storeStructure'])->name('store');
            Route::get('/{feeStructure}', [AdminFeeController::class, 'showStructure'])->name('show');
            Route::get('/{feeStructure}/edit', [AdminFeeController::class, 'editStructure'])->name('edit');
            Route::put('/{feeStructure}', [AdminFeeController::class, 'updateStructure'])->name('update');
            Route::delete('/{feeStructure}', [AdminFeeController::class, 'destroyStructure'])->name('destroy');
        });
    });

    // Department Management
    Route::resource('departments', DepartmentController::class);
    Route::post('/departments/{department}/toggle-status', [DepartmentController::class, 'toggleStatus'])->name('departments.toggle-status');
    Route::post('/departments/{department}/assign-head', [DepartmentController::class, 'assignHead'])->name('departments.assign-head');

    // Application Management
    Route::get('/applications', [AdminApplicationController::class, 'index'])->name('applications.index');
    Route::get('/applications/{application}', [AdminApplicationController::class, 'show'])->name('applications.show');
    Route::post('/applications/{application}/approve', [AdminApplicationController::class, 'approve'])->name('applications.approve');
    Route::post('/applications/{application}/reject', [AdminApplicationController::class, 'reject'])->name('applications.reject');

    // Reports - Comprehensive reporting system
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [AdminReportController::class, 'index'])->name('index');

        // Student Reports
        Route::get('/students', [AdminReportController::class, 'students'])->name('students');
        Route::get('/students/export', [AdminReportController::class, 'exportStudents'])->name('students.export');

        // Faculty Reports
        Route::get('/faculty', [AdminReportController::class, 'faculty'])->name('faculty');
        Route::get('/faculty/export', [AdminReportController::class, 'exportFaculty'])->name('faculty.export');

        // Course Reports
        Route::get('/courses', [AdminReportController::class, 'courses'])->name('courses');
        Route::get('/courses/export', [AdminReportController::class, 'exportCourses'])->name('courses.export');

        // Fee Reports
        Route::get('/fees', [AdminReportController::class, 'fees'])->name('fees');
        Route::get('/fees/export', [AdminReportController::class, 'exportFees'])->name('fees.export');

        // New Report Routes
        Route::get('/enrollment', [AdminReportController::class, 'enrollment'])->name('enrollment');
        Route::get('/enrollment/export', [AdminReportController::class, 'exportEnrollment'])->name('enrollment.export');

        Route::get('/financial', [AdminReportController::class, 'financial'])->name('financial');
        Route::get('/financial/export', [AdminReportController::class, 'exportFinancial'])->name('financial.export');

        Route::get('/academic', [AdminReportController::class, 'academic'])->name('academic');
        Route::get('/academic/export', [AdminReportController::class, 'exportAcademic'])->name('academic.export');

        Route::get('/attendance', [AdminReportController::class, 'attendance'])->name('attendance');
        Route::get('/attendance/export', [AdminReportController::class, 'exportAttendance'])->name('attendance.export');
    });

    // System Settings
    Route::get('/settings', [SystemController::class, 'index'])->name('settings.index');
    Route::put('/settings', [SystemController::class, 'update'])->name('settings.update');

    // Staff Assignment Guide
    Route::get('/staff-assignment-guide', [DepartmentController::class, 'staffAssignmentGuide'])->name('staff-assignment-guide');
});

// Faculty Routes
Route::middleware(['auth', 'role:faculty'])->prefix('faculty')->name('faculty.')->group(function () {
    // Course Management
    Route::get('/courses', [FacultyCourseController::class, 'index'])->name('courses.index');
    Route::get('/courses/{course}', [FacultyCourseController::class, 'show'])->name('courses.show');
    Route::put('/courses/{course}', [FacultyCourseController::class, 'update'])->name('courses.update');

    // Material Management
    Route::get('/courses/{course}/materials', [FacultyMaterialController::class, 'index'])->name('courses.materials.index');
    Route::post('/courses/{course}/materials', [FacultyMaterialController::class, 'store'])->name('courses.materials.store');
    Route::delete('/courses/{course}/materials/{material}', [FacultyMaterialController::class, 'destroy'])->name('courses.materials.destroy');

    // Assignment Management
    Route::resource('assignments', AssignmentController::class);
    Route::get('/courses/{course}/assignments', [AssignmentController::class, 'courseAssignments'])->name('courses.assignments.index');
    Route::post('/courses/{course}/assignments', [AssignmentController::class, 'store'])->name('courses.assignments.store');

    // Attendance Management
    Route::get('/courses/{course}/attendance', [FacultyAttendanceController::class, 'index'])->name('courses.attendance.index');
    Route::post('/courses/{course}/attendance', [FacultyAttendanceController::class, 'store'])->name('courses.attendance.store');
    Route::put('/courses/{course}/attendance/{attendance}', [FacultyAttendanceController::class, 'update'])->name('courses.attendance.update');

    // Grading
    Route::get('/courses/{course}/grades', [FacultyGradingController::class, 'index'])->name('courses.grades.index');
    Route::post('/courses/{course}/grades', [FacultyGradingController::class, 'store'])->name('courses.grades.store');
    Route::put('/courses/{course}/grades/{grade}', [FacultyGradingController::class, 'update'])->name('courses.grades.update');

    // Announcements
    Route::resource('announcements', AnnouncementController::class);
});

// Student Routes
Route::middleware(['auth', 'role:student'])->prefix('student')->name('student.')->group(function () {
    // Course Management
    Route::get('/courses', [StudentCourseController::class, 'index'])->name('courses.index');
    Route::get('/courses/{course}', [StudentCourseController::class, 'show'])->name('courses.show');
    Route::get('/courses/{course}/materials', [StudentCourseController::class, 'materials'])->name('courses.materials');
    Route::post('/courses/{course}/enroll', [StudentCourseController::class, 'enroll'])->name('courses.enroll');
    Route::delete('/courses/{course}/unenroll', [StudentCourseController::class, 'unenroll'])->name('courses.unenroll');

    // Assignment Management
    Route::get('/assignments', [StudentAssignmentController::class, 'index'])->name('assignments.index');
    Route::get('/assignments/{assignment}', [StudentAssignmentController::class, 'show'])->name('assignments.show');
    Route::post('/assignments/{assignment}/submit', [StudentAssignmentController::class, 'submit'])->name('assignments.submit');
    Route::put('/assignments/{assignment}/submissions/{submission}', [StudentAssignmentController::class, 'updateSubmission'])->name('assignments.submissions.update');

    // Grade Management
    Route::get('/grades', [StudentGradeController::class, 'index'])->name('grades.index');
    Route::get('/grades/{course}', [StudentGradeController::class, 'course'])->name('grades.course');
    Route::get('/transcript', [StudentGradeController::class, 'transcript'])->name('transcript');

    // Fee Management
    Route::get('/fees', [StudentFeeController::class, 'index'])->name('fees.index');
    Route::get('/fees/{fee}', [StudentFeeController::class, 'show'])->name('fees.show');
    Route::post('/fees/{fee}/pay', [StudentFeeController::class, 'pay'])->name('fees.pay');

    // Attendance
    Route::get('/attendance', [StudentAttendanceController::class, 'index'])->name('attendance.index');
    Route::get('/attendance/{course}', [StudentAttendanceController::class, 'course'])->name('attendance.course');
});

// Shared routes (All authenticated users)
Route::middleware(['auth'])->group(function () {
    // General course browsing
    Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
    Route::get('/courses/{course}', [CourseController::class, 'show'])->name('courses.show');

    // General student browsing (for faculty and admin)
    Route::get('/students', [StudentController::class, 'index'])->name('students.index');
    Route::get('/students/{student}', [StudentController::class, 'show'])->name('students.show');

    // Announcements
    Route::get('/announcements', [AnnouncementController::class, 'index'])->name('announcements.index');
    Route::get('/announcements/{announcement}', [AnnouncementController::class, 'show'])->name('announcements.show');
});
