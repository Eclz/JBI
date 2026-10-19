<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\Department;
use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrarDashboardDisplayTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_defaults_to_admitted_students_and_shows_applicants_only_on_the_applicants_tab(): void
    {
        $admin = User::factory()->admin()->create(['role_id' => null]);
        $department = Department::factory()->create();

        $admittedStudent = User::factory()->student()->create([
            'name' => 'Admitted Student',
            'email' => 'admitted@example.test',
        ]);
        Application::create($this->applicationData('APP-ADMITTED', 'admitted@example.test', 'admitted'));
        StudentProfile::create([
            'user_id' => $admittedStudent->id,
            'admission_number' => 'ADM-0001',
            'admission_date' => now()->toDateString(),
            'department_id' => $department->id,
            'program' => 'Computer Science',
            'status' => 'inactive',
            'application_status' => 'approved',
        ]);

        Application::create($this->applicationData('APP-PENDING', 'applicant@example.test', 'pending'));

        $this->actingAs($admin)
            ->get(route('admin.registrar.dashboard'))
            ->assertOk()
            ->assertSee('Admitted Student')
            ->assertDontSee('Pending Applicant');

        $this->actingAs($admin)
            ->get(route('admin.registrar.dashboard', ['student_view' => 'active']))
            ->assertOk()
            ->assertDontSee('Admitted Student');

        $this->actingAs($admin)
            ->get(route('admin.registrar.dashboard', ['student_view' => 'applicants']))
            ->assertOk()
            ->assertSee('Pending Applicant')
            ->assertDontSee('Admitted Student');
    }

    public function test_applications_page_displays_status_tabs_below_the_filters(): void
    {
        $admin = User::factory()->admin()->create(['role_id' => null]);

        $this->actingAs($admin)
            ->get(route('admin.applications.index'))
            ->assertOk()
            ->assertSeeInOrder([
                'Search Here',
                'Filter',
                'All',
                'Pending',
                'Under Review',
                'Approved',
                'Admitted',
                'Rejected',
            ])
            ->assertDontSee('Payment Pending');
    }

    private function applicationData(string $number, string $email, string $status): array
    {
        $name = $status === 'admitted' ? 'Admitted Student' : 'Pending Applicant';

        return [
            'application_number' => $number,
            'type' => 'student',
            'first_name' => $name,
            'last_name' => 'Example',
            'email' => $email,
            'phone' => '+15551234567',
            'date_of_birth' => '2002-05-10',
            'gender' => 'female',
            'address' => '100 University Rd',
            'program' => 'Computer Science',
            'status' => $status,
        ];
    }
}
