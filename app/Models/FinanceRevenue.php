<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinanceRevenue extends Model
{
    use HasFactory;

    protected $fillable = [
        'revenue_code',
        'category',
        'title',
        'amount',
        'transaction_date',
        'payment_method',
        'reference_number',
        'payer_name',
        'notes',
        'received_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'transaction_date' => 'date',
    ];

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }
}
