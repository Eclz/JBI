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
        Schema::create('assignment_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('submission_text')->nullable();
            $table->json('submitted_files')->nullable(); // Array of file paths
            $table->datetime('submitted_at');
            $table->boolean('is_late')->default(false);
            $table->integer('days_late')->default(0);
            $table->decimal('score', 5, 2)->nullable();
            $table->decimal('adjusted_score', 5, 2)->nullable(); // After late penalties
            $table->text('feedback')->nullable();
            $table->json('rubric_scores')->nullable(); // Detailed rubric scoring
            $table->enum('status', ['submitted', 'graded', 'returned', 'resubmitted'])->default('submitted');
            $table->datetime('graded_at')->nullable();
            $table->foreignId('graded_by')->nullable()->constrained('users')->onDelete('set null');
            $table->integer('attempt_number')->default(1);
            $table->timestamps();

            // Custom unique constraint name (e.g., 'uniq_assn_subm_id_user_atnum')
            $table->unique(['assignment_id', 'user_id', 'attempt_number'], 'uniq_assn_subm_id_user_atnum');
            $table->index(['assignment_id', 'status']);
            $table->index(['user_id', 'submitted_at']);
            $table->index('submitted_at');
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('assignment_submissions');
    }
};
