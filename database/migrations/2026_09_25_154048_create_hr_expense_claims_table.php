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
        Schema::create('hr_expense_claims', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->date('claim_date');
            $table->string('category'); // Travel, Meals, Supplies, Other
            $table->decimal('amount', 10, 2);
            $table->string('currency')->default('USD');
            $table->text('description');
            $table->string('receipt_path')->nullable();
            $table->string('status')->default('Pending'); // Pending, Approved, Rejected, Paid
            $table->foreignId('approved_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hr_expense_claims');
    }
};
