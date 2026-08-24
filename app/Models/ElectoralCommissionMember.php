<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ElectoralCommissionMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'voting_session_id',
        'user_id',
        'role_title',
        'appointed_at',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'appointed_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function session()
    {
        return $this->belongsTo(VotingSession::class, 'voting_session_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
