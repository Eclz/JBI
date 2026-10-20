<?php

namespace App\Jobs;

use App\Mail\NotificationMail;
use App\Models\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendNotificationEmail implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $timeout = 30;

    public function __construct(public int $notificationId)
    {
        $this->onQueue('onboarding');
        $this->afterCommit();
    }

    public function backoff(): array
    {
        return [30, 120, 300];
    }

    public function handle(): void
    {
        $notification = Notification::with('user')->find($this->notificationId);
        if (! $notification || $notification->email_sent || ! $notification->user?->email) {
            return;
        }
        Mail::to($notification->user->email)->send(new NotificationMail($notification, $notification->user));
        $notification->updateQuietly([
            'email_sent' => true,
            'data' => array_merge($notification->data ?? [], ['email_status' => 'sent']),
        ]);
    }

    public function failed(?\Throwable $exception): void
    {
        $notification = Notification::find($this->notificationId);
        if ($notification && ! $notification->email_sent) {
            $notification->updateQuietly(['data' => array_merge($notification->data ?? [], ['email_status' => 'failed'])]);
        }
    }
}
