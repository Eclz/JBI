<?php

namespace App\Mail;

use App\Models\Application;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminAdmissionNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $student;
    public $application;
    public $link;

    public function __construct(User $student, ?Application $application = null)
    {
        $this->student = $student;
        $this->application = $application;
        $this->link = $application ? route('admin.applications.show', $application) : route('admin.dashboard');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Student Admitted Successfully: ' . $this->student->full_name . ' - JBI University',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.admin-admission-notification',
        );
    }
}
