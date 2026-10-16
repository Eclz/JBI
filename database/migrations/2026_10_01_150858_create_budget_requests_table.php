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
        Schema::create('budget_requests', function (Blueprint $table) {
                        $table->id();
            $table->foreignId('department_id')->nullable()->constrained('departments')->onDelete('cascade');
            $table->foreignId('requested_by')->nullable()->constrained('users')->onDelete('cascade'); // The Dean
            $table->string('title');
            $table->text('description');
            $table->decimal('amount', 15, 2);
            $table->enum('status', ['Draft', 'Pending Finance Approval', 'Approved', 'Rejected'])->default('Draft');
            $table->text('finance_notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('budget_requests');
    }
};
