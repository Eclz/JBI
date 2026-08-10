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
            if (!Schema::hasColumn('student_profiles', 'admission_acknowledged_at')) {
                $table->timestamp('admission_acknowledged_at')->nullable()->after('notes');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_profiles', function (Blueprint $table) {
            if (Schema::hasColumn('student_profiles', 'admission_acknowledged_at')) {
                $table->dropColumn('admission_acknowledged_at');
            }
        });
    }
};
