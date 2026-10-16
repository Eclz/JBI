<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HrSuccessor extends Model
{
    protected $fillable = [
        'hr_succession_plan_id',
        'user_id',
        'readiness_level',
        'development_needs',
        'status',
    ];

    public function plan()
    {
        return $this->belongsTo(HrSuccessionPlan::class, 'hr_succession_plan_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
