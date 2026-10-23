<?php

namespace Tests\Feature;

use App\Models\HrOnboarding;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeeRoleModuleVisibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_faculty_navigation_only_shows_modules_granted_to_the_assigned_role(): void
    {
        $role = Role::create([
            'name' => 'Course Lecturer',
            'slug' => 'course_lecturer',
            'guard_role' => 'faculty',
            'permissions' => [
                'courses' => ['view' => true],
            ],
            'is_active' => true,
        ]);
        $user = User::factory()->create([
            'role' => 'faculty',
            'role_id' => $role->id,
        ]);

        $this->actingAs($user);
        $html = view('partials.sidebar-nav')->render();

        $this->assertStringContainsString('My Courses', $html);
        $this->assertStringContainsString('Assignments', $html);
        $this->assertStringContainsString('Course Materials', $html);
        $this->assertStringNotContainsString('LMS Analytics', $html);
        $this->assertStringNotContainsString('Exams', $html);
        $this->assertStringNotContainsString('Attendance', $html);
        $this->assertStringNotContainsString('Grading', $html);
        $this->assertStringNotContainsString('HOD Administration', $html);
    }

    public function test_hod_role_alias_shows_hod_navigation_and_redirects_after_onboarding(): void
    {
        $role = Role::create([
            'name' => 'HOD',
            'slug' => 'hod',
            'guard_role' => 'faculty',
            'permissions' => [],
            'is_active' => true,
        ]);
        $user = User::factory()->create([
            'role' => 'faculty',
            'role_id' => $role->id,
        ]);
        HrOnboarding::create([
            'user_id' => $user->id,
            'status' => 'Completed',
            'progress_percentage' => 100,
            'completed_date' => today(),
        ]);

        $this->actingAs($user);
        $html = view('partials.sidebar-nav')->render();

        $this->assertStringContainsString('HOD Administration', $html);

        $this->get(route('onboarding.staff-status'))
            ->assertRedirect(route('faculty.hod.department.index'));
    }

    public function test_admin_guard_employee_sees_only_their_permission_granted_modules(): void
    {
        $role = Role::create([
            'name' => 'Finance Officer',
            'slug' => 'test_finance_officer',
            'guard_role' => 'admin',
            'permissions' => [
                'finance_hub' => ['view' => true],
                'payables' => ['view' => true],
            ],
            'is_active' => true,
        ]);
        $user = User::factory()->create([
            'role' => 'admin',
            'role_id' => $role->id,
        ]);

        $this->actingAs($user);
        $html = view('partials.sidebar-nav')->render();

        $this->assertStringContainsString('Finance & Governance', $html);
        $this->assertStringContainsString('Accounts Payable', $html);
        $this->assertStringNotContainsString('User Management', $html);
    }

    public function test_finance_officer_can_access_finance_hub_after_onboarding_is_completed(): void
    {
        $role = Role::create([
            'name' => 'Onboarded Finance Officer',
            'slug' => 'onboarded_finance_officer',
            'guard_role' => 'admin',
            'permissions' => [
                'finance_hub' => ['view' => true],
            ],
            'is_active' => true,
        ]);
        $user = User::factory()->create([
            'role' => 'admin',
            'role_id' => $role->id,
        ]);
        $onboarding = HrOnboarding::create([
            'user_id' => $user->id,
            'status' => 'In Progress',
            'progress_percentage' => 50,
            'due_date' => today()->addDays(7),
        ]);

        $this->actingAs($user)
            ->get(route('admin.finance.dashboard'))
            ->assertRedirect(route('onboarding.staff-status'));

        $onboarding->update([
            'status' => 'Completed',
            'progress_percentage' => 100,
            'completed_date' => today(),
        ]);

        $this->get(route('admin.finance.dashboard'))->assertOk();
    }
}
