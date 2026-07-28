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
        Schema::table('faculty_profiles', function (Blueprint $table) {
            // Make designation nullable or add default value
            if (Schema::hasColumn('faculty_profiles', 'designation')) {
                $table->string('designation')->nullable()->default('Faculty Member')->change();
            }

            // Add employee_id if it doesn't exist
            if (!Schema::hasColumn('faculty_profiles', 'employee_id')) {
                $table->string('employee_id')->unique()->nullable()->after('user_id');
            }

            // Add position if it doesn't exist
            if (!Schema::hasColumn('faculty_profiles', 'position')) {
                $table->string('position')->default('Faculty Member')->after('department_id');
            }

            // Add hire_date if it doesn't exist
            if (!Schema::hasColumn('faculty_profiles', 'hire_date')) {
                $table->date('hire_date')->nullable()->after('position');
            }

            // Add employment_status if it doesn't exist
            if (!Schema::hasColumn('faculty_profiles', 'employment_status')) {
                $table->enum('employment_status', ['pending', 'active', 'inactive', 'on_leave', 'terminated'])->default('pending')->after('hire_date');
            }

            // Add application_status if it doesn't exist
            if (!Schema::hasColumn('faculty_profiles', 'application_status')) {
                $table->enum('application_status', ['submitted', 'under_review', 'approved', 'rejected'])->default('submitted')->after('employment_status');
            }

            // Add salary if it doesn't exist
            if (!Schema::hasColumn('faculty_profiles', 'salary')) {
                $table->decimal('salary', 10, 2)->nullable()->after('application_status');
            }

            // Add office_location if it doesn't exist
            if (!Schema::hasColumn('faculty_profiles', 'office_location')) {
                $table->string('office_location')->nullable()->after('salary');
            }

            // Add office_hours if it doesn't exist
            if (!Schema::hasColumn('faculty_profiles', 'office_hours')) {
                $table->json('office_hours')->nullable()->after('office_location');
            }

            // Add qualifications if it doesn't exist
            if (!Schema::hasColumn('faculty_profiles', 'qualifications')) {
                $table->json('qualifications')->nullable()->after('office_hours');
            }

            // Add experience if it doesn't exist
            if (!Schema::hasColumn('faculty_profiles', 'experience')) {
                $table->json('experience')->nullable()->after('qualifications');
            }

            // Add documents if it doesn't exist
            if (!Schema::hasColumn('faculty_profiles', 'documents')) {
                $table->json('documents')->nullable()->after('experience');
            }

            // Add application_notes if it doesn't exist
            if (!Schema::hasColumn('faculty_profiles', 'application_notes')) {
                $table->text('application_notes')->nullable()->after('documents');
            }

            // Add notes if it doesn't exist
            if (!Schema::hasColumn('faculty_profiles', 'notes')) {
                $table->text('notes')->nullable()->after('application_notes');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('faculty_profiles', function (Blueprint $table) {
            // Revert designation to not nullable if needed
            if (Schema::hasColumn('faculty_profiles', 'designation')) {
                $table->string('designation')->nullable(false)->change();
            }

            // Drop indexes if they exist using raw SQL to avoid method limitations
            try {
                DB::statement('ALTER TABLE faculty_profiles DROP INDEX IF EXISTS faculty_profiles_employment_status_idx');
            } catch (\Exception $e) {
                // Log or ignore error
            }

            try {
                DB::statement('ALTER TABLE faculty_profiles DROP INDEX IF EXISTS faculty_profiles_application_status_idx');
            } catch (\Exception $e) {
                // Log or ignore error
            }

            try {
                DB::statement('ALTER TABLE faculty_profiles DROP INDEX IF EXISTS faculty_profiles_dept_employment_idx');
            } catch (\Exception $e) {
                // Log or ignore error
            }

            // Drop columns if they exist
            if (Schema::hasColumn('faculty_profiles', 'employee_id')) {
                $table->dropColumn('employee_id');
            }
            if (Schema::hasColumn('faculty_profiles', 'position')) {
                $table->dropColumn('position');
            }
            if (Schema::hasColumn('faculty_profiles', 'hire_date')) {
                $table->dropColumn('hire_date');
            }
            if (Schema::hasColumn('faculty_profiles', 'employment_status')) {
                $table->dropColumn('employment_status');
            }
            if (Schema::hasColumn('faculty_profiles', 'application_status')) {
                $table->dropColumn('application_status');
            }
            if (Schema::hasColumn('faculty_profiles', 'salary')) {
                $table->dropColumn('salary');
            }
            if (Schema::hasColumn('faculty_profiles', 'office_location')) {
                $table->dropColumn('office_location');
            }
            if (Schema::hasColumn('faculty_profiles', 'office_hours')) {
                $table->dropColumn('office_hours');
            }
            if (Schema::hasColumn('faculty_profiles', 'qualifications')) {
                $table->dropColumn('qualifications');
            }
            if (Schema::hasColumn('faculty_profiles', 'experience')) {
                $table->dropColumn('experience');
            }
            if (Schema::hasColumn('faculty_profiles', 'documents')) {
                $table->dropColumn('documents');
            }
            if (Schema::hasColumn('faculty_profiles', 'application_notes')) {
                $table->dropColumn('application_notes');
            }
            if (Schema::hasColumn('faculty_profiles', 'notes')) {
                $table->dropColumn('notes');
            }
        });
    }
};
