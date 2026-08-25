<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fee_structures', function (Blueprint $table) {
            $table->foreignId('program_id')->nullable()->after('semester_id')->constrained('programs')->nullOnDelete();
            $table->foreignId('program_level_id')->nullable()->after('program_id')->constrained('program_levels')->nullOnDelete();
            $table->string('currency', 3)->default('ZAR')->after('amount');
            $table->string('student_region', 20)->default('local')->after('currency');
            $table->decimal('total_amount', 12, 2)->nullable()->after('student_region');
            $table->decimal('total_amount_max', 12, 2)->nullable()->after('total_amount');
            $table->string('academic_session', 20)->nullable()->after('total_amount_max');
            $table->string('source_url')->nullable()->after('academic_session');

            $table->index(['program_id', 'currency', 'academic_year_id'], 'fees_program_currency_year_idx');
        });
    }

    public function down(): void
    {
        Schema::table('fee_structures', function (Blueprint $table) {
            $table->dropIndex('fees_program_currency_year_idx');
            $table->dropConstrainedForeignId('program_id');
            $table->dropConstrainedForeignId('program_level_id');
            $table->dropColumn(['currency', 'student_region', 'total_amount', 'total_amount_max', 'academic_session', 'source_url']);
        });
    }
};
