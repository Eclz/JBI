<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VotingCandidate extends Model
{
    use HasFactory;

    protected $fillable = [
        'voting_session_id',
        'voting_position_id',
        'user_id',
        'name',
        'slogan',
        'photo',
        'manifesto',
        'party_affiliation',
        'cgpa',
        'year_of_study',
        'faculty_id',
        'supporting_documents',
        'application_status',
        'candidate_status',
        'vetting_score',
        'status',
        'vetted_at',
        'vetted_by',
        'vetting_notes',
        'votes_count',
    ];

    protected $casts = [
        'supporting_documents' => 'array',
        'vetted_at' => 'datetime',
        'cgpa' => 'float',
        'vetting_score' => 'float',
        'year_of_study' => 'integer',
        'votes_count' => 'integer',
    ];

    public function session()
    {
        return $this->belongsTo(VotingSession::class, 'voting_session_id');
    }

    public function position()
    {
        return $this->belongsTo(VotingPosition::class, 'voting_position_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function faculty()
    {
        return $this->belongsTo(Faculty::class);
    }

    public function vetter()
    {
        return $this->belongsTo(User::class, 'vetted_by');
    }

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }

    public function getIsApprovedAttribute(): bool
    {
        return $this->candidate_status === 'approved_candidate'
            || $this->candidate_status === 'elected_student_leader'
            || $this->application_status === 'vetted_approved'
            || $this->status === 'approved';
    }

    public function getIsElectedAttribute(): bool
    {
        return $this->candidate_status === 'elected_student_leader';
    }

    public function getApplicationStatusBadgeAttribute(): string
    {
        return match ($this->application_status) {
            'draft' => 'secondary',
            'submitted' => 'info',
            'under_review' => 'warning',
            'vetted_approved' => 'success',
            'rejected' => 'danger',
            'withdrawn' => 'dark',
            default => 'secondary',
        };
    }

    public function getCandidateStatusBadgeAttribute(): string
    {
        return match ($this->candidate_status) {
            'applicant' => 'info',
            'approved_candidate' => 'primary',
            'elected_student_leader' => 'success',
            'not_elected' => 'secondary',
            default => 'secondary',
        };
    }
}
