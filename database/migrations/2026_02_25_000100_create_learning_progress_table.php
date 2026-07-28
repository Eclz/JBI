<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('learning_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->enum('content_type', ['material', 'assignment', 'quiz', 'exam']);
            $table->unsignedBigInteger('content_id');
            $table->timestamp('read_at')->nullable();
            $table->unsignedInteger('video_watched_seconds')->default(0);
            $table->unsignedInteger('video_duration_seconds')->default(0);
            $table->unsignedInteger('last_video_position_seconds')->default(0);
            $table->boolean('is_video_completed')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('last_accessed_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'course_id', 'content_type', 'content_id'], 'learning_progress_unique_content');
            $table->index(['course_id', 'user_id']);
            $table->index(['content_type', 'content_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('learning_progress');
    }
};
