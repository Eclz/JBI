<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HrOnboarding extends Model
{
    protected $fillable = [
        'user_id',
        'status',
        'hr_officer_id',
        'due_date',
        'completed_date',
        'progress_percentage',
        'template_name',
    ];

    protected $casts = [
        'due_date' => 'date',
        'completed_date' => 'date',
    ];

    public function getStatusAttribute($value): string
    {
        return $value !== 'Completed' && $this->due_date?->lt(today()) ? 'Overdue' : $value;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function hrOfficer()
    {
        return $this->belongsTo(User::class, 'hr_officer_id');
    }

    public function tasks()
    {
        return $this->hasMany(HrOnboardingTask::class);
    }
}
