<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VotingCandidate extends Model
{
    use HasFactory;

    protected $fillable = [
        'voting_position_id',
        'user_id',
        'name',
        'photo',
        'manifesto',
        'party_affiliation',
        'status',
        'vetted_at',
        'vetting_notes',
    ];

    protected $casts = [
        'vetted_at' => 'datetime',
    ];

    public function position()
    {
        return $this->belongsTo(VotingPosition::class, 'voting_position_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }
}
