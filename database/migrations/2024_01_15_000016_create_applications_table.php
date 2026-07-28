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
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->string('application_number')->unique();
            $table->enum('type', ['student', 'faculty'])->default('student');

            // Personal Information
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('phone');
            $table->date('date_of_birth');
            $table->enum('gender', ['male', 'female', 'other']);
            $table->text('address');

            // For Student Applications
            $table->string('program')->nullable();
            $table->string('previous_school')->nullable();
            $table->string('previous_qualification')->nullable();
            $table->decimal('previous_gpa', 3, 2)->nullable();

            // For Faculty Applications
            $table->string('department')->nullable();
            $table->string('position')->nullable();
            $table->string('highest_degree')->nullable();
            $table->string('specialization')->nullable();
            $table->integer('years_of_experience')->nullable();

            // Documents
            $table->json('documents')->nullable(); // Store file paths as JSON

            // Application Status
            $table->enum('status', ['pending', 'under_review', 'approved', 'rejected'])->default('pending');
            $table->text('review_notes')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('reviewed_at')->nullable();

            $table->timestamps();

            $table->index('application_number');
            $table->index('type');
            $table->index('status');
            $table->index('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
