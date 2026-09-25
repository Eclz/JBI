<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FacilityBooking extends Model
{
    protected $fillable = [
        'facility_room_id',
        'user_id',
        'title',
        'purpose',
        'booking_type',
        'start_time',
        'end_time',
        'status',
        'notes',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public function facilityRoom()
    {
        return $this->belongsTo(FacilityRoom::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
