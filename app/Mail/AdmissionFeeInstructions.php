<?php

namespace App\Mail;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdmissionFeeInstructions extends Mailable
{
    use Queueable, SerializesModels;

    public $application;
    public $admissionFee;

    public function __construct(Application $application, $admissionFee = 50000)
    {
        $this->application = $application;
        $this->admissionFee = $admissionFee;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Admission Fee Payment Instructions - JBI University',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.admission-fee-instructions',
            text: 'emails.admission-fee-instructions-text',
        );
    }
}
