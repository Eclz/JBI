<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicYear extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'year',
        'start_date',
        'end_date',
        'is_current',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_current' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Get semesters for this academic year
     */
    public function semesters()
    {
        return $this->hasMany(Semester::class);
    }

    /**
     * Get active semesters
     */
    public function activeSemesters()
    {
        return $this->hasMany(Semester::class)->where('is_active', true);
    }

    /**
     * Get current semester
     */
    public function currentSemester()
    {
        return $this->hasOne(Semester::class)->where('is_current', true);
    }

    /**
     * Get fee structures for this academic year
     */
    public function feeStructures()
    {
        return $this->hasMany(FeeStructure::class);
    }

    /**
     * Scope for current academic year
     */
    public function scopeCurrent($query)
    {
        return $query->where('is_current', true);
    }

    /**
     * Scope for active academic years
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
