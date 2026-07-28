<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_id',
        'user_id',
        'started_at',
        'submitted_at',
        'submission_count',
        'time_remaining_seconds',
        'answers',
        'submission_file_url',
        'marks_obtained',
        'feedback',
        'graded_by',
        'graded_at',
        'status',
        'payment_verified',
        'payment_amount',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'submitted_at' => 'datetime',
        'submission_count' => 'integer',
        'graded_at' => 'datetime',
        'payment_verified' => 'boolean',
        'marks_obtained' => 'decimal:2',
        'payment_amount' => 'decimal:2',
    ];

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function grader()
    {
        return $this->belongsTo(User::class, 'graded_by');
    }

    public function getAnswersArrayAttribute()
    {
        return json_decode($this->answers, true) ?? [];
    }

    public function setAnswersArrayAttribute($value)
    {
        $this->attributes['answers'] = json_encode($value);
    }

    public function isInProgress()
    {
        return $this->status === 'in_progress';
    }

    public function isSubmitted()
    {
        return in_array($this->status, ['submitted', 'graded']);
    }

    public function getPercentageAttribute()
    {
        if (!$this->marks_obtained || !$this->exam) {
            return 0;
        }

        return ($this->marks_obtained / $this->exam->total_marks) * 100;
    }

    public function hasPassed()
    {
        if (!$this->exam || !$this->marks_obtained) {
            return false;
        }

        return $this->marks_obtained >= $this->exam->passing_marks;
    }
}
