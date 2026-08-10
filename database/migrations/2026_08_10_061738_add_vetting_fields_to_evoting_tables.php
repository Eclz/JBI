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
        Schema::table('voting_sessions', function (Blueprint $table) {
            if (!Schema::hasColumn('voting_sessions', 'vetting_start_at')) {
                $table->dateTime('vetting_start_at')->nullable()->after('end_time');
            }
            if (!Schema::hasColumn('voting_sessions', 'vetting_end_at')) {
                $table->dateTime('vetting_end_at')->nullable()->after('vetting_start_at');
            }
        });

        Schema::table('voting_candidates', function (Blueprint $table) {
            if (!Schema::hasColumn('voting_candidates', 'status')) {
                $table->string('status')->default('approved')->after('party_affiliation');
            }
            if (!Schema::hasColumn('voting_candidates', 'vetted_at')) {
                $table->dateTime('vetted_at')->nullable()->after('status');
            }
            if (!Schema::hasColumn('voting_candidates', 'vetting_notes')) {
                $table->text('vetting_notes')->nullable()->after('vetted_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('voting_sessions', function (Blueprint $table) {
            if (Schema::hasColumn('voting_sessions', 'vetting_start_at')) {
                $table->dropColumn(['vetting_start_at', 'vetting_end_at']);
            }
        });

        Schema::table('voting_candidates', function (Blueprint $table) {
            if (Schema::hasColumn('voting_candidates', 'status')) {
                $table->dropColumn(['status', 'vetted_at', 'vetting_notes']);
            }
        });
    }
};
