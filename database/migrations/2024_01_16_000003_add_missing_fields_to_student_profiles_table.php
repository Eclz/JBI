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
        Schema::table('student_profiles', function (Blueprint $table) {
            // Add application_status enum if it doesn't exist
            if (!Schema::hasColumn('student_profiles', 'application_status')) {
                $table->enum('application_status', ['submitted', 'under_review', 'approved', 'rejected'])->default('submitted')->after('status');
            }

            // Add previous_school_address if it doesn't exist
            if (!Schema::hasColumn('student_profiles', 'previous_school_address')) {
                $table->string('previous_school_address')->nullable()->after('previous_school');
            }

            // Add graduation_year if it doesn't exist
            if (!Schema::hasColumn('student_profiles', 'graduation_year')) {
                $table->year('graduation_year')->nullable()->after('previous_school_address');
            }

            // Add previous_gpa if it doesn't exist
            if (!Schema::hasColumn('student_profiles', 'previous_gpa')) {
                $table->decimal('previous_gpa', 3, 2)->nullable()->after('graduation_year');
            }

            // Add qualifications if it doesn't exist
            if (!Schema::hasColumn('student_profiles', 'qualifications')) {
                $table->json('qualifications')->nullable()->after('academic_history');
            }

            // Add documents if it doesn't exist
            if (!Schema::hasColumn('student_profiles', 'documents')) {
                $table->json('documents')->nullable()->after('qualifications');
            }

            // Add application_notes if it doesn't exist
            if (!Schema::hasColumn('student_profiles', 'application_notes')) {
                $table->text('application_notes')->nullable()->after('documents');
            }

            // Update status enum to include 'pending' if not already there
            $table->enum('status', ['pending', 'active', 'inactive', 'graduated', 'dropped', 'suspended'])->default('pending')->change();

            // Add index for application_status
            if (!Schema::hasIndex('student_profiles', ['application_status'])) {
                $table->index('application_status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'application_status',
                'previous_school_address',
                'graduation_year',
                'previous_gpa',
                'qualifications',
                'documents',
                'application_notes'
            ]);

            // Revert status enum to original values
            $table->enum('status', ['active', 'inactive', 'graduated', 'dropped', 'suspended'])->default('active')->change();
        });
    }
};
