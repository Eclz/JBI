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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // e.g., THEO301
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('credits')->default(3);
            $table->foreignId('department_id')->constrained()->onDelete('cascade');
            $table->foreignId('instructor_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('semester_id')->constrained()->onDelete('cascade');
            $table->json('schedule')->nullable(); // Day/time schedule
            $table->string('room')->nullable();
            $table->integer('max_students')->default(30);
            $table->enum('status', ['active', 'inactive', 'completed', 'cancelled'])->default('active');
            $table->string('syllabus_file')->nullable();
            $table->json('prerequisites')->nullable(); // Course IDs that are prerequisites
            $table->decimal('fee_amount', 10, 2)->nullable();
            $table->text('learning_objectives')->nullable();
            $table->text('assessment_methods')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['code', 'semester_id']);
            $table->index(['instructor_id', 'status']);
            $table->index(['department_id', 'status']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
