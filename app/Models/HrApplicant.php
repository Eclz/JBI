<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HrApplicant extends Model
{
    protected $fillable = [
        'hr_vacancy_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'cv_path',
        'cover_letter',
        'status',
        'hired_user_id',
    ];

    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function vacancy()
    {
        return $this->belongsTo(HrVacancy::class, 'hr_vacancy_id');
    }

    public function hiredUser()
    {
        return $this->belongsTo(User::class, 'hired_user_id');
    }
}
