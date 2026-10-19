<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Quiz extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'course_id',
        'created_by',
        'title',
        'description',
        'duration_minutes',
        'total_marks',
        'passing_marks',
        'start_time',
        'end_time',
        'max_attempts',
        'shuffle_questions',
        'shuffle_options',
        'show_results',
        'show_correct_answers',
        'is_published',
        'quiz_type',
        'status',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'shuffle_questions' => 'boolean',
        'shuffle_options' => 'boolean',
        'show_correct_answers' => 'boolean',
        'is_published' => 'boolean',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function questions()
    {
        return $this->hasMany(QuizQuestion::class)->orderBy('order');
    }

    public function attempts()
    {
        return $this->hasMany(QuizAttempt::class);
    }

    public function getDurationAttribute()
    {
        return $this->duration_minutes ?: 30;
    }

    public function getPassingPercentageAttribute()
    {
        if ($this->total_marks > 0 && $this->passing_marks !== null) {
            return round(($this->passing_marks / $this->total_marks) * 100, 1);
        }
        return 50;
    }

    public function getEffectiveStatusAttribute(): string
    {
        if ($this->status === 'closed') {
            return 'closed';
        }
        if ($this->status === 'draft' || !$this->is_published) {
            return 'draft';
        }
        if ($this->status === 'active') {
            return 'active';
        }

        $now = Carbon::now();
        if ($this->start_time && $now->lt($this->start_time)) {
            return 'upcoming';
        }
        if ($this->end_time && $now->gt($this->end_time)) {
            return 'closed';
        }

        return 'active';
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->effective_status) {
            'active' => 'success',
            'upcoming' => 'info',
            'draft' => 'secondary',
            'closed' => 'danger',
            default => 'secondary',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->effective_status) {
            'active' => 'Active / Open',
            'upcoming' => 'Upcoming',
            'draft' => 'Draft / Unpublished',
            'closed' => 'Closed',
            default => ucfirst($this->effective_status),
        };
    }

    public function isActive(): bool
    {
        if ($this->status === 'closed' || $this->status === 'draft' || !$this->is_published) {
            return false;
        }

        if ($this->status === 'active') {
            return true;
        }

        $now = Carbon::now();
        return (!$this->start_time || $this->start_time <= $now) &&
               (!$this->end_time || $this->end_time >= $now);
    }

    public function canAttempt($userId): bool
    {
        if (!$this->isActive()) {
            return false;
        }

        if (!$this->max_attempts || $this->max_attempts <= 0) {
            return true;
        }

        $completedAttempts = $this->attempts()->where('user_id', $userId)
            ->whereIn('status', ['submitted', 'graded'])
            ->count();

        return $completedAttempts < $this->max_attempts;
    }

    public function close(): bool
    {
        $this->status = 'closed';
        if ($this->end_time && $this->end_time->isFuture()) {
            $this->end_time = Carbon::now();
        }
        return $this->save();
    }

    public function reopen($extendDays = 7): bool
    {
        $this->status = 'active';
        $this->is_published = true;
        if (!$this->end_time || $this->end_time->isPast()) {
            $this->end_time = Carbon::now()->addDays($extendDays);
        }
        if ($this->start_time && $this->start_time->isFuture()) {
            $this->start_time = Carbon::now();
        }
        return $this->save();
    }

    public function studentAttempts($userId)
    {
        return $this->attempts()->where('user_id', $userId)->get();
    }

    public function bestAttempt($userId)
    {
        return $this->attempts()
            ->where('user_id', $userId)
            ->whereIn('status', ['submitted', 'graded'])
            ->orderBy('score', 'desc')
            ->first();
    }
}
