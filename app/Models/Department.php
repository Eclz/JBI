<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Department extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'description',
        'faculty_id',
        'head_of_department_id',
        'location',
        'phone',
        'email',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the faculty that owns the department.
     */
    public function faculty(): BelongsTo
    {
        return $this->belongsTo(Faculty::class);
    }

    /**
     * Get the head of department.
     */
    public function headOfDepartment(): BelongsTo
    {
        return $this->belongsTo(User::class, 'head_of_department_id');
    }

    /**
     * Backward compatibility alias for headOfDepartment.
     */
    public function head(): BelongsTo
    {
        return $this->headOfDepartment();
    }

    /**
     * Get the faculty members assigned to this department.
     */
    public function facultyMembers(): HasMany
    {
        return $this->hasMany(FacultyProfile::class);
    }

    /**
     * Get the students assigned to this department.
     */
    public function students(): HasMany
    {
        return $this->hasMany(StudentProfile::class);
    }

    /**
     * Get the courses offered by this department.
     */
    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }

    /**
     * Scope to get only active departments.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get departments with their faculty.
     */
    public function scopeWithFaculty($query)
    {
        return $query->with('faculty');
    }

    /**
     * Get the full name with faculty.
     */
    public function getFullNameAttribute(): string
    {
        return $this->faculty ? "{$this->faculty->name} - {$this->name}" : $this->name;
    }

    /**
     * Check if department has a head.
     */
    public function hasHead(): bool
    {
        return !is_null($this->head_of_department_id);
    }

    /**
     * Get department statistics.
     */
    public function getStatsAttribute(): array
    {
        return [
            'faculty_count' => $this->facultyMembers()->count(),
            'student_count' => $this->students()->count(),
            'course_count' => $this->courses()->count(),
            'active_courses' => $this->courses()->where('is_active', true)->count(),
        ];
    }
}
