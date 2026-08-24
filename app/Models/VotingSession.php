<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VotingSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'target_semester',
        'academic_year_id',
        'status',
        'application_start_at',
        'application_end_at',
        'vetting_start_at',
        'vetting_end_at',
        'start_time',
        'end_time',
        'results_published_at',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'application_start_at' => 'datetime',
        'application_end_at' => 'datetime',
        'vetting_start_at' => 'datetime',
        'vetting_end_at' => 'datetime',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'results_published_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function positions()
    {
        return $this->hasMany(VotingPosition::class)->orderBy('display_order');
    }

    public function candidates()
    {
        return $this->hasMany(VotingCandidate::class);
    }

    public function commissionMembers()
    {
        return $this->hasMany(ElectoralCommissionMember::class);
    }

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'Draft / Setup',
            'applications_open' => 'Applications Open',
            'vetting' => 'Candidate Vetting',
            'voting_scheduled' => 'Voting Scheduled',
            'voting_open' => 'Voting in Progress',
            'voting_closed' => 'Voting Closed',
            'results_under_review' => 'Results Under Review',
            'results_published' => 'Results Published',
            'completed' => 'Election Completed',
            default => ucfirst(str_replace('_', ' ', $this->status ?? 'draft')),
        };
    }

    public function getStatusBadgeColorAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'secondary',
            'applications_open' => 'info',
            'vetting' => 'warning',
            'voting_scheduled' => 'primary',
            'voting_open' => 'success',
            'voting_closed' => 'dark',
            'results_under_review' => 'warning',
            'results_published' => 'success',
            'completed' => 'secondary',
            default => 'secondary',
        };
    }

    public function getIsApplicationOpenAttribute(): bool
    {
        if ($this->status === 'applications_open') {
            return true;
        }

        if (!$this->application_start_at || !$this->application_end_at) {
            return false;
        }

        $now = now();
        return $now->gte($this->application_start_at) && $now->lte($this->application_end_at);
    }

    public function getIsVettingOpenAttribute(): bool
    {
        if ($this->status === 'vetting') {
            return true;
        }

        if (!$this->vetting_start_at || !$this->vetting_end_at) {
            return false;
        }

        $now = now();
        return $now->gte($this->vetting_start_at) && $now->lte($this->vetting_end_at);
    }

    public function getIsVotingOpenAttribute(): bool
    {
        if ($this->status === 'voting_open') {
            if ($this->start_time && $this->end_time) {
                $now = now();
                return $now->gte($this->start_time) && $now->lte($this->end_time);
            }
            return true;
        }

        if ($this->status === 'voting_scheduled' && $this->start_time && $this->end_time) {
            $now = now();
            return $now->gte($this->start_time) && $now->lte($this->end_time);
        }

        return false;
    }

    public function getIsResultsPublishedAttribute(): bool
    {
        return in_array($this->status, ['results_published', 'completed']) || !is_null($this->results_published_at);
    }

    public function getTotalVotersCountAttribute(): int
    {
        return $this->votes()->distinct('user_id')->count('user_id');
    }
}
