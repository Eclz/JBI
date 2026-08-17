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
     * Accessor for registration_start
     * Returns explicit admin setting if present, otherwise defaults to semester start date
     */
    public function getRegistrationStartAttribute($value)
    {
        if (!empty($value)) {
            return \Carbon\Carbon::parse($value);
        }
        return $this->start_date ? \Carbon\Carbon::parse($this->start_date) : null;
    }

    /**
     * Accessor for registration_end
     * Returns explicit admin setting if present, otherwise defaults to midweek date of semester
     */
    public function getRegistrationEndAttribute($value)
    {
        if (!empty($value)) {
            return \Carbon\Carbon::parse($value);
        }

        if ($this->start_date && $this->end_date) {
            $start = \Carbon\Carbon::parse($this->start_date);
            $end = \Carbon\Carbon::parse($this->end_date);
            $halfDays = (int) floor($start->diffInDays($end) / 2);
            return $start->copy()->addDays($halfDays);
        }

        if ($this->start_date) {
            return \Carbon\Carbon::parse($this->start_date)->addWeeks(6);
        }

        return null;
    }

    /**
     * Check if registration is open
     */
    public function getIsRegistrationOpenAttribute()
    {
        $today = now()->startOfDay();
        $start = $this->registration_start ? $this->registration_start->copy()->startOfDay() : null;
        $end = $this->registration_end ? $this->registration_end->copy()->endOfDay() : null;

        if ($start && $end) {
            return $today->gte($start) && $today->lte($end);
        }

        if ($start) {
            return $today->gte($start);
        }

        return $this->is_active ?? true;
    }
}
