<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modules = array_keys(config('university_permissions.modules', []));
        $actions = array_keys(config('university_permissions.actions', []));
        $all = $this->matrix($modules, $actions);

        $rolePermissions = [
            'super_administrator' => $all,
            'registrar' => $this->matrix(['students', 'enrollments', 'programs', 'courses', 'applications', 'reports'], $actions),
            'dean' => $this->matrix(['faculty', 'departments', 'programs', 'courses', 'attendance', 'grades', 'reports'], ['view', 'create', 'edit', 'approve', 'export']),
            'head_of_department' => $this->matrix(['faculty', 'courses', 'attendance', 'grades', 'lms', 'exams', 'reports'], ['view', 'create', 'edit', 'approve', 'export']),
            'lecturer' => $this->matrix(['courses', 'attendance', 'grades', 'lms', 'exams'], ['view', 'create', 'edit']),
            'finance_officer' => $this->matrix(['fees', 'students', 'reports', 'finance_hub', 'revenue', 'budgets', 'expenses', 'payables', 'receivables', 'payroll', 'assets', 'banking', 'financial_statements'], ['view', 'create', 'edit', 'approve', 'export']),
            'admissions_officer' => array_merge_recursive(
                $this->matrix(['applications', 'students', 'fees', 'reports'], ['view', 'create', 'edit', 'approve', 'export']),
                $this->matrix(['courses', 'programs', 'departments'], ['view'])
            ),
            'student' => $this->matrix(['courses', 'fees', 'attendance', 'grades', 'lms', 'exams'], ['view']),
            'parent_guardian' => $this->matrix(['fees', 'attendance', 'grades', 'reports'], ['view']),
        ];

        foreach (config('university_permissions.defaults', []) as $slug => $roleConfig) {
            Role::updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $roleConfig['name'],
                    'guard_role' => $roleConfig['guard_role'],
                    'description' => $roleConfig['description'],
                    'permissions' => $rolePermissions[$slug] ?? [],
                    'is_system' => true,
                    'is_active' => true,
                ]
            );
        }

        // Attach default role_id to existing users without one
        DB::table('users')->whereNull('role_id')->orderBy('id')->each(function ($user) {
            $slug = match ($user->role) {
                'admin' => 'super_administrator',
                'faculty' => 'lecturer',
                'parent' => 'parent_guardian',
                default => 'student',
            };

            $roleId = DB::table('roles')->where('slug', $slug)->value('id');
            if ($roleId) {
                DB::table('users')->where('id', $user->id)->update(['role_id' => $roleId]);
            }
        });
    }

    /**
     * Helper to generate a permission matrix
     */
    private function matrix(array $modules, array $actions): array
    {
        $permissions = [];

        foreach ($modules as $module) {
            foreach ($actions as $action) {
                $permissions[$module][$action] = true;
            }
        }

        return $permissions;
    }
}
