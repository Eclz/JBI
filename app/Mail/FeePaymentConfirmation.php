<?php

namespace App\Mail;

use App\Models\FeeRecord;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FeePaymentConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $feeRecord;
    public $paymentAmount;

    /**
     * Create a new message instance.
     */
    public function __construct(FeeRecord $feeRecord, float $paymentAmount)
    {
        $this->feeRecord = $feeRecord;
        $this->paymentAmount = $paymentAmount;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Payment Confirmation - Invoice #' . $this->feeRecord->invoice_number,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.fee-payment-confirmation',
            with: [
                'feeRecord' => $this->feeRecord,
                'student' => $this->feeRecord->student,
                'paymentAmount' => $this->paymentAmount,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
