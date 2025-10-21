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
            // Add application_status enum if it doesn't exist
            if (!Schema::hasColumn('faculty_profiles', 'application_status')) {
                $columnAfter = Schema::hasColumn('faculty_profiles', 'employment_status') ? 'employment_status' : 'specialization';
                $table->enum('application_status', ['submitted', 'under_review', 'approved', 'rejected'])
                      ->default('submitted')
                      ->after($columnAfter);
            }

            // Add qualifications if it doesn't exist
            if (!Schema::hasColumn('faculty_profiles', 'qualifications')) {
                $columnAfter = Schema::hasColumn('faculty_profiles', 'application_status') ? 'application_status' : 'specialization';
                $table->json('qualifications')->nullable()->after($columnAfter);
            }

            // Add experience if it doesn't exist
            if (!Schema::hasColumn('faculty_profiles', 'experience')) {
                $columnAfter = Schema::hasColumn('faculty_profiles', 'qualifications') ? 'qualifications' : 'application_status';
                $table->json('experience')->nullable()->after($columnAfter);
            }

            // Add documents if it doesn't exist
            if (!Schema::hasColumn('faculty_profiles', 'documents')) {
                $columnAfter = Schema::hasColumn('faculty_profiles', 'experience') ? 'experience' : 'qualifications';
                $table->json('documents')->nullable()->after($columnAfter);
            }

            // Add application_notes if it doesn't exist
            if (!Schema::hasColumn('faculty_profiles', 'application_notes')) {
                $columnAfter = Schema::hasColumn('faculty_profiles', 'documents') ? 'documents' : 'experience';
                $table->text('application_notes')->nullable()->after($columnAfter);
            }

            // Add index for application_status
            if (!Schema::hasIndex('faculty_profiles', ['application_status'])) {
                $table->index('application_status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('faculty_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'application_status',
                'qualifications',
                'experience',
                'documents',
                'application_notes'
            ]);
        });
    }
};
