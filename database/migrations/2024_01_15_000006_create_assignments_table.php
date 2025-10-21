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
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->text('instructions')->nullable();
            $table->enum('type', ['homework', 'quiz', 'exam', 'project', 'essay', 'presentation'])->default('homework');
            $table->integer('max_points')->default(100);
            $table->decimal('weight_percentage', 5, 2)->default(0.00); // Weight in final grade
            $table->datetime('due_date');
            $table->datetime('available_from')->nullable();
            $table->datetime('available_until')->nullable();
            $table->boolean('allow_late_submission')->default(false);
            $table->integer('late_penalty_per_day')->default(0); // Percentage
            $table->json('allowed_file_types')->nullable(); // ['pdf', 'doc', 'docx']
            $table->integer('max_file_size')->default(10240); // KB
            $table->boolean('is_published')->default(false);
            $table->text('rubric')->nullable();
            $table->json('settings')->nullable(); // Additional settings
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['course_id', 'due_date']);
            $table->index(['course_id', 'is_published']);
            $table->index('due_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assignments');
    }
};
