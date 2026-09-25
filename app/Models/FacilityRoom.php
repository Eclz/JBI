<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacilityRoom extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'building', 'room_type', 'capacity', 'status', 'notes',
    ];

    protected $casts = [
        'capacity' => 'integer',
    ];
}
