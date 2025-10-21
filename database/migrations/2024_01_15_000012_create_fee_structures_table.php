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
        Schema::create('fee_structures', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "Tuition Fee", "Library Fee"
            $table->text('description')->nullable();
            $table->enum('type', ['tuition', 'library', 'laboratory', 'technology', 'activity', 'other'])->default('other');
            $table->decimal('amount', 10, 2);
            $table->enum('frequency', ['one_time', 'semester', 'monthly', 'annual'])->default('semester');
            $table->foreignId('academic_year_id')->constrained()->onDelete('cascade');
            $table->foreignId('semester_id')->nullable()->constrained()->onDelete('cascade');
            $table->json('applicable_to')->nullable(); // Roles, departments, courses
            $table->boolean('is_mandatory')->default(true);
            $table->boolean('is_active')->default(true);
            $table->date('due_date')->nullable();
            $table->decimal('late_fee_amount', 10, 2)->default(0);
            $table->integer('late_fee_days')->default(0);
            $table->timestamps();
            
            $table->index(['academic_year_id', 'is_active']);
            $table->index(['semester_id', 'is_active']);
            $table->index(['type', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fee_structures');
    }
};
