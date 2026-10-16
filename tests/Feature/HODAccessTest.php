<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Department;
use App\Models\Course;
use App\Models\Role;
use App\Models\Faculty;

class HODAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Ensure roles exist
        $this->artisan('db:seed', ['--class' => 'RolePermissionSeeder']);
    }

    public function test_hod_can_view_their_department_dashboard()
    {
        $hod = User::factory()->create(['role' => 'faculty']);
        $role = Role::where('name', 'hod')->first();
        $hod->roleCatalog()->associate($role);
        $hod->save();

        $faculty = Faculty::factory()->create();
        $department = Department::factory()->create([
            'head_of_department_id' => $hod->id,
            'is_active' => true,
            'faculty_id' => $faculty->id,
        ]);

        $response = $this->actingAs($hod)->get(route('faculty.hod.department.index'));

        $response->assertStatus(200);
        $response->assertSee($department->name);
    }

    public function test_hod_cannot_view_dashboard_if_not_assigned_to_department()
    {
        $hod = User::factory()->create(['role' => 'faculty']);
        $role = Role::where('name', 'hod')->first();
        $hod->roleCatalog()->associate($role);
        $hod->save();

        // Department without this user as head
        $department = Department::factory()->create([
            'is_active' => true,
        ]);

        $response = $this->actingAs($hod)->get(route('faculty.hod.department.index'));

        $response->assertStatus(404);
    }

    public function test_hod_can_assign_lecturer_to_their_department_course()
    {
        $hod = User::factory()->create(['role' => 'faculty']);
        $role = Role::where('name', 'hod')->first();
        $hod->roleCatalog()->associate($role);
        $hod->save();

        $faculty = Faculty::factory()->create();
        $department = Department::factory()->create([
            'head_of_department_id' => $hod->id,
            'is_active' => true,
            'faculty_id' => $faculty->id,
        ]);

        $lecturer = User::factory()->create(['role' => 'faculty']);
        
        $course = Course::factory()->create([
            'department_id' => $department->id
        ]);

        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);
        $response = $this->actingAs($hod)->post(route('faculty.hod.department.courses.assign', $course), [
            'instructor_id' => $lecturer->id
        ]);

        if ($response->status() !== 302) {
            $response->dump();
        }
        $response->assertRedirect();
        $response->assertSessionHas('success');
        
        $this->assertEquals($lecturer->id, $course->fresh()->instructor_id);
    }

    public function test_hod_cannot_assign_lecturer_to_other_department_course()
    {
        $hod = User::factory()->create(['role' => 'faculty']);
        $role = Role::where('name', 'hod')->first();
        $hod->roleCatalog()->associate($role);
        $hod->save();

        $department = Department::factory()->create([
            'head_of_department_id' => $hod->id,
            'is_active' => true,
        ]);

        $otherDepartment = Department::factory()->create();

        $lecturer = User::factory()->create(['role' => 'faculty']);
        
        $course = Course::factory()->create([
            'department_id' => $otherDepartment->id,
            'instructor_id' => null,
        ]);

        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);

        $response = $this->actingAs($hod)->post(route('faculty.hod.department.courses.assign', $course), [
            'instructor_id' => $lecturer->id
        ]);

        $response->assertStatus(403);
        $this->assertNotEquals($lecturer->id, $course->fresh()->instructor_id);
    }
}
