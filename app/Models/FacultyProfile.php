<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacultyProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'employee_id',
        'department_id',
        'designation',
        'position',
        'qualification',
        'specialization',
        'joining_date',
        'hire_date',
        'employment_type',
        'employment_status',
        'application_status',
        'salary',
        'office_location',
        'office_hours',
        'research_interests',
        'publications',
        'certifications',
        'years_of_experience',
        'bio',
        'linkedin_profile',
        'personal_website',
        'status',
        'qualifications',
        'experience',
        'documents',
        'application_notes',
        'notes',
    ];

    protected $casts = [
        'joining_date' => 'date',
        'hire_date' => 'date',
        'salary' => 'decimal:2',
        'publications' => 'array',
        'certifications' => 'array',
        'office_hours' => 'array',
        'research_interests' => 'array',
        'qualifications' => 'array',
        'experience' => 'array',
        'documents' => 'array',
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

    /**
     * Get courses taught by this faculty
     */
    public function courses()
    {
        return $this->hasMany(Course::class, 'instructor_id', 'user_id');
    }

    /**
     * Scope for active faculty
     */
    public function scopeActive($query)
    {
        return $query->where('employment_status', 'active')
                    ->orWhere('status', 'active');
    }

    /**
     * Scope for pending faculty
     */
    public function scopePending($query)
    {
        return $query->where('employment_status', 'pending');
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
     * Scope by employment type
     */
    public function scopeByEmploymentType($query, $type)
    {
        return $query->where('employment_type', $type);
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
     * Check if faculty is active
     */
    public function isActive()
    {
        return $this->employment_status === 'active' || $this->status === 'active';
    }

    /**
     * Get formatted qualifications
     */
    public function getFormattedQualificationsAttribute()
    {
        $qualifications = $this->qualifications;
        if (is_string($qualifications)) {
            $qualifications = json_decode($qualifications, true);
        }
        return $qualifications ?? [];
    }

    /**
     * Get formatted experience
     */
    public function getFormattedExperienceAttribute()
    {
        $experience = $this->experience;
        if (is_string($experience)) {
            $experience = json_decode($experience, true);
        }
        return $experience ?? [];
    }

    /**
     * Get position or designation
     */
    public function getPositionTitleAttribute()
    {
        return $this->position ?? $this->designation ?? 'Faculty Member';
    }

    /**
     * Get qualification display
     */
    public function getQualificationDisplayAttribute()
    {
        return $this->qualification ?? 'Not specified';
    }
}
