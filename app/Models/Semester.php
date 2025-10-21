<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Semester extends Model
{
    use HasFactory;

    protected $fillable = [
        'academic_year_id',
        'name',
        'start_date',
        'end_date',
        'registration_start',
        'registration_end',
        'is_current',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'registration_start' => 'date',
        'registration_end' => 'date',
        'is_current' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Get the academic year
     */
    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * Get courses for this semester
     */
    public function courses()
    {
        return $this->hasMany(Course::class);
    }

    /**
     * Get active courses for this semester
     */
    public function activeCourses()
    {
        return $this->hasMany(Course::class)->where('status', 'active');
    }

    /**
     * Get fee structures for this semester
     */
    public function feeStructures()
    {
        return $this->hasMany(FeeStructure::class);
    }

    /**
     * Scope for current semester
     */
    public function scopeCurrent($query)
    {
        return $query->where('is_current', true);
    }

    /**
     * Scope for active semesters
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Check if registration is open
     */
    public function getIsRegistrationOpenAttribute()
    {
        $now = now()->toDateString();
        return $now >= $this->registration_start && $now <= $this->registration_end;
    }
}
