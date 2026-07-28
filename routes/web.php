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
use App\Http\Controllers\ReceiptVerificationController;

// Faculty Controllers
use App\Http\Controllers\Faculty\DashboardController as FacultyDashboardController;
use App\Http\Controllers\Faculty\ExamController as FacultyExamController;
use App\Http\Controllers\Faculty\QuizController as FacultyQuizController;
use App\Http\Controllers\Faculty\AssignmentController as FacultyAssignmentController;
use App\Http\Controllers\Faculty\CourseController as FacultyCourseController;
use App\Http\Controllers\Faculty\AttendanceController as FacultyAttendanceController;
use App\Http\Controllers\Faculty\GradingController as FacultyGradingController;
use App\Http\Controllers\Faculty\MaterialController as FacultyMaterialController;

// Admin Controllers
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\RoleController as AdminRoleController;
use App\Http\Controllers\Admin\StudentController as AdminStudentController;
use App\Http\Controllers\Admin\FacultyController as AdminFacultyController;
use App\Http\Controllers\Admin\FacultyStaffController as AdminFacultyStaffController;
use App\Http\Controllers\Admin\CourseController as AdminCourseController;
use App\Http\Controllers\Admin\FeeController as AdminFeeController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Admin\ApplicationController as AdminApplicationController;
use App\Http\Controllers\Admin\EnrollmentController;
use App\Http\Controllers\Admin\ProgramController as AdminProgramController;
use App\Http\Controllers\Admin\ProgramLevelController as AdminProgramLevelController;
use App\Http\Controllers\Admin\ProgramChangeController as AdminProgramChangeController;
use App\Http\Controllers\Admin\AcademicYearController as AdminAcademicYearController;
use App\Http\Controllers\Admin\SemesterController as AdminSemesterController;




// Student Controllers
use App\Http\Controllers\Student\CourseController as StudentCourseController;
use App\Http\Controllers\Student\AssignmentController as StudentAssignmentController;
use App\Http\Controllers\Student\GradeController as StudentGradeController;
use App\Http\Controllers\Student\FeeController as StudentFeeController;
use App\Http\Controllers\Student\AttendanceController as StudentAttendanceController;
use App\Http\Controllers\Student\DashboardController as StudentDashboardController;
use App\Http\Controllers\Student\ExamController as StudentExamController;
use App\Http\Controllers\Student\QuizController as StudentQuizController;
use App\Http\Controllers\Student\ProgramChangeController as StudentProgramChangeController;
use App\Http\Controllers\Student\LmsController as StudentLmsController;
use App\Http\Controllers\StudentsApplicationController;
use App\Http\Controllers\Faculty\LmsController as FacultyLmsController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('generate', function (){
    \Illuminate\Support\Facades\Artisan::call('storage:link');
    echo 'ok';
});



Route::get('/clear', function() {

    \Illuminate\Support\Facades\Artisan::call('cache:clear');
    \Illuminate\Support\Facades\Artisan::call('config:clear');
    \Illuminate\Support\Facades\Artisan::call('config:cache');
    \Illuminate\Support\Facades\Artisan::call('view:clear');

    return "Cleared!";

 });

Route::get('/receipts/verify', [ReceiptVerificationController::class, 'showForm'])->name('receipts.verify');
Route::post('/receipts/verify', [ReceiptVerificationController::class, 'verify'])->name('receipts.verify.submit');

// Public Application Routes
Route::get('/apply', [StudentsApplicationController::class, 'create'])->name('applications.create');
Route::post('/apply', [StudentsApplicationController::class, 'store'])->name('applications.store');
Route::get('/application/success/{application}', [StudentsApplicationController::class, 'success'])->name('applications.success');
Route::get('/application/payment/{token}', [StudentController::class, 'uploadPayment'])->name('applications.upload-payment');
Route::post('/application/payment/{token}', [StudentController::class, 'storePayment'])->name('applications.store-payment');
Route::get('/application/payment-success/{token}', [StudentsApplicationController::class, 'paymentSuccess'])->name('applications.payment-success');

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
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');

    // User Settings
    Route::get('/settings', [SystemController::class, 'index'])->name('settings.index');
    Route::put('/settings', [SystemController::class, 'update'])->name('settings.update');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/{notification}', [NotificationController::class, 'show'])->name('notifications.show');
    Route::post('/notifications/{notification}/mark-read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy'])->name('notifications.destroy');

    // Help & Support
    Route::get('/help', [HelpController::class, 'index'])->name('help.index');
    Route::get('/support', [SupportController::class, 'index'])->name('support.index');
    Route::post('/support', [SupportController::class, 'store'])->name('support.store');

    // Forums (All users)
    Route::get('/forums', [ForumController::class, 'index'])->name('forums.index');
    Route::get('/forums/{forum}', [ForumController::class, 'show'])->name('forums.show');
    Route::post('/forums/{forum}/topics', [ForumController::class, 'createTopic'])->name('forums.topics.store');
    Route::get('/forums/topics/{topic}', [ForumController::class, 'showTopic'])->name('forums.topics.show');
    Route::post('/forums/topics/{topic}/replies', [ForumController::class, 'replyToTopic'])->name('forums.topics.replies.store');
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
    Route::resource('roles', AdminRoleController::class)->except(['show']);

    // Student Management
    Route::get('/students/next-admission-number', [AdminStudentController::class, 'getNextAdmissionNumber'])->name('students.next-admission-number');
    Route::get('/students/export', [AdminStudentController::class, 'export'])->name('students.export');
    Route::post('/students/bulk-import', [AdminStudentController::class, 'bulkImport'])->name('students.bulk-import');

    Route::resource('students', AdminStudentController::class);
    Route::get('/students/{student}/academic-record', [AdminStudentController::class, 'academicRecord'])->name('students.academic-record');
    Route::get('/students/{student}/attendance', [AdminStudentController::class, 'attendance'])->name('students.attendance');
    Route::get('/students/{student}/fees', [AdminStudentController::class, 'fees'])->name('students.fees');
    Route::get('/students/{student}/enroll-course', [AdminStudentController::class, 'showEnrollCourse'])->name('students.enroll-course');
    Route::post('/students/{student}/enroll-course', [AdminStudentController::class, 'enrollCourse'])->name('students.enroll-course.store');
    Route::delete('/students/{student}/enrollments/{enrollment}', [AdminStudentController::class, 'removeEnrollment'])->name('students.remove-enrollment');
    Route::post('/students/{student}/notes', [AdminStudentController::class, 'addNote'])->name('students.notes.add');
    Route::post('/students/{student}/toggle-status', [AdminStudentController::class, 'toggleStatus'])->name('students.toggle-status');

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

    // Enrollment Management
    Route::prefix('enrollments')->name('enrollments.')->group(function () {
        Route::get('/', [EnrollmentController::class, 'index'])->name('index');
        Route::get('/create', [EnrollmentController::class, 'create'])->name('create');
        Route::post('/', [EnrollmentController::class, 'store'])->name('store');
        Route::get('/{enrollment}', [EnrollmentController::class, 'show'])->name('show');
        Route::get('/{enrollment}/edit', [EnrollmentController::class, 'edit'])->name('edit');
        Route::put('/{enrollment}', [EnrollmentController::class, 'update'])->name('update');
        Route::delete('/{enrollment}', [EnrollmentController::class, 'destroy'])->name('destroy');
        Route::post('/{enrollment}/approve', [EnrollmentController::class, 'approve'])->name('approve');
        Route::post('/{enrollment}/reject', [EnrollmentController::class, 'reject'])->name('reject');
        Route::post('/bulk-enroll', [EnrollmentController::class, 'bulkEnroll'])->name('bulk-enroll');
    });

    // Fee Management
    Route::prefix('fees')->name('fees.')->group(function () {
        // Main fees index route
        Route::get('/', [AdminFeeController::class, 'index'])->name('index');

        // Fee Records Management
        Route::prefix('records')->name('records.')->group(function () {
            Route::get('/', [AdminFeeController::class, 'index'])->name('index');
            Route::get('/create', [AdminFeeController::class, 'create'])->name('create');
            Route::post('/', [AdminFeeController::class, 'store'])->name('store');
            Route::get('/{fee}', [AdminFeeController::class, 'show'])->name('show');
            Route::get('/{fee}/demand-notice', [AdminFeeController::class, 'demandNotice'])->name('demand-notice');
            Route::get('/{fee}/receipt', [AdminFeeController::class, 'receipt'])->name('receipt');
            Route::get('/{fee}/edit', [AdminFeeController::class, 'edit'])->name('edit');
            Route::put('/{fee}', [AdminFeeController::class, 'update'])->name('update');
            Route::delete('/{fee}', [AdminFeeController::class, 'destroy'])->name('destroy');
            Route::get('/{fee}/payment', [AdminFeeController::class, 'showPayment'])->name('payment');
            Route::post('/{fee}/payment', [AdminFeeController::class, 'processPayment'])->name('process-payment');
            Route::post('/generate-invoices', [AdminFeeController::class, 'generateInvoices'])->name('generate-invoices');
            Route::post('/send-reminders', [AdminFeeController::class, 'sendReminders'])->name('send-reminders');
            Route::get('/export', [AdminFeeController::class, 'export'])->name('export');
        });

        Route::prefix('payments')->name('payments.')->group(function () {
            Route::post('/{payment}/approve', [AdminFeeController::class, 'approvePayment'])->name('approve');
            Route::get('/{payment}/receipt', [AdminFeeController::class, 'transactionReceipt'])->name('receipt');
        });

        // Fee Structures Management
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

    Route::resource('program-levels', AdminProgramLevelController::class)->except(['show']);
    Route::resource('programs', AdminProgramController::class);
    Route::get('/program-changes', [AdminProgramChangeController::class, 'index'])->name('program-changes.index');
    Route::post('/program-changes/{programChange}/approve', [AdminProgramChangeController::class, 'approve'])->name('program-changes.approve');
    Route::post('/program-changes/{programChange}/reject', [AdminProgramChangeController::class, 'reject'])->name('program-changes.reject');

    Route::resource('academic-years', AdminAcademicYearController::class)->except(['show']);
    Route::resource('semesters', AdminSemesterController::class)->except(['show']);
    Route::post('/departments/{department}/toggle-status', [DepartmentController::class, 'toggleStatus'])->name('departments.toggle-status');
    Route::post('/departments/{department}/assign-head', [DepartmentController::class, 'assignHead'])->name('departments.assign-head');

    // Application Management
    Route::get('/applications', [AdminApplicationController::class, 'index'])->name('applications.index');
    Route::get('/applications/{application}', [AdminApplicationController::class, 'show'])->name('applications.show');
    Route::post('/applications/bulk-approve', [AdminApplicationController::class, 'bulkApprove'])->name('applications.bulk-approve');
    Route::post('/applications/{application}/approve', [AdminApplicationController::class, 'approve'])->name('applications.approve');
    Route::post('/applications/{application}/reject', [AdminApplicationController::class, 'reject'])->name('applications.reject');
    // Payment verification route
    Route::post('/applications/{application}/verify-payment', [AdminApplicationController::class, 'verifyPayment'])->name('applications.verify-payment');

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
    Route::get('/settings', [SystemController::class, 'adminSettings'])->name('settings');
    Route::put('/settings', [SystemController::class, 'updateAdminSettings'])->name('settings.update');

    // Staff Assignment Guide
    Route::get('/staff-assignment-guide', [DepartmentController::class, 'staffAssignmentGuide'])->name('staff-assignment-guide');

    Route::get('/test-email', function () {
        try {
            $testEmail = auth()->user()->email;

            Log::info('Testing email system', [
                'to' => $testEmail,
                'driver' => config('mail.default'),
                'from' => config('mail.from.address')
            ]);

            Mail::raw('This is a test email from JBI University Management System.', function ($message) use ($testEmail) {
                $message->to($testEmail)
                       ->subject('Test Email - JBI UMS');
            });

            return response()->json([
                'success' => true,
                'message' => 'Test email sent to ' . $testEmail . '. Check your email and Laravel logs.',
                'config' => [
                    'driver' => config('mail.default'),
                    'from' => config('mail.from.address'),
                    'host' => config('mail.mailers.smtp.host'),
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Test email failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    })->name('test-email');
});

// Faculty Routes
Route::middleware(['auth', 'role:faculty'])->prefix('faculty')->name('faculty.')->group(function () {
    Route::get('/dashboard', [FacultyDashboardController::class, 'index'])->name('dashboard');
    Route::get('/lms', [FacultyLmsController::class, 'index'])->name('lms.index');
    Route::get('/lms/{course}', [FacultyLmsController::class, 'show'])->name('lms.show');

    // General overview routes
    Route::get('/attendance', [FacultyAttendanceController::class, 'overview'])->name('attendance.index');
    Route::get('/grading', [FacultyGradingController::class, 'overview'])->name('grading.index');
    Route::get('/materials', [FacultyMaterialController::class, 'overview'])->name('materials.index');

    // Course Management
    Route::get('/courses', [FacultyCourseController::class, 'index'])->name('courses.index');
    Route::get('/courses/{course}', [FacultyCourseController::class, 'show'])->name('courses.show');
    Route::put('/courses/{course}', [FacultyCourseController::class, 'update'])->name('courses.update');

    // Material Management
    Route::get('/courses/{course}/materials', [FacultyMaterialController::class, 'index'])->name('courses.materials.index');
    Route::get('/courses/{course}/materials/create', [FacultyMaterialController::class, 'create'])->name('courses.materials.create');
    Route::post('/courses/{course}/materials', [FacultyMaterialController::class, 'store'])->name('courses.materials.store');
    Route::delete('/courses/{course}/materials/{material}', [FacultyMaterialController::class, 'destroy'])->name('courses.materials.destroy');

    // Assignment Management
    Route::prefix('assignments')->name('assignments.')->group(function () {
        Route::get('/', [FacultyAssignmentController::class, 'index'])->name('index');
        Route::get('/create', [FacultyAssignmentController::class, 'create'])->name('create');
        Route::post('/', [FacultyAssignmentController::class, 'store'])->name('store');
        Route::get('/{assignment}', [FacultyAssignmentController::class, 'show'])->name('show');
        Route::get('/{assignment}/edit', [FacultyAssignmentController::class, 'edit'])->name('edit');
        Route::put('/{assignment}', [FacultyAssignmentController::class, 'update'])->name('update');
        Route::delete('/{assignment}', [FacultyAssignmentController::class, 'destroy'])->name('destroy');
        Route::get('/{assignment}/submissions', [FacultyAssignmentController::class, 'submissions'])->name('submissions');
        Route::post('/{assignment}/submissions/{submission}/grade', [FacultyAssignmentController::class, 'gradeSubmission'])->name('submissions.grade');
    });
    Route::get('/courses/{course}/assignments', [FacultyAssignmentController::class, 'courseAssignments'])->name('courses.assignments.index');
    Route::post('/courses/{course}/assignments', [FacultyAssignmentController::class, 'store'])->name('courses.assignments.store');

    Route::prefix('exams')->name('exams.')->group(function () {
        Route::get('/', [FacultyExamController::class, 'index'])->name('index');
        Route::get('/create', [FacultyExamController::class, 'create'])->name('create');
        Route::post('/', [FacultyExamController::class, 'store'])->name('store');
        Route::get('/{exam}', [FacultyExamController::class, 'show'])->name('show');
        Route::get('/{exam}/edit', [FacultyExamController::class, 'edit'])->name('edit');
        Route::put('/{exam}', [FacultyExamController::class, 'update'])->name('update');
        Route::delete('/{exam}', [FacultyExamController::class, 'destroy'])->name('destroy');
        Route::get('/{exam}/attempts', [FacultyExamController::class, 'attempts'])->name('attempts');
        Route::get('/{exam}/attempts/{attempt}', [FacultyExamController::class, 'submission'])->name('attempts.show');
        Route::post('/{exam}/attempts/{attempt}/grade', [FacultyExamController::class, 'gradeAttempt'])->name('attempts.grade');
        Route::post('/{exam}/grade/{attempt}', [FacultyExamController::class, 'gradeAttempt'])->name('grade');
    });

    Route::prefix('quizzes')->name('quizzes.')->group(function () {
        Route::get('/', [FacultyQuizController::class, 'index'])->name('index');
        Route::get('/create', [FacultyQuizController::class, 'create'])->name('create');
        Route::post('/', [FacultyQuizController::class, 'store'])->name('store');
        Route::get('/{quiz}', [FacultyQuizController::class, 'show'])->name('show');
        Route::get('/{quiz}/edit', [FacultyQuizController::class, 'edit'])->name('edit');
        Route::put('/{quiz}', [FacultyQuizController::class, 'update'])->name('update');
        Route::delete('/{quiz}', [FacultyQuizController::class, 'destroy'])->name('destroy');
        Route::get('/{quiz}/questions', [FacultyQuizController::class, 'questions'])->name('questions');
        Route::post('/{quiz}/questions', [FacultyQuizController::class, 'storeQuestion'])->name('questions.store');
        Route::put('/{quiz}/questions/{question}', [FacultyQuizController::class, 'updateQuestion'])->name('questions.update');
        Route::delete('/{quiz}/questions/{question}', [FacultyQuizController::class, 'destroyQuestion'])->name('questions.destroy');
        Route::get('/{quiz}/attempts', [FacultyQuizController::class, 'attempts'])->name('attempts');
    });

    // Attendance Management
    Route::get('/courses/{course}/attendance', [FacultyAttendanceController::class, 'index'])->name('courses.attendance.index');
    Route::post('/courses/{course}/attendance', [FacultyAttendanceController::class, 'store'])->name('courses.attendance.store');
    Route::put('/courses/{course}/attendance/{attendance}', [FacultyAttendanceController::class, 'update'])->name('courses.attendance.update');

    // Grading
    Route::get('/courses/{course}/grades', [FacultyGradingController::class, 'index'])->name('courses.grades.index');
    Route::post('/courses/{course}/grades', [FacultyGradingController::class, 'store'])->name('courses.grades.store');
    Route::put('/courses/{course}/grades/{grade}', [FacultyGradingController::class, 'update'])->name('courses.grades.update');
    Route::get('/courses/{course}/grading', [FacultyGradingController::class, 'course'])->name('courses.grading');
    Route::get('/courses/{course}/gradebook', [FacultyGradingController::class, 'gradebook'])->name('courses.gradebook');
    Route::post('/assignments/{assignment}/grade', [FacultyGradingController::class, 'storeGrade'])->name('grading.store');
    Route::post('/assignments/{assignment}/bulk-grade', [FacultyGradingController::class, 'bulkGrade'])->name('grading.bulk');
    Route::post('/assignments/{assignment}/publish', [FacultyGradingController::class, 'publishGrades'])->name('grading.publish');

    // Announcements
    Route::resource('announcements', AnnouncementController::class);
});

// Student Routes
Route::middleware(['auth', 'role:student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');
    Route::get('/lms', [StudentLmsController::class, 'index'])->name('lms.index');
    Route::get('/lms/{course}', [StudentLmsController::class, 'show'])->name('lms.show');
    Route::post('/lms/{course}/complete', [StudentLmsController::class, 'markComplete'])->name('lms.complete');
    Route::get('/lms/{course}/materials/{material}', [StudentLmsController::class, 'material'])->name('lms.material');
    Route::post('/lms/{course}/materials/{material}/track', [StudentLmsController::class, 'trackMaterial'])->name('lms.material.track');
    Route::get('/lms/{course}/certificate', [StudentLmsController::class, 'certificate'])->name('lms.certificate');

    // Course Management
    Route::get('/courses', [StudentCourseController::class, 'index'])->name('courses.index');
    Route::get('/courses/enroll', [StudentCourseController::class, 'available'])->name('courses.enrollments');
    Route::get('/courses/{course}', [StudentCourseController::class, 'show'])->name('courses.show');
    Route::get('/courses/{course}/materials', [StudentCourseController::class, 'materials'])->name('courses.materials');
    Route::get('/courses/{course}/attendance', [StudentAttendanceController::class, 'course'])->name('courses.attendance');
    Route::get('/courses/{course}/grades', [StudentGradeController::class, 'course'])->name('courses.grades');
    Route::post('/courses/{course}/enroll', [StudentCourseController::class, 'enroll'])->name('courses.enroll');
    Route::delete('/courses/{course}/unenroll', [StudentCourseController::class, 'unenroll'])->name('courses.unenroll');

    // Assignment Management
    Route::get('/assignments', [StudentAssignmentController::class, 'index'])->name('assignments.index');
    Route::get('/assignments/{assignment}', [StudentAssignmentController::class, 'show'])->name('assignments.show');
    Route::post('/assignments/{assignment}/submit', [StudentAssignmentController::class, 'submit'])->name('assignments.submit');
    Route::put('/assignments/{assignment}/submissions/{submission}', [StudentAssignmentController::class, 'updateSubmission'])->name('assignments.submissions.update');

    // Quiz Management
    Route::prefix('quizzes')->name('quizzes.')->group(function () {
        Route::get('/', [StudentQuizController::class, 'index'])->name('index');
        Route::get('/{quiz}', [StudentQuizController::class, 'show'])->name('show');
        Route::post('/{quiz}/start', [StudentQuizController::class, 'start'])->name('start');
        Route::get('/{quiz}/attempt/{attempt}', [StudentQuizController::class, 'take'])->name('take');
        Route::post('/{quiz}/attempt/{attempt}/submit', [StudentQuizController::class, 'submit'])->name('submit');
        Route::get('/{quiz}/attempt/{attempt}/result', [StudentQuizController::class, 'result'])->name('result');
    });

    // Grade Management
    Route::get('/grades', [StudentGradeController::class, 'index'])->name('grades.index');
    Route::get('/grades/{course}', [StudentGradeController::class, 'course'])->name('grades.course');
    Route::get('/transcript', [StudentGradeController::class, 'transcript'])->name('transcript');

    // Program Change Requests
    Route::get('/program-changes', [StudentProgramChangeController::class, 'index'])->name('program-changes.index');
    Route::get('/program-changes/create', [StudentProgramChangeController::class, 'create'])->name('program-changes.create');
    Route::post('/program-changes', [StudentProgramChangeController::class, 'store'])->name('program-changes.store');

    // Fee Management
    Route::get('/fees', [StudentFeeController::class, 'index'])->name('fees.index');
    Route::get('/fees/{fee}/pay', [StudentFeeController::class, 'pay'])->name('fees.pay');
    Route::post('/fees/{fee}/pay', [StudentFeeController::class, 'processPayment'])->name('fees.processPayment');
    Route::get('/fees/{fee}/receipt', [StudentFeeController::class, 'receipt'])->name('fees.receipt');
    Route::get('/fees/{fee}/payments/{payment}/receipt', [StudentFeeController::class, 'transactionReceipt'])->name('fees.transaction-receipt');

    // Attendance
    Route::get('/attendance', [StudentAttendanceController::class, 'index'])->name('attendance.index');
    Route::get('/attendance/{course}', [StudentAttendanceController::class, 'course'])->name('attendance.course');

    // Exam Management
    Route::get('/exams', [StudentExamController::class, 'index'])->name('exams.index');
    Route::get('/exams/{exam}', [StudentExamController::class, 'show'])->name('exams.show');
    Route::post('/exams/{exam}/start', [StudentExamController::class, 'startExam'])->name('exams.start');
    Route::get('/exams/{exam}/take', [StudentExamController::class, 'take'])->name('exams.take');
    Route::post('/exams/{exam}/submit', [StudentExamController::class, 'submitExam'])->name('exams.submit');
    Route::post('/exams/{exam}/autosave', [StudentExamController::class, 'autosave'])->name('exams.autosave');
    Route::get('/exams/{exam}/download-paper', [StudentExamController::class, 'downloadPaper'])->name('exams.download-paper');
    Route::get('/exams/{exam}/download-booklet', [StudentExamController::class, 'downloadAnswerBooklet'])->name('exams.download-booklet');
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
