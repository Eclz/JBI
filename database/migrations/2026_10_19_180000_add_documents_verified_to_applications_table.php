<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->timestamp('documents_verified_at')->nullable()->after('documents');
            $table->foreignId('documents_verified_by')->nullable()->after('documents_verified_at')->constrained('users')->nullOnDelete();
            $table->text('documents_verified_notes')->nullable()->after('documents_verified_by');
        });
    }

    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropForeign(['documents_verified_by']);
            $table->dropColumn(['documents_verified_at', 'documents_verified_by', 'documents_verified_notes']);
        });
    }
};
