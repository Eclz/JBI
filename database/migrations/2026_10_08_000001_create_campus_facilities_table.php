<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campus_facilities', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('location')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::table('facility_rooms', function (Blueprint $table) {
            $table->foreignId('campus_facility_id')
                ->nullable()
                ->after('building')
                ->constrained('campus_facilities')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('facility_rooms', function (Blueprint $table) {
            $table->dropConstrainedForeignId('campus_facility_id');
        });

        Schema::dropIfExists('campus_facilities');
    }
};
