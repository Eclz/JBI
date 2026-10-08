<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HrPerformanceReview extends Model
{
    protected $fillable = [
        'user_id',
        'reviewer_id',
        'review_period',
        'review_date',
        'status',
        'overall_rating',
        'comments',
    ];

    protected $casts = [
        'review_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }
}
