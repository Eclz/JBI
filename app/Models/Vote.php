<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vote extends Model
{
    use HasFactory;

    protected $fillable = [
        'voting_session_id',
        'voting_position_id',
        'voting_candidate_id',
        'user_id',
        'ip_address',
    ];

    public function session()
    {
        return $this->belongsTo(VotingSession::class, 'voting_session_id');
    }

    public function position()
    {
        return $this->belongsTo(VotingPosition::class, 'voting_position_id');
    }

    public function candidate()
    {
        return $this->belongsTo(VotingCandidate::class, 'voting_candidate_id');
    }

    public function voter()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
