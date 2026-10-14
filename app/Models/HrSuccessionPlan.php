<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HrSuccessionPlan extends Model
{
    protected $fillable = [
        'position_name',
        'department',
        'current_holder_id',
        'hr_vacancy_id',
        'status',
        'notes',
    ];

    public function currentHolder()
    {
        return $this->belongsTo(User::class, 'current_holder_id');
    }

    public function successors()
    {
        return $this->hasMany(HrSuccessor::class, 'hr_succession_plan_id');
    }

    public function vacancy()
    {
        return $this->belongsTo(HrVacancy::class, 'hr_vacancy_id');
    }
}
