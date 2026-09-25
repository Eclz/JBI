<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HrPerformanceGoal extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'due_date',
        'status',
        'progress_percentage',
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
