<?php

namespace App\Mail;

use App\Models\FeeRecord;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FeePaymentReminder extends Mailable
{
    use Queueable, SerializesModels;

    public $feeRecord;
    public $reminderType;

    /**
     * Create a new message instance.
     */
    public function __construct(FeeRecord $feeRecord)
    {
        $this->feeRecord = $feeRecord;

        // Determine reminder type
        if ($feeRecord->status === 'overdue' || $feeRecord->isOverdue()) {
            $this->reminderType = 'overdue';
        } elseif ($feeRecord->due_date->diffInDays(now()) <= 7) {
            $this->reminderType = 'due_soon';
        } else {
            $this->reminderType = 'general';
        }
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = match($this->reminderType) {
            'overdue' => 'Urgent: Overdue Fee Payment Required',
            'due_soon' => 'Reminder: Fee Payment Due Soon',
            default => 'Fee Payment Reminder',
        };

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.fee-payment-reminder',
            with: [
                'feeRecord' => $this->feeRecord,
                'student' => $this->feeRecord->student,
                'feeStructure' => $this->feeRecord->feeStructure,
                'reminderType' => $this->reminderType,
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
