<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GeneralLedgerEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'entry_code',
        'account_name',
        'account_type',
        'debit_amount',
        'credit_amount',
        'entry_date',
        'reference_module',
        'description',
    ];

    protected $casts = [
        'debit_amount' => 'decimal:2',
        'credit_amount' => 'decimal:2',
        'entry_date' => 'date',
    ];
}
