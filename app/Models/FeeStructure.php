<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeeStructure extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'type',
        'amount',
        'currency',
        'student_region',
        'total_amount',
        'total_amount_max',
        'academic_session',
        'source_url',
        'frequency',
        'academic_year_id',
        'semester_id',
        'program_id',
        'program_level_id',
        'applicable_to',
        'is_mandatory',
        'is_active',
        'due_date',
        'late_fee_amount',
        'late_fee_days',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'total_amount_max' => 'decimal:2',
        'late_fee_amount' => 'decimal:2',
        'applicable_to' => 'array',
        'is_mandatory' => 'boolean',
        'is_active' => 'boolean',
        'due_date' => 'date',
    ];

    /**
     * Get the academic year
     */
    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * Get the semester (if applicable)
     */
    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function programLevel()
    {
        return $this->belongsTo(ProgramLevel::class);
    }

    /**
     * Get fee records
     */
    public function feeRecords()
    {
        return $this->hasMany(FeeRecord::class);
    }

    /**
     * Scope for active fee structures
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for mandatory fees
     */
    public function scopeMandatory($query)
    {
        return $query->where('is_mandatory', true);
    }

    /**
     * Scope by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }
}
