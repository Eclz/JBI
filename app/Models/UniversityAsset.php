<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UniversityAsset extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_tag',
        'asset_name',
        'category',
        'department_id',
        'purchase_cost',
        'purchase_date',
        'current_value',
        'annual_depreciation_rate',
        'status',
        'location',
    ];

    protected $casts = [
        'purchase_cost' => 'decimal:2',
        'current_value' => 'decimal:2',
        'annual_depreciation_rate' => 'decimal:2',
        'purchase_date' => 'date',
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }
}
