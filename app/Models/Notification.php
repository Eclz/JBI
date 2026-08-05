<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'data',
        'action_url',
        'priority',
        'is_read',
        'email_sent',
        'sms_sent',
        'read_at',
        'scheduled_for',
    ];

    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
        'email_sent' => 'boolean',
        'sms_sent' => 'boolean',
        'read_at' => 'datetime',
        'scheduled_for' => 'datetime',
    ];

    /**
     * Get the user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for unread notifications
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope for read notifications
     */
    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    /**
     * Scope by priority
     */
    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Mark as read
     */
    public function markAsRead()
    {
        $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    /**
     * Automatically send email notification when created.
     */
    protected static function booted()
    {
        static::created(function ($notification) {
            try {
                $user = $notification->user;
                if ($user && !empty($user->email)) {
                    if (in_array($user->role, ['student', 'applicant'])) {
                        \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\NotificationMail($notification, $user));
                        $notification->updateQuietly(['email_sent' => true]);
                    }
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Failed to send notification email: ' . $e->getMessage());
            }
        });
    }
}
