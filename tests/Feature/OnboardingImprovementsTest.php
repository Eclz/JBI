<?php

namespace Tests\Feature;

use App\Jobs\SendNotificationEmail;
use App\Mail\AccountCreatedSetupPassword;
use App\Mail\NotificationMail;
use App\Models\Application;
use App\Models\Department;
use App\Models\Notification;
use App\Models\Payment;
use App\Models\Program;
use App\Models\Role;
use App\Models\Semester;
use App\Models\SystemSetting;
use App\Models\User;
use App\Services\AccountInvitationService;
use App\Services\AdmissionPaymentService;
use App\Services\OnboardingNotificationService;
use App\Services\StaffOnboardingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class OnboardingImprovementsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
        Queue::fake();
        Storage::fake('local');
        Storage::fake('public');
    }

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin', 'role_id' => null]);
    }

    private function application(User $student, array $overrides = []): Application
    {
        return Application::create(array_merge([
            'application_number' => 'STU-TEST-'.$student->id,
            'type' => 'student', 'first_name' => 'Test', 'last_name' => 'Student',
            'email' => $student->email, 'phone' => '0700000000',
            'date_of_birth' => '2001-01-01', 'gender' => 'female', 'address' => 'Kampala',
            'status' => 'pending', 'payment_status' => 'pending',
        ], $overrides));
    }

    private function submission(Program $program): array
    {
        return [
            'type' => 'student', 'first_name' => 'Test', 'last_name' => 'Student',
            'phone' => '0700000000', 'date_of_birth' => '2001-01-01', 'gender' => 'female',
            'address' => 'Kampala', 'program_id_1' => $program->id,
            'emergency_contact_name' => 'Guardian', 'emergency_contact_phone' => '0700000001',
            'documents' => [UploadedFile::fake()->create('transcript.pdf', 10, 'application/pdf')],
        ];
    }

    public function test_invalid_resubmission_preserves_rejected_application(): void
    {
        $student = User::factory()->student()->create();
        $application = $this->application($student, ['status' => 'rejected', 'review_notes' => 'Incomplete credentials']);
        $this->actingAs($student)->post(route('applications.store'), [])->assertSessionHasErrors();
        $this->assertSame('rejected', $application->fresh()->status);
        $this->assertSame(0, DB::table('application_revisions')->count());
    }

    public function test_valid_resubmission_keeps_history_invalidates_review_and_preserves_verified_payment(): void
    {
        $student = User::factory()->student()->create();
        $program = Program::create(['name' => 'Science', 'code' => 'SCI', 'department_id' => Department::factory()->active()->create()->id, 'is_active' => true]);
        $application = $this->application($student, [
            'status' => 'rejected', 'review_notes' => 'Missing certificate', 'program_id' => $program->id,
            'documents_verified_at' => now(), 'payment_status' => 'verified', 'payment_verified_at' => now(),
        ]);
        $this->actingAs($student)->post(route('applications.store'), $this->submission($program))
            ->assertSessionHasNoErrors()->assertRedirect(route('dashboard'));
        $application->refresh();
        $this->assertSame('pending', $application->status);
        $this->assertNull($application->documents_verified_at);
        $this->assertNull($application->program_id);
        $this->assertSame('verified', $application->payment_status);
        $snapshot = json_decode(DB::table('application_revisions')->first()->snapshot, true);
        $this->assertSame('rejected', $snapshot['status']);
        $this->assertSame('Missing certificate', $snapshot['review_notes']);
        Storage::disk('local')->assertExists($application->documents[0]);
        Storage::disk('public')->assertMissing($application->documents[0]);
    }

    public function test_applicants_cannot_view_or_replace_another_students_receipt(): void
    {
        $owner = User::factory()->student()->create();
        $other = User::factory()->student()->create();
        $application = $this->application($owner, ['payment_proof' => 'payment-proofs/test.pdf']);
        Storage::disk('local')->put($application->payment_proof, 'private receipt');
        $this->actingAs($other)->get(route('applications.files.show', [$application, 'receipt']))->assertForbidden();
        $this->actingAs($other)->post(route('applications.store-payment', $application->application_number), [
            'payment_proof' => UploadedFile::fake()->create('receipt.pdf', 10, 'application/pdf'),
        ])->assertForbidden();
        $this->actingAs($owner)->get(route('applications.files.show', [$application, 'receipt']))->assertOk();
        $this->actingAs($this->admin())->get(route('applications.files.show', [$application, 'receipt']))->assertOk();
    }

    public function test_applicant_can_choose_usd_registration_fee_and_finance_keeps_the_currency(): void
    {
        $student = User::factory()->student()->create();
        $application = $this->application($student, [
            'application_number' => 'REG-USD-'.$student->id,
            'status' => 'approved',
        ]);

        $this->withoutMiddleware(\App\Http\Middleware\RoleMiddleware::class);
        $this->actingAs($student)
            ->get(route('student.dashboard'))
            ->assertOk()
            ->assertSee('Registration Fee')
            ->assertSee('ZAR 500.00 / USD 35.00')
            ->assertSee('USD 35.00');

        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);
        $this->actingAs($student)
            ->post(route('applications.store-payment', $application->application_number), [
                'payment_currency' => 'USD',
                'payment_proof' => UploadedFile::fake()->create('receipt.pdf', 10, 'application/pdf'),
            ])
            ->assertRedirect(route('dashboard'));

        $application->refresh();
        $this->assertSame('USD', $application->payment_currency);
        $this->assertSame('uploaded', $application->payment_status);

        $admin = $this->admin();
        $application->update([
            'payment_status' => 'verified',
            'payment_verified_at' => now(),
            'payment_ref' => 'PAY-USD-'.$student->id,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.applications.show', $application))
            ->assertOk()
            ->assertSee('USD 35.00');

        $payment = AdmissionPaymentService::recordAdmissionPayment($application, $admin->id);

        $this->assertInstanceOf(Payment::class, $payment);
        $this->assertSame('USD', $payment->currency);
        $this->assertSame('35.00', $payment->amount);
        $this->assertDatabaseHas('fee_records', [
            'invoice_number' => 'PAY-USD-'.$student->id,
            'currency' => 'USD',
            'amount' => 35,
        ]);
        $this->assertDatabaseHas('finance_revenues', [
            'reference_number' => 'PAY-USD-'.$student->id,
            'currency' => 'ZAR',
            'amount' => 500,
        ]);
    }

    public function test_force_approve_cannot_fabricate_document_verification(): void
    {
        $student = User::factory()->student()->create();
        $program = Program::create(['name' => 'Science', 'code' => 'SCI', 'department_id' => Department::factory()->active()->create()->id]);
        $application = $this->application($student, ['payment_status' => 'verified', 'program_id' => $program->id]);
        $this->actingAs($this->admin())->get(route('admin.applications.show', $application))->assertOk()->assertSee('Process in order');
        $this->actingAs($this->admin())->post(route('admin.applications.approve', $application), ['force_approve' => true])
            ->assertSessionHas('error');
        $this->assertNull($application->fresh()->documents_verified_at);
        $this->assertSame('pending', $application->fresh()->status);
    }

    public function test_no_documents_cannot_be_marked_verified(): void
    {
        $application = $this->application(User::factory()->student()->create(), ['payment_status' => 'verified']);
        $this->actingAs($this->admin())->post(route('admin.applications.verify-documents', $application))->assertSessionHas('error');
        $this->assertNull($application->fresh()->documents_verified_at);
    }

    public function test_staff_preparation_is_idempotent_and_uses_role_specific_tasks(): void
    {
        $admin = $this->admin();
        $staff = User::factory()->create(['role' => 'faculty']);
        $plan = StaffOnboardingService::ensure($staff, $admin->id);
        $again = StaffOnboardingService::ensure($staff, $admin->id);
        $this->assertSame($plan->id, $again->id);
        $this->assertSame(1, $staff->hrProfile()->count());
        $this->assertTrue($plan->tasks()->where('task_name', 'Teaching courses and timetable assigned')->exists());
        Mail::assertNothingSent();
    }

    public function test_tasks_require_evidence_and_completion_updates_progress_to_completed(): void
    {
        $admin = $this->admin();
        $staff = User::factory()->create(['role' => 'faculty']);
        $plan = StaffOnboardingService::ensure($staff, $admin->id);
        $task = $plan->tasks()->where('task_name', 'Employment documents verified')->firstOrFail();
        $this->actingAs($admin)->post(route('human-resources.onboarding.tasks.update', $task), ['status' => 'Completed'])
            ->assertSessionHasErrors('comments');
        $plan->tasks()->where('id', '!=', $task->id)->update(['status' => 'Completed']);
        $this->actingAs($admin)->post(route('human-resources.onboarding.tasks.update', $task), [
            'status' => 'Completed', 'comments' => 'Original documents checked', 'assigned_to' => $admin->id,
            'attachment' => UploadedFile::fake()->create('evidence.pdf', 10, 'application/pdf'),
        ])->assertSessionHasNoErrors()->assertSessionHas('success');
        $this->assertSame('Completed', $plan->fresh()->status);
        $this->assertEquals(100, $plan->fresh()->progress_percentage);
        Storage::disk('local')->assertExists($task->fresh()->attachment_path);
        $this->assertDatabaseHas('audit_logs', ['action' => 'onboarding_task_updated', 'model_id' => $task->id]);
    }

    public function test_password_task_checks_real_setup_and_overdue_is_derived_from_due_date(): void
    {
        $admin = $this->admin();
        $staff = User::factory()->create(['role' => 'faculty', 'must_change_password' => true]);
        $plan = StaffOnboardingService::ensure($staff, $admin->id, ['due_date' => today()->subDay()]);
        $this->assertSame('Overdue', $plan->status);
        $task = $plan->tasks()->where('task_name', 'Account password set')->firstOrFail();
        $this->actingAs($admin)->post(route('human-resources.onboarding.tasks.update', $task), [
            'status' => 'Completed', 'comments' => 'Claimed done',
        ])->assertSessionHasErrors('status');
        $this->assertSame('Pending', $task->fresh()->status);
    }

    public function test_password_change_is_enforced_before_accessing_protected_pages(): void
    {
        $user = User::factory()->create(['must_change_password' => true]);
        $this->actingAs($user)->get(route('dashboard'))->assertRedirect(route('password.change.form'));
        $this->actingAs($user)->get(route('password.change.form'))->assertOk();
    }

    public function test_admin_direct_creation_provisions_hr_checklist_and_secure_invitation(): void
    {
        $role = Role::where('slug', 'dean')->firstOrFail();
        $department = Department::factory()->active()->create();
        $this->actingAs($this->admin())->post(route('admin.users.store'), [
            'name' => 'New Faculty', 'email' => 'new.faculty@example.test', 'role_id' => $role->id,
            'department_id' => $department->id, 'status' => 'active',
        ])->assertSessionHasNoErrors()->assertSessionHas('success');
        $user = User::where('email', 'new.faculty@example.test')->firstOrFail();
        $this->assertTrue($user->must_change_password);
        $this->assertFalse(Hash::check('ABCxyz,.?123', $user->password));
        $this->assertNotNull($user->setup_email_sent_at);
        $this->assertNotNull($user->hrProfile);
        $this->assertDatabaseHas('hr_onboardings', ['user_id' => $user->id]);
        Mail::assertSent(AccountCreatedSetupPassword::class);
    }

    public function test_invitation_failure_is_recorded_without_claiming_delivery(): void
    {
        $user = User::factory()->create();
        Mail::shouldReceive('to')->once()->andThrow(new \RuntimeException('SMTP unavailable'));
        $this->assertFalse(AccountInvitationService::send($user));
        $this->assertNotNull($user->fresh()->setup_email_error);
        $this->assertNull($user->fresh()->setup_email_sent_at);
    }

    public function test_admin_alerts_target_active_permission_holders_and_record_actual_send_status(): void
    {
        $admin = $this->admin();
        $inactive = User::factory()->create(['role' => 'admin', 'role_id' => null, 'is_active' => false]);
        $student = User::factory()->student()->create();
        OnboardingNotificationService::notify('applications', 'Application received', 'Review this applicant', route('admin.applications.index'));
        $notification = Notification::where('user_id', $admin->id)->where('title', 'Application received')->firstOrFail();
        $this->assertFalse($notification->email_sent);
        $this->assertSame('queued', $notification->data['email_status']);
        $this->assertFalse(Notification::whereIn('user_id', [$inactive->id, $student->id])->where('title', 'Application received')->exists());
        $job = new SendNotificationEmail($notification->id);
        $job->handle();
        $job->handle();
        Mail::assertSent(NotificationMail::class, 1);
        $this->assertTrue($notification->fresh()->email_sent);
    }

    public function test_email_retry_is_scoped_to_the_recipient_and_tracks_failure(): void
    {
        $admin = $this->admin();
        $notification = Notification::create(['user_id' => $admin->id, 'title' => 'Review', 'message' => 'Test', 'type' => 'info']);
        (new SendNotificationEmail($notification->id))->failed(new \RuntimeException('SMTP unavailable'));
        $this->assertSame('failed', $notification->fresh()->data['email_status']);
        $this->actingAs(User::factory()->student()->create())->post(route('notifications.retry-email', $notification))->assertNotFound();
        $this->actingAs($admin)->post(route('notifications.retry-email', $notification))->assertSessionHas('success');
        $this->assertSame('queued', $notification->fresh()->data['email_status']);
    }

    public function test_registration_preparation_extends_semester_and_includes_entire_closing_day(): void
    {
        $semester = Semester::factory()->current()->create(['end_date' => '2026-10-01']);
        $this->artisan('onboarding:prepare', ['--registration-start' => '2026-10-20', '--registration-end' => '2026-12-30'])->assertSuccessful();
        $semester->refresh();
        $this->assertSame('2026-12-30', $semester->end_date->toDateString());
        $this->travelTo(\Illuminate\Support\Carbon::parse('2026-12-30 23:59:00'));
        $this->assertTrue($semester->is_registration_open);
        $this->travelBack();
    }

    public function test_hub_and_task_pages_render_and_student_cannot_access_hub(): void
    {
        $admin = $this->admin();
        $plan = StaffOnboardingService::ensure(User::factory()->create(['role' => 'faculty']), $admin->id);
        $this->actingAs($admin)->get(route('onboarding.index'))->assertOk()->assertSee('Student admissions');
        $this->actingAs($admin)->get(route('human-resources.onboarding.show', $plan))->assertOk()->assertSee('Save task');
        $this->actingAs(User::factory()->student()->create())->get(route('onboarding.index'))->assertForbidden();
    }

    public function test_unverified_applicant_gets_a_clear_verification_step(): void
    {
        $student = User::factory()->student()->create(['email_verified_at' => null]);
        $this->actingAs($student)->post(route('applications.store'), [])->assertRedirect(route('student.dashboard'));
        $this->get(route('student.dashboard'))->assertOk()->assertViewIs('student.activate');
        $this->get(route('verification.notice'))->assertRedirect(route('student.dashboard'));
    }

    public function test_existing_staff_hire_requires_explicit_link_and_preserves_password(): void
    {
        $admin = $this->admin();
        $staff = User::factory()->create(['role' => 'faculty', 'must_change_password' => false]);
        $password = $staff->password;
        $vacancy = \App\Models\HrVacancy::create(['position_title' => 'Lecturer', 'status' => 'Open', 'employment_type' => 'Full-time']);
        $applicant = \App\Models\HrApplicant::create([
            'hr_vacancy_id' => $vacancy->id, 'first_name' => 'Existing', 'last_name' => 'Lecturer',
            'email' => $staff->email, 'status' => 'Offer',
        ]);
        $payload = [
            'role_id' => Role::where('slug', 'dean')->firstOrFail()->id,
            'department_id' => Department::factory()->active()->create()->id,
            'workspace' => 'Office 12', 'due_date' => today()->addDays(14)->toDateString(),
        ];
        $count = User::count();
        $this->actingAs($admin)->post(route('human-resources.recruiting.applicants.approve-hire', $applicant), $payload)->assertStatus(422);
        $this->post(route('human-resources.recruiting.applicants.approve-hire', $applicant), $payload + ['existing_user_id' => $staff->id])
            ->assertSessionHasNoErrors()->assertSessionHas('success');
        $this->assertSame($count, User::count());
        $this->assertSame($staff->id, $applicant->fresh()->hired_user_id);
        $this->assertSame($password, $staff->fresh()->password);
        $this->assertSame('Office 12', $staff->fresh()->hrProfile->workspace);
        $this->get(route('human-resources.recruiting.show', $vacancy))->assertOk();
        Mail::assertNotSent(AccountCreatedSetupPassword::class);
    }

    public function test_separate_registration_fee_is_not_automatically_marked_paid_on_admission(): void
    {
        SystemSetting::setSetting('admission_covers_registration', false, 'boolean');
        $student = User::factory()->student()->create();
        $profile = \App\Models\StudentProfile::factory()->create(['user_id' => $student->id, 'registration_fee_paid_at' => null]);
        \App\Services\AdmissionWorkflow::activateStudent($student);
        $this->assertNull($profile->fresh()->registration_fee_paid_at);
        $this->assertSame('active', $profile->fresh()->status);
    }

    public function test_legacy_public_files_are_protected_without_changing_paths(): void
    {
        $application = $this->application(User::factory()->student()->create(), ['documents' => ['applications/legacy.pdf']]);
        Storage::disk('public')->put('applications/legacy.pdf', 'private certificate');
        $this->artisan('onboarding:prepare', ['--protect-files' => true])->assertSuccessful();
        Storage::disk('public')->assertMissing('applications/legacy.pdf');
        $this->assertSame('private certificate', Storage::disk('local')->get('applications/legacy.pdf'));
        $this->assertSame(['applications/legacy.pdf'], $application->fresh()->documents);
    }

    public function test_payment_step_shows_official_instructions_without_simulated_channels(): void
    {
        $student = User::factory()->student()->create();
        $this->application($student);
        SystemSetting::setSetting('admission_payment_instructions', 'First National Bank (FNB) — Account 63144144184');
        $this->actingAs($student)->get(route('student.dashboard'))->assertOk()
            ->assertSee('First National Bank (FNB)')
            ->assertSee('63144144184')
            ->assertDontSee('1234-5678-9012')
            ->assertDontSee('PayPal checkout simulated');
    }
}
