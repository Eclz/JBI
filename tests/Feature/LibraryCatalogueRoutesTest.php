<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LibraryCatalogueRoutesTest extends TestCase
{
    use RefreshDatabase;

    public function test_catalogue_create_route_renders_the_create_form(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get('/library/catalogue/create')
            ->assertOk()
            ->assertSee('Add Catalogue Item');
    }
}
