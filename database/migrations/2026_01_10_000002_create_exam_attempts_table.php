<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('exam_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->dateTime('started_at')->nullable();
            $table->dateTime('submitted_at')->nullable();
            $table->integer('time_remaining_seconds')->nullable();
            $table->text('answers')->nullable(); // JSON storage
            $table->string('submission_file_url')->nullable();
            $table->decimal('marks_obtained', 8, 2)->nullable();
            $table->text('feedback')->nullable();
            $table->foreignId('graded_by')->nullable()->constrained('users');
            $table->dateTime('graded_at')->nullable();
            $table->enum('status', ['not_started', 'in_progress', 'submitted', 'graded'])->default('not_started');
            $table->boolean('payment_verified')->default(false);
            $table->decimal('payment_amount', 10, 2)->nullable();
            $table->timestamps();

            $table->unique(['exam_id', 'user_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('exam_attempts');
    }
};
