<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QualityReview extends Model
{
    protected $fillable = [
        'program_id',
        'dean_id',
        'title',
        'review_notes',
        'metrics',
        'status',
        'registrar_feedback',
    ];

    protected $casts = [
        'metrics' => 'array',
    ];

    public function program()
    {
        return $this->belongsTo(\App\Models\Program::class);
    }

    public function dean()
    {
        return $this->belongsTo(\App\Models\User::class, 'dean_id');
    }
}
