<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResearchGrant extends Model
{
    use HasFactory;

    protected $fillable = [
        'grant_code',
        'project_title',
        'donor_organization',
        'total_grant_amount',
        'disbursed_amount',
        'spent_amount',
        'start_date',
        'end_date',
        'principal_investigator_id',
        'status',
    ];

    protected $casts = [
        'total_grant_amount' => 'decimal:2',
        'disbursed_amount' => 'decimal:2',
        'spent_amount' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function principalInvestigator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'principal_investigator_id');
    }
}
