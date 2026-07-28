<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_number',
        'type',
        'first_name',
        'last_name',
        'email',
        'phone',
        'date_of_birth',
        'gender',
        'address',
        'program',
        'program_id',
        'previous_school',
        'previous_qualification',
        'previous_gpa',
        'department',
        'position',
        'highest_degree',
        'specialization',
        'years_of_experience',
        'documents',
        'status',
        'review_notes',
        'reviewed_by',
        'reviewed_at',
        'payment_status',
        'payment_proof',
        'payment_uploaded_at',
        'payment_verified_by',
        'payment_verified_at',
        'payment_notes',
        'admission_number',
        'student_number',
        'admitted_at',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'previous_gpa' => 'decimal:2',
        'documents' => 'array',
        'reviewed_at' => 'datetime',
        'payment_uploaded_at' => 'datetime',
        'payment_verified_at' => 'datetime',
        'admitted_at' => 'datetime',
    ];

    /**
     * Get the reviewer who reviewed this application.
     */
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Get the payment verifier who verified this application's payment.
     */
    public function paymentVerifier()
    {
        return $this->belongsTo(User::class, 'payment_verified_by');
    }

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    /**
     * Scope to get pending applications.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope to get applications under review.
     */
    public function scopeUnderReview($query)
    {
        return $query->where('status', 'under_review');
    }

    /**
     * Scope to get approved applications.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope to get rejected applications.
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Scope to get student applications.
     */
    public function scopeStudents($query)
    {
        return $query->where('type', 'student');
    }

    /**
     * Scope to get faculty applications.
     */
    public function scopeFaculty($query)
    {
        return $query->where('type', 'faculty');
    }

    /**
     * Scope to get applications with payment pending.
     */
    public function scopePaymentPending($query)
    {
        return $query->where('payment_status', 'pending');
    }

    /**
     * Scope to get applications with payment uploaded.
     */
    public function scopePaymentUploaded($query)
    {
        return $query->where('payment_status', 'uploaded');
    }

    /**
     * Scope to get applications with payment verified.
     */
    public function scopePaymentVerified($query)
    {
        return $query->where('payment_status', 'verified');
    }

    /**
     * Get the full name of the applicant.
     */
    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Get the status badge class.
     */
    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            'approved' => 'badge-success',
            'pending' => 'badge-warning',
            'under_review' => 'badge-info',
            'rejected' => 'badge-danger',
            default => 'badge-secondary',
        };
    }

    /**
     * Get the payment status badge class.
     */
    public function getPaymentStatusBadgeClassAttribute()
    {
        return match($this->payment_status) {
            'verified' => 'badge-success',
            'uploaded' => 'badge-info',
            'pending' => 'badge-warning',
            'rejected' => 'badge-danger',
            default => 'badge-secondary',
        };
    }

    /**
     * Get the type label.
     */
    public function getTypeLabelAttribute()
    {
        return ucfirst($this->type);
    }

    /**
     * Generate a unique application number.
     */
    public static function generateApplicationNumber($type)
    {
        $prefix = $type === 'student' ? 'STU' : 'FAC';
        $year = date('Y');
        $lastApplication = self::where('type', $type)
            ->whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        $number = $lastApplication ? intval(substr($lastApplication->application_number, -4)) + 1 : 1;

        return $prefix . $year . str_pad($number, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Generate a unique admission number.
     */
    public static function generateAdmissionNumber()
    {
        $year = date('Y');
        $lastAdmission = self::whereNotNull('admission_number')
            ->whereYear('admitted_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        $number = $lastAdmission ? intval(substr($lastAdmission->admission_number, -5)) + 1 : 1;

        return 'ADM' . $year . str_pad($number, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Generate a unique student number.
     */
    public static function generateStudentNumber()
    {
        $year = date('Y');
        $lastStudent = self::whereNotNull('student_number')
            ->whereYear('admitted_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        $number = $lastStudent ? intval(substr($lastStudent->student_number, -5)) + 1 : 1;

        return 'JBI' . $year . str_pad($number, 5, '0', STR_PAD_LEFT);
    }
}
