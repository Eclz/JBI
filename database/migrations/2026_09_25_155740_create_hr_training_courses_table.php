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
        Schema::create('hr_training_courses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('provider')->nullable();
            $table->decimal('duration_hours', 5, 1)->default(0);
            $table->decimal('cost', 10, 2)->default(0);
            $table->text('description')->nullable();
            $table->string('status')->default('Active'); // Active, Retired
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hr_training_courses');
    }
};
