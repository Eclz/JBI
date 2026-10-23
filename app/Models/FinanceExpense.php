<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinanceExpense extends Model
{
    use HasFactory;

    public const FUNCTION_ACADEMIC = 'academic';
    public const FUNCTION_ADMINISTRATIVE = 'administrative';
    public const FUNCTION_OPERATIONAL = 'operational';

    protected $fillable = [
        'expense_number',
        'department_id',
        'category',
        'classification',
        'title',
        'amount',
        'expense_date',
        'status',
        'payment_method',
        'requested_by',
        'approved_by',
        'description',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expense_date' => 'date',
    ];

    protected static function booted(): void
    {
        static::creating(function (FinanceExpense $expense) {
            if (empty($expense->classification)) {
                $expense->classification = self::getFunctionForCategory($expense->category, $expense->department_id);
            }
        });
    }

    public static function getCategoriesByFunction(): array
    {
        return [
            self::FUNCTION_ACADEMIC => [
                'Instructional Materials & Curriculum',
                'Laboratory & Research Supplies',
                'Examinations & Assessment',
                'Academic Staff Development & Research',
                'Library & Academic Resources',
                'Academic Fieldwork & Practicum',
                'Supplies & Stationery',
            ],
            self::FUNCTION_ADMINISTRATIVE => [
                'Executive & Institutional Governance',
                'Finance, Audit & Compliance',
                'Human Resources & Staff Welfare',
                'Admissions & Student Registry',
                'ICT Infrastructure, Licenses & Systems',
                'Legal, Professional & Consultancy',
                'General Office Supplies & Administration',
                'Miscellaneous',
            ],
            self::FUNCTION_OPERATIONAL => [
                'Campus Utilities (Electricity & Water)',
                'Facilities Repairs & Maintenance',
                'Estates Cleaning, Sanitation & Waste',
                'Campus Safety, Security & Surveillance',
                'Transport, Fleet Maintenance & Fuel',
                'Hostels & Accommodation Operations',
                'Equipment & Repairs',
                'Utilities & Internet',
                'Travel & Fuel',
            ],
        ];
    }

    public static function getFunctionForCategory(?string $category, ?int $departmentId = null): string
    {
        if ($departmentId) {
            $dept = Department::find($departmentId);
            if ($dept && in_array($dept->type, [Department::TYPE_ADMINISTRATIVE, Department::TYPE_OPERATIONAL], true)) {
                return $dept->type;
            }
        }

        if (!$category) {
            return self::FUNCTION_ACADEMIC;
        }

        foreach (self::getCategoriesByFunction() as $func => $cats) {
            if (in_array($category, $cats, true)) {
                return $func;
            }
        }

        $lower = strtolower($category);
        if (str_contains($lower, 'utilit') || str_contains($lower, 'repair') || str_contains($lower, 'maint') || str_contains($lower, 'secur') || str_contains($lower, 'clean') || str_contains($lower, 'hostel') || str_contains($lower, 'transport') || str_contains($lower, 'fuel')) {
            return self::FUNCTION_OPERATIONAL;
        }

        if (str_contains($lower, 'admin') || str_contains($lower, 'audit') || str_contains($lower, 'legal') || str_contains($lower, 'human') || str_contains($lower, 'hr') || str_contains($lower, 'govern') || str_contains($lower, 'license') || str_contains($lower, 'software') || str_contains($lower, 'regist')) {
            return self::FUNCTION_ADMINISTRATIVE;
        }

        return self::FUNCTION_ACADEMIC;
    }

    public function isAcademic(): bool
    {
        return ($this->classification ?? self::FUNCTION_ACADEMIC) === self::FUNCTION_ACADEMIC;
    }

    public function isAdministrative(): bool
    {
        return $this->classification === self::FUNCTION_ADMINISTRATIVE;
    }

    public function isOperational(): bool
    {
        return $this->classification === self::FUNCTION_OPERATIONAL;
    }

    public function scopeAcademic($query)
    {
        return $query->where('classification', self::FUNCTION_ACADEMIC);
    }

    public function scopeAdministrative($query)
    {
        return $query->where('classification', self::FUNCTION_ADMINISTRATIVE);
    }

    public function scopeOperational($query)
    {
        return $query->where('classification', self::FUNCTION_OPERATIONAL);
    }

    public function scopeFunction($query, ?string $function)
    {
        if (!$function || $function === 'all') {
            return $query;
        }

        return $query->where('classification', $function);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
