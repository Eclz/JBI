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
        Schema::table('courses', function (Blueprint $table) {
            // Add capacity field if it doesn't exist
            if (!Schema::hasColumn('courses', 'capacity')) {
                $table->integer('capacity')->nullable()->after('max_students');
            }

            // Add course_code field if it doesn't exist
            if (!Schema::hasColumn('courses', 'course_code')) {
                $table->string('course_code')->unique()->after('id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn(['capacity', 'course_code']);
        });
    }
};
