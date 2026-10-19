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

    public function test_dashboard_tabs_show_admitted_students_by_active_status_and_never_show_applicants(): void
    {
        $admin = User::factory()->admin()->create(['role_id' => null]);
        $department = Department::factory()->create();

        $this->createAdmittedStudent($department, 'Active Student', 'active@example.test', 'ADM-0001', 'active');
        $this->createAdmittedStudent($department, 'Inactive Student', 'inactive@example.test', 'ADM-0002', 'inactive');

        Application::create($this->applicationData('APP-PENDING', 'applicant@example.test', 'pending'));

        $this->actingAs($admin)
            ->get(route('admin.registrar.dashboard'))
            ->assertOk()
            ->assertSee('Active Student')
            ->assertSee('Inactive Student')
            ->assertDontSee('Pending Applicant');

        $this->actingAs($admin)
            ->get(route('admin.registrar.dashboard', ['student_view' => 'active']))
            ->assertOk()
            ->assertSee('Active Student')
            ->assertDontSee('Inactive Student')
            ->assertDontSee('Pending Applicant');

        $this->actingAs($admin)
            ->get(route('admin.registrar.dashboard', ['student_view' => 'inactive']))
            ->assertOk()
            ->assertSee('Inactive Student')
            ->assertDontSee('Active Student')
            ->assertDontSee('Pending Applicant');
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

    private function createAdmittedStudent(Department $department, string $name, string $email, string $admissionNumber, string $status): void
    {
        $student = User::factory()->student()->create([
            'name' => $name,
            'email' => $email,
        ]);
        Application::create($this->applicationData('APP-' . $admissionNumber, $email, 'admitted'));
        StudentProfile::create([
            'user_id' => $student->id,
            'admission_number' => $admissionNumber,
            'admission_date' => now()->toDateString(),
            'department_id' => $department->id,
            'program' => 'Computer Science',
            'status' => $status,
            'application_status' => 'approved',
        ]);
    }
}
