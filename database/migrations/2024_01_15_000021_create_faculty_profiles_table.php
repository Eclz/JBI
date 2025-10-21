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
        Schema::create('faculty_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('department_id')->constrained()->onDelete('cascade');
            $table->string('designation'); // Professor, Associate Professor, etc.
            $table->string('qualification');
            $table->text('specialization')->nullable();
            $table->date('joining_date');
            $table->enum('employment_type', ['full_time', 'part_time', 'contract', 'visiting'])->default('full_time');
            $table->decimal('salary', 10, 2)->nullable();
            $table->string('office_location')->nullable();
            $table->string('office_hours')->nullable();
            $table->text('research_interests')->nullable();
            $table->json('publications')->nullable();
            $table->json('certifications')->nullable();
            $table->integer('years_of_experience')->default(0);
            $table->text('bio')->nullable();
            $table->string('linkedin_profile')->nullable();
            $table->string('personal_website')->nullable();
            $table->enum('status', ['active', 'inactive', 'on_leave', 'retired'])->default('active');
            $table->timestamps();
            
            $table->index(['user_id', 'status']);
            $table->index(['department_id', 'status']);
            $table->index(['designation', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('faculty_profiles');
    }
};
