<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Faculty;
use App\Models\FacultyProfile;
use App\Models\Department;
use App\Models\Program;
use App\Models\Role;
use App\Models\StudentProfile;
use App\Models\SystemSetting;

class DeanAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Ensure roles exist
        $this->artisan('db:seed', ['--class' => 'RolePermissionSeeder']);
    }

    public function test_dean_can_view_their_faculty_dashboard()
    {
        $dean = User::factory()->create(['role' => 'faculty']);
        $role = Role::where('slug', 'dean')->first();
        $dean->roleCatalog()->associate($role);
        $dean->save();

        $faculty = Faculty::factory()->create([
            'dean_id' => $dean->id,
            'is_active' => true,
        ]);

        $response = $this->actingAs($dean)->get(route('faculty.dean.quality.index'));

        $response->assertStatus(200);
    }

    public function test_dean_can_view_quality_reviews_if_not_assigned_to_faculty()
    {
        $dean = User::factory()->create(['role' => 'faculty']);
        $role = Role::where('slug', 'dean')->first();
        $dean->roleCatalog()->associate($role);
        $dean->save();

        // Faculty without this user as dean
        $faculty = Faculty::factory()->create([
            'is_active' => true,
        ]);

        $response = $this->actingAs($dean)->get(route('faculty.dean.quality.index'));

        // Should return 200 and just show empty lists
        $response->assertStatus(200);
    }

    public function test_dean_create_pages_load_scoped_academic_data_from_profile_faculty()
    {
        SystemSetting::setSetting('default_currency', 'ZAR', 'string', 'financial', true);

        $role = Role::where('slug', 'dean')->first();
        $dean = User::factory()->faculty()->create(['role_id' => $role->id]);
        $faculty = Faculty::factory()->create(['dean_id' => null]);
        $department = Department::factory()->create([
            'faculty_id' => $faculty->id,
            'name' => 'Theology',
            'code' => 'THEO999',
        ]);
        FacultyProfile::factory()->create([
            'user_id' => $dean->id,
            'department_id' => $department->id,
        ]);

        $facultyMember = User::factory()->faculty()->create(['name' => 'Dr Scoped Lecturer']);
        FacultyProfile::factory()->create([
            'user_id' => $facultyMember->id,
            'department_id' => $department->id,
        ]);

        $student = User::factory()->student()->create(['name' => 'Scoped Student']);
        StudentProfile::factory()->create([
            'user_id' => $student->id,
            'department_id' => $department->id,
        ]);

        $program = Program::create([
            'department_id' => $department->id,
            'name' => 'Bachelor of Theology',
            'code' => 'BTHEO',
            'description' => 'A scoped dean program.',
            'is_active' => true,
        ]);

        $this->actingAs($dean)
            ->get(route('faculty.dean.evaluations.create'))
            ->assertStatus(200)
            ->assertSee('Dr Scoped Lecturer');

        $this->actingAs($dean)
            ->get(route('faculty.dean.student-issues.create'))
            ->assertStatus(200)
            ->assertSee('Scoped Student');

        $this->actingAs($dean)
            ->get(route('faculty.dean.budgets.create'))
            ->assertStatus(200)
            ->assertSee('Theology')
            ->assertSee('ZAR')
            ->assertSee('Dr Scoped Lecturer');

        $this->actingAs($dean)
            ->get(route('faculty.dean.quality.create'))
            ->assertStatus(200)
            ->assertSee($program->name);
    }
}
