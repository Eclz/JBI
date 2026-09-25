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
        Schema::create('hr_applicants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hr_vacancy_id')->constrained('hr_vacancies')->cascadeOnDelete();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('cv_path')->nullable();
            $table->text('cover_letter')->nullable();
            $table->string('status')->default('Applied'); // Applied, Screening, Shortlisted, Interview, Assessment, Reference Check, Offer, Hired, Rejected
            $table->foreignId('hired_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hr_applicants');
    }
};
