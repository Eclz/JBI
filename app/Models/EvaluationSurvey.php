<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluationSurvey extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'academic_year_id',
        'semester_number',
        'is_active',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function questions()
    {
        return $this->hasMany(EvaluationQuestion::class, 'survey_id')->orderBy('display_order');
    }

    public function responses()
    {
        return $this->hasMany(EvaluationResponse::class, 'survey_id');
    }
}
