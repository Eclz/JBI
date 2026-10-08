<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hostel extends Model
{
    use HasFactory;

    protected $table = 'campus_facilities';

    protected $fillable = ['name', 'type', 'hall_type', 'location', 'capacity', 'description', 'is_active', 'dean_id'];

    protected static function booted(): void
    {
        static::addGlobalScope('hall', fn ($query) => $query->where('type', 'hall'));
    }

    public function getTypeAttribute(): ?string
    {
        return $this->attributes['hall_type'] ?? null;
    }

    public function setTypeAttribute(string $type): void
    {
        $this->attributes['type'] = 'hall';
        $this->attributes['hall_type'] = $type;
    }

    public function rooms()
    {
        return $this->hasMany(HostelRoom::class, 'campus_facility_id');
    }
}
