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
        Schema::table('student_profiles', function (Blueprint $table) {
            $table->timestamp('registration_fee_paid_at')->nullable()->after('admission_date');
            $table->timestamp('registration_deadline_at')->nullable()->after('registration_fee_paid_at');
            $table->timestamp('tuition_deadline_at')->nullable()->after('registration_deadline_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_profiles', function (Blueprint $table) {
            $table->dropColumn(['registration_fee_paid_at', 'registration_deadline_at', 'tuition_deadline_at']);
        });
    }
};
