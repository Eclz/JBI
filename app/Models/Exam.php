<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Exam extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'title',
        'description',
        'duration_minutes',
        'total_marks',
        'passing_marks',
        'start_time',
        'end_time',
        'required_payment',
        'exam_paper_url',
        'answer_booklet_url',
        'instructions',
        'allow_online_editor',
        'is_published',
        'exam_type',
        'exam_mode',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'is_published' => 'boolean',
        'allow_online_editor' => 'boolean',
        'total_marks' => 'decimal:2',
        'passing_marks' => 'decimal:2',
        'required_payment' => 'decimal:2',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function attempts()
    {
        return $this->hasMany(ExamAttempt::class);
    }

    public function isActive()
    {
        $now = Carbon::now();
        return $this->start_time <= $now && $this->end_time >= $now;
    }

    public function isUpcoming()
    {
        return $this->start_time > Carbon::now();
    }

    public function isPast()
    {
        return $this->end_time < Carbon::now();
    }

    public function canAttempt($userId)
    {
        if (!$this->isActive() || !$this->is_published) {
            return false;
        }

        $attempt = $this->attempts()->where('user_id', $userId)->first();

        if (!$attempt) {
            return true;
        }

        return $attempt->status === 'not_started';
    }

    public function studentAttempt($userId)
    {
        return $this->attempts()->where('user_id', $userId)->first();
    }
}
