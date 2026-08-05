<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VotingPosition extends Model
{
    use HasFactory;

    protected $fillable = [
        'voting_session_id',
        'title',
        'description',
        'display_order',
    ];

    public function session()
    {
        return $this->belongsTo(VotingSession::class, 'voting_session_id');
    }

    public function candidates()
    {
        return $this->hasMany(VotingCandidate::class);
    }

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }
}
