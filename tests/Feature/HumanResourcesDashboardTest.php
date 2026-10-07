<?php

namespace Tests\Feature;

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
}
