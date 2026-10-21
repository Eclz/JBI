<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use App\Models\Course;
use App\Models\Semester;
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

    public function test_assignment_create_lists_all_assigned_courses_and_selects_the_requested_course(): void
    {
        $role = Role::create([
            'name' => 'Faculty Course Creator',
            'slug' => 'faculty_course_creator',
            'guard_role' => 'faculty',
            'permissions' => [
                'courses' => ['create' => true],
            ],
            'is_active' => true,
        ]);
        $lecturer = User::factory()->create(['role' => 'faculty', 'role_id' => $role->id]);
        $currentSemester = Semester::factory()->current()->create();
        $previousSemester = Semester::factory()->create();
        $assignedCourse = Course::factory()->withInstructor($lecturer)->create([
            'code' => 'OLD101',
            'name' => 'Assigned Previous Semester Course',
            'semester_id' => $previousSemester->id,
        ]);
        Course::factory()->withInstructor(User::factory()->create(['role' => 'faculty']))->create([
            'code' => 'OTHER101',
            'name' => 'Another Lecturer Course',
            'semester_id' => $currentSemester->id,
        ]);

        $this->actingAs($lecturer)
            ->get(route('faculty.assignments.create').'?'.$assignedCourse->id)
            ->assertOk()
            ->assertSee('Assigned Previous Semester Course')
            ->assertDontSee('Another Lecturer Course')
            ->assertSee('value="'.$assignedCourse->id.'" selected', false);
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
