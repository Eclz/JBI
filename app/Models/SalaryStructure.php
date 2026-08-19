<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalaryStructure extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'basic_salary',
        'housing_allowance',
        'transport_allowance',
        'other_allowances',
        'tax_deduction_rate',
        'pension_deduction_rate',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
