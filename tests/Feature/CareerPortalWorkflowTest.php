<?php

namespace Tests\Feature;

use App\Mail\AccountCreatedSetupPassword;
use App\Models\Department;
use App\Models\HrApplicant;
use App\Models\HrEmployee;
use App\Models\HrOnboarding;
use App\Models\HrVacancy;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CareerPortalWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_apply_for_an_open_vacancy_and_track_status_privately(): void
    {
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);
        Storage::fake('local');
        $vacancy = HrVacancy::create([
            'position_title' => 'Lecturer',
            'status' => 'Open',
            'published_at' => now(),
            'opening_date' => today(),
            'employment_type' => 'Full-time',
        ]);

        $response = $this->post(route('careers.apply', $vacancy), [
            'first_name' => 'Taylor',
            'last_name' => 'Applicant',
            'email' => 'Taylor.Applicant@example.com',
            'phone' => '+27123456789',
            'cv' => UploadedFile::fake()->create('cv.pdf', 20, 'application/pdf'),
            'documents' => [UploadedFile::fake()->create('qualification.pdf', 20, 'application/pdf')],
            'cover_letter' => 'I would like to apply.',
            'consent' => '1',
        ]);

        $response->assertOk()
            ->assertViewIs('careers.submitted')
            ->assertSee('Application received');

        $applicant = HrApplicant::firstOrFail();
        $this->assertSame('taylor.applicant@example.com', $applicant->email);
        $this->assertSame('Applied', $applicant->status);
        $this->assertNotEmpty($applicant->tracking_token_hash);
        $this->assertNotSame('Taylor.Applicant@example.com', $applicant->tracking_token_hash);
        Storage::disk('local')->assertExists($applicant->cv_path);
        Storage::disk('local')->assertExists($applicant->documents[0]);

        $trackingUrl = $response->viewData('trackingUrl');
        $token = basename(parse_url($trackingUrl, PHP_URL_PATH));
        $this->get(route('careers.track', $token))
            ->assertOk()
            ->assertSee('Current stage:')
            ->assertSee('Applied');
    }

    public function test_approved_offer_creates_role_assigned_user_employee_and_onboarding(): void
    {
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);
        Mail::fake();

        $admin = User::factory()->create(['role' => 'admin', 'role_id' => null]);
        $department = Department::factory()->create();
        $role = Role::where('slug', 'dean')->firstOrFail();
        $vacancy = HrVacancy::create([
            'position_title' => 'Dean of Science',
            'status' => 'Open',
            'published_at' => now(),
            'opening_date' => today(),
            'employment_type' => 'Full-time',
        ]);
        $applicant = HrApplicant::create([
            'hr_vacancy_id' => $vacancy->id,
            'first_name' => 'Alex',
            'last_name' => 'Candidate',
            'email' => 'alex.candidate@example.com',
            'status' => 'Offer',
        ]);

        $this->actingAs($admin)
            ->post(route('human-resources.recruiting.applicants.approve-hire', $applicant), [
                'role_id' => $role->id,
                'department_id' => $department->id,
                'workspace' => 'Science Building, Office 12',
                'due_date' => today()->addDays(14)->toDateString(),
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors()
            ->assertSessionHas('success');

        $applicant->refresh();
        $this->assertSame('Hired', $applicant->status);
        $this->assertNotNull($applicant->hired_at);

        $user = User::findOrFail($applicant->hired_user_id);
        $this->assertSame($role->id, $user->role_id);
        $this->assertSame('faculty', $user->role);
        $this->assertTrue($user->is_active);
        $this->assertTrue($user->must_change_password);
        $this->assertTrue($user->hasRole('dean'));
        $this->assertDatabaseHas('faculty_profiles', [
            'user_id' => $user->id,
            'department_id' => $department->id,
        ]);

        $employee = HrEmployee::where('source_applicant_id', $applicant->id)->firstOrFail();
        $this->assertSame($department->id, $employee->department_id);
        $this->assertSame('Science Building, Office 12', $employee->workspace);

        $onboarding = HrOnboarding::where('user_id', $user->id)->firstOrFail();
        $this->assertDatabaseHas('hr_onboarding_tasks', [
            'hr_onboarding_id' => $onboarding->id,
            'task_name' => 'ID card issued',
            'status' => 'Pending',
        ]);
        $this->assertDatabaseHas('hr_onboarding_tasks', [
            'hr_onboarding_id' => $onboarding->id,
            'task_name' => 'Workspace assigned',
            'status' => 'Pending',
        ]);
        Mail::assertSent(AccountCreatedSetupPassword::class);

        $user->forceFill([
            'password' => Hash::make('SetupPassword123!'),
            'must_change_password' => false,
        ])->save();
        auth()->logout();

        $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'SetupPassword123!',
        ])->assertRedirect(route('faculty.dean.quality.index'));

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $user->id,
            'action' => 'login',
        ]);
    }
}
