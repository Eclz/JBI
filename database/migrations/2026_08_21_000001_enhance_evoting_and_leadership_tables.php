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
            if (!Schema::hasColumn('voting_sessions', 'status')) {
                $table->string('status')->default('draft')->after('is_active');
            }
            if (!Schema::hasColumn('voting_sessions', 'application_start_at')) {
                $table->dateTime('application_start_at')->nullable()->after('status');
            }
            if (!Schema::hasColumn('voting_sessions', 'application_end_at')) {
                $table->dateTime('application_end_at')->nullable()->after('application_start_at');
            }
            if (!Schema::hasColumn('voting_sessions', 'results_published_at')) {
                $table->dateTime('results_published_at')->nullable()->after('end_time');
            }
            if (!Schema::hasColumn('voting_sessions', 'created_by')) {
                $table->foreignId('created_by')->nullable()->after('results_published_at')->constrained('users')->nullOnDelete();
            }
        });

        Schema::table('voting_positions', function (Blueprint $table) {
            if (!Schema::hasColumn('voting_positions', 'scope')) {
                $table->string('scope')->default('university_wide')->after('description'); // university_wide, faculty_specific
            }
            if (!Schema::hasColumn('voting_positions', 'faculty_id')) {
                $table->foreignId('faculty_id')->nullable()->after('scope')->constrained('faculties')->nullOnDelete();
            }
            if (!Schema::hasColumn('voting_positions', 'max_votes_per_voter')) {
                $table->integer('max_votes_per_voter')->default(1)->after('faculty_id');
            }
            if (!Schema::hasColumn('voting_positions', 'requirements')) {
                $table->text('requirements')->nullable()->after('max_votes_per_voter');
            }
        });

        Schema::table('voting_candidates', function (Blueprint $table) {
            if (!Schema::hasColumn('voting_candidates', 'voting_session_id')) {
                $table->foreignId('voting_session_id')->nullable()->after('id')->constrained('voting_sessions')->cascadeOnDelete();
            }
            if (!Schema::hasColumn('voting_candidates', 'slogan')) {
                $table->string('slogan')->nullable()->after('name');
            }
            if (!Schema::hasColumn('voting_candidates', 'cgpa')) {
                $table->decimal('cgpa', 4, 2)->nullable()->after('slogan');
            }
            if (!Schema::hasColumn('voting_candidates', 'year_of_study')) {
                $table->integer('year_of_study')->nullable()->after('cgpa');
            }
            if (!Schema::hasColumn('voting_candidates', 'faculty_id')) {
                $table->foreignId('faculty_id')->nullable()->after('year_of_study')->constrained('faculties')->nullOnDelete();
            }
            if (!Schema::hasColumn('voting_candidates', 'supporting_documents')) {
                $table->json('supporting_documents')->nullable()->after('manifesto');
            }
            if (!Schema::hasColumn('voting_candidates', 'application_status')) {
                $table->string('application_status')->default('submitted')->after('supporting_documents'); // draft, submitted, under_review, vetted_approved, rejected, withdrawn
            }
            if (!Schema::hasColumn('voting_candidates', 'candidate_status')) {
                $table->string('candidate_status')->default('applicant')->after('application_status'); // applicant, approved_candidate, elected_student_leader, not_elected
            }
            if (!Schema::hasColumn('voting_candidates', 'vetting_score')) {
                $table->decimal('vetting_score', 5, 2)->nullable()->after('candidate_status');
            }
            if (!Schema::hasColumn('voting_candidates', 'vetted_by')) {
                $table->foreignId('vetted_by')->nullable()->after('vetting_notes')->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('voting_candidates', 'votes_count')) {
                $table->integer('votes_count')->default(0)->after('vetted_by');
            }
        });

        if (!Schema::hasTable('electoral_commission_members')) {
            Schema::create('electoral_commission_members', function (Blueprint $table) {
                $table->id();
                $table->foreignId('voting_session_id')->constrained('voting_sessions')->cascadeOnDelete();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->string('role_title')->default('Commissioner'); // Chairperson, Vice Chairperson, Returning Officer, Secretary, Commissioner, Vetting Officer
                $table->dateTime('appointed_at')->nullable();
                $table->text('notes')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->unique(['voting_session_id', 'user_id'], 'unique_session_commission_member');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('electoral_commission_members');

        Schema::table('voting_candidates', function (Blueprint $table) {
            $table->dropForeign(['voting_session_id']);
            $table->dropForeign(['faculty_id']);
            $table->dropForeign(['vetted_by']);
            $table->dropColumn([
                'voting_session_id',
                'slogan',
                'cgpa',
                'year_of_study',
                'faculty_id',
                'supporting_documents',
                'application_status',
                'candidate_status',
                'vetting_score',
                'vetted_by',
                'votes_count',
            ]);
        });

        Schema::table('voting_positions', function (Blueprint $table) {
            $table->dropForeign(['faculty_id']);
            $table->dropColumn(['scope', 'faculty_id', 'max_votes_per_voter', 'requirements']);
        });

        Schema::table('voting_sessions', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropColumn([
                'status',
                'application_start_at',
                'application_end_at',
                'results_published_at',
                'created_by',
            ]);
        });
    }
};
