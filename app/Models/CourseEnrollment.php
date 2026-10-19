<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseEnrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'student_id',
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

    /**
     * Map student_id to user_id for CourseEnrollment.
     */
    public function setStudentIdAttribute($value)
    {
        $this->attributes['user_id'] = $value;
    }

    public function getStudentIdAttribute()
    {
        return $this->attributes['user_id'] ?? null;
    }

    /**
     * Create a new Eloquent query builder for the model that transparently maps student_id queries to user_id.
     */
    public function newEloquentBuilder($query)
    {
        return new class($query) extends \Illuminate\Database\Eloquent\Builder {
            public function where($column, $operator = null, $value = null, $boolean = 'and')
            {
                if (is_string($column)) {
                    if ($column === 'student_id') {
                        $column = 'user_id';
                    } elseif ($column === 'course_enrollments.student_id') {
                        $column = 'course_enrollments.user_id';
                    }
                }
                return parent::where($column, $operator, $value, $boolean);
            }
        };
    }

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
     * Get status badge class for UI (Bootstrap 5)
     */
    public function getStatusBadgeClass()
    {
        return match($this->status) {
            'enrolled' => 'bg-success text-white',
            'completed' => 'bg-primary text-white',
            'dropped' => 'bg-danger text-white',
            'failed' => 'bg-danger text-white',
            'pending' => 'bg-warning text-dark',
            default => 'bg-secondary text-white',
        };
    }

    /**
     * Get formatted status text
     */
    public function getStatusText()
    {
        return ucfirst($this->status);
    }

    /**
     * Enrollment types
     */
    const ENROLLMENT_TYPE_NORMAL = 'normal';
    const ENROLLMENT_TYPE_RETAKE = 'retake';
    const ENROLLMENT_TYPE_MISSED_PAPER = 'missed_paper';
    const ENROLLMENT_TYPE_REDO = 'redo';

    /**
     * Get enrollment type options
     */
    public static function getEnrollmentTypeOptions(): array
    {
        return [
            self::ENROLLMENT_TYPE_NORMAL => 'Normal',
            self::ENROLLMENT_TYPE_RETAKE => 'Retake',
            self::ENROLLMENT_TYPE_MISSED_PAPER => 'Missed Paper',
            self::ENROLLMENT_TYPE_REDO => 'Redo',
        ];
    }

    /**
     * Get human-readable enrollment type text
     */
    public function getEnrollmentTypeText(): string
    {
        return match($this->enrollment_type) {
            self::ENROLLMENT_TYPE_RETAKE => 'Retake',
            self::ENROLLMENT_TYPE_MISSED_PAPER => 'Missed Paper',
            self::ENROLLMENT_TYPE_REDO => 'Redo',
            default => 'Normal',
        };
    }

    /**
     * Get badge class for enrollment type (Bootstrap 5)
     */
    public function getEnrollmentTypeBadgeClass(): string
    {
        return match($this->enrollment_type) {
            self::ENROLLMENT_TYPE_RETAKE => 'bg-warning text-dark',
            self::ENROLLMENT_TYPE_MISSED_PAPER => 'bg-danger text-white',
            self::ENROLLMENT_TYPE_REDO => 'bg-info text-white',
            default => 'bg-secondary text-white',
        };
    }

    /**
     * Default academic registrar grading criteria scale
     */
    public static function getDefaultGradingCriteria(): array
    {
        return [
            ['min' => 90, 'max' => 100, 'letter' => 'A+', 'grade_points' => 4.0, 'remark' => 'Distinction / Exceptional'],
            ['min' => 85, 'max' => 89.99, 'letter' => 'A', 'grade_points' => 4.0, 'remark' => 'Excellent'],
            ['min' => 80, 'max' => 84.99, 'letter' => 'A-', 'grade_points' => 3.7, 'remark' => 'Very Good'],
            ['min' => 75, 'max' => 79.99, 'letter' => 'B+', 'grade_points' => 3.3, 'remark' => 'Good'],
            ['min' => 70, 'max' => 74.99, 'letter' => 'B', 'grade_points' => 3.0, 'remark' => 'Above Average'],
            ['min' => 65, 'max' => 69.99, 'letter' => 'B-', 'grade_points' => 2.7, 'remark' => 'Average'],
            ['min' => 60, 'max' => 64.99, 'letter' => 'C+', 'grade_points' => 2.3, 'remark' => 'Satisfactory'],
            ['min' => 55, 'max' => 59.99, 'letter' => 'C', 'grade_points' => 2.0, 'remark' => 'Pass'],
            ['min' => 50, 'max' => 54.99, 'letter' => 'D', 'grade_points' => 1.0, 'remark' => 'Marginal Pass'],
            ['min' => 0, 'max' => 49.99, 'letter' => 'F', 'grade_points' => 0.0, 'remark' => 'Fail'],
        ];
    }

    /**
     * Get official grading criteria set by the Academic Registrar
     */
    public static function getAcademicRegistrarGradingCriteria(): array
    {
        $saved = SystemSetting::getSetting('academic_registrar_grading_criteria');
        if (is_array($saved) && !empty($saved)) {
            return $saved;
        }

        return self::getDefaultGradingCriteria();
    }

    /**
     * Determine letter grade based on percentage using Academic Registrar criteria
     */
    public static function determineLetterGrade(?float $percentage, ?array $criteria = null): ?string
    {
        if ($percentage === null) {
            return null;
        }

        $criteria = $criteria ?: self::getAcademicRegistrarGradingCriteria();

        foreach ($criteria as $rule) {
            $min = (float)($rule['min'] ?? $rule['min_score'] ?? 0);
            $max = (float)($rule['max'] ?? $rule['max_score'] ?? 100);
            $letter = $rule['letter'] ?? $rule['letter_grade'] ?? '';

            if ($percentage >= $min && $percentage <= $max) {
                return $letter;
            }
        }

        return $percentage >= 50 ? 'D' : 'F';
    }

    /**
     * Determine grade points based on percentage using Academic Registrar criteria
     */
    public static function determineGradePoints(?float $percentage, ?array $criteria = null): ?float
    {
        if ($percentage === null) {
            return null;
        }

        $criteria = $criteria ?: self::getAcademicRegistrarGradingCriteria();

        foreach ($criteria as $rule) {
            $min = (float)($rule['min'] ?? $rule['min_score'] ?? 0);
            $max = (float)($rule['max'] ?? $rule['max_score'] ?? 100);
            $points = (float)($rule['grade_points'] ?? $rule['grade_point'] ?? 0.0);

            if ($percentage >= $min && $percentage <= $max) {
                return $points;
            }
        }

        return $percentage >= 50 ? 1.0 : 0.0;
    }

    /**
     * Automatically calculate student results for this course enrollment.
     */
    public function calculateStudentResults(): array
    {
        $userId = $this->user_id;
        $courseId = $this->course_id;

        // 1. Gather points from Grade records
        $grades = Grade::where('user_id', $userId)
            ->where('course_id', $courseId)
            ->get();

        $totalPointsEarned = 0;
        $totalPointsPossible = 0;
        $countPercentages = 0;
        $sumPercentages = 0;

        foreach ($grades as $grade) {
            if ($grade->points_possible && (float)$grade->points_possible > 0) {
                $totalPointsEarned += (float)$grade->points_earned;
                $totalPointsPossible += (float)$grade->points_possible;
            } elseif ($grade->percentage !== null) {
                $sumPercentages += (float)$grade->percentage;
                $countPercentages++;
            }
        }

        // 2. Gather from AssignmentSubmissions if not duplicated in Grade
        $assignmentSubmissions = AssignmentSubmission::where('user_id', $userId)
            ->whereHas('assignment', fn($q) => $q->where('course_id', $courseId))
            ->whereNotNull('score')
            ->with('assignment')
            ->get();

        foreach ($assignmentSubmissions as $submission) {
            $alreadyRecorded = $grades->contains('assignment_id', $submission->assignment_id);
            if (!$alreadyRecorded && $submission->assignment && (float)$submission->assignment->points > 0) {
                $totalPointsEarned += (float)$submission->score;
                $totalPointsPossible += (float)$submission->assignment->points;
            }
        }

        // 3. Gather from ExamAttempts if any
        $examAttempts = ExamAttempt::where('user_id', $userId)
            ->whereHas('exam', fn($q) => $q->where('course_id', $courseId))
            ->where('status', 'graded')
            ->with('exam')
            ->get();

        foreach ($examAttempts as $attempt) {
            if ($attempt->exam && (float)$attempt->exam->total_marks > 0) {
                $totalPointsEarned += (float)$attempt->marks_obtained;
                $totalPointsPossible += (float)$attempt->exam->total_marks;
            }
        }

        $finalGrade = null;
        if ($totalPointsPossible > 0) {
            $finalGrade = round(($totalPointsEarned / $totalPointsPossible) * 100, 2);
        } elseif ($countPercentages > 0) {
            $finalGrade = round($sumPercentages / $countPercentages, 2);
        } elseif ($this->final_grade !== null) {
            $finalGrade = (float)$this->final_grade;
        }

        $criteria = self::getAcademicRegistrarGradingCriteria();
        $letterGrade = self::determineLetterGrade($finalGrade, $criteria);
        $gradePoints = self::determineGradePoints($finalGrade, $criteria);

        $semesterEndDate = $this->course?->semester?->end_date ?? $this->semester?->end_date;

        return [
            'final_grade' => $finalGrade,
            'letter_grade' => $letterGrade,
            'grade_points' => $gradePoints,
            'semester_end_date' => $semesterEndDate,
            'total_points_earned' => $totalPointsEarned,
            'total_points_possible' => $totalPointsPossible,
            'results_count' => $grades->count() + $assignmentSubmissions->count() + $examAttempts->count(),
            'criteria' => $criteria,
        ];
    }

    /**
     * Get semester end closure date for this enrollment
     */
    public function getSemesterClosureDate()
    {
        return $this->course?->semester?->end_date ?? $this->semester?->end_date;
    }
}
