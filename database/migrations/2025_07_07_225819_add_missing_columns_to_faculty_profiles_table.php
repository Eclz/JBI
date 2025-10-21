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

            // Add indexes for performance, with try-catch to avoid duplicate index errors
            try {
                $table->index(['employment_status'], 'faculty_profiles_employment_status_idx');
            } catch (\Exception $e) {
                // Log or ignore duplicate index error
            }

            try {
                $table->index(['application_status'], 'faculty_profiles_application_status_idx');
            } catch (\Exception $e) {
                // Log or ignore duplicate index error
            }

            try {
                $table->index(['department_id', 'employment_status'], 'faculty_profiles_dept_employment_idx');
            } catch (\Exception $e) {
                // Log or ignore duplicate index error
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('faculty_profiles', function (Blueprint $table) {
            // Drop indexes if they exist using raw SQL
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
            $columns = [
                'employee_id',
                'position',
                'hire_date',
                'employment_status',
                'application_status',
                'salary',
                'office_location',
                'office_hours',
                'qualifications',
                'experience',
                'documents',
                'application_notes',
                'notes',
            ];

            // Only drop columns that exist
            $existingColumns = array_filter($columns, function ($column) {
                return Schema::hasColumn('faculty_profiles', $column);
            });

            if (!empty($existingColumns)) {
                $table->dropColumn($existingColumns);
            }
        });
    }
};
