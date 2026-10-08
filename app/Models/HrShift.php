<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HrShift extends Model
{
    protected $fillable = [
        'name',
        'start_time',
        'end_time',
        'grace_period_minutes',
        'is_active',
    ];

    public function assignments()
    {
        return $this->hasMany(HrShiftAssignment::class, 'hr_shift_id');
    }
}
