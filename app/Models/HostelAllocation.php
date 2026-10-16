<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HostelAllocation extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'hostel_room_id', 'semester_id', 'allocation_date', 'status', 'notes'];

    protected $casts = [
        'allocation_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function room()
    {
        return $this->belongsTo(HostelRoom::class, 'hostel_room_id');
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }
}
