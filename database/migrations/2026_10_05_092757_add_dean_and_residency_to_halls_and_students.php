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
        Schema::table('hostels', function (Blueprint $table) {
            $table->foreignId('dean_id')->nullable()->constrained('users')->nullOnDelete();
        });

        Schema::table('student_profiles', function (Blueprint $table) {
            $table->foreignId('hostel_id')->nullable()->constrained('hostels')->nullOnDelete();
            $table->enum('residency_status', ['resident', 'non_resident'])->default('non_resident');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_profiles', function (Blueprint $table) {
            $table->dropForeign(['hostel_id']);
            $table->dropColumn(['hostel_id', 'residency_status']);
        });

        Schema::table('hostels', function (Blueprint $table) {
            $table->dropForeign(['dean_id']);
            $table->dropColumn('dean_id');
        });
    }
};
