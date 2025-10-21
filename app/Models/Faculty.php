<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Faculty extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'dean_id',
        'location',
        'phone',
        'email',
        'website',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the dean of this faculty.
     */
    public function dean(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dean_id');
    }

    /**
     * Get all departments in this faculty.
     */
    public function departments(): HasMany
    {
        return $this->hasMany(Department::class);
    }

    /**
     * Get active departments in this faculty.
     */
    public function activeDepartments(): HasMany
    {
        return $this->hasMany(Department::class)->where('is_active', true);
    }

    /**
     * Get all faculty members through departments.
     */
    public function facultyMembers(): HasManyThrough
    {
        return $this->hasManyThrough(
            User::class,
            Department::class,
            'faculty_id', // Foreign key on departments table
            'id', // Foreign key on users table
            'id', // Local key on faculties table
            'id' // Local key on departments table
        )->whereHas('facultyProfile', function ($query) {
            $query->whereColumn('faculty_profiles.department_id', 'departments.id');
        });
    }

    /**
     * Get all students through departments.
     */
    public function students(): HasManyThrough
    {
        return $this->hasManyThrough(
            User::class,
            Department::class,
            'faculty_id', // Foreign key on departments table
            'id', // Foreign key on users table
            'id', // Local key on faculties table
            'id' // Local key on departments table
        )->whereHas('studentProfile', function ($query) {
            $query->whereColumn('student_profiles.department_id', 'departments.id');
        });
    }

    /**
     * Get all courses through departments.
     */
    public function courses(): HasManyThrough
    {
        return $this->hasManyThrough(Course::class, Department::class);
    }

    /**
     * Scope to get only active faculties.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
