<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'course_code',
        'name',
        'description',
        'credits',
        'department_id',
        'program',
        'program_id',
        'instructor_id',
        'semester_id',
        'year_of_study',
        'schedule',
        'room',
        'capacity',
        'max_students',
        'status',
        'syllabus_file',
        'prerequisites',
        'fee_amount',
        'learning_objectives',
        'assessment_methods',
    ];

    protected $casts = [
        'schedule' => 'array',
        'prerequisites' => 'array',
        'max_students' => 'integer',
        'capacity' => 'integer',
        'credits' => 'integer',
        'year_of_study' => 'integer',
        'fee_amount' => 'decimal:2',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted()
    {
        static::saving(function ($course) {
            if ($course->code && !$course->course_code) {
                $course->course_code = $course->code;
            } elseif ($course->course_code && !$course->code) {
                $course->code = $course->course_code;
            }
        });
    }

    /**
     * Course statuses
     */
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    /**
     * Get the instructor / faculty of the course
     */
    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function faculty()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function getTitleAttribute()
    {
        return $this->attributes['title'] ?? $this->attributes['name'] ?? $this->attributes['code'] ?? '';
    }

    public function getYearAttribute()
    {
        return $this->attributes['year_of_study'] ?? 1;
    }

    public function getSemesterAttribute()
    {
        return $this->attributes['semester_number'] ?? $this->attributes['semester_id'] ?? 1;
    }

    /**
     * Get the department of the course
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }


    /**
     * Get the program of the course.
     */
    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    /**
     * Get the semester of the course
     */
    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    /**
     * Get enrolled students
     */
    public function students()
    {
        return $this->belongsToMany(User::class, 'course_enrollments', 'course_id', 'user_id')
                ->withPivot('enrollment_date', 'status', 'final_grade', 'letter_grade', 'grade_points', 'completion_date')
                ->withTimestamps();
    }

    /**
     * Get course enrollments
     */
    public function enrollments()
    {
        return $this->hasMany(CourseEnrollment::class);
    }

    /**
     * Get active enrollments
     */
    public function activeEnrollments()
    {
        return $this->hasMany(CourseEnrollment::class)->where('status', 'enrolled');
    }

    /**
     * Get course assignments
     */
    public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }

    /**
     * Get published assignments
     */
    public function publishedAssignments()
    {
        return $this->hasMany(Assignment::class)->where('is_published', true);
    }

    /**
     * Get course materials
     */
    public function materials()
    {
        return $this->hasMany(CourseMaterial::class);
    }

    /**
     * Get course quizzes
     */
    public function quizzes()
    {
        return $this->hasMany(Quiz::class);
    }

    /**
     * Get published materials
     */
    public function publishedMaterials()
    {
        return $this->hasMany(CourseMaterial::class)->where('is_published', true);
    }

    /**
     * Get course announcements
     */
    public function announcements()
    {
        return $this->hasMany(Announcement::class);
    }

    /**
     * Get attendance records for this course
     */
    public function attendance()
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * Get attendance records for this course (alias for consistency)
     */
    public function attendanceRecords()
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * Get course grades
     */
    public function grades()
    {
        return $this->hasMany(Grade::class);
    }

    /**
     * Get course forums
     */
    public function forums()
    {
        return $this->hasMany(Forum::class);
    }

    /**
     * Get enrolled students count
     */
    public function getEnrolledCountAttribute()
    {
        return $this->activeEnrollments()->count();
    }

    /**
     * Get course capacity (use capacity field, fallback to max_students)
     */
    public function getCapacityAttribute($value)
    {
        return $value ?? $this->attributes['max_students'] ?? null;
    }

    /**
     * Check if course is full
     */
    public function getIsFullAttribute()
    {
        $capacity = $this->capacity ?? $this->max_students;
        if (!$capacity) {
            return false; // Unlimited capacity
        }
        return $this->enrolled_count >= $capacity;
    }

    /**
     * Get course schedule as formatted string
     */
    public function getScheduleStringAttribute()
    {
        if (!$this->schedule) {
            return 'Schedule TBA';
        }

        $scheduleString = '';
        foreach ($this->schedule as $day => $times) {
            $scheduleString .= ucfirst($day) . ': ' . $times['start'] . ' - ' . $times['end'] . ' ';
        }

        return trim($scheduleString);
    }

    /**
     * Scope for active courses
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Scope for current semester - Fixed to work with actual database structure
     */
    public function scopeCurrentSemester($query)
    {
        // Get the current semester from the semesters table
        $currentSemester = \App\Models\Semester::where('is_current', true)->first();

        if ($currentSemester) {
            return $query->where('semester_id', $currentSemester->id);
        }

        // Fallback: if no current semester is set, return empty result
        return $query->whereRaw('1 = 0');
    }

    /**
     * Scope by instructor
     */
    public function scopeByInstructor($query, $instructorId)
    {
        return $query->where('instructor_id', $instructorId);
    }
}
