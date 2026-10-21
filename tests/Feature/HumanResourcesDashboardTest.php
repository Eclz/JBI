<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\HrEmployee;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HumanResourcesDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_the_human_resources_dashboard(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get(route('human-resources.index'))
            ->assertOk()
            ->assertSee('Human Resources Dashboard')
            ->assertSee('Active Staff')
            ->assertSee('Pending Leave Requests')
            ->assertSee('Onboarding Records');
    }

    public function test_staff_edit_form_uses_system_departments(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $admin->assignRole(Role::firstOrCreate(
            ['slug' => 'super_administrator'],
            ['name' => 'Super Administrator', 'is_active' => true]
        ));

        $department = Department::create([
            'name' => 'Quality Assurance',
            'code' => 'QA',
            'is_active' => true,
        ]);
        $employee = User::factory()->create(['role' => 'lecturer']);
        $profile = HrEmployee::create([
            'user_id' => $employee->id,
            'employee_number' => 'EMP-TEST-001',
            'job_title' => 'Lecturer',
            'department' => $department->name,
            'department_id' => $department->id,
        ]);

        $this->actingAs($admin)
            ->get(route('human-resources.staff.edit', $employee))
            ->assertOk()
            ->assertSee('name="department_id"', false)
            ->assertSee('value="' . $department->id . '" selected', false)
            ->assertSee($department->name)
            ->assertDontSee('name="department"', false);

        $this->withSession(['_token' => 'test-token'])
            ->withHeader('X-CSRF-TOKEN', 'test-token')
            ->put(route('human-resources.staff.update', $employee), [
                'employee_number' => $profile->employee_number,
                'job_title' => 'Lecturer',
                'department_id' => $department->id,
                'employment_type' => 'Full-time',
                'status' => 'Active',
            ])->assertRedirect(route('human-resources.staff.index'));

        $this->assertDatabaseHas('hr_employees', [
            'user_id' => $employee->id,
            'department_id' => $department->id,
            'department' => $department->name,
        ]);

        $this->assertSame($employee->id, $profile->user_id);
    }
}
