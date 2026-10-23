<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Department extends Model
{
    use HasFactory, SoftDeletes;

    public const TYPE_ACADEMIC = 'academic';
    public const TYPE_ADMINISTRATIVE = 'administrative';
    public const TYPE_OPERATIONAL = 'operational';

    protected $fillable = [
        'name',
        'code',
        'description',
        'type',
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
     * Get programs under this department.
     */
    public function programs(): HasMany
    {
        return $this->hasMany(Program::class);
    }

    /**
     * Get the finance budgets for this department.
     */
    public function budgets(): HasMany
    {
        return $this->hasMany(FinanceBudget::class);
    }

    /**
     * Get the latest finance budget for this department.
     */
    public function latestBudget(): HasOne
    {
        return $this->hasOne(FinanceBudget::class)->latestOfMany();
    }

    /**
     * Get the recorded finance expenses for this department.
     */
    public function expenses(): HasMany
    {
        return $this->hasMany(FinanceExpense::class);
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
     * Check if this department is an academic department.
     */
    public function isAcademic(): bool
    {
        return ($this->type ?? self::TYPE_ACADEMIC) === self::TYPE_ACADEMIC;
    }

    /**
     * Check if this department is an administrative department.
     */
    public function isAdministrative(): bool
    {
        return $this->type === self::TYPE_ADMINISTRATIVE;
    }

    /**
     * Check if this department is an operational / facilities department.
     */
    public function isOperational(): bool
    {
        return $this->type === self::TYPE_OPERATIONAL;
    }

    /**
     * Scope query to academic departments.
     */
    public function scopeAcademic($query)
    {
        return $query->where('type', self::TYPE_ACADEMIC);
    }

    /**
     * Scope query to administrative departments.
     */
    public function scopeAdministrative($query)
    {
        return $query->where('type', self::TYPE_ADMINISTRATIVE);
    }

    /**
     * Scope query to operational departments.
     */
    public function scopeOperational($query)
    {
        return $query->where('type', self::TYPE_OPERATIONAL);
    }

    /**
     * Scope query by department functional type.
     */
    public function scopeByType($query, ?string $type)
    {
        if (!$type || $type === 'all') {
            return $query;
        }

        return $query->where('type', $type);
    }
}
