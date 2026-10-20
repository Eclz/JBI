<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Application;
use App\Models\Assignment;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\Department;
use App\Models\Exam;
use App\Models\FeeRecord;
use App\Models\FeeStructure;
use App\Models\Program;
use App\Models\Quiz;
use App\Models\Semester;
use App\Models\StudentProfile;
use App\Models\SystemSetting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class StudentExamTuitionPermitTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);
        Mail::fake();
        SystemSetting::setSetting('admission_window_open', '1');
    }

    public function test_student_applicant_is_not_automatically_assigned_programme_on_submission(): void
    {
        $department = Department::factory()->create();
        $program1 = Program::create([
            'name' => 'Computer Science',
            'code' => 'CS101',
            'department_id' => $department->id,
            'is_active' => true,
        ]);
        $program2 = Program::create([
            'name' => 'Information Technology',
            'code' => 'IT101',
            'department_id' => $department->id,
            'is_active' => true,
        ]);

        $applicant = User::factory()->student()->create(['email' => 'jane.applicant@test.com']);
        $response = $this->actingAs($applicant)->post(route('applications.store'), [
            'documents' => [\Illuminate\Http\UploadedFile::fake()->create('certificate.pdf', 10, 'application/pdf')],
            'type' => 'student',
            'first_name' => 'Jane',
            'last_name' => 'Applicant',
            'email' => 'jane.applicant@test.com',
            'phone' => '+1 555 123 4567',
            'date_of_birth' => '2002-05-15',
            'gender' => 'female',
            'address' => '123 Campus Way',
            'emergency_contact_name' => 'Mary Applicant',
            'emergency_contact_phone' => '+1 555 987 6543',
            'program_id_1' => $program1->id,
            'program_id_2' => $program2->id,
        ]);

        $response->assertRedirect(route('dashboard'));

        $application = Application::where('email', 'jane.applicant@test.com')->first();
        $this->assertNotNull($application);
        // Assert that program_id and program are NOT automatically assigned
        $this->assertNull($application->program_id);
        $this->assertNull($application->program);
        // Assert that program_choices reflect the selected programs
        $this->assertEquals([$program1->id, $program2->id], $application->program_choices);
    }

    public function test_admin_must_manually_assign_programme_before_approving_student_application(): void
    {
        $admin = User::factory()->admin()->create(['role_id' => null]);
        $department = Department::factory()->create();
        $program = Program::create([
            'name' => 'Software Engineering',
            'code' => 'SE101',
            'department_id' => $department->id,
            'is_active' => true,
        ]);

        $application = Application::create([
            'type' => 'student',
            'first_name' => 'Sam',
            'last_name' => 'Student',
            'email' => 'sam.student@test.com',
            'phone' => '+1 555 333 4444',
            'date_of_birth' => '2001-08-20',
            'gender' => 'male',
            'address' => '456 University Ave',
            'application_number' => 'APP-STU-2026-9999',
            'documents_verified_at' => now(),
            'status' => 'pending',
            'program_choices' => [$program->id],
            'program_id' => null,
            'program' => null,
            'payment_status' => 'verified',
            'payment_verified_at' => now(),
        ]);

        // Attempt to approve without assigned program should fail
        $response = $this->actingAs($admin)->post(route('admin.applications.approve', $application->id));
        $response->assertSessionHas('error');
        $this->assertEquals('pending', $application->fresh()->status);

        // Admin manually assigns the programme
        $assignResponse = $this->actingAs($admin)->post(route('admin.applications.update-program', $application->id), [
            'program_id' => $program->id,
        ]);
        $assignResponse->assertSessionHas('success');
        $this->assertEquals($program->id, $application->fresh()->program_id);

        // Now approval succeeds
        $approveResponse = $this->actingAs($admin)->post(route('admin.applications.approve', $application->id), [
            'force_approve' => true,
        ]);
        $approveResponse->assertSessionHas('success');
        $this->assertEquals('admitted', $application->fresh()->status);
    }

    public function test_student_with_unpaid_tuition_cannot_sit_for_exams_or_access_permit(): void
    {
        $student = User::factory()->student()->create();
        $studentProfile = StudentProfile::factory()->active()->create(['user_id' => $student->id]);
        $department = Department::factory()->create();
        $course = Course::factory()->create(['department_id' => $department->id]);
        $feeStructure = FeeStructure::factory()->create(['type' => 'tuition']);

        CourseEnrollment::create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'status' => 'enrolled',
            'enrollment_date' => now(),
        ]);

        $exam = Exam::create([
            'course_id' => $course->id,
            'created_by' => $student->id,
            'title' => 'Midterm Assessment',
            'description' => 'Midterm Exam',
            'start_time' => Carbon::now()->subMinutes(10),
            'end_time' => Carbon::now()->addHours(2),
            'duration_minutes' => 60,
            'total_marks' => 100,
            'passing_marks' => 50,
            'exam_type' => 'midterm',
            'is_published' => true,
        ]);

        // Create unpaid tuition record
        FeeRecord::create([
            'user_id' => $student->id,
            'fee_structure_id' => $feeStructure->id,
            'invoice_number' => 'INV-TUI-TEST-001',
            'amount' => 1500,
            'total_amount' => 1500,
            'paid_amount' => 0,
            'balance_amount' => 1500,
            'type' => 'tuition',
            'status' => 'pending',
            'due_date' => now()->addDays(15),
        ]);

        $this->assertFalse($student->hasZeroTuitionBalance());
        $this->assertEquals(1500, $student->getTuitionBalance());

        // Attempting to start the exam should be blocked
        $response = $this->actingAs($student)->post(route('student.exams.start', $exam));
        $response->assertRedirect(route('student.exams.show', $exam));
        $response->assertSessionHas('error');

        // Attempting to access the Examination Permit should be denied
        $permitResponse = $this->actingAs($student)->get(route('student.exams.permit'));
        $permitResponse->assertRedirect(route('student.exams.index'));
        $permitResponse->assertSessionHas('error');
    }

    public function test_student_with_zero_tuition_balance_qualifies_for_exams_and_can_print_permit(): void
    {
        $student = User::factory()->student()->create();
        $studentProfile = StudentProfile::factory()->active()->create(['user_id' => $student->id]);
        $department = Department::factory()->create();
        $course = Course::factory()->create(['department_id' => $department->id]);
        $feeStructure = FeeStructure::factory()->create(['type' => 'tuition']);

        CourseEnrollment::create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'status' => 'enrolled',
            'enrollment_date' => now(),
        ]);

        $exam = Exam::create([
            'course_id' => $course->id,
            'created_by' => $student->id,
            'title' => 'Final Examination',
            'description' => 'Final Exam',
            'start_time' => Carbon::now()->subMinutes(10),
            'end_time' => Carbon::now()->addHours(2),
            'duration_minutes' => 120,
            'total_marks' => 100,
            'passing_marks' => 50,
            'exam_type' => 'final',
            'is_published' => true,
        ]);

        // Create fully paid tuition record (zero balance)
        FeeRecord::create([
            'user_id' => $student->id,
            'fee_structure_id' => $feeStructure->id,
            'invoice_number' => 'INV-TUI-PAID-001',
            'amount' => 1500,
            'total_amount' => 1500,
            'paid_amount' => 1500,
            'balance_amount' => 0,
            'type' => 'tuition',
            'status' => 'paid',
            'due_date' => now()->addDays(15),
        ]);

        $this->assertTrue($student->hasZeroTuitionBalance());
        $this->assertEquals(0, $student->getTuitionBalance());

        // Student can start the exam
        $response = $this->actingAs($student)->post(route('student.exams.start', $exam));
        $response->assertRedirect(route('student.exams.take', $exam));

        // Student can access and print the Examination Clearance Permit
        $permitResponse = $this->actingAs($student)->get(route('student.exams.permit'));
        $permitResponse->assertOk();
        $permitResponse->assertViewIs('student.exams.permit');
        $permitResponse->assertSee('OFFICIAL EXAMINATION CLEARANCE PERMIT');
        $permitResponse->assertSee('TUITION CLEARED (0.00 BALANCE)');
        $permitResponse->assertSee($course->code);
    }

    public function test_student_lms_index_loads_successfully_with_upcoming_activities(): void
    {
        $student = User::factory()->student()->create();
        StudentProfile::factory()->active()->create(['user_id' => $student->id]);
        $department = Department::factory()->create();
        $course = Course::factory()->create(['department_id' => $department->id]);

        CourseEnrollment::create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'status' => 'enrolled',
            'enrollment_date' => now(),
        ]);

        Assignment::factory()->create([
            'course_id' => $course->id,
            'title' => 'Upcoming Essay Assignment',
            'due_date' => Carbon::now()->addDays(2),
            'is_published' => true,
        ]);

        Quiz::create([
            'course_id' => $course->id,
            'created_by' => $student->id,
            'title' => 'Upcoming Quick Quiz',
            'start_time' => Carbon::now()->addDays(3),
            'end_time' => Carbon::now()->addDays(3)->addHour(),
            'is_published' => true,
        ]);

        Exam::create([
            'course_id' => $course->id,
            'created_by' => $student->id,
            'title' => 'Upcoming Semester Exam',
            'start_time' => Carbon::now()->addDays(5),
            'end_time' => Carbon::now()->addDays(5)->addHours(2),
            'duration_minutes' => 120,
            'total_marks' => 100,
            'passing_marks' => 50,
            'exam_type' => 'final',
            'is_published' => true,
        ]);

        $response = $this->actingAs($student)->get(route('student.lms.index'));

        $response->assertOk();
        $response->assertViewIs('student.lms.index');
        $response->assertSee('Upcoming Essay Assignment');
        $response->assertSee('Upcoming Quick Quiz');
        $response->assertSee('Upcoming Semester Exam');
    }
}

