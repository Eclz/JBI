<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HrBenefitEnrollment extends Model
{
    protected $fillable = [
        'user_id',
        'hr_benefit_plan_id',
        'enrollment_date',
        'status',
    ];

    protected $casts = [
        'enrollment_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function plan()
    {
        return $this->belongsTo(HrBenefitPlan::class, 'hr_benefit_plan_id');
    }
}
