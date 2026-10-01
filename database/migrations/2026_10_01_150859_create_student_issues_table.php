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
        Schema::create('student_issues', function (Blueprint $table) {
                        $table->id();
            $table->foreignId('student_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('reported_by')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('issue_type'); // Academic, Disciplinary, Welfare
            $table->text('description');
            $table->text('action_taken')->nullable();
            $table->enum('status', ['Open', 'In Progress', 'Resolved', 'Escalated'])->default('Open');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_issues');
    }
};
