<?php

namespace Tests\Feature;

use App\Mail\EmailVerification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class StudentRegistrationExperienceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
    }


    public function test_unverified_student_cannot_bypass_activation_with_direct_actions(): void
    {
        $student = User::factory()->student()->unverified()->create();
        $this->actingAs($student)->get(route('student.courses.index'))->assertRedirect(route('student.dashboard'));
        $this->get(route('profile.show'))->assertRedirect(route('student.dashboard'));
        $this->post('/student/courses/999/enroll')->assertRedirect(route('student.dashboard'));
        $this->postJson('/student/courses/999/enroll')->assertForbidden()->assertJsonPath('redirect', route('student.dashboard'));
        $this->assertDatabaseCount('course_enrollments', 0);
        $this->get('/student/')->assertRedirect('/student/dashboard');
    }

    public function test_invited_student_can_activate_before_required_password_change(): void
    {
        $student = User::factory()->student()->unverified()->create(['must_change_password' => true]);
        $this->actingAs($student)->get(route('student.dashboard'))->assertOk()->assertViewIs('student.activate');
        $this->get(route('password.change.form'))->assertRedirect(route('student.dashboard'));
        $this->get((new EmailVerification($student))->verificationUrl)->assertRedirect(route('student.dashboard'));
        $this->get(route('student.dashboard'))->assertRedirect(route('password.change.form'));
    }

    public function test_activation_status_updates_after_verification_in_another_tab(): void
    {
        $student = User::factory()->student()->unverified()->create();
        $this->actingAs($student)->getJson(route('verification.notice'))->assertOk()->assertJsonPath('verified', false);
        $student->markEmailAsVerified();
        $this->getJson(route('verification.notice'))->assertOk()->assertJsonPath('verified', true)
            ->assertJsonPath('redirect', route('student.dashboard'));
    }

    public function test_verification_email_renders_branding_and_the_signed_activation_link(): void
    {
        $student = User::factory()->student()->unverified()->create(['first_name' => 'Jane']);
        $mail = new EmailVerification($student);
        $html = $mail->render();
        $this->assertStringContainsString('images/jbi-email-logo.png', $html);
        $this->assertStringContainsString('Activate my account', $html);
        $this->assertStringContainsString(e($mail->verificationUrl), $html);
        $this->assertStringContainsString('24 hours', $html);
        $this->assertStringContainsString('Hello Jane', $html);
    }

    public function test_applicant_menu_contains_only_dashboard_and_unlocks_after_admission(): void
    {
        $student = User::factory()->student()->create();
        $this->actingAs($student);
        $html = view('partials.sidebar-nav')->render();
        $this->assertStringContainsString('Applicant navigation', $html);
        $this->assertStringContainsString(route('student.dashboard'), $html);
        foreach (['student.courses.index', 'student.hostel.index', 'library.index', 'forums.index', 'messages.index', 'profile.show'] as $route) {
            $this->assertStringNotContainsString('href="'.route($route).'"', $html);
        }
        \App\Models\StudentProfile::factory()->create(['user_id' => $student->id, 'status' => 'active']);
        $student->unsetRelation('studentProfile');
        $html = view('partials.sidebar-nav')->render();
        $this->assertStringNotContainsString('Applicant navigation', $html);
        $this->assertStringContainsString('data-group-id="library-module"', $html);
        foreach (['student.courses.index', 'student.hostel.index', 'forums.index'] as $route) {
            $this->assertStringContainsString('href="'.route($route).'"', $html);
        }
    }

    public function test_verified_applicant_cannot_access_modules_before_admission(): void
    {
        $student = User::factory()->student()->create();
        $this->actingAs($student)->get(route('student.dashboard'))->assertOk();
        foreach (['student.courses.index', 'student.hostel.index', 'library.index', 'forums.index', 'messages.index'] as $route) {
            $this->get(route($route))->assertRedirect(route('student.dashboard'));
        }
        $this->postJson('/student/courses/999/enroll')->assertForbidden();
        $this->post(route('applications.store'), [])->assertSessionHasErrors();
    }

    public function test_applicant_cannot_activate_an_inactive_profile_by_acknowledging_admission(): void
    {
        $student = User::factory()->student()->create();
        $profile = \App\Models\StudentProfile::factory()->create(['user_id' => $student->id, 'status' => 'inactive']);
        $this->actingAs($student)->post(route('student.dashboard.acknowledge'))->assertRedirect(route('student.dashboard'));
        $this->assertSame('inactive', $profile->fresh()->status);
        $this->assertNull($profile->fresh()->admission_acknowledged_at);
        $this->get(route('student.courses.index'))->assertRedirect(route('student.dashboard'));
    }

    private function details(array $changes = []): array
    {
        return array_merge([
            'first_name' => ' Jane ', 'last_name' => ' Applicant ',
            'email' => ' Jane.Applicant@Example.Test ', 'phone' => '+27 68 443 8415',
            'password' => 'A secure student phrase', 'password_confirmation' => 'A secure student phrase', 'terms' => '1',
        ], $changes);
    }

    public function test_registration_uses_dedicated_mobile_layout_and_useful_input_attributes(): void
    {
        $this->get(route('register'))->assertOk()->assertSee('css/admissions.css', false)
            ->assertDontSee('sidebar-wrapper', false)->assertSee('autocomplete="new-password"', false)
            ->assertSee('inputmode="email"', false)->assertSee('data-password-toggle="password"', false)
            ->assertSee('Verify email')->assertDontSee('href="#"', false);
    }

    public function test_signup_normalizes_details_logs_in_and_sends_one_verification_email(): void
    {
        $this->post(route('register'), $this->details())->assertSessionHasNoErrors()->assertRedirect(route('student.dashboard'));
        $user = User::where('email', 'jane.applicant@example.test')->firstOrFail();
        $this->assertSame('Jane', $user->first_name);
        $this->assertSame('Applicant', $user->last_name);
        $this->assertSame('student', $user->role);
        $this->assertNull($user->email_verified_at);
        $this->assertTrue(Hash::check('A secure student phrase', $user->password));
        $this->assertAuthenticatedAs($user);
        $this->assertNull($user->studentProfile);
        Mail::assertSent(EmailVerification::class, 1);
        $this->get(route('verification.notice'))->assertRedirect(route('student.dashboard'));
        $this->get(route('student.dashboard'))->assertOk()->assertViewIs('student.activate')->assertSee('jane.applicant@example.test')->assertSee('Update email and resend');
    }

    public function test_invalid_signup_preserves_contact_details_without_flashing_passwords(): void
    {
        $this->from(route('register'))->post(route('register'), $this->details(['password_confirmation' => 'Different password', 'terms' => '0']))
            ->assertSessionHasErrors(['password', 'terms'])->assertRedirect(route('register'))
            ->assertSessionHasInput('first_name', 'Jane');
        $this->assertNull(session()->getOldInput('password'));
        $this->assertNull(session()->getOldInput('password_confirmation'));
        $this->assertDatabaseCount('users', 0);
        Mail::assertNothingSent();
    }

    public function test_duplicate_email_offers_recovery_and_does_not_create_another_account(): void
    {
        User::factory()->student()->create(['email' => 'jane.applicant@example.test']);
        $this->from(route('register'))->post(route('register'), $this->details())->assertSessionHasErrors('email');
        $this->get(route('register'))->assertOk()->assertSee('Sign in or reset your password.');
        $this->assertDatabaseCount('users', 1);
        Mail::assertNothingSent();
    }

    public function test_verification_failure_keeps_the_account_and_offers_resend(): void
    {
        Mail::shouldReceive('to')->once()->andThrow(new \RuntimeException('Mail transport unavailable'));
        $this->post(route('register'), $this->details())->assertRedirect(route('student.dashboard'))->assertSessionHas('warning');
        $this->assertDatabaseCount('users', 1);
        $this->assertAuthenticated();
        $this->get(route('student.dashboard'))->assertOk()->assertSee('Resend verification email');
    }

    public function test_verified_link_in_the_same_session_continues_directly_to_the_application(): void
    {
        $student = User::factory()->student()->unverified()->create();
        $link = (new EmailVerification($student))->verificationUrl;
        $this->actingAs($student)->get($link)->assertRedirect(route('student.dashboard'));
        $this->assertNotNull($student->fresh()->email_verified_at);
        $this->get(route('verification.notice'))->assertRedirect(route('student.dashboard'));
        $this->get(route('student.dashboard'))->assertOk()->assertViewIs('student.dashboard_unadmitted');
    }

    public function test_cross_device_verification_does_not_log_someone_in_automatically(): void
    {
        $student = User::factory()->student()->unverified()->create();
        $this->get((new EmailVerification($student))->verificationUrl)->assertOk()->assertSee('Sign in to continue');
        $this->assertGuest();
        $this->assertNotNull($student->fresh()->email_verified_at);
    }

    public function test_verifying_another_accounts_link_preserves_the_current_login(): void
    {
        $signedIn = User::factory()->student()->create();
        $target = User::factory()->student()->unverified()->create();
        $this->actingAs($signedIn)->get((new EmailVerification($target))->verificationUrl)
            ->assertOk()->assertSee('signed in to a different account');
        $this->assertAuthenticatedAs($signedIn);
        $this->assertNotNull($target->fresh()->email_verified_at);
    }

    public function test_expired_and_tampered_links_have_recovery_instructions_and_cannot_verify(): void
    {
        $student = User::factory()->student()->unverified()->create();
        $expired = URL::temporarySignedRoute('verification.verify', now()->subMinute(), ['id' => $student->id, 'hash' => sha1($student->email)]);
        $this->get($expired)->assertForbidden()->assertSee('Sign in to request a new link');
        $tampered = (new EmailVerification($student))->verificationUrl . 'x';
        $this->get($tampered)->assertForbidden()->assertSee('This link is no longer valid');
        $this->assertNull($student->fresh()->email_verified_at);
    }

    public function test_wrong_email_can_be_corrected_without_creating_a_second_account(): void
    {
        $student = User::factory()->student()->unverified()->create();
        $oldLink = (new EmailVerification($student))->verificationUrl;
        $this->actingAs($student)->post(route('verification.email.update'), ['email' => ' Correct.Address@example.test '])
            ->assertSessionHasNoErrors()->assertRedirect(route('verification.notice'))->assertSessionHas('success');
        $this->assertDatabaseCount('users', 1);
        $this->assertSame('correct.address@example.test', $student->fresh()->email);
        $this->get($oldLink)->assertForbidden();
        $this->assertNull($student->fresh()->email_verified_at);
        Mail::assertSent(EmailVerification::class, fn ($mail) => $mail->hasTo('correct.address@example.test'));
        $this->get((new EmailVerification($student->fresh()))->verificationUrl)->assertRedirect(route('student.dashboard'));
    }

    public function test_email_correction_cannot_take_another_users_address_or_change_a_verified_account(): void
    {
        $existing = User::factory()->student()->create();
        $student = User::factory()->student()->unverified()->create();
        $this->actingAs($student)->post(route('verification.email.update'), ['email' => $existing->email])->assertSessionHasErrors('email');
        $this->assertNotSame($existing->email, $student->fresh()->email);
        $this->actingAs($existing)->post(route('verification.email.update'), ['email' => 'different@example.test'])->assertForbidden();
    }

    public function test_resend_only_sends_to_the_signed_in_users_address(): void
    {
        $student = User::factory()->student()->unverified()->create();
        $this->actingAs($student)->post(route('verification.resend'), ['email' => 'unrelated@example.test'])
            ->assertRedirect(route('verification.notice'))->assertSessionHas('success');
        Mail::assertSent(EmailVerification::class, fn ($mail) => $mail->hasTo($student->email));
    }

    public function test_password_recovery_uses_the_mobile_layout_and_sends_the_reset_link(): void
    {
        User::factory()->student()->create(['email' => 'existing@example.test']);
        $this->get(route('password.request'))->assertOk()->assertSee('css/admissions.css', false)->assertDontSee('sidebar-wrapper', false);
        $this->from(route('password.request'))->post(route('password.email'), ['email' => ' Existing@Example.Test '])
            ->assertSessionHasNoErrors()->assertSessionHas('status');
        Mail::assertSent(\App\Mail\ResetPasswordMail::class, fn ($mail) => $mail->hasTo('existing@example.test'));
    }

    public function test_reset_email_failure_does_not_claim_success_or_block_a_retry(): void
    {
        User::factory()->student()->create(['email' => 'existing@example.test']);
        Mail::shouldReceive('to')->once()->andThrow(new \RuntimeException('Transport unavailable'));
        $this->from(route('password.request'))->post(route('password.email'), ['email' => 'existing@example.test'])
            ->assertSessionHas('warning')->assertSessionMissing('status');
        $this->assertDatabaseMissing('password_reset_tokens', ['email' => 'existing@example.test']);
    }

    public function test_password_setup_shows_success_and_leaves_the_user_ready_to_sign_in(): void
    {
        $user = User::factory()->student()->unverified()->create(['must_change_password' => true]);
        $token = \Illuminate\Support\Facades\Password::broker()->createToken($user);
        $this->get(route('password.reset', ['token' => $token, 'email' => $user->email]))
            ->assertOk()->assertSee('css/admissions.css', false)->assertDontSee('sidebar-wrapper', false);
        $this->post(route('password.update'), [
            'email' => $user->email, 'token' => $token,
            'password' => 'A secure reset phrase', 'password_confirmation' => 'A secure reset phrase',
        ])->assertRedirect(route('login'))->assertSessionHas('status');
        $this->assertTrue(Hash::check('A secure reset phrase', $user->fresh()->password));
        $this->assertFalse($user->fresh()->must_change_password);
        $this->get(route('login'))->assertOk()->assertSee('Sign in below to continue.');
    }
}
