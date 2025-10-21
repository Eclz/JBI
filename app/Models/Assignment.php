<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Assignment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'course_id',
        'title',
        'description',
        'instructions',
        'type',
        'max_points',
        'weight_percentage',
        'due_date',
        'available_from',
        'available_until',
        'allow_late_submission',
        'late_penalty_per_day',
        'allowed_file_types',
        'max_file_size',
        'is_published',
        'rubric',
        'settings',
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'available_from' => 'datetime',
        'available_until' => 'datetime',
        'allow_late_submission' => 'boolean',
        'is_published' => 'boolean',
        'allowed_file_types' => 'array',
        'settings' => 'array',
        'weight_percentage' => 'decimal:2',
    ];

    /**
     * Get the course this assignment belongs to
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get all submissions for this assignment
     */
    public function submissions()
    {
        return $this->hasMany(AssignmentSubmission::class);
    }

    /**
     * Get submitted submissions
     */
    public function submittedSubmissions()
    {
        return $this->hasMany(AssignmentSubmission::class)->where('status', '!=', 'draft');
    }

    /**
     * Get graded submissions
     */
    public function gradedSubmissions()
    {
        return $this->hasMany(AssignmentSubmission::class)->whereNotNull('score');
    }

    /**
     * Get grades for this assignment
     */
    public function grades()
    {
        return $this->hasMany(Grade::class);
    }

    /**
     * Scope for published assignments
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    /**
     * Scope for available assignments
     */
    public function scopeAvailable($query)
    {
        $now = now();
        return $query->where('is_published', true)
                    ->where(function($q) use ($now) {
                        $q->whereNull('available_from')
                          ->orWhere('available_from', '<=', $now);
                    })
                    ->where(function($q) use ($now) {
                        $q->whereNull('available_until')
                          ->orWhere('available_until', '>=', $now);
                    });
    }

    /**
     * Check if assignment is overdue
     */
    public function getIsOverdueAttribute()
    {
        return now() > $this->due_date;
    }

    /**
     * Get submission for a specific user
     */
    public function getSubmissionForUser($userId)
    {
        return $this->submissions()->where('user_id', $userId)->latest()->first();
    }
}
