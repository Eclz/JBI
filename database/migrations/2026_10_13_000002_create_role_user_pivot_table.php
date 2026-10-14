<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('role_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('role_id')->constrained('roles')->onDelete('cascade');
            $table->boolean('is_primary')->default(false);
            $table->timestamps();

            $table->unique(['user_id', 'role_id']);
        });

        // 1. Backfill existing primary role_id references
        DB::statement("
            INSERT IGNORE INTO role_user (user_id, role_id, is_primary, created_at, updated_at)
            SELECT id, role_id, 1, NOW(), NOW()
            FROM users
            WHERE role_id IS NOT NULL
        ");

        // 2. Backfill users that only have string 'role' without 'role_id'
        $roles = DB::table('roles')->get();
        foreach ($roles as $r) {
            DB::statement("
                INSERT IGNORE INTO role_user (user_id, role_id, is_primary, created_at, updated_at)
                SELECT id, {$r->id}, 1, NOW(), NOW()
                FROM users
                WHERE (role = '{$r->slug}' OR role = '{$r->guard_role}')
                AND id NOT IN (SELECT user_id FROM role_user)
            ");
        }

        // 3. Automatically link appointed Deans with Dean role
        $deanRole = DB::table('roles')->where('slug', 'dean')->first();
        if ($deanRole) {
            DB::statement("
                INSERT IGNORE INTO role_user (user_id, role_id, is_primary, created_at, updated_at)
                SELECT dean_id, {$deanRole->id}, 0, NOW(), NOW()
                FROM faculties
                WHERE dean_id IS NOT NULL
            ");
        }

        // 4. Automatically link appointed HODs with Head of Department role
        $hodRole = DB::table('roles')->where('slug', 'head_of_department')->first();
        if ($hodRole) {
            DB::statement("
                INSERT IGNORE INTO role_user (user_id, role_id, is_primary, created_at, updated_at)
                SELECT head_of_department_id, {$hodRole->id}, 0, NOW(), NOW()
                FROM departments
                WHERE head_of_department_id IS NOT NULL
            ");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_user');
    }
};
