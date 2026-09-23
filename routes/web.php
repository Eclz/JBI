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
use App\Http\Controllers\Faculty\TimetableController as FacultyTimetableController;

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
use App\Http\Controllers\Admin\TimetableController as AdminTimetableController;
use App\Http\Controllers\Admin\EVotingController as AdminEVotingController;
use App\Http\Controllers\Admin\EvaluationSurveyController as AdminEvaluationSurveyController;
use App\Http\Controllers\Admin\AcademicSetupController as AdminAcademicSetupController;




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
use App\Http\Controllers\Student\TimetableController as StudentTimetableController;
use App\Http\Controllers\Student\EVotingController as StudentEVotingController;
use App\Http\Controllers\Student\EvaluationSurveyController as StudentEvaluationSurveyController;
use App\Http\Controllers\Student\ProgrammeCoursesController as StudentProgrammeCoursesController;
use App\Http\Controllers\StudentsApplicationController;
use App\Http\Controllers\Faculty\LmsController as FacultyLmsController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::get('/', function () {
    $programs = \App\Models\Program::with(['department', 'level'])
        ->withCount('courses')
        ->where('is_active', true)
        ->orderBy('name')
        ->get();

    return view('welcome', compact('programs'));
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
Route::get('/apply', function () {
    return redirect()->route('register');
})->name('applications.create');
Route::post('/apply', [StudentsApplicationController::class, 'store'])->name('applications.store');
Route::get('/application/success/{application}', [StudentsApplicationController::class, 'success'])->name('applications.success');
Route::get('/application/payment/{token}', [StudentsApplicationController::class, 'uploadPayment'])->name('applications.upload-payment');
Route::post('/application/payment/{token}', [StudentsApplicationController::class, 'storePayment'])->name('applications.store-payment');
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

// Email Verification Routes
Route::get('/email/verify/{id}/{hash}', [RegisterController::class, 'verifyEmail'])
    ->middleware(['signed'])
    ->name('verification.verify');
Route::post('/email/resend', [RegisterController::class, 'resendVerification'])
    ->name('verification.resend');

// Authenticated routes
Route::middleware(['auth'])->group(function () {
    // Logout
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Regenerate payment reference
    Route::post('/application/{application}/regenerate-payment-ref', [StudentsApplicationController::class, 'regeneratePaymentRef'])->name('applications.regenerate-payment-ref');

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

// Library routes for all authenticated users
Route::middleware(['auth'])->prefix('library')->name('library.')->group(function () {
    Route::get('/catalogue', [\App\Http\Controllers\LibraryController::class, 'catalogueIndex'])->name('catalogue.index');
    Route::get('/catalogue/{item}', [\App\Http\Controllers\LibraryController::class, 'show'])->name('catalogue.show');
    Route::get('/my-loans', [\App\Http\Controllers\LibraryController::class, 'myLoans'])->name('my-loans');
    Route::get('/loans/create', [\App\Http\Controllers\LibraryController::class, 'createLoan'])->name('loans.create');
    Route::post('/loans', [\App\Http\Controllers\LibraryController::class, 'storeLoan'])->name('loans.store');
});

// Library management module routes
Route::middleware(['auth', 'permission:library,view'])->prefix('library')->name('library.')->group(function () {
    Route::get('/', [\App\Http\Controllers\LibraryController::class, 'index'])->name('index');
    Route::get('/catalogue/create', [\App\Http\Controllers\LibraryController::class, 'create'])->name('catalogue.create')->middleware('permission:library_catalogue,create');
    Route::post('/catalogue', [\App\Http\Controllers\LibraryController::class, 'store'])->name('catalogue.store')->middleware('permission:library_catalogue,create');
    Route::get('/catalogue/{item}/edit', [\App\Http\Controllers\LibraryController::class, 'edit'])->name('catalogue.edit')->middleware('permission:library_catalogue,edit');
    Route::put('/catalogue/{item}', [\App\Http\Controllers\LibraryController::class, 'update'])->name('catalogue.update')->middleware('permission:library_catalogue,edit');
    Route::delete('/catalogue/{item}', [\App\Http\Controllers\LibraryController::class, 'destroy'])->name('catalogue.destroy')->middleware('permission:library_catalogue,delete');
    Route::get('/loans', [\App\Http\Controllers\LibraryController::class, 'loansIndex'])->name('loans.index');
    Route::get('/loans/{loan}', [\App\Http\Controllers\LibraryController::class, 'showLoan'])->name('loans.show');
    Route::get('/loans/{loan}/edit', [\App\Http\Controllers\LibraryController::class, 'editLoan'])->name('loans.edit');
    Route::put('/loans/{loan}', [\App\Http\Controllers\LibraryController::class, 'updateLoan'])->name('loans.update');
    Route::put('/loans/{loan}/renew', [\App\Http\Controllers\LibraryController::class, 'renewLoan'])->name('loans.renew');
    Route::put('/loans/{loan}/return', [\App\Http\Controllers\LibraryController::class, 'returnLoan'])->name('loans.return');
});

// Human resources module routes
Route::middleware(['auth'])->prefix('human-resources')->name('human-resources.')->group(function () {
    Route::get('/', [\App\Http\Controllers\HumanResourcesController::class, 'index'])->name('index');
    Route::get('/staff', [\App\Http\Controllers\HumanResourcesController::class, 'staffIndex'])->name('staff.index')->middleware('permission:human_resources,view');
    Route::get('/staff/create', [\App\Http\Controllers\HumanResourcesController::class, 'create'])->name('staff.create')->middleware('permission:hr_core,create');
    Route::get('/staff/{employee}', [\App\Http\Controllers\HumanResourcesController::class, 'show'])->name('staff.show')->middleware('permission:human_resources,view');
    Route::post('/staff', [\App\Http\Controllers\HumanResourcesController::class, 'store'])->name('staff.store')->middleware('permission:hr_core,create');
    Route::get('/staff/{employee}/edit', [\App\Http\Controllers\HumanResourcesController::class, 'edit'])->name('staff.edit')->middleware('permission:hr_core,edit');
    Route::put('/staff/{employee}', [\App\Http\Controllers\HumanResourcesController::class, 'update'])->name('staff.update')->middleware('permission:hr_core,edit');
    Route::delete('/staff/{employee}', [\App\Http\Controllers\HumanResourcesController::class, 'destroy'])->name('staff.destroy')->middleware('permission:hr_core,delete');
    
    // Leave Management Routes
    Route::get('/leaves', [\App\Http\Controllers\LeaveRequestController::class, 'index'])->name('leaves.index');
    Route::get('/leaves/create', [\App\Http\Controllers\LeaveRequestController::class, 'create'])->name('leaves.create');
    Route::post('/leaves', [\App\Http\Controllers\LeaveRequestController::class, 'store'])->name('leaves.store');
    Route::get('/leaves/{leave}', [\App\Http\Controllers\LeaveRequestController::class, 'show'])->name('leaves.show');
    Route::get('/leaves/{leave}/edit', [\App\Http\Controllers\LeaveRequestController::class, 'edit'])->name('leaves.edit');
    Route::put('/leaves/{leave}', [\App\Http\Controllers\LeaveRequestController::class, 'update'])->name('leaves.update');
    Route::delete('/leaves/{leave}', [\App\Http\Controllers\LeaveRequestController::class, 'destroy'])->name('leaves.destroy');
    Route::patch('/leaves/{leave}/status', [\App\Http\Controllers\LeaveRequestController::class, 'updateStatus'])->name('leaves.status')->middleware('permission:human_resources,view');
    
    // Job Roles
    Route::resource('sections/job-roles', \App\Http\Controllers\JobRoleController::class)->names('job-roles')->middleware('permission:human_resources,view');
    
    // Employee Directory
    Route::get('/directory', [\App\Http\Controllers\HumanResourcesController::class, 'directory'])->name('directory');
    
    // Other Sections placeholder
    Route::get('/sections/{section}', [\App\Http\Controllers\HumanResourcesController::class, 'section'])->name('sections.show')->middleware('permission:human_resources,view');
});

// Facilities routes for all authenticated users
Route::middleware(['auth'])->prefix('facilities')->name('facilities.')->group(function () {
    Route::get('/rooms', [\App\Http\Controllers\FacilitiesController::class, 'roomsIndex'])->name('rooms.index');
    Route::get('/bookings', [\App\Http\Controllers\FacilitiesController::class, 'bookingsIndex'])->name('bookings.index');
    Route::post('/bookings', [\App\Http\Controllers\FacilitiesController::class, 'storeBooking'])->name('bookings.store');
    Route::get('/bookings/{booking}', [\App\Http\Controllers\FacilitiesController::class, 'showBooking'])->name('bookings.show');
    Route::put('/bookings/{booking}', [\App\Http\Controllers\FacilitiesController::class, 'updateBooking'])->name('bookings.update');
    Route::delete('/bookings/{booking}', [\App\Http\Controllers\FacilitiesController::class, 'destroyBooking'])->name('bookings.destroy');
});

// Estates & facilities management routes
Route::middleware(['auth', 'permission:facilities,view'])->prefix('facilities')->name('facilities.')->group(function () {
    Route::get('/', [\App\Http\Controllers\FacilitiesController::class, 'index'])->name('index');
    Route::get('/rooms/create', [\App\Http\Controllers\FacilitiesController::class, 'create'])->name('rooms.create')->middleware('permission:facilities_rooms,create');
    Route::post('/rooms', [\App\Http\Controllers\FacilitiesController::class, 'store'])->name('rooms.store')->middleware('permission:facilities_rooms,create');
    Route::get('/rooms/{room}/edit', [\App\Http\Controllers\FacilitiesController::class, 'edit'])->name('rooms.edit')->middleware('permission:facilities_rooms,edit');
    Route::put('/rooms/{room}', [\App\Http\Controllers\FacilitiesController::class, 'update'])->name('rooms.update')->middleware('permission:facilities_rooms,edit');
    Route::delete('/rooms/{room}', [\App\Http\Controllers\FacilitiesController::class, 'destroy'])->name('rooms.destroy')->middleware('permission:facilities_rooms,delete');
});

// Admin Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // User Management
    Route::middleware('permission:users,view')->group(function () {
        Route::resource('users', AdminUserController::class);
        Route::post('/users/{user}/toggle-status', [AdminUserController::class, 'toggleStatus'])->name('users.toggle-status')->middleware('permission:users,edit');
        Route::post('/users/{user}/reset-password', [AdminUserController::class, 'resetPassword'])->name('users.reset-password')->middleware('permission:users,edit');
    });
    Route::middleware('permission:roles,view')->group(function () {
        Route::resource('roles', AdminRoleController::class)->except(['show']);
    });

    // Student Management
    Route::middleware('permission:students,view')->group(function () {
        Route::get('/students', [AdminStudentController::class, 'index'])->name('students.index');
        Route::get('/students/{student}', [AdminStudentController::class, 'show'])->name('students.show');
        Route::get('/students/{student}/academic-record', [AdminStudentController::class, 'academicRecord'])->name('students.academic-record');
        Route::get('/students/{student}/attendance', [AdminStudentController::class, 'attendance'])->name('students.attendance');
        Route::get('/students/{student}/fees', [AdminStudentController::class, 'fees'])->name('students.fees');
    });

    Route::middleware('permission:students,create')->group(function () {
        Route::get('/students/create', [AdminStudentController::class, 'create'])->name('students.create');
        Route::post('/students', [AdminStudentController::class, 'store'])->name('students.store');
        Route::get('/students/next-admission-number', [AdminStudentController::class, 'getNextAdmissionNumber'])->name('students.next-admission-number');
        Route::post('/students/bulk-import', [AdminStudentController::class, 'bulkImport'])->name('students.bulk-import');
    });

    Route::middleware('permission:students,edit')->group(function () {
        Route::get('/students/{student}/edit', [AdminStudentController::class, 'edit'])->name('students.edit');
        Route::put('/students/{student}', [AdminStudentController::class, 'update'])->name('students.update');
        Route::post('/students/{student}/notes', [AdminStudentController::class, 'addNote'])->name('students.notes.add');
        Route::post('/students/{student}/toggle-status', [AdminStudentController::class, 'toggleStatus'])->name('students.toggle-status');
    });

    Route::middleware('permission:students,delete')->group(function () {
        Route::delete('/students/{student}', [AdminStudentController::class, 'destroy'])->name('students.destroy');
    });

    Route::get('/students/export', [AdminStudentController::class, 'export'])->name('students.export')->middleware('permission:students,export');

    Route::get('/students/{student}/enroll-course', [AdminStudentController::class, 'showEnrollCourse'])->name('students.enroll-course')->middleware('permission:enrollments,create');
    Route::post('/students/{student}/enroll-course', [AdminStudentController::class, 'enrollCourse'])->name('students.enroll-course.store')->middleware('permission:enrollments,create');
    Route::delete('/students/{student}/enrollments/{enrollment}', [AdminStudentController::class, 'removeEnrollment'])->name('students.remove-enrollment')->middleware('permission:enrollments,delete');

    // Faculty Management (Academic Divisions)
    Route::middleware('permission:departments,view')->group(function () {
        Route::get('/faculties', [AdminFacultyController::class, 'index'])->name('faculties.index');
        Route::get('/faculties/create', [AdminFacultyController::class, 'create'])->name('faculties.create');
        Route::post('/faculties', [AdminFacultyController::class, 'store'])->name('faculties.store');
        Route::get('/faculties/{faculty}', [AdminFacultyController::class, 'show'])->name('faculties.show');
        Route::get('/faculties/{faculty}/edit', [AdminFacultyController::class, 'edit'])->name('faculties.edit');
        Route::put('/faculties/{faculty}', [AdminFacultyController::class, 'update'])->name('faculties.update');
        Route::delete('/faculties/{faculty}', [AdminFacultyController::class, 'destroy'])->name('faculties.destroy');
        Route::post('/faculties/{faculty}/toggle-status', [AdminFacultyController::class, 'toggleStatus'])->name('faculties.toggle-status');
    });

    // Faculty Staff Management (Individual Faculty Members)
    Route::middleware('permission:faculty,view')->group(function () {
        Route::get('/faculty-staff', [AdminFacultyStaffController::class, 'index'])->name('faculty-staff.index');
        Route::get('/faculty-staff/create', [AdminFacultyStaffController::class, 'create'])->name('faculty-staff.create');
        Route::post('/faculty-staff', [AdminFacultyStaffController::class, 'store'])->name('faculty-staff.store');
        Route::get('/faculty-staff/{facultyStaff}', [AdminFacultyStaffController::class, 'show'])->name('faculty-staff.show');
        Route::get('/faculty-staff/{facultyStaff}/edit', [AdminFacultyStaffController::class, 'edit'])->name('faculty-staff.edit');
        Route::put('/faculty-staff/{facultyStaff}', [AdminFacultyStaffController::class, 'update'])->name('faculty-staff.update');
        Route::delete('/faculty-staff/{facultyStaff}', [AdminFacultyStaffController::class, 'destroy'])->name('faculty-staff.destroy');
        Route::post('/faculty-staff/{facultyStaff}/toggle-status', [AdminFacultyStaffController::class, 'toggleStatus'])->name('faculty-staff.toggle-status');
        Route::get('/faculty-staff/{facultyStaff}/courses', [AdminFacultyStaffController::class, 'courses'])->name('faculty-staff.courses');
        Route::post('/faculty-staff/{facultyStaff}/assign-course', [AdminFacultyStaffController::class, 'assignCourse'])->name('faculty-staff.assign-course');
    });

    // Course Management
    Route::middleware('permission:courses,view')->group(function () {
        Route::resource('courses', AdminCourseController::class);
        Route::get('/courses/{course}/enrollments', [AdminCourseController::class, 'enrollments'])->name('courses.enrollments');
        Route::get('/courses/{course}/materials', [AdminCourseController::class, 'materials'])->name('courses.materials');
        Route::post('/courses/{course}/materials', [AdminCourseController::class, 'storeMaterial'])->name('courses.materials.store');
        Route::delete('/courses/{course}/materials/{material}', [AdminCourseController::class, 'destroyMaterial'])->name('courses.materials.destroy');
        Route::get('/courses/{course}/assignments', [AdminCourseController::class, 'assignments'])->name('courses.assignments');
        Route::get('/courses/{course}/grades', [AdminCourseController::class, 'grades'])->name('courses.grades');
        Route::post('/courses/{course}/toggle-status', [AdminCourseController::class, 'toggleStatus'])->name('courses.toggle-status');
        Route::post('/courses/{course}/enroll-student', [AdminCourseController::class, 'enrollStudent'])->name('courses.enroll-student')->middleware('permission:enrollments,create');
        Route::delete('/courses/{course}/enrollments/{enrollment}/drop', [AdminCourseController::class, 'dropStudent'])->name('courses.drop-student')->middleware('permission:enrollments,delete');
    });

    // Enrollment Management
    Route::prefix('enrollments')->name('enrollments.')->middleware('permission:enrollments,view')->group(function () {
        Route::get('/', [EnrollmentController::class, 'index'])->name('index');
        Route::get('/create', [EnrollmentController::class, 'create'])->name('create')->middleware('permission:enrollments,create');
        Route::post('/', [EnrollmentController::class, 'store'])->name('store')->middleware('permission:enrollments,create');
        Route::get('/{enrollment}', [EnrollmentController::class, 'show'])->name('show');
        Route::get('/{enrollment}/edit', [EnrollmentController::class, 'edit'])->name('edit')->middleware('permission:enrollments,edit');
        Route::put('/{enrollment}', [EnrollmentController::class, 'update'])->name('update')->middleware('permission:enrollments,edit');
        Route::delete('/{enrollment}', [EnrollmentController::class, 'destroy'])->name('destroy')->middleware('permission:enrollments,delete');
        Route::post('/{enrollment}/approve', [EnrollmentController::class, 'approve'])->name('approve')->middleware('permission:enrollments,approve');
        Route::post('/{enrollment}/reject', [EnrollmentController::class, 'reject'])->name('reject')->middleware('permission:enrollments,approve');
        Route::post('/bulk-enroll', [EnrollmentController::class, 'bulkEnroll'])->name('bulk-enroll')->middleware('permission:enrollments,create');
    });

    // Fee Management
    Route::prefix('fees')->name('fees.')->middleware('permission:fees,view')->group(function () {
        // Main fees index route
        Route::get('/', [AdminFeeController::class, 'index'])->name('index');

        // Fee Records Management
        Route::prefix('records')->name('records.')->group(function () {
            Route::get('/', [AdminFeeController::class, 'index'])->name('index');
            Route::get('/create', [AdminFeeController::class, 'create'])->name('create')->middleware('permission:fees,create');
            Route::post('/', [AdminFeeController::class, 'store'])->name('store')->middleware('permission:fees,create');
            Route::get('/{fee}', [AdminFeeController::class, 'show'])->name('show');
            Route::get('/{fee}/demand-notice', [AdminFeeController::class, 'demandNotice'])->name('demand-notice');
            Route::post('/{fee}/send-demand-notice', [AdminFeeController::class, 'sendDemandNotice'])->name('send-demand-notice');
            Route::get('/{fee}/receipt', [AdminFeeController::class, 'receipt'])->name('receipt');
            Route::get('/{fee}/edit', [AdminFeeController::class, 'edit'])->name('edit')->middleware('permission:fees,edit');
            Route::put('/{fee}', [AdminFeeController::class, 'update'])->name('update')->middleware('permission:fees,edit');
            Route::delete('/{fee}', [AdminFeeController::class, 'destroy'])->name('destroy')->middleware('permission:fees,delete');
            Route::get('/{fee}/payment', [AdminFeeController::class, 'showPayment'])->name('payment')->middleware('permission:fees,create');
            Route::post('/{fee}/payment', [AdminFeeController::class, 'processPayment'])->name('process-payment')->middleware('permission:fees,create');
            Route::post('/generate-invoices', [AdminFeeController::class, 'generateInvoices'])->name('generate-invoices')->middleware('permission:fees,create');
            Route::post('/send-reminders', [AdminFeeController::class, 'sendReminders'])->name('send-reminders')->middleware('permission:fees,edit');
            Route::get('/export', [AdminFeeController::class, 'export'])->name('export')->middleware('permission:fees,export');
        });

        Route::prefix('payments')->name('payments.')->group(function () {
            Route::post('/{payment}/approve', [AdminFeeController::class, 'approvePayment'])->name('approve')->middleware('permission:fees,approve');
            Route::get('/{payment}/receipt', [AdminFeeController::class, 'transactionReceipt'])->name('receipt');
        });

        // Fee Structures Management
        Route::prefix('structures')->name('structures.')->group(function () {
            Route::get('/', [AdminFeeController::class, 'structures'])->name('index');
            Route::get('/create', [AdminFeeController::class, 'createStructure'])->name('create')->middleware('permission:fees,create');
            Route::post('/', [AdminFeeController::class, 'storeStructure'])->name('store')->middleware('permission:fees,create');
            Route::get('/{feeStructure}', [AdminFeeController::class, 'showStructure'])->name('show');
            Route::get('/{feeStructure}/edit', [AdminFeeController::class, 'editStructure'])->name('edit')->middleware('permission:fees,edit');
            Route::put('/{feeStructure}', [AdminFeeController::class, 'updateStructure'])->name('update')->middleware('permission:fees,edit');
            Route::delete('/{feeStructure}', [AdminFeeController::class, 'destroyStructure'])->name('destroy')->middleware('permission:fees,delete');
        });
    });

    // Academic Faculties Management (already defined above via AdminFacultyController)

    // Comprehensive Bursar & Finance Hub (17 Sub-Modules)
    // Comprehensive Bursar & Finance Hub (17 Sub-Modules)
    Route::prefix('finance')->name('finance.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\BursarFinanceController::class, 'dashboard'])->name('dashboard')->middleware('permission:finance_hub,view');
        
        Route::middleware('permission:revenue,view')->group(function () {
            Route::get('/revenue', [\App\Http\Controllers\Admin\BursarFinanceController::class, 'revenue'])->name('revenue.index');
            Route::post('/revenue', [\App\Http\Controllers\Admin\BursarFinanceController::class, 'storeRevenue'])->name('revenue.store')->middleware('permission:revenue,create');
            Route::get('/revenue/{id}', [\App\Http\Controllers\Admin\BursarFinanceController::class, 'showRevenue'])->name('revenue.show');
            Route::get('/revenue/{id}/edit', [\App\Http\Controllers\Admin\BursarFinanceController::class, 'editRevenue'])->name('revenue.edit')->middleware('permission:revenue,update');
            Route::put('/revenue/{id}', [\App\Http\Controllers\Admin\BursarFinanceController::class, 'updateRevenue'])->name('revenue.update')->middleware('permission:revenue,update');
            Route::delete('/revenue/{id}', [\App\Http\Controllers\Admin\BursarFinanceController::class, 'destroyRevenue'])->name('revenue.destroy')->middleware('permission:revenue,delete');
        });

        Route::middleware('permission:budgets,view')->group(function () {
            Route::get('/budgets', [\App\Http\Controllers\Admin\BursarFinanceController::class, 'budgets'])->name('budgets.index');
            Route::post('/budgets', [\App\Http\Controllers\Admin\BursarFinanceController::class, 'storeBudget'])->name('budgets.store')->middleware('permission:budgets,create');
            Route::get('/budgets/{id}', [\App\Http\Controllers\Admin\BursarFinanceController::class, 'showBudget'])->name('budgets.show');
            Route::get('/budgets/{id}/edit', [\App\Http\Controllers\Admin\BursarFinanceController::class, 'editBudget'])->name('budgets.edit')->middleware('permission:budgets,update');
            Route::put('/budgets/{id}', [\App\Http\Controllers\Admin\BursarFinanceController::class, 'updateBudget'])->name('budgets.update')->middleware('permission:budgets,update');
            Route::delete('/budgets/{id}', [\App\Http\Controllers\Admin\BursarFinanceController::class, 'destroyBudget'])->name('budgets.destroy')->middleware('permission:budgets,delete');
        });

        Route::middleware('permission:expenses,view')->group(function () {
            Route::get('/expenses', [\App\Http\Controllers\Admin\BursarFinanceController::class, 'expenses'])->name('expenses.index');
            Route::post('/expenses', [\App\Http\Controllers\Admin\BursarFinanceController::class, 'storeExpense'])->name('expenses.store')->middleware('permission:expenses,create');
            Route::get('/expenses/{id}', [\App\Http\Controllers\Admin\BursarFinanceController::class, 'showExpense'])->name('expenses.show');
            Route::get('/expenses/{id}/edit', [\App\Http\Controllers\Admin\BursarFinanceController::class, 'editExpense'])->name('expenses.edit')->middleware('permission:expenses,update');
            Route::put('/expenses/{id}', [\App\Http\Controllers\Admin\BursarFinanceController::class, 'updateExpense'])->name('expenses.update')->middleware('permission:expenses,update');
            Route::delete('/expenses/{id}', [\App\Http\Controllers\Admin\BursarFinanceController::class, 'destroyExpense'])->name('expenses.destroy')->middleware('permission:expenses,delete');
            Route::get('/procurement', [\App\Http\Controllers\Admin\BursarFinanceController::class, 'procurement'])->name('procurement.index');
        });

        Route::middleware('permission:payables,view')->group(function () {
            Route::get('/payables', [\App\Http\Controllers\Admin\BursarFinanceController::class, 'payables'])->name('payables.index');
            Route::post('/payables/suppliers', [\App\Http\Controllers\Admin\BursarFinanceController::class, 'storeSupplier'])->name('payables.suppliers.store')->middleware('permission:payables,create');
            Route::get('/payables/suppliers/{id}', [\App\Http\Controllers\Admin\BursarFinanceController::class, 'showSupplier'])->name('payables.suppliers.show');
            Route::get('/payables/suppliers/{id}/edit', [\App\Http\Controllers\Admin\BursarFinanceController::class, 'editSupplier'])->name('payables.suppliers.edit')->middleware('permission:payables,update');
            Route::put('/payables/suppliers/{id}', [\App\Http\Controllers\Admin\BursarFinanceController::class, 'updateSupplier'])->name('payables.suppliers.update')->middleware('permission:payables,update');
            Route::delete('/payables/suppliers/{id}', [\App\Http\Controllers\Admin\BursarFinanceController::class, 'destroySupplier'])->name('payables.suppliers.destroy')->middleware('permission:payables,delete');

            Route::post('/payables/invoices', [\App\Http\Controllers\Admin\BursarFinanceController::class, 'storeVendorInvoice'])->name('payables.invoices.store')->middleware('permission:payables,create');
            Route::get('/payables/invoices/{id}', [\App\Http\Controllers\Admin\BursarFinanceController::class, 'showVendorInvoice'])->name('payables.invoices.show');
            Route::get('/payables/invoices/{id}/edit', [\App\Http\Controllers\Admin\BursarFinanceController::class, 'editVendorInvoice'])->name('payables.invoices.edit')->middleware('permission:payables,update');
            Route::put('/payables/invoices/{id}', [\App\Http\Controllers\Admin\BursarFinanceController::class, 'updateVendorInvoice'])->name('payables.invoices.update')->middleware('permission:payables,update');
            Route::delete('/payables/invoices/{id}', [\App\Http\Controllers\Admin\BursarFinanceController::class, 'destroyVendorInvoice'])->name('payables.invoices.destroy')->middleware('permission:payables,delete');
        });

        Route::middleware('permission:receivables,view')->group(function () {
            Route::get('/receivables', [\App\Http\Controllers\Admin\BursarFinanceController::class, 'receivables'])->name('receivables.index');
            Route::get('/receivables/{id}', [\App\Http\Controllers\Admin\BursarFinanceController::class, 'showReceivable'])->name('receivables.show');
            
            Route::get('/grants', [\App\Http\Controllers\Admin\BursarFinanceController::class, 'grants'])->name('grants.index');
            Route::post('/grants', [\App\Http\Controllers\Admin\BursarFinanceController::class, 'storeGrant'])->name('grants.store')->middleware('permission:receivables,create');
            Route::get('/grants/{id}', [\App\Http\Controllers\Admin\BursarFinanceController::class, 'showGrant'])->name('grants.show');
            Route::get('/grants/{id}/edit', [\App\Http\Controllers\Admin\BursarFinanceController::class, 'editGrant'])->name('grants.edit')->middleware('permission:receivables,update');
            Route::put('/grants/{id}', [\App\Http\Controllers\Admin\BursarFinanceController::class, 'updateGrant'])->name('grants.update')->middleware('permission:receivables,update');
            Route::delete('/grants/{id}', [\App\Http\Controllers\Admin\BursarFinanceController::class, 'destroyGrant'])->name('grants.destroy')->middleware('permission:receivables,delete');
        });

        Route::middleware('permission:payroll,view')->group(function () {
            Route::get('/payroll', [\App\Http\Controllers\Admin\BursarFinanceController::class, 'payroll'])->name('payroll.index');
            Route::post('/payroll/generate', [\App\Http\Controllers\Admin\BursarFinanceController::class, 'generatePayroll'])->name('payroll.generate')->middleware('permission:payroll,create');
            Route::get('/payroll/{id}', [\App\Http\Controllers\Admin\BursarFinanceController::class, 'showPayroll'])->name('payroll.show');
            Route::put('/payroll/{id}', [\App\Http\Controllers\Admin\BursarFinanceController::class, 'updatePayroll'])->name('payroll.update')->middleware('permission:payroll,edit');
            Route::delete('/payroll/{id}', [\App\Http\Controllers\Admin\BursarFinanceController::class, 'destroyPayroll'])->name('payroll.destroy')->middleware('permission:payroll,delete');
        });

        Route::middleware('permission:assets,view')->group(function () {
            Route::get('/assets', [\App\Http\Controllers\Admin\BursarFinanceController::class, 'assets'])->name('assets.index');
            Route::post('/assets', [\App\Http\Controllers\Admin\BursarFinanceController::class, 'storeAsset'])->name('assets.store')->middleware('permission:assets,create');
            Route::get('/assets/{id}', [\App\Http\Controllers\Admin\BursarFinanceController::class, 'showAsset'])->name('assets.show');
            Route::get('/assets/{id}/edit', [\App\Http\Controllers\Admin\BursarFinanceController::class, 'editAsset'])->name('assets.edit')->middleware('permission:assets,update');
            Route::put('/assets/{id}', [\App\Http\Controllers\Admin\BursarFinanceController::class, 'updateAsset'])->name('assets.update')->middleware('permission:assets,update');
            Route::delete('/assets/{id}', [\App\Http\Controllers\Admin\BursarFinanceController::class, 'destroyAsset'])->name('assets.destroy')->middleware('permission:assets,delete');
        });

        Route::middleware('permission:banking,view')->group(function () {
            Route::get('/banking', [\App\Http\Controllers\Admin\BursarFinanceController::class, 'banking'])->name('banking.index');
            Route::post('/banking', [\App\Http\Controllers\Admin\BursarFinanceController::class, 'storeBankAccount'])->name('banking.store')->middleware('permission:banking,create');
            Route::get('/banking/{id}', [\App\Http\Controllers\Admin\BursarFinanceController::class, 'showBankAccount'])->name('banking.show');
            Route::get('/banking/{id}/edit', [\App\Http\Controllers\Admin\BursarFinanceController::class, 'editBankAccount'])->name('banking.edit')->middleware('permission:banking,update');
            Route::put('/banking/{id}', [\App\Http\Controllers\Admin\BursarFinanceController::class, 'updateBankAccount'])->name('banking.update')->middleware('permission:banking,update');
            Route::delete('/banking/{id}', [\App\Http\Controllers\Admin\BursarFinanceController::class, 'destroyBankAccount'])->name('banking.destroy')->middleware('permission:banking,delete');
        });

        Route::get('/reports', [\App\Http\Controllers\Admin\BursarFinanceController::class, 'reports'])->name('reports.index')->middleware('permission:financial_statements,view');
        Route::get('/audit', [\App\Http\Controllers\Admin\BursarFinanceController::class, 'audit'])->name('audit.index')->middleware('permission:finance_hub,view');
    });

    // Department Management
    Route::get('/academic-setup', [AdminAcademicSetupController::class, 'index'])->name('academic-setup.index');
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

    // Admin Timetable Management
    Route::resource('timetables', AdminTimetableController::class);

    // Admin E-Voting & Student Leadership Management
    Route::prefix('evoting')->name('evoting.')->group(function () {
        Route::get('/', [AdminEVotingController::class, 'index'])->name('index');
        Route::post('/sessions', [AdminEVotingController::class, 'storeSession'])->name('sessions.store');
        Route::get('/sessions/{session}', [AdminEVotingController::class, 'show'])->name('show');
        Route::put('/sessions/{session}', [AdminEVotingController::class, 'updateSession'])->name('sessions.update');
        Route::post('/sessions/{session}/status', [AdminEVotingController::class, 'updateSessionStatus'])->name('sessions.status');
        Route::delete('/sessions/{session}', [AdminEVotingController::class, 'destroySession'])->name('sessions.destroy');
        Route::post('/sessions/{session}/publish', [AdminEVotingController::class, 'publishResults'])->name('sessions.publish');
        Route::get('/sessions/{session}/results', [AdminEVotingController::class, 'results'])->name('results');

        // Electoral Commission
        Route::post('/sessions/{session}/commission', [AdminEVotingController::class, 'storeCommissionMember'])->name('commission.store');
        Route::delete('/sessions/{session}/commission/{member}', [AdminEVotingController::class, 'destroyCommissionMember'])->name('commission.destroy');

        // Positions
        Route::post('/sessions/{session}/positions', [AdminEVotingController::class, 'storePosition'])->name('positions.store');
        Route::put('/positions/{position}', [AdminEVotingController::class, 'updatePosition'])->name('positions.update');
        Route::delete('/positions/{position}', [AdminEVotingController::class, 'destroyPosition'])->name('positions.destroy');

        // Candidate Vetting
        Route::post('/candidates/{candidate}/vet', [AdminEVotingController::class, 'vetCandidate'])->name('candidates.vet');

        // Elected Student Leaders
        Route::get('/leaders', [AdminEVotingController::class, 'leadersIndex'])->name('leaders');
    });

    // Admin Evaluation Surveys
    Route::resource('evaluation-surveys', AdminEvaluationSurveyController::class)->parameters([
        'evaluation-surveys' => 'survey'
    ]);
    Route::post('/evaluation-surveys/{survey}/toggle-status', [AdminEvaluationSurveyController::class, 'toggleStatus'])->name('evaluation-surveys.toggle-status');
    Route::post('/evaluation-surveys/{survey}/questions', [AdminEvaluationSurveyController::class, 'addQuestion'])->name('evaluation-surveys.questions.store');
    Route::delete('/evaluation-surveys/{survey}/questions/{question}', [AdminEvaluationSurveyController::class, 'destroyQuestion'])->name('evaluation-surveys.questions.destroy');


    // Application Management
    Route::get('/applications', [AdminApplicationController::class, 'index'])->name('applications.index');
    Route::get('/applications/{application}', [AdminApplicationController::class, 'show'])->name('applications.show');
    Route::post('/applications/bulk-approve', [AdminApplicationController::class, 'bulkApprove'])->name('applications.bulk-approve');
    Route::post('/applications/{application}/approve', [AdminApplicationController::class, 'approve'])->name('applications.approve');
    Route::post('/applications/{application}/reject', [AdminApplicationController::class, 'reject'])->name('applications.reject');
    Route::post('/applications/{application}/update-program', [AdminApplicationController::class, 'updateProgram'])->name('applications.update-program');
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
    Route::get('/lms', [FacultyLmsController::class, 'index'])->name('lms.index')->middleware('permission:lms,view');
    Route::get('/lms/{course}', [FacultyLmsController::class, 'show'])->name('lms.show')->middleware('permission:lms,view');

    // General overview routes
    Route::get('/attendance', [FacultyAttendanceController::class, 'overview'])->name('attendance.index')->middleware('permission:attendance,view');
    Route::get('/grading', [FacultyGradingController::class, 'overview'])->name('grading.index')->middleware('permission:grades,view');
    Route::get('/materials', [FacultyMaterialController::class, 'overview'])->name('materials.index')->middleware('permission:courses,view');

    // Course Management
    Route::get('/courses', [FacultyCourseController::class, 'index'])->name('courses.index')->middleware('permission:courses,view');
    Route::get('/courses/{course}', [FacultyCourseController::class, 'show'])->name('courses.show')->middleware('permission:courses,view');
    Route::put('/courses/{course}', [FacultyCourseController::class, 'update'])->name('courses.update')->middleware('permission:courses,edit');
    Route::post('/courses/{course}/enroll-student', [FacultyCourseController::class, 'enrollStudent'])->name('courses.enroll-student')->middleware('permission:courses,edit');
    Route::delete('/courses/{course}/enrollments/{enrollment}/drop', [FacultyCourseController::class, 'dropStudent'])->name('courses.drop-student')->middleware('permission:courses,edit');

    // Material Management
    Route::get('/courses/{course}/materials', [FacultyMaterialController::class, 'index'])->name('courses.materials.index')->middleware('permission:courses,view');
    Route::get('/courses/{course}/materials/create', [FacultyMaterialController::class, 'create'])->name('courses.materials.create')->middleware('permission:courses,edit');
    Route::post('/courses/{course}/materials', [FacultyMaterialController::class, 'store'])->name('courses.materials.store')->middleware('permission:courses,edit');
    Route::delete('/courses/{course}/materials/{material}', [FacultyMaterialController::class, 'destroy'])->name('courses.materials.destroy')->middleware('permission:courses,edit');

    // Assignment Management
    Route::prefix('assignments')->name('assignments.')->middleware('permission:courses,view')->group(function () {
        Route::get('/', [FacultyAssignmentController::class, 'index'])->name('index');
        Route::get('/create', [FacultyAssignmentController::class, 'create'])->name('create')->middleware('permission:courses,edit');
        Route::post('/', [FacultyAssignmentController::class, 'store'])->name('store')->middleware('permission:courses,edit');
        Route::get('/{assignment}', [FacultyAssignmentController::class, 'show'])->name('show');
        Route::get('/{assignment}/edit', [FacultyAssignmentController::class, 'edit'])->name('edit')->middleware('permission:courses,edit');
        Route::put('/{assignment}', [FacultyAssignmentController::class, 'update'])->name('update')->middleware('permission:courses,edit');
        Route::delete('/{assignment}', [FacultyAssignmentController::class, 'destroy'])->name('destroy')->middleware('permission:courses,edit');
        Route::get('/{assignment}/submissions', [FacultyAssignmentController::class, 'submissions'])->name('submissions');
        Route::post('/{assignment}/submissions/{submission}/grade', [FacultyAssignmentController::class, 'gradeSubmission'])->name('submissions.grade')->middleware('permission:grades,edit');
    });
    Route::get('/courses/{course}/assignments', [FacultyAssignmentController::class, 'courseAssignments'])->name('courses.assignments.index')->middleware('permission:courses,view');
    Route::post('/courses/{course}/assignments', [FacultyAssignmentController::class, 'store'])->name('courses.assignments.store')->middleware('permission:courses,edit');

    Route::prefix('exams')->name('exams.')->middleware('permission:exams,view')->group(function () {
        Route::get('/', [FacultyExamController::class, 'index'])->name('index');
        Route::get('/create', [FacultyExamController::class, 'create'])->name('create')->middleware('permission:exams,edit');
        Route::post('/', [FacultyExamController::class, 'store'])->name('store')->middleware('permission:exams,edit');
        Route::get('/{exam}', [FacultyExamController::class, 'show'])->name('show');
        Route::get('/{exam}/edit', [FacultyExamController::class, 'edit'])->name('edit')->middleware('permission:exams,edit');
        Route::put('/{exam}', [FacultyExamController::class, 'update'])->name('update')->middleware('permission:exams,edit');
        Route::delete('/{exam}', [FacultyExamController::class, 'destroy'])->name('destroy')->middleware('permission:exams,edit');
        Route::get('/{exam}/attempts', [FacultyExamController::class, 'attempts'])->name('attempts');
        Route::get('/{exam}/attempts/{attempt}', [FacultyExamController::class, 'submission'])->name('attempts.show');
        Route::post('/{exam}/attempts/{attempt}/grade', [FacultyExamController::class, 'gradeAttempt'])->name('attempts.grade')->middleware('permission:grades,edit');
        Route::post('/{exam}/grade/{attempt}', [FacultyExamController::class, 'gradeAttempt'])->name('grade')->middleware('permission:grades,edit');
    });

    Route::prefix('quizzes')->name('quizzes.')->middleware('permission:exams,view')->group(function () {
        Route::get('/', [FacultyQuizController::class, 'index'])->name('index');
        Route::get('/create', [FacultyQuizController::class, 'create'])->name('create')->middleware('permission:exams,edit');
        Route::post('/', [FacultyQuizController::class, 'store'])->name('store')->middleware('permission:exams,edit');
        Route::get('/{quiz}', [FacultyQuizController::class, 'show'])->name('show');
        Route::get('/{quiz}/edit', [FacultyQuizController::class, 'edit'])->name('edit')->middleware('permission:exams,edit');
        Route::put('/{quiz}', [FacultyQuizController::class, 'update'])->name('update')->middleware('permission:exams,edit');
        Route::delete('/{quiz}', [FacultyQuizController::class, 'destroy'])->name('destroy')->middleware('permission:exams,edit');
        Route::get('/{quiz}/questions', [FacultyQuizController::class, 'questions'])->name('questions');
        Route::post('/{quiz}/questions', [FacultyQuizController::class, 'storeQuestion'])->name('questions.store')->middleware('permission:exams,edit');
        Route::put('/{quiz}/questions/{question}', [FacultyQuizController::class, 'updateQuestion'])->name('questions.update')->middleware('permission:exams,edit');
        Route::delete('/{quiz}/questions/{question}', [FacultyQuizController::class, 'destroyQuestion'])->name('questions.destroy')->middleware('permission:exams,edit');
        Route::get('/{quiz}/attempts', [FacultyQuizController::class, 'attempts'])->name('attempts');
    });

    // Attendance Management
    Route::get('/courses/{course}/attendance', [FacultyAttendanceController::class, 'index'])->name('courses.attendance.index')->middleware('permission:attendance,view');
    Route::get('/courses/{course}/attendance/show', [FacultyAttendanceController::class, 'show'])->name('courses.attendance.show')->middleware('permission:attendance,view');
    Route::get('/courses/{course}/attendance/qr', [FacultyAttendanceController::class, 'generateQRCode'])->name('courses.attendance.qr')->middleware('permission:attendance,edit');
    Route::post('/courses/{course}/attendance', [FacultyAttendanceController::class, 'store'])->name('courses.attendance.store')->middleware('permission:attendance,edit');
    Route::put('/courses/{course}/attendance/{attendance}', [FacultyAttendanceController::class, 'update'])->name('courses.attendance.update')->middleware('permission:attendance,edit');

    // Faculty Timetables
    Route::get('/timetables', [FacultyTimetableController::class, 'index'])->name('timetables.index');

    // Grading
    Route::get('/courses/{course}/grades', [FacultyGradingController::class, 'index'])->name('courses.grades.index')->middleware('permission:grades,view');
    Route::post('/courses/{course}/grades', [FacultyGradingController::class, 'store'])->name('courses.grades.store')->middleware('permission:grades,edit');
    Route::put('/courses/{course}/grades/{grade}', [FacultyGradingController::class, 'update'])->name('courses.grades.update')->middleware('permission:grades,edit');
    Route::get('/courses/{course}/grading', [FacultyGradingController::class, 'course'])->name('courses.grading')->middleware('permission:grades,view');
    Route::get('/courses/{course}/gradebook', [FacultyGradingController::class, 'gradebook'])->name('courses.gradebook')->middleware('permission:grades,view');
    Route::post('/assignments/{assignment}/grade', [FacultyGradingController::class, 'storeGrade'])->name('grading.store')->middleware('permission:grades,edit');
    Route::post('/assignments/{assignment}/bulk-grade', [FacultyGradingController::class, 'bulkGrade'])->name('grading.bulk')->middleware('permission:grades,edit');
    Route::post('/assignments/{assignment}/publish', [FacultyGradingController::class, 'publishGrades'])->name('grading.publish')->middleware('permission:grades,edit');

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
    Route::get('/fees/structure', [StudentFeeController::class, 'structure'])->name('fees.structure');
    Route::get('/ledger', [StudentFeeController::class, 'ledger'])->name('fees.ledger');
    Route::get('/fees/{fee}/pay', [StudentFeeController::class, 'pay'])->name('fees.pay');
    Route::post('/fees/{fee}/pay', [StudentFeeController::class, 'processPayment'])->name('fees.processPayment');
    Route::get('/fees/{fee}/receipt', [StudentFeeController::class, 'receipt'])->name('fees.receipt');
    Route::get('/fees/{fee}/payments/{payment}/receipt', [StudentFeeController::class, 'transactionReceipt'])->name('fees.transaction-receipt');
    Route::post('/prn/generate', [StudentFeeController::class, 'generatePrn'])->name('fees.prn.generate');
    Route::get('/prn/{prn}', [StudentFeeController::class, 'showPrn'])->name('fees.prn.show');
    Route::post('/prn/{prn}/pay', [StudentFeeController::class, 'processPrnPayment'])->name('fees.prn.pay');

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

    // Student Timetables
    Route::get('/timetables/teaching', [StudentTimetableController::class, 'teaching'])->name('timetables.teaching');
    Route::get('/timetables/tests', [StudentTimetableController::class, 'tests'])->name('timetables.tests');
    Route::get('/timetables/exams', [StudentTimetableController::class, 'exams'])->name('timetables.exams');

    // E-Voting & Student Leadership
    Route::prefix('evoting')->name('evoting.')->group(function () {
        Route::get('/', [StudentEVotingController::class, 'index'])->name('index');
        Route::get('/sessions/{session}/apply', [StudentEVotingController::class, 'apply'])->name('apply');
        Route::post('/sessions/{session}/apply', [StudentEVotingController::class, 'storeApplication'])->name('apply.store');
        Route::get('/my-applications', [StudentEVotingController::class, 'myApplications'])->name('my-applications');
        Route::get('/sessions/{session}/ballot', [StudentEVotingController::class, 'ballot'])->name('ballot');
        Route::post('/sessions/{session}/vote', [StudentEVotingController::class, 'castVote'])->name('vote');
        Route::get('/sessions/{session}/results', [StudentEVotingController::class, 'results'])->name('results');
        Route::get('/leaders', [StudentEVotingController::class, 'leaders'])->name('leaders');
    });

    // Evaluation Surveys
    Route::get('/evaluation-surveys', [StudentEvaluationSurveyController::class, 'index'])->name('evaluation-surveys.index');
    Route::get('/evaluation-surveys/{survey}/course/{course}', [StudentEvaluationSurveyController::class, 'show'])->name('evaluation-surveys.show');
    Route::post('/evaluation-surveys/{survey}/course/{course}', [StudentEvaluationSurveyController::class, 'store'])->name('evaluation-surveys.store');

    // My Programme & Enrollment
    Route::get('/my-programme', [StudentProgrammeCoursesController::class, 'myProgramme'])->name('my-programme');
    Route::get('/enrollment', [StudentProgrammeCoursesController::class, 'showEnrollment'])->name('enrollment.index');
    Route::post('/enrollment', [StudentProgrammeCoursesController::class, 'processEnrollment'])->name('enrollment.store');
    Route::delete('/enrollment/unenroll/{course}', [StudentProgrammeCoursesController::class, 'unenroll'])->name('enrollment.unenroll');
    Route::get('/admission-letter', [StudentDashboardController::class, 'showAdmissionLetter'])->name('admission-letter.show');
    Route::match(['get', 'post'], '/dashboard/acknowledge', [StudentDashboardController::class, 'acknowledgeAdmission'])->name('dashboard.acknowledge');
});


// Shared routes (All authenticated users)
Route::middleware(['auth'])->group(function () {
    // Mailbox & Messaging
    Route::get('/messages', [\App\Http\Controllers\MessageController::class, 'index'])->name('messages.index');
    Route::post('/messages', [\App\Http\Controllers\MessageController::class, 'store'])->name('messages.store');
    Route::post('/messages/group', [\App\Http\Controllers\MessageController::class, 'storeGroup'])->name('messages.storeGroup');
    Route::get('/messages/{message}', [\App\Http\Controllers\MessageController::class, 'show'])->name('messages.show');

    // Academic Calendar
    Route::get('/academic-calendar', [\App\Http\Controllers\CalendarController::class, 'index'])->name('academic-calendar.index');

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
