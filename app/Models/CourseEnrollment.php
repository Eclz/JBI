<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseEnrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'course_id',
        'enrollment_date',
        'status',
        'enrollment_type',
        'final_grade',
        'letter_grade',
        'grade_points',
        'completion_date',
        'notes',
    ];

    protected $casts = [
        'enrollment_date' => 'date',
        'completion_date' => 'date',
        'final_grade' => 'decimal:2',
        'grade_points' => 'decimal:2',
    ];

    /**
     * Get the student
     */
    public function student()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the course
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the semester through the course
     */
    public function semester()
    {
        return $this->hasOneThrough(
            Semester::class,
            Course::class,
            'id', // Foreign key on courses table
            'id', // Foreign key on semesters table
            'course_id', // Local key on course_enrollments table
            'semester_id' // Local key on courses table
        );
    }

    /**
     * Get all grades for this enrollment
     */
    public function grades()
    {
        return $this->hasMany(Grade::class, 'user_id', 'user_id')
                    ->where('course_id', $this->course_id);
    }

    /**
     * Get all attendance records for this enrollment
     */
    public function attendances()
    {
        return $this->hasMany(\App\Models\Attendance::class, 'user_id', 'user_id')
                    ->where('course_id', $this->course_id);
    }

    /**
     * Calculate attendance percentage for this enrollment
     */
    public function getAttendancePercentageAttribute()
    {
        $total = $this->attendances()->count();
        if ($total === 0) {
            return 0;
        }

        $present = $this->attendances()
                        ->whereIn('status', ['present', 'late'])
                        ->count();

        return round(($present / $total) * 100, 2);
    }

    /**
     * Scope for active enrollments
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'enrolled');
    }

    /**
     * Scope for completed enrollments
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope for pending enrollments
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for dropped enrollments
     */
    public function scopeDropped($query)
    {
        return $query->where('status', 'dropped');
    }

    /**
     * Get status badge class for UI
     */
    public function getStatusBadgeClass()
    {
        return match($this->status) {
            'enrolled' => 'badge-success',
            'completed' => 'badge-primary',
            'dropped' => 'badge-warning',
            'failed' => 'badge-danger',
            'pending' => 'badge-info',
            default => 'badge-secondary',
        };
    }

    /**
     * Get formatted status text
     */
    public function getStatusText()
    {
        return ucfirst($this->status);
    }
}
