<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VotingPosition extends Model
{
    use HasFactory;

    protected $fillable = [
        'voting_session_id',
        'title',
        'description',
        'scope',
        'faculty_id',
        'max_votes_per_voter',
        'requirements',
        'display_order',
    ];

    public function session()
    {
        return $this->belongsTo(VotingSession::class, 'voting_session_id');
    }

    public function faculty()
    {
        return $this->belongsTo(Faculty::class, 'faculty_id');
    }

    public function candidates()
    {
        return $this->hasMany(VotingCandidate::class);
    }

    public function approvedCandidates()
    {
        return $this->hasMany(VotingCandidate::class)
            ->where(function ($q) {
                $q->where('candidate_status', 'approved_candidate')
                  ->orWhere('candidate_status', 'elected_student_leader')
                  ->orWhere('application_status', 'vetted_approved')
                  ->orWhere('status', 'approved');
            });
    }

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }

    public function isEligibleForStudent(User $student): bool
    {
        if ($this->scope === 'university_wide' || !$this->faculty_id) {
            return true;
        }

        $studentProfile = $student->studentProfile;
        if (!$studentProfile) {
            return false;
        }

        // Check if student profile has direct faculty_id or through department
        if ($studentProfile->department && $studentProfile->department->faculty_id) {
            return (int) $studentProfile->department->faculty_id === (int) $this->faculty_id;
        }

        return false;
    }

    public function getWinningCandidateAttribute(): ?VotingCandidate
    {
        return $this->approvedCandidates()
            ->withCount('votes')
            ->orderByDesc('votes_count')
            ->first();
    }
}
