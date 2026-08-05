<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evaluation_surveys', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('academic_year_id')->nullable()->constrained('academic_years')->onDelete('set null');
            $table->integer('semester_number')->default(1);
            $table->boolean('is_active')->default(true);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->timestamps();
        });

        Schema::create('evaluation_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_id')->constrained('evaluation_surveys')->onDelete('cascade');
            $table->string('question_text');
            $table->string('category')->default('Teaching Quality'); // e.g. Teaching Quality, Punctuality, Course Material
            $table->enum('question_type', ['rating', 'text', 'boolean'])->default('rating');
            $table->integer('display_order')->default(1);
            $table->timestamps();
        });

        Schema::create('evaluation_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_id')->constrained('evaluation_surveys')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
            $table->foreignId('lecturer_id')->nullable()->constrained('users')->onDelete('set null');
            $table->json('answers');
            $table->text('comments')->nullable();
            $table->timestamp('submitted_at')->useCurrent();
            $table->timestamps();

            $table->unique(['survey_id', 'student_id', 'course_id'], 'unique_student_course_evaluation');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluation_responses');
        Schema::dropIfExists('evaluation_questions');
        Schema::dropIfExists('evaluation_surveys');
    }
};
