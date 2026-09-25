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
        Schema::create('facility_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('facility_room_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('purpose');
            $table->string('booking_type')->default('Room Booking'); // Room Booking, Maintenance
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->string('status')->default('Pending'); // Pending, Approved, Assigned, In Progress, Completed, Cancelled
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facility_bookings');
    }
};
