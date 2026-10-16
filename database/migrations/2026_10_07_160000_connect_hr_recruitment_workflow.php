<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hr_vacancies', function (Blueprint $table) {
            $table->foreignId('role_id')->nullable()->after('department')
                ->constrained('roles')->nullOnDelete();
            $table->foreignId('department_id')->nullable()->after('role_id')
                ->constrained('departments')->nullOnDelete();
            $table->foreignId('source_succession_plan_id')->nullable()->after('hiring_manager_id')
                ->constrained('hr_succession_plans')->nullOnDelete();
            $table->timestamp('published_at')->nullable()->after('opening_date');
        });

        Schema::table('hr_succession_plans', function (Blueprint $table) {
            $table->foreignId('hr_vacancy_id')->nullable()->after('current_holder_id')
                ->constrained('hr_vacancies')->nullOnDelete();
        });

        Schema::table('hr_applicants', function (Blueprint $table) {
            $table->string('tracking_token_hash', 64)->nullable()->unique()->after('cv_path');
            $table->json('documents')->nullable()->after('tracking_token_hash');
            $table->timestamp('consent_at')->nullable()->after('documents');
            $table->timestamp('hired_at')->nullable()->after('hired_user_id');
            $table->foreignId('hired_by')->nullable()->after('hired_at')
                ->constrained('users')->nullOnDelete();
        });

        Schema::table('hr_employees', function (Blueprint $table) {
            $table->foreignId('department_id')->nullable()->after('department')
                ->constrained('departments')->nullOnDelete();
            $table->string('workspace')->nullable()->after('department_id');
            $table->foreignId('source_applicant_id')->nullable()->unique()->after('user_id')
                ->constrained('hr_applicants')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('hr_employees', function (Blueprint $table) {
            $table->dropForeign(['source_applicant_id']);
            $table->dropUnique(['source_applicant_id']);
            $table->dropForeign(['department_id']);
            $table->dropColumn(['source_applicant_id', 'workspace', 'department_id']);
        });

        Schema::table('hr_applicants', function (Blueprint $table) {
            $table->dropForeign(['hired_by']);
            $table->dropUnique(['tracking_token_hash']);
            $table->dropColumn(['tracking_token_hash', 'documents', 'consent_at', 'hired_at', 'hired_by']);
        });

        Schema::table('hr_succession_plans', function (Blueprint $table) {
            $table->dropForeign(['hr_vacancy_id']);
            $table->dropColumn('hr_vacancy_id');
        });

        Schema::table('hr_vacancies', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropForeign(['department_id']);
            $table->dropForeign(['source_succession_plan_id']);
            $table->dropColumn(['role_id', 'department_id', 'source_succession_plan_id', 'published_at']);
        });
    }
};
