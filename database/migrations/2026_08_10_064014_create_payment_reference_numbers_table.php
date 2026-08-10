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
        Schema::create('payment_reference_numbers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('fee_record_id')->nullable()->constrained('fee_records')->onDelete('cascade');
            $table->foreignId('fee_structure_id')->nullable()->constrained('fee_structures')->onDelete('set null');
            $table->string('prn_number')->unique();
            $table->string('fee_item_name');
            $table->decimal('amount', 12, 2);
            $table->string('payment_type')->default('full'); // full or partial
            $table->string('status')->default('pending'); // pending, paid, expired
            $table->timestamp('generated_at')->useCurrent();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->string('transaction_reference')->nullable();
            $table->string('payment_method')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_reference_numbers');
    }
};
