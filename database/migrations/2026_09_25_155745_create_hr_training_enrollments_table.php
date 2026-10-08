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
        Schema::create('hr_training_enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('hr_training_course_id')->constrained('hr_training_courses')->cascadeOnDelete();
            $table->date('enrollment_date');
            $table->date('completion_date')->nullable();
            $table->string('status')->default('Enrolled'); // Enrolled, In Progress, Completed, Failed
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hr_training_enrollments');
    }
};
