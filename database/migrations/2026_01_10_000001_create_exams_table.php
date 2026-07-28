<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('duration_minutes');
            $table->decimal('total_marks', 8, 2);
            $table->decimal('passing_marks', 8, 2);
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->decimal('required_payment', 10, 2)->default(0);
            $table->string('exam_paper_url')->nullable();
            $table->string('answer_booklet_url')->nullable();
            $table->text('instructions')->nullable();
            $table->boolean('allow_online_editor')->default(true);
            $table->boolean('is_published')->default(false);
            $table->enum('exam_type', ['midterm', 'final', 'quiz', 'assignment'])->default('midterm');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('exams');
    }
};
