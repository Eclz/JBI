<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'created_by',
        'note',
        'type',
        'priority',
        'is_private',
        'noted_at',
    ];

    protected $casts = [
        'noted_at' => 'datetime',
        'is_private' => 'boolean',
    ];

    /**
     * Get the student that owns the note.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Get the user who created the note.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope for filtering by type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope for filtering by priority
     */
    public function scopeOfPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Scope for public notes only
     */
    public function scopePublic($query)
    {
        return $query->where('is_private', false);
    }

    /**
     * Scope for private notes only
     */
    public function scopePrivate($query)
    {
        return $query->where('is_private', true);
    }

    /**
     * Get priority badge class
     */
    public function getPriorityBadgeAttribute()
    {
        return match($this->priority) {
            'low' => 'badge-secondary',
            'medium' => 'badge-primary',
            'high' => 'badge-warning',
            'urgent' => 'badge-danger',
            default => 'badge-secondary'
        };
    }

    /**
     * Get type badge class
     */
    public function getTypeBadgeAttribute()
    {
        return match($this->type) {
            'general' => 'badge-secondary',
            'academic' => 'badge-primary',
            'disciplinary' => 'badge-danger',
            'counseling' => 'badge-info',
            'medical' => 'badge-warning',
            default => 'badge-secondary'
        };
    }
}
