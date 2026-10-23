<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hr_employees', function (Blueprint $table) {
            if (!Schema::hasColumn('hr_employees', 'bank_name')) {
                $table->string('bank_name')->nullable()->after('salary_band');
            }
            if (!Schema::hasColumn('hr_employees', 'bank_account_name')) {
                $table->string('bank_account_name')->nullable()->after('bank_name');
            }
            if (!Schema::hasColumn('hr_employees', 'bank_account_number')) {
                $table->string('bank_account_number')->nullable()->after('bank_account_name');
            }
            if (!Schema::hasColumn('hr_employees', 'bank_branch')) {
                $table->string('bank_branch')->nullable()->after('bank_account_number');
            }
        });

        // Make employee_number nullable so it can be generated last upon onboarding completion
        try {
            DB::statement('ALTER TABLE hr_employees MODIFY employee_number VARCHAR(255) NULL');
        } catch (\Throwable $e) {
            // In case of non-MySQL or already nullable
        }
    }

    public function down(): void
    {
        Schema::table('hr_employees', function (Blueprint $table) {
            $table->dropColumn([
                'bank_name',
                'bank_account_name',
                'bank_account_number',
                'bank_branch',
            ]);
        });
    }
};
