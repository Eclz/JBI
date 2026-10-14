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
        Schema::create('hr_succession_plans', function (Blueprint $table) {
            $table->id();
            $table->string('position_name');
            $table->string('department')->nullable();
            $table->foreignId('current_holder_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status')->default('Active'); // Active, Closed
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hr_succession_plans');
    }
};
