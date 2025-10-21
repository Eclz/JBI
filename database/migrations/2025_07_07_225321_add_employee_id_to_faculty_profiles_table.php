<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('faculty_profiles', function (Blueprint $table) {
            // Add employee_id if it doesn't exist
            if (!Schema::hasColumn('faculty_profiles', 'employee_id')) {
                $table->string('employee_id')->unique()->nullable()->after('user_id');
            }

            // Add index for employee_id
            if (!Schema::hasIndex('faculty_profiles', ['employee_id'])) {
                $table->index('employee_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('faculty_profiles', function (Blueprint $table) {
            $table->dropColumn('employee_id');
        });
    }
};
