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
        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('assignment_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('grade_type'); // 'assignment', 'quiz', 'exam', 'final', 'participation'
            $table->decimal('points_earned', 5, 2);
            $table->decimal('points_possible', 5, 2);
            $table->decimal('percentage', 5, 2);
            $table->string('letter_grade', 2)->nullable();
            $table->decimal('grade_points', 5, 2)->nullable(); // For GPA calculation
            $table->text('comments')->nullable();
            $table->boolean('is_published')->default(false);
            $table->datetime('graded_at');
            $table->foreignId('graded_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            
            $table->index(['user_id', 'course_id']);
            $table->index(['course_id', 'grade_type']);
            $table->index(['user_id', 'graded_at']);
            $table->index('is_published');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grades');
    }
};
