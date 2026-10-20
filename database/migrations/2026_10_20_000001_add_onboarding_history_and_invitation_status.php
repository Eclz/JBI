<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('setup_email_sent_at')->nullable();
            $table->text('setup_email_error')->nullable();
        });
        Schema::create('application_revisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained()->cascadeOnDelete();
            $table->foreignId('submitted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->json('snapshot');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('application_revisions');
        Schema::table('users', fn (Blueprint $table) => $table->dropColumn(['setup_email_sent_at', 'setup_email_error']));
    }
};
