<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HrJobRole extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'role_id',
        'departments',
        'salary_band_min',
        'salary_band_max',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'salary_band_min' => 'decimal:2',
        'salary_band_max' => 'decimal:2',
        'departments' => 'array',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }
}
