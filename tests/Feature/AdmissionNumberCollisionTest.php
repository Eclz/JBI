<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\Department;
use App\Models\Program;
use App\Models\StudentProfile;
use App\Models\User;
use App\Services\AdmissionWorkflow;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AdmissionNumberCollisionTest extends TestCase
{
    use RefreshDatabase;

    public function test_admission_workflow_replaces_an_application_number_already_used_by_another_student(): void
    {
        $department = Department::factory()->create(['code' => 'CSC']);
        $existingStudent = User::factory()->student()->create();
        StudentProfile::factory()->create([
            'user_id' => $existingStudent->id,
            'department_id' => $department->id,
            'admission_number' => 'CSC'.now()->year.'0001',
        ]);

        $application = Application::create([
            'application_number' => 'STU'.now()->year.'9876',
            'type' => 'student',
            'first_name' => 'New',
            'last_name' => 'Student',
            'email' => 'new.student@example.test',
            'phone' => '0712345678',
            'date_of_birth' => '2005-01-01',
            'gender' => 'female',
            'address' => 'Campus Road',
            'program' => 'Computer Science',
            'admission_number' => 'CSC'.now()->year.'0001',
        ]);

        $resolvedNumber = AdmissionWorkflow::resolveAdmissionNumber(
            $department->code,
            $application->admission_number,
            null,
            $application->id
        );

        $this->assertSame('CSC'.now()->year.'0002', $resolvedNumber);
    }

    public function test_approving_application_replaces_its_stale_duplicate_admission_number(): void
    {
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);
        Mail::fake();

        $admin = User::factory()->admin()->create(['role_id' => null]);
        $department = Department::factory()->create(['code' => 'CSC']);
        $program = Program::create([
            'department_id' => $department->id,
            'name' => 'Computer Science',
            'code' => 'BCS',
            'is_active' => true,
        ]);

        $existingStudent = User::factory()->student()->create();
        StudentProfile::factory()->create([
            'user_id' => $existingStudent->id,
            'department_id' => $department->id,
            'admission_number' => 'CSC'.now()->year.'0001',
        ]);

        $application = Application::create([
            'application_number' => 'STU'.now()->year.'9875',
            'type' => 'student',
            'first_name' => 'New',
            'last_name' => 'Student',
            'email' => 'new.approval@example.test',
            'phone' => '0712345678',
            'date_of_birth' => '2005-01-01',
            'gender' => 'female',
            'address' => 'Campus Road',
            'program' => $program->name,
            'program_id' => $program->id,
            'admission_number' => 'CSC'.now()->year.'0001',
            'payment_status' => 'verified',
            'payment_verified_at' => now(),
            'status' => 'pending',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.applications.approve', $application), ['force_approve' => true])
            ->assertRedirect(route('admin.applications.index'))
            ->assertSessionHas('success');

        $application->refresh();
        $this->assertSame('CSC'.now()->year.'0002', $application->admission_number);
        $this->assertDatabaseHas('student_profiles', [
            'user_id' => User::where('email', $application->email)->value('id'),
            'admission_number' => 'CSC'.now()->year.'0002',
        ]);
    }
}
