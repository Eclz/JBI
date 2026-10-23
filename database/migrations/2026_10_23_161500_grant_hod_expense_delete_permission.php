<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $this->setExpenseDeletePermission(true);
    }

    public function down(): void
    {
        $this->setExpenseDeletePermission(false);
    }

    private function setExpenseDeletePermission(bool $granted): void
    {
        DB::table('roles')
            ->where('slug', 'head_of_department')
            ->get(['id', 'permissions'])
            ->each(function ($role) use ($granted): void {
                $permissions = is_array($role->permissions)
                    ? $role->permissions
                    : (json_decode($role->permissions ?? '', true) ?: []);

                if ($granted) {
                    data_set($permissions, 'expenses.delete', true);
                } else {
                    unset($permissions['expenses']['delete']);
                }

                DB::table('roles')->where('id', $role->id)->update([
                    'permissions' => json_encode($permissions),
                ]);
            });
    }
};
