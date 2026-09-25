<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\HrJobRole;

class HrJobRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $systemRoles = Role::whereNotIn('slug', ['parent_guardian'])->get();

        foreach ($systemRoles as $sysRole) {
            $existing = HrJobRole::where('role_id', $sysRole->id)
                ->orWhere('title', $sysRole->name)
                ->first();

            $isFaculty = $sysRole->guard_role === 'faculty';
            $minBand = $isFaculty ? 2500000 : 3000000;
            $maxBand = $isFaculty ? 3500000 : 4500000;

            if (!$existing) {
                HrJobRole::create([
                    'title' => $sysRole->name,
                    'role_id' => $sysRole->id,
                    'description' => $sysRole->description,
                    'departments' => [],
                    'salary_band_min' => $minBand,
                    'salary_band_max' => $maxBand,
                    'is_active' => true,
                ]);
            } else {
                if (!$existing->role_id) {
                    $existing->role_id = $sysRole->id;
                }
                if ($existing->salary_band_min === null && $existing->salary_band_max === null) {
                    $existing->salary_band_min = $minBand;
                    $existing->salary_band_max = $maxBand;
                }
                $existing->save();
            }
        }
    }
}
