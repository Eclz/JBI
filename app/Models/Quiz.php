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
        'show_correct_answers',
        'is_published',
        'quiz_type',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'shuffle_questions' => 'boolean',
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

    public function isActive()
    {
        $now = Carbon::now();
        return (!$this->start_time || $this->start_time <= $now) &&
               (!$this->end_time || $this->end_time >= $now);
    }

    public function canAttempt($userId)
    {
        if (!$this->isActive()) {
            return false;
        }

        $attempts = $this->attempts()->where('user_id', $userId)
            ->whereIn('status', ['submitted', 'graded'])
            ->count();

        return $attempts < $this->max_attempts;
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
