<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Add 'admitted' to the status enum
        if (DB::connection()->getDriverName() === 'sqlite') {
            Schema::table('applications', function (Blueprint $table) {
                $table->string('status')->default('pending')->change();
            });
        } else {
            DB::statement("ALTER TABLE applications MODIFY COLUMN status ENUM('pending', 'under_review', 'approved', 'rejected', 'admitted') DEFAULT 'pending'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove 'admitted' from the status enum
        if (DB::connection()->getDriverName() === 'sqlite') {
            Schema::table('applications', function (Blueprint $table) {
                $table->string('status')->default('pending')->change();
            });
        } else {
            DB::statement("ALTER TABLE applications MODIFY COLUMN status ENUM('pending', 'under_review', 'approved', 'rejected') DEFAULT 'pending'");
        }
    }
};
