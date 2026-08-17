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
            // Use schema builder for SQLite, raw SQL for other databases to handle single quote in default value
            if (Schema::hasColumn('faculty_profiles', 'qualification')) {
                if (DB::connection()->getDriverName() === 'sqlite') {
                    $table->string('qualification')->nullable()->default("Bachelor''s Degree")->change();
                } else {
                    DB::statement("ALTER TABLE `faculty_profiles` MODIFY `qualification` VARCHAR(255) NULL DEFAULT 'Bachelor''s Degree'");
                }
            }

            if (Schema::hasColumn('faculty_profiles', 'department_id')) {
                $table->unsignedBigInteger('department_id')->nullable()->change();
            }

            // Make specialization nullable if it exists
            if (Schema::hasColumn('faculty_profiles', 'specialization')) {
                $table->string('specialization')->nullable()->change();
            }

            // Make joining_date nullable if it exists
            if (Schema::hasColumn('faculty_profiles', 'joining_date')) {
                $table->date('joining_date')->nullable()->change();
            }

            // Make employment_type nullable if it exists
            if (Schema::hasColumn('faculty_profiles', 'employment_type')) {
                $table->string('employment_type')->nullable()->default('full_time')->change();
            }

            // Make years_of_experience nullable if it exists
            if (Schema::hasColumn('faculty_profiles', 'years_of_experience')) {
                $table->integer('years_of_experience')->nullable()->default(0)->change();
            }

            // Make bio nullable if it exists
            if (Schema::hasColumn('faculty_profiles', 'bio')) {
                $table->text('bio')->nullable()->change();
            }

            // Make linkedin_profile nullable if it exists
            if (Schema::hasColumn('faculty_profiles', 'linkedin_profile')) {
                $table->string('linkedin_profile')->nullable()->change();
            }

            // Make personal_website nullable if it exists
            if (Schema::hasColumn('faculty_profiles', 'personal_website')) {
                $table->string('personal_website')->nullable()->change();
            }

            // Make status nullable if it exists
            if (Schema::hasColumn('faculty_profiles', 'status')) {
                $table->string('status')->nullable()->default('active')->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('faculty_profiles', function (Blueprint $table) {
            // Revert qualification to not nullable using schema builder for SQLite, raw SQL for other databases
            if (Schema::hasColumn('faculty_profiles', 'qualification')) {
                if (DB::connection()->getDriverName() === 'sqlite') {
                    $table->string('qualification')->nullable(false)->change();
                } else {
                    DB::statement('ALTER TABLE `faculty_profiles` MODIFY `qualification` VARCHAR(255) NOT NULL');
                }
            }

            // Revert specialization to not nullable
            if (Schema::hasColumn('faculty_profiles', 'specialization')) {
                $table->string('specialization')->nullable(false)->default(null)->change();
            }

            // Revert joining_date to not nullable
            if (Schema::hasColumn('faculty_profiles', 'joining_date')) {
                $table->date('joining_date')->nullable(false)->change();
            }

            // Revert employment_type to not nullable
            if (Schema::hasColumn('faculty_profiles', 'employment_type')) {
                $table->string('employment_type')->nullable(false)->default(null)->change();
            }

            // Revert years_of_experience to not nullable
            if (Schema::hasColumn('faculty_profiles', 'years_of_experience')) {
                $table->integer('years_of_experience')->nullable(false)->default(null)->change();
            }

            // Revert bio to not nullable
            if (Schema::hasColumn('faculty_profiles', 'bio')) {
                $table->text('bio')->nullable(false)->change();
            }

            // Revert linkedin_profile to not nullable
            if (Schema::hasColumn('faculty_profiles', 'linkedin_profile')) {
                $table->string('linkedin_profile')->nullable(false)->default(null)->change();
            }

            // Revert personal_website to not nullable
            if (Schema::hasColumn('faculty_profiles', 'personal_website')) {
                $table->string('personal_website')->nullable(false)->default(null)->change();
            }

            // Revert status to not nullable
            if (Schema::hasColumn('faculty_profiles', 'status')) {
                $table->string('status')->nullable(false)->default(null)->change();
            }

            if (Schema::hasColumn('faculty_profiles', 'department_id')) {
                // Set a default value for existing NULL entries before making NOT NULL
                DB::table('faculty_profiles')
                    ->whereNull('department_id')
                    ->update(['department_id' => 1]); // Replace 1 with a valid default department_id
                $table->unsignedBigInteger('department_id')->nullable(false)->change();
            }
        });
    }
};
