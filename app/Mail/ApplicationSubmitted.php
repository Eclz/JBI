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

class ApplicationSubmitted extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $application;
    public $user;
    public $applicationNumber;

    public function __construct($applicationOrUser)
    {
        if ($applicationOrUser instanceof User) {
            $this->user = $applicationOrUser;
            $this->application = null;
            if ($applicationOrUser->studentProfile) {
                $this->applicationNumber = $applicationOrUser->studentProfile->admission_number;
            } elseif ($applicationOrUser->facultyProfile) {
                $this->applicationNumber = $applicationOrUser->facultyProfile->employee_id;
            } else {
                $this->applicationNumber = 'N/A';
            }
        } else {
            $this->application = $applicationOrUser;
            $this->applicationNumber = $applicationOrUser->application_number;
            
            $this->user = (object) [
                'first_name' => $applicationOrUser->first_name,
                'last_name' => $applicationOrUser->last_name,
                'email' => $applicationOrUser->email,
                'role' => $applicationOrUser->type,
                'created_at' => $applicationOrUser->created_at ?? now(),
                'studentProfile' => $applicationOrUser->type === 'student' ? (object) [
                    'program' => $applicationOrUser->program,
                    'department' => (object) [
                        'name' => $applicationOrUser->department ?? 'N/A'
                    ],
                    'admission_number' => $applicationOrUser->admission_number ?? 'Pending'
                ] : null,
                'facultyProfile' => $applicationOrUser->type === 'faculty' ? (object) [
                    'position' => $applicationOrUser->position,
                    'department' => (object) [
                        'name' => $applicationOrUser->department ?? 'N/A'
                    ],
                    'employee_id' => $applicationOrUser->employee_id ?? 'Pending'
                ] : null,
            ];
        }
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
        );
    }
}

