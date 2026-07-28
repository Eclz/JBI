<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssignmentSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'assignment_id',
        'user_id',
        'submission_text',
        'submitted_files',
        'submitted_at',
        'is_late',
        'days_late',
        'score',
        'adjusted_score',
        'feedback',
        'rubric_scores',
        'status',
        'graded_at',
        'graded_by',
        'attempt_number',
    ];

    protected $casts = [
        'submitted_files' => 'array',
        'submitted_at' => 'datetime',
        'graded_at' => 'datetime',
        'is_late' => 'boolean',
        'score' => 'decimal:2',
        'adjusted_score' => 'decimal:2',
        'rubric_scores' => 'array',
    ];

    /**
     * Get the assignment
     */
    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
    }

    /**
     * Get the student who submitted
     */
    public function student()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the faculty who graded
     */
    public function grader()
    {
        return $this->belongsTo(User::class, 'graded_by');
    }

    /**
     * Get the grade record
     */
    public function grade()
    {
        return $this->hasOne(Grade::class, 'assignment_id', 'assignment_id')
            ->whereColumn('grades.user_id', 'assignment_submissions.user_id');
    }

    /**
     * Scope for graded submissions
     */
    public function scopeGraded($query)
    {
        return $query->whereNotNull('score');
    }

    /**
     * Scope for pending grading
     */
    public function scopePendingGrading($query)
    {
        return $query->whereNull('score')->where('status', 'submitted');
    }

    /**
     * Calculate if submission is late
     */
    public function calculateLateness()
    {
        if ($this->submitted_at > $this->assignment->due_date) {
            $this->is_late = true;
            $this->days_late = $this->submitted_at->diffInDays($this->assignment->due_date);
        }
    }
}
