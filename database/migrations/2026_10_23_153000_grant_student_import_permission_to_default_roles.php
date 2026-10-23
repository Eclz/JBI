<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $this->setStudentImportPermission(true);
    }

    public function down(): void
    {
        $this->setStudentImportPermission(false);
    }

    private function setStudentImportPermission(bool $granted): void
    {
        DB::table('roles')
            ->whereIn('slug', ['registrar', 'super_administrator'])
            ->get(['id', 'permissions'])
            ->each(function ($role) use ($granted) {
                $permissions = is_array($role->permissions)
                    ? $role->permissions
                    : (json_decode($role->permissions ?? '', true) ?: []);

                if ($granted) {
                    data_set($permissions, 'students.import', true);
                } else {
                    unset($permissions['students']['import']);
                }

                DB::table('roles')->where('id', $role->id)->update([
                    'permissions' => json_encode($permissions),
                ]);
            });
    }
};
