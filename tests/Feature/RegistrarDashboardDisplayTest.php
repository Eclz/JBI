<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\Department;
use App\Models\Role;
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

    public function test_student_management_excludes_applicants_and_pending_admissions(): void
    {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $admin = User::factory()->admin()->create(['role_id' => null]);
        $department = Department::factory()->create();
        $studentRoleId = Role::where('slug', 'student')->value('id');
        $enrolledStudent = User::factory()->student()->create([
            'name' => 'Enrolled Student',
            'email' => 'enrolled@example.test',
            'role_id' => $studentRoleId,
        ]);
        Application::create($this->applicationData('APP-ADM-0003', $enrolledStudent->email, 'admitted'));
        StudentProfile::create([
            'user_id' => $enrolledStudent->id,
            'admission_number' => 'ADM-0003',
            'admission_date' => now()->toDateString(),
            'department_id' => $department->id,
            'program' => 'Computer Science',
            'status' => 'active',
            'application_status' => 'approved',
        ]);

        $applicant = User::factory()->student()->create([
            'name' => 'Unadmitted Applicant',
            'role_id' => $studentRoleId,
        ]);
        Application::create($this->applicationData('APP-UNADMITTED', $applicant->email, 'pending'));

        $pendingAdmission = User::factory()->student()->create([
            'name' => 'Pending Admission',
            'role_id' => $studentRoleId,
        ]);
        StudentProfile::factory()->create([
            'user_id' => $pendingAdmission->id,
            'department_id' => $department->id,
            'status' => 'pending',
            'application_status' => 'approved',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.students.index'))
            ->assertOk()
            ->assertSee('enrolled@example.test')
            ->assertDontSee('Unadmitted Applicant')
            ->assertDontSee('Pending Admission');
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
