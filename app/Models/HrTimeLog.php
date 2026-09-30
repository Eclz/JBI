<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HrTimeLog extends Model
{
    protected $fillable = ['user_id', 'date', 'clock_in', 'clock_out', 'total_hours', 'status'];

    protected $casts = [
        'date' => 'date',
        'clock_in' => 'datetime',
        'clock_out' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
