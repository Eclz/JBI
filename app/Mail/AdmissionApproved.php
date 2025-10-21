<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdmissionApproved extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $defaultPassword;
    public $loginUrl;

    public function __construct(User $user, $defaultPassword)
    {
        $this->user = $user;
        $this->defaultPassword = $defaultPassword;
        $this->loginUrl = route('login');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Congratulations! Your Application Has Been Approved - JBI University',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.admission-approved',
            with: [
                'user' => $this->user,
                'defaultPassword' => $this->defaultPassword,
                'loginUrl' => $this->loginUrl,
            ],
        );
    }
}
