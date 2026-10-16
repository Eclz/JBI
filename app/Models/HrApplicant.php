<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HrApplicant extends Model
{
    protected $fillable = [
        'hr_vacancy_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'cv_path',
        'tracking_token_hash',
        'documents',
        'consent_at',
        'cover_letter',
        'status',
        'hired_user_id',
        'hired_at',
        'hired_by',
    ];

    protected $casts = [
        'documents' => 'array',
        'consent_at' => 'datetime',
        'hired_at' => 'datetime',
    ];

    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function vacancy()
    {
        return $this->belongsTo(HrVacancy::class, 'hr_vacancy_id');
    }

    public function hiredUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'hired_user_id');
    }

    public function hiredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'hired_by');
    }
}
