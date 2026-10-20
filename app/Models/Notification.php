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
            $user = $notification->user;
            $priority = strtolower($notification->priority ?? 'normal');
            $emailPref = data_get($user?->preferences, 'notifications.email',
                data_get($user?->preferences, 'email_notifications', true));
            if ($user?->is_active && $user->email && (in_array($priority, ['urgent', 'high']) || $emailPref)) {
                $notification->updateQuietly(['data' => array_merge($notification->data ?? [], ['email_status' => 'queued'])]);
                try {
                    \App\Jobs\SendNotificationEmail::dispatch($notification->id);
                } catch (\Throwable $exception) {
                    $notification->updateQuietly(['data' => array_merge($notification->data ?? [], ['email_status' => 'failed'])]);
                    \Illuminate\Support\Facades\Log::error('Notification email could not be queued', ['notification_id' => $notification->id]);
                }
            }
        });
    }
}
