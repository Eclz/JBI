<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HrBenefitPlan extends Model
{
    protected $fillable = [
        'name',
        'provider',
        'type',
        'description',
        'employee_cost',
        'company_cost',
        'status',
    ];

    public function enrollments()
    {
        return $this->hasMany(HrBenefitEnrollment::class, 'hr_benefit_plan_id');
    }
}
