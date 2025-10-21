<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewApplicationNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $applicant;
    public $adminDashboardUrl;

    public function __construct(User $applicant)
    {
        $this->applicant = $applicant;
        $this->adminDashboardUrl = route('admin.applications.index');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Application Received - JBI University',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.new-application-notification',
            with: [
                'applicant' => $this->applicant,
                'adminDashboardUrl' => $this->adminDashboardUrl,
            ],
        );
    }
}
