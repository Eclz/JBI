<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HrTrainingCourse extends Model
{
    protected $fillable = [
        'title',
        'provider',
        'duration_hours',
        'cost',
        'description',
        'status',
    ];

    public function enrollments()
    {
        return $this->hasMany(HrTrainingEnrollment::class, 'hr_training_course_id');
    }
}
