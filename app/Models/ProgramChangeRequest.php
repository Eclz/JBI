<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgramChangeRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'current_program_id',
        'requested_program_id',
        'reason',
        'status',
        'reviewed_by',
        'reviewed_at',
        'review_notes',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function currentProgram()
    {
        return $this->belongsTo(Program::class, 'current_program_id');
    }

    public function requestedProgram()
    {
        return $this->belongsTo(Program::class, 'requested_program_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
