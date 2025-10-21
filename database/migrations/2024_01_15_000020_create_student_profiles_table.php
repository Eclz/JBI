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
        Schema::create('student_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('admission_number')->unique();
            $table->date('admission_date');
            $table->foreignId('department_id')->constrained()->onDelete('cascade');
            $table->string('program'); // Bachelor's, Master's, etc.
            $table->string('specialization')->nullable();
            $table->integer('current_semester')->default(1);
            $table->enum('status', ['pending', 'active', 'inactive', 'graduated', 'dropped', 'suspended'])->default('pending');
            $table->enum('application_status', ['submitted', 'under_review', 'approved', 'rejected'])->default('submitted');
            $table->decimal('current_gpa', 3, 2)->default(0.00);
            $table->decimal('cumulative_gpa', 3, 2)->default(0.00);
            $table->integer('total_credits_earned')->default(0);
            $table->integer('total_credits_required')->default(120);
            $table->date('expected_graduation_date')->nullable();
            $table->date('actual_graduation_date')->nullable();
            $table->string('guardian_name')->nullable();
            $table->string('guardian_phone')->nullable();
            $table->string('guardian_email')->nullable();
            $table->text('guardian_address')->nullable();
            $table->string('previous_school')->nullable();
            $table->string('previous_school_address')->nullable();
            $table->year('graduation_year')->nullable();
            $table->decimal('previous_gpa', 3, 2)->nullable();
            $table->json('academic_history')->nullable();
            $table->json('qualifications')->nullable();
            $table->json('achievements')->nullable();
            $table->json('documents')->nullable();
            $table->text('application_notes')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['department_id', 'status']);
            $table->index('admission_number');
            $table->index(['current_semester', 'status']);
            $table->index('application_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_profiles');
    }
};
