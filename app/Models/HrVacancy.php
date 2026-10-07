<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HrVacancy extends Model
{
    protected $fillable = [
        'position_title',
        'department',
        'role_id',
        'department_id',
        'job_description',
        'requirements',
        'num_openings',
        'employment_type',
        'location',
        'salary_range',
        'hiring_manager_id',
        'source_succession_plan_id',
        'opening_date',
        'published_at',
        'closing_date',
        'status',
    ];

    protected $casts = [
        'opening_date' => 'date',
        'closing_date' => 'date',
        'published_at' => 'datetime',
    ];

    public function hiringManager()
    {
        return $this->belongsTo(User::class, 'hiring_manager_id');
    }

    public function applicants()
    {
        return $this->hasMany(HrApplicant::class, 'hr_vacancy_id');
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function departmentRecord()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function successionPlan()
    {
        return $this->belongsTo(HrSuccessionPlan::class, 'source_succession_plan_id');
    }
}
