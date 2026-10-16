<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentIssue extends Model
{
    protected $fillable = [
        'student_id',
        'reported_by',
        'issue_type',
        'description',
        'action_taken',
        'status',
    ];

    public function student()
    {
        return $this->belongsTo(\App\Models\User::class, 'student_id');
    }

    public function reporter()
    {
        return $this->belongsTo(\App\Models\User::class, 'reported_by');
    }
}
