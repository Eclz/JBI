<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('voting_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('target_semester')->default(2); // e.g. semester 2 as required by prompt
            $table->foreignId('academic_year_id')->nullable()->constrained('academic_years')->onDelete('set null');
            $table->dateTime('start_time')->nullable();
            $table->dateTime('end_time')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('voting_positions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('voting_session_id')->constrained('voting_sessions')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('display_order')->default(1);
            $table->timestamps();
        });

        Schema::create('voting_candidates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('voting_position_id')->constrained('voting_positions')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('name');
            $table->string('photo')->nullable();
            $table->text('manifesto')->nullable();
            $table->string('party_affiliation')->nullable();
            $table->timestamps();
        });

        Schema::create('votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('voting_session_id')->constrained('voting_sessions')->onDelete('cascade');
            $table->foreignId('voting_position_id')->constrained('voting_positions')->onDelete('cascade');
            $table->foreignId('voting_candidate_id')->constrained('voting_candidates')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('ip_address')->nullable();
            $table->timestamps();

            // Prevent double voting per position per student
            $table->unique(['voting_session_id', 'voting_position_id', 'user_id'], 'unique_user_position_vote');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('votes');
        Schema::dropIfExists('voting_candidates');
        Schema::dropIfExists('voting_positions');
        Schema::dropIfExists('voting_sessions');
    }
};
