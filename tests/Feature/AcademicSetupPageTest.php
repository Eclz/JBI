<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AcademicSetupPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_follow_the_academic_setup_in_dependency_order(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get(route('admin.academic-setup.index'));

        $response->assertOk();
        $response->assertSeeInOrder([
            route('admin.program-levels.index'),
            route('admin.faculties.index'),
            route('admin.departments.index'),
            route('admin.programs.index'),
            route('admin.fees.structures.index'),
            route('admin.courses.index'),
        ], false);
    }
}
