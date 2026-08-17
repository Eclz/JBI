<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluationQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'survey_id',
        'question_text',
        'category',
        'question_type',
        'display_order',
    ];

    public function survey()
    {
        return $this->belongsTo(EvaluationSurvey::class, 'survey_id');
    }
}
