<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BudgetRequest extends Model
{
    protected $fillable = [
        'department_id',
        'requested_by',
        'title',
        'description',
        'amount',
        'status',
        'finance_notes',
    ];

    public function department()
    {
        return $this->belongsTo(\App\Models\Department::class);
    }

    public function requester()
    {
        return $this->belongsTo(\App\Models\User::class, 'requested_by');
    }
}
