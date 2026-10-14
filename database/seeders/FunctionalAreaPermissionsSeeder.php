<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class FunctionalAreaPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $actions = array_keys(config('university_permissions.actions', []));
        $all = array_fill_keys($actions, true);
        $readWrite = array_intersect_key($all, array_flip(['view', 'create', 'edit']));

        $roleGrants = [
            'super_administrator' => [
                'halls_of_residence' => $all,
                'registrar_hub' => $all,
                'dean_administration' => $all,
                'hr_onboarding' => $all,
            ],
            'registrar' => ['registrar_hub' => $all],
            'dean' => ['dean_administration' => $all],
            'hr_manager' => ['hr_onboarding' => $all],
            'hr_specialist' => ['hr_onboarding' => $readWrite],
            'estates_manager' => ['halls_of_residence' => $all],
            'facilities_officer' => ['halls_of_residence' => $readWrite],
        ];

        foreach ($roleGrants as $slug => $moduleGrants) {
            $defaults = config("university_permissions.defaults.{$slug}");
            if (!$defaults) {
                throw new \LogicException("Missing role defaults for [{$slug}].");
            }

            $role = Role::firstOrNew(['slug' => $slug]);
            if (!$role->exists) {
                $role->fill($defaults);
                $role->is_system = true;
                $role->is_active = true;
            }

            $permissions = $role->permissions ?? [];
            foreach ($moduleGrants as $module => $grants) {
                $permissions[$module] = array_merge($permissions[$module] ?? [], $grants);
            }

            $role->permissions = $permissions;
            $role->save();
        }
    }
}
