<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->enum('payment_status', ['pending', 'uploaded', 'verified', 'rejected'])->default('pending')->after('status');
            $table->string('payment_proof')->nullable()->after('payment_status');
            $table->timestamp('payment_uploaded_at')->nullable()->after('payment_proof');
            $table->foreignId('payment_verified_by')->nullable()->constrained('users')->onDelete('set null')->after('payment_uploaded_at');
            $table->timestamp('payment_verified_at')->nullable()->after('payment_verified_by');
            $table->text('payment_notes')->nullable()->after('payment_verified_at');

            $table->string('admission_number')->nullable()->unique()->after('payment_notes');
            $table->string('student_number')->nullable()->unique()->after('admission_number');
            $table->timestamp('admitted_at')->nullable()->after('student_number');
        });
    }

    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn([
                'payment_status',
                'payment_proof',
                'payment_uploaded_at',
                'payment_verified_by',
                'payment_verified_at',
                'payment_notes',
                'admission_number',
                'student_number',
                'admitted_at'
            ]);
        });
    }
};
