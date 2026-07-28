<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('guard_role')->default('student');
            $table->text('description')->nullable();
            $table->json('permissions')->nullable();
            $table->boolean('is_system')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['guard_role', 'is_active']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')->nullable()->after('role')->constrained()->nullOnDelete();
        });

        $now = now();
        $modules = array_keys(config('university_permissions.modules', []));
        $actions = array_keys(config('university_permissions.actions', []));
        $all = $this->matrix($modules, $actions);

        $rolePermissions = [
            'super_administrator' => $all,
            'registrar' => $this->matrix(['students', 'enrollments', 'programs', 'courses', 'applications', 'reports'], $actions),
            'dean' => $this->matrix(['faculty', 'departments', 'programs', 'courses', 'attendance', 'grades', 'reports'], ['view', 'create', 'edit', 'approve', 'export']),
            'head_of_department' => $this->matrix(['faculty', 'courses', 'attendance', 'grades', 'lms', 'exams', 'reports'], ['view', 'create', 'edit', 'approve', 'export']),
            'lecturer' => $this->matrix(['courses', 'attendance', 'grades', 'lms', 'exams'], ['view', 'create', 'edit']),
            'finance_officer' => $this->matrix(['fees', 'students', 'reports'], ['view', 'create', 'edit', 'approve', 'export']),
            'admissions_officer' => $this->matrix(['applications', 'students', 'fees', 'reports'], ['view', 'create', 'edit', 'approve', 'export']),
            'student' => $this->matrix(['courses', 'fees', 'attendance', 'grades', 'lms', 'exams'], ['view']),
            'parent_guardian' => $this->matrix(['fees', 'attendance', 'grades', 'reports'], ['view']),
        ];

        foreach (config('university_permissions.defaults', []) as $slug => $role) {
            DB::table('roles')->insert([
                'name' => $role['name'],
                'slug' => $slug,
                'guard_role' => $role['guard_role'],
                'description' => $role['description'],
                'permissions' => json_encode($rolePermissions[$slug] ?? []),
                'is_system' => true,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        DB::table('users')->whereNull('role_id')->orderBy('id')->each(function ($user) {
            $slug = match ($user->role) {
                'admin' => 'super_administrator',
                'faculty' => 'lecturer',
                'parent' => 'parent_guardian',
                default => 'student',
            };

            $roleId = DB::table('roles')->where('slug', $slug)->value('id');
            DB::table('users')->where('id', $user->id)->update(['role_id' => $roleId]);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('role_id');
        });

        Schema::dropIfExists('roles');
    }

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
};
