<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->foreignId('program_id')->nullable()->after('department_id')->constrained('programs')->nullOnDelete();
        });

        Schema::table('student_profiles', function (Blueprint $table) {
            $table->foreignId('program_id')->nullable()->after('department_id')->constrained('programs')->nullOnDelete();
        });

        Schema::table('applications', function (Blueprint $table) {
            $table->foreignId('program_id')->nullable()->after('program')->constrained('programs')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropConstrainedForeignId('program_id');
        });

        Schema::table('student_profiles', function (Blueprint $table) {
            $table->dropConstrainedForeignId('program_id');
        });

        Schema::table('applications', function (Blueprint $table) {
            $table->dropConstrainedForeignId('program_id');
        });
    }
};
