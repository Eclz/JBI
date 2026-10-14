<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HrShiftAssignment extends Model
{
    protected $fillable = [
        'user_id',
        'hr_shift_id',
        'start_date',
        'end_date',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function shift()
    {
        return $this->belongsTo(HrShift::class, 'hr_shift_id');
    }
}
