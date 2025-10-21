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
            if (!Schema::hasColumn('faculty_profiles', 'employment_status')) {
                $table->enum('employment_status', ['pending', 'active', 'inactive', 'on_leave', 'terminated', 'retired'])
                      ->default('pending')
                      ->after('employment_type');
            }

            if (!Schema::hasColumn('faculty_profiles', 'application_status')) {
                $table->enum('application_status', ['submitted', 'under_review', 'approved', 'rejected'])
                      ->default('submitted')
                      ->after('employment_status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('faculty_profiles', function (Blueprint $table) {
            if (Schema::hasColumn('faculty_profiles', 'employment_status')) {
                $table->dropColumn('employment_status');
            }

            if (Schema::hasColumn('faculty_profiles', 'application_status')) {
                $table->dropColumn('application_status');
            }
        });
    }
};
