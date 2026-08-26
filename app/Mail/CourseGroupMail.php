<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Models\Course;

class CourseGroupMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $recipient;
    public $sender;
    public $course;
    public $subjectLine;
    public $body;

    public function __construct(User $recipient, User $sender, Course $course, string $subjectLine, string $body)
    {
        $this->recipient = $recipient;
        $this->sender = $sender;
        $this->course = $course;
        $this->subjectLine = $subjectLine;
        $this->body = $body;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "[{$this->course->course_code}] {$this->subjectLine}",
            replyTo: [$this->sender->email => $this->sender->full_name],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.course-group-mail',
        );
    }
}
