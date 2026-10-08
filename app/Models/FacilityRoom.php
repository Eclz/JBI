<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FacilityRoom extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'building', 'campus_facility_id', 'room_type', 'capacity', 'status', 'notes',
    ];

    protected $casts = [
        'capacity' => 'integer',
    ];

    public function campusFacility(): BelongsTo
    {
        return $this->belongsTo(CampusFacility::class);
    }
}
