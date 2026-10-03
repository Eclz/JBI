<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hostel extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'type', 'location', 'capacity', 'description', 'is_active'];

    public function rooms()
    {
        return $this->hasMany(HostelRoom::class);
    }
}
