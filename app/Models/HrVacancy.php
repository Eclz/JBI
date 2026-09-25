<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HrVacancy extends Model
{
    protected $fillable = [
        'position_title',
        'department',
        'job_description',
        'requirements',
        'num_openings',
        'employment_type',
        'location',
        'salary_range',
        'hiring_manager_id',
        'opening_date',
        'closing_date',
        'status',
    ];

    protected $casts = [
        'opening_date' => 'date',
        'closing_date' => 'date',
    ];

    public function hiringManager()
    {
        return $this->belongsTo(User::class, 'hiring_manager_id');
    }

    public function applicants()
    {
        return $this->hasMany(HrApplicant::class, 'hr_vacancy_id');
    }
}
