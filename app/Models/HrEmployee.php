<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HrEmployee extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'source_applicant_id', 'employee_number', 'job_title', 'department', 'department_id',
        'workspace', 'employment_type',
        'salary_band', 'emergency_contact', 'status', 'notes',
        'manager_id', 'next_of_kin', 'next_of_kin_contact',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function departmentRecord(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function directReports()
    {
        return $this->hasMany(HrEmployee::class, 'manager_id', 'user_id');
    }
}
