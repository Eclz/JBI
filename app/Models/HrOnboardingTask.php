<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HrOnboardingTask extends Model
{
    protected $fillable = [
        'hr_onboarding_id',
        'task_name',
        'assigned_to',
        'due_date',
        'status',
        'completed_date',
        'comments',
        'attachment_path',
    ];

    protected $casts = [
        'due_date' => 'date',
        'completed_date' => 'date',
    ];

    public function onboarding()
    {
        return $this->belongsTo(HrOnboarding::class, 'hr_onboarding_id');
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
