<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class School extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'description',
        'dean_id',
        'location',
        'phone',
        'email',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the dean of this school.
     */
    public function dean(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dean_id');
    }

    /**
     * Get all departments under this school.
     */
    public function departments(): HasMany
    {
        return $this->hasMany(Department::class);
    }

    /**
     * Get active departments under this school.
     */
    public function activeDepartments(): HasMany
    {
        return $this->hasMany(Department::class)->where('is_active', true);
    }
}
