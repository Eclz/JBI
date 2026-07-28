<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseFee extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', // Added user_id to fillable
        'course_id',
        'academic_year_id',
        'semester_id',
        'amount',
        'paid_amount',
        'exam_fee',
        'exam_fee_paid',
        'status',
        'due_date',
        'paid_date',
        'payment_reference',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'exam_fee' => 'decimal:2',
        'exam_fee_paid' => 'decimal:2',
        'due_date' => 'date',
        'paid_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function getBalanceAttribute()
    {
        return $this->amount - $this->paid_amount;
    }

    public function getExamFeeBalanceAttribute()
    {
        return $this->exam_fee - $this->exam_fee_paid;
    }

    public function isExamFeePaid()
    {
        return $this->exam_fee_paid >= $this->exam_fee;
    }
}
