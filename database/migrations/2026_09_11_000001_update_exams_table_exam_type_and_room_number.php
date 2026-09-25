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
        Schema::table('exams', function (Blueprint $table) {
            $table->string('exam_type', 50)->default('midterm')->change();
            if (!Schema::hasColumn('exams', 'room_number')) {
                $table->string('room_number', 50)->nullable()->after('exam_mode');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            if (Schema::hasColumn('exams', 'room_number')) {
                $table->dropColumn('room_number');
            }
        });
    }
};
