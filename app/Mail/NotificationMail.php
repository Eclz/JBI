<?php

namespace App\Mail;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $notification;
    public $user;

    public function __construct(Notification $notification, User $user)
    {
        $this->notification = $notification;
        $this->user = $user;
    }

    public function envelope(): Envelope
    {
        $priority = strtolower($this->notification->priority ?? 'normal');
        $prefix = match ($priority) {
            'urgent' => '[URGENT] ',
            'high' => '[IMPORTANT] ',
            default => '',
        };

        $subject = $prefix . ($this->notification->title ?: 'New Notification from ' . config('app.name', 'JBI University'));

        return new Envelope(
            subject: $subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.notification-mail',
            with: [
                'notification' => $this->notification,
                'user' => $this->user,
            ],
        );
    }
}
