<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add enrollment_type to course_enrollments
        if (Schema::hasTable('course_enrollments') && !Schema::hasColumn('course_enrollments', 'enrollment_type')) {
            Schema::table('course_enrollments', function (Blueprint $table) {
                $table->string('enrollment_type')->default('normal')->after('status'); // normal, retake, missed_paper
            });
        }

        // 2. Add is_enrollment_open to semesters
        if (Schema::hasTable('semesters') && !Schema::hasColumn('semesters', 'is_enrollment_open')) {
            Schema::table('semesters', function (Blueprint $table) {
                $table->boolean('is_enrollment_open')->default(true)->after('is_active');
            });
        }

        // 3. Add invoice_type & fee_type to fee_records
        if (Schema::hasTable('fee_records') && !Schema::hasColumn('fee_records', 'type')) {
            Schema::table('fee_records', function (Blueprint $table) {
                $table->string('type')->default('tuition')->after('amount'); // tuition, retake_fee, missed_paper_fee, registration
            });
        }

        // 4. Create messages table for mailbox system
        if (!Schema::hasTable('messages')) {
            Schema::create('messages', function (Blueprint $table) {
                $table->id();
                $table->foreignId('sender_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('receiver_id')->nullable()->constrained('users')->onDelete('cascade');
                $table->string('subject');
                $table->text('body');
                $table->string('type')->default('message'); // message, assignment_alert, quiz_alert, exam_alert, system
                $table->boolean('is_read')->default(false);
                $table->string('related_link')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');

        if (Schema::hasTable('course_enrollments') && Schema::hasColumn('course_enrollments', 'enrollment_type')) {
            Schema::table('course_enrollments', function (Blueprint $table) {
                $table->dropColumn('enrollment_type');
            });
        }

        if (Schema::hasTable('semesters') && Schema::hasColumn('semesters', 'is_enrollment_open')) {
            Schema::table('semesters', function (Blueprint $table) {
                $table->dropColumn('is_enrollment_open');
            });
        }

        if (Schema::hasTable('fee_records') && Schema::hasColumn('fee_records', 'type')) {
            Schema::table('fee_records', function (Blueprint $table) {
                $table->dropColumn('type');
            });
        }
    }
};
