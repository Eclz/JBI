<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FacultyPermissionAndLoginRedirectTest extends TestCase
{
    use RefreshDatabase;

    public function test_faculty_create_permissions_allow_the_requested_pages(): void
    {
        $role = Role::create([
            'name' => 'Faculty Course Creator',
            'slug' => 'faculty_course_creator',
            'guard_role' => 'faculty',
            'permissions' => [
                'courses' => ['view' => true, 'create' => true],
                'exams' => ['view' => true, 'create' => true],
            ],
            'is_active' => true,
        ]);

        $user = User::factory()->create(['role' => 'faculty', 'role_id' => $role->id]);

        $this->actingAs($user)
            ->get(route('faculty.exams.create'))
            ->assertOk();

        $this->actingAs($user)
            ->get(route('faculty.quizzes.create'))
            ->assertOk();

        $this->actingAs($user)
            ->get(route('faculty.courses.index'))
            ->assertOk();

        $this->actingAs($user)
            ->get(route('faculty.assignments.create'))
            ->assertOk();

        $this->actingAs($user)
            ->post(route('faculty.assignments.store'), [])
            ->assertSessionHasErrors('course_id');
    }

    public function test_assignment_creation_requires_create_permission_without_view_permission(): void
    {
        $role = Role::create([
            'name' => 'Faculty Assignment Creator',
            'slug' => 'faculty_assignment_creator',
            'guard_role' => 'faculty',
            'permissions' => [
                'courses' => ['create' => true],
            ],
            'is_active' => true,
        ]);

        $user = User::factory()->create(['role' => 'faculty', 'role_id' => $role->id]);

        $this->actingAs($user)
            ->get(route('faculty.assignments.create'))
            ->assertOk();
    }

    public function test_faculty_login_uses_role_dashboard_instead_of_generic_dashboard_intended_url(): void
    {
        $role = Role::create([
            'name' => 'Faculty Login Test',
            'slug' => 'faculty_login_test',
            'guard_role' => 'faculty',
            'permissions' => [],
            'is_active' => true,
        ]);

        $user = User::factory()->create([
            'role' => 'faculty',
            'role_id' => $role->id,
            'password' => bcrypt('password'),
        ]);

        $this->get(route('dashboard'));

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect(route('faculty.dashboard'));
    }
}
