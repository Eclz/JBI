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
        Schema::create('hr_successors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hr_succession_plan_id')->constrained('hr_succession_plans')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('readiness_level'); // Ready Now, 1-2 Years, 3-5 Years
            $table->text('development_needs')->nullable();
            $table->string('status')->default('Active'); // Active, Promoted, Removed
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hr_successors');
    }
};
