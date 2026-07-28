<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LearningProgress extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'course_id',
        'content_type',
        'content_id',
        'read_at',
        'video_watched_seconds',
        'video_duration_seconds',
        'last_video_position_seconds',
        'is_video_completed',
        'completed_at',
        'last_accessed_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
        'video_watched_seconds' => 'integer',
        'video_duration_seconds' => 'integer',
        'last_video_position_seconds' => 'integer',
        'is_video_completed' => 'boolean',
        'completed_at' => 'datetime',
        'last_accessed_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
