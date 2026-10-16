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
        Schema::create('quality_reviews', function (Blueprint $table) {
                        $table->id();
            $table->foreignId('program_id')->nullable()->constrained('programs')->onDelete('cascade');
            $table->foreignId('dean_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->text('review_notes');
            $table->json('metrics')->nullable();
            $table->enum('status', ['Draft', 'Pending Registrar Approval', 'Approved', 'Rejected'])->default('Draft');
            $table->text('registrar_feedback')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quality_reviews');
    }
};
