<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'admission_number',
        'admission_date',
        'registration_fee_paid_at',
        'registration_deadline_at',
        'tuition_deadline_at',
        'department_id',
        'program_id',
        'program',
        'specialization',
        'current_semester',
        'year_of_study',
        'status',
        'application_status',
        'current_gpa',
        'cumulative_gpa',
        'total_credits_earned',
        'total_credits_required',
        'expected_graduation_date',
        'actual_graduation_date',
        'guardian_name',
        'guardian_phone',
        'guardian_email',
        'guardian_address',
        'previous_school',
        'previous_school_address',
        'graduation_year',
        'previous_gpa',
        'academic_history',
        'qualifications',
        'achievements',
        'documents',
        'application_notes',
        'notes',
    ];

    protected $casts = [
        'admission_date' => 'date',
        'registration_fee_paid_at' => 'datetime',
        'registration_deadline_at' => 'datetime',
        'tuition_deadline_at' => 'datetime',
        'current_gpa' => 'decimal:2',
        'cumulative_gpa' => 'decimal:2',
        'previous_gpa' => 'decimal:2',
        'expected_graduation_date' => 'date',
        'actual_graduation_date' => 'date',
        'academic_history' => 'array',
        'qualifications' => 'array',
        'achievements' => 'array',
        'documents' => 'array',
        'year_of_study' => 'integer',
    ];

    /**
     * Get the user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the department
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    /**
     * Scope for active students
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for pending students
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for graduated students
     */
    public function scopeGraduated($query)
    {
        return $query->where('status', 'graduated');
    }

    /**
     * Scope for approved applications
     */
    public function scopeApproved($query)
    {
        return $query->where('application_status', 'approved');
    }

    /**
     * Scope for submitted applications
     */
    public function scopeSubmitted($query)
    {
        return $query->where('application_status', 'submitted');
    }

    /**
     * Get progress percentage
     */
    public function getProgressPercentageAttribute()
    {
        if ($this->total_credits_required == 0) return 0;
        return round(($this->total_credits_earned / $this->total_credits_required) * 100, 2);
    }

    /**
     * Check if application is pending
     */
    public function isPending()
    {
        return $this->application_status === 'submitted' || $this->application_status === 'under_review';
    }

    /**
     * Check if application is approved
     */
    public function isApproved()
    {
        return $this->application_status === 'approved';
    }

    /**
     * Check if student is active
     */
    public function isActive()
    {
        return $this->status === 'active';
    }
}
