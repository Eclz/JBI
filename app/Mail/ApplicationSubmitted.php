<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ApplicationSubmitted extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $applicationNumber;

    public function __construct(User $user)
    {
        $this->user = $user;
        $this->applicationNumber = $this->generateApplicationNumber();
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Application Submitted Successfully - JBI University',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.application-submitted',
            with: [
                'user' => $this->user,
                'applicationNumber' => $this->applicationNumber,
            ],
        );
    }

    private function generateApplicationNumber()
    {
        return 'APP' . date('Y') . str_pad($this->user->id, 6, '0', STR_PAD_LEFT);
    }
}
