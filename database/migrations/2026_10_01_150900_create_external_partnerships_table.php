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
        Schema::create('external_partnerships', function (Blueprint $table) {
                        $table->id();
            $table->foreignId('managed_by')->nullable()->constrained('users')->onDelete('set null'); // The Dean
            $table->string('organization_name');
            $table->string('partnership_type'); // Industry, Donor, Alumni
            $table->text('objectives');
            $table->decimal('funding_amount', 15, 2)->default(0);
            $table->enum('status', ['Active', 'Pending', 'Concluded'])->default('Pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('external_partnerships');
    }
};
