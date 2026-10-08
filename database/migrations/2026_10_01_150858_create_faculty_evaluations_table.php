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
        Schema::create('faculty_evaluations', function (Blueprint $table) {
                        $table->id();
            $table->foreignId('faculty_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('evaluator_id')->nullable()->constrained('users')->onDelete('cascade'); // The Dean
            $table->string('academic_year');
            $table->integer('performance_score');
            $table->text('comments');
            $table->enum('status', ['Draft', 'Pending Approval', 'Approved', 'Rejected'])->default('Draft');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('faculty_evaluations');
    }
};
