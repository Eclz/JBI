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

class NewApplicationNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $application;
    public $link;

    public function __construct($applicationOrUser)
    {
        if ($applicationOrUser instanceof User) {
            $user = $applicationOrUser;
            $this->link = route('admin.users.show', $user);
            
            $this->application = (object) [
                'application_number' => $user->studentProfile 
                    ? $user->studentProfile->admission_number 
                    : ($user->facultyProfile ? $user->facultyProfile->employee_id : 'N/A'),
                'full_name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'type' => $user->role,
                'type_label' => ucfirst($user->role),
                'program' => $user->studentProfile ? $user->studentProfile->program : null,
                'previous_school' => $user->studentProfile ? $user->studentProfile->previous_school : null,
                'previous_gpa' => $user->studentProfile ? $user->studentProfile->previous_gpa : null,
                'position' => $user->facultyProfile ? $user->facultyProfile->position : null,
                'department' => $user->facultyProfile 
                    ? ($user->facultyProfile->department->name ?? 'N/A') 
                    : ($user->studentProfile ? ($user->studentProfile->department->name ?? 'N/A') : 'N/A'),
                'highest_degree' => $user->facultyProfile 
                    ? ($user->facultyProfile->qualifications['highest_degree'] ?? 'N/A') 
                    : 'N/A',
                'years_of_experience' => $user->facultyProfile 
                    ? ($user->facultyProfile->experience['years_of_experience'] ?? 0) 
                    : 0,
                'created_at' => $user->created_at ?? now(),
            ];
        } else {
            $this->application = $applicationOrUser;
            $this->link = route('admin.applications.show', $applicationOrUser);
        }
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
        );
    }
}

