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
        Schema::create('hr_vacancies', function (Blueprint $table) {
            $table->id();
            $table->string('position_title');
            $table->string('department')->nullable();
            $table->text('job_description')->nullable();
            $table->text('requirements')->nullable();
            $table->integer('num_openings')->default(1);
            $table->string('employment_type')->default('Full-time');
            $table->string('location')->nullable();
            $table->string('salary_range')->nullable();
            $table->foreignId('hiring_manager_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('opening_date')->nullable();
            $table->date('closing_date')->nullable();
            $table->string('status')->default('Open'); // Open, Closed, Draft
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hr_vacancies');
    }
};
