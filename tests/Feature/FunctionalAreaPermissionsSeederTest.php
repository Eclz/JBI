<?php

namespace Tests\Feature;

use App\Models\Role;
use Database\Seeders\FunctionalAreaPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FunctionalAreaPermissionsSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_adds_the_four_functional_areas_and_seeds_role_grants(): void
    {
        $this->seed(FunctionalAreaPermissionsSeeder::class);

        $this->assertSame('Halls of Residence', config('university_permissions.modules.halls_of_residence'));
        $this->assertSame('Registrar Hub', config('university_permissions.modules.registrar_hub'));
        $this->assertSame('Dean Administration', config('university_permissions.modules.dean_administration'));
        $this->assertSame('HR Onboarding', config('university_permissions.modules.hr_onboarding'));

        $this->assertTrue(Role::where('slug', 'registrar')->firstOrFail()->hasPermission('registrar_hub', 'view'));
        $dean = Role::where('slug', 'dean')->firstOrFail();
        $this->assertTrue($dean->hasPermission('dean_administration', 'approve'));
        $this->assertTrue($dean->hasPermission('faculty', 'view'));
        $this->assertTrue(Role::where('slug', 'hr_manager')->firstOrFail()->hasPermission('hr_onboarding', 'approve'));
        $this->assertTrue(Role::where('slug', 'hr_specialist')->firstOrFail()->hasPermission('hr_onboarding', 'edit'));
        $this->assertFalse(Role::where('slug', 'hr_specialist')->firstOrFail()->hasPermission('hr_onboarding', 'approve'));
        $this->assertTrue(Role::where('slug', 'estates_manager')->firstOrFail()->hasPermission('halls_of_residence', 'approve'));
        $this->assertTrue(Role::where('slug', 'facilities_officer')->firstOrFail()->hasPermission('halls_of_residence', 'create'));
    }
}
