<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Faculty;
use App\Models\Role;

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
        $role = Role::where('name', 'dean')->first();
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
        $role = Role::where('name', 'dean')->first();
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
}
