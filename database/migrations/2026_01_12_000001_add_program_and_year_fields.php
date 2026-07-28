<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->string('program')->nullable()->after('department_id');
            $table->unsignedTinyInteger('year_of_study')->nullable()->after('semester_id');
        });

        Schema::table('student_profiles', function (Blueprint $table) {
            $table->unsignedTinyInteger('year_of_study')->nullable()->after('current_semester');
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn(['program', 'year_of_study']);
        });

        Schema::table('student_profiles', function (Blueprint $table) {
            $table->dropColumn('year_of_study');
        });
    }
};
