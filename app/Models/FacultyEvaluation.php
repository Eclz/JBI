<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FacultyEvaluation extends Model
{
    protected $fillable = [
        'faculty_id',
        'evaluator_id',
        'academic_year',
        'performance_score',
        'comments',
        'status',
    ];

    public function faculty()
    {
        return $this->belongsTo(\App\Models\User::class, 'faculty_id');
    }

    public function evaluator()
    {
        return $this->belongsTo(\App\Models\User::class, 'evaluator_id');
    }
}
