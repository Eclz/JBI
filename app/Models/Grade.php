<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'course_id',
        'assignment_id',
        'grade_type',
        'points_earned',
        'points_possible',
        'percentage',
        'letter_grade',
        'grade_points',
        'comments',
        'is_published',
        'graded_at',
        'graded_by',
        'total_marks'
    ];

    protected $casts = [
        'points_earned' => 'decimal:2',
        'points_possible' => 'decimal:2',
        'percentage' => 'decimal:2',
        'grade_points' => 'decimal:2',
        'is_published' => 'boolean',
        'graded_at' => 'datetime',
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
     * Get the assignment (if applicable)
     */
    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
    }

    /**
     * Get the grader
     */
    public function grader()
    {
        return $this->belongsTo(User::class, 'graded_by');
    }

    /**
     * Scope for published grades
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    /**
     * Scope by grade type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('grade_type', $type);
    }

    /**
     * Scope by student (using user_id)
     */
    public function scopeByStudent($query, $studentId)
    {
        return $query->where('user_id', $studentId);
    }
}
