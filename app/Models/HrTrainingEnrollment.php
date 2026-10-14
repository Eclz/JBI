<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HrTrainingEnrollment extends Model
{
    protected $fillable = [
        'user_id',
        'hr_training_course_id',
        'enrollment_date',
        'completion_date',
        'status',
    ];

    protected $casts = [
        'enrollment_date' => 'date',
        'completion_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(HrTrainingCourse::class, 'hr_training_course_id');
    }
}
