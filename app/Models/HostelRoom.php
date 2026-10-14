<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HostelRoom extends Model
{
    use HasFactory;

    protected $fillable = ['campus_facility_id', 'room_number', 'capacity', 'occupancy', 'fee_per_semester', 'status'];

    public function hostel()
    {
        return $this->belongsTo(CampusFacility::class, 'campus_facility_id')->where('type', 'hall');
    }

    public function campusFacility()
    {
        return $this->belongsTo(CampusFacility::class);
    }

    public function allocations()
    {
        return $this->hasMany(HostelAllocation::class);
    }
}
