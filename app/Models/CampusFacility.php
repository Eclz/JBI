<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CampusFacility extends Model
{
    protected $fillable = [
        'type',
        'hall_type',
        'capacity',
        'dean_id',
        'name',
        'location',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'capacity' => 'integer',
    ];

    public function rooms(): HasMany
    {
        return $this->hasMany(FacilityRoom::class);
    }

    public function hostelRooms(): HasMany
    {
        return $this->hasMany(HostelRoom::class);
    }
}
