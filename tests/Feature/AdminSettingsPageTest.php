<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSettingsPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_settings_page_renders_the_distinct_admission_and_registration_statuses(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get(route('admin.settings'))
            ->assertOk()
            ->assertSee('Admission applications')
            ->assertSee('Semester registration')
            ->assertSee('Admission Application Window');
    }
}
