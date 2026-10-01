<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HrExpenseClaim extends Model
{
    protected $fillable = [
        'user_id',
        'claim_date',
        'category',
        'amount',
        'currency',
        'description',
        'receipt_path',
        'status',
        'approved_by_id',
        'rejection_reason',
    ];

    protected $casts = [
        'claim_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by_id');
    }
}
