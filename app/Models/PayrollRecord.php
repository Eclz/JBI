<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PayrollRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'month_year',
        'basic_salary',
        'total_allowances',
        'gross_salary',
        'tax_deductions',
        'pension_deductions',
        'net_salary',
        'payment_date',
        'status',
    ];

    protected $casts = [
        'basic_salary' => 'decimal:2',
        'total_allowances' => 'decimal:2',
        'gross_salary' => 'decimal:2',
        'tax_deductions' => 'decimal:2',
        'pension_deductions' => 'decimal:2',
        'net_salary' => 'decimal:2',
        'payment_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
