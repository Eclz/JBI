<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VotingSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'target_semester',
        'academic_year_id',
        'start_time',
        'end_time',
        'vetting_start_at',
        'vetting_end_at',
        'is_active',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'vetting_start_at' => 'datetime',
        'vetting_end_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function positions()
    {
        return $this->hasMany(VotingPosition::class)->orderBy('display_order');
    }

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }

    public function getIsVettingOpenAttribute()
    {
        if (!$this->is_active) {
            return false;
        }

        if (!$this->vetting_start_at || !$this->vetting_end_at) {
            return true;
        }

        $now = now();
        return $now->gte($this->vetting_start_at) && $now->lte($this->vetting_end_at);
    }
}
