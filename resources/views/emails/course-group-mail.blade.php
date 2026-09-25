@extends('emails.layouts.master')

@section('title', $subjectLine . ' - ' . config('app.name', 'JBI University'))

@section('header_badge')
    <div style="margin-top: 14px;">
        <span class="badge badge-primary">📚 {{ $course->course_code }} &bull; {{ $course->name }}</span>
    </div>
@endsection

@section('content')
    <h2 class="greeting">Dear {{ $recipient->first_name ?? $recipient->name }},</h2>

    <p style="margin-bottom: 16px; color: #4a5568; font-size: 15px;">
        Your course lecturer, <strong>{{ $sender->full_name ?? $sender->name }}</strong>, has sent an announcement to all students enrolled in <strong>{{ $course->course_code }} ({{ $course->name }})</strong>:
    </p>

    <!-- Message Content Box -->
    <div class="callout" style="border-left-color: #3b5bdb;">
        <h3 style="margin: 0 0 10px 0; font-size: 16px; color: #1e293b;">{{ $subjectLine }}</h3>
        <div style="font-size: 15px; color: #334155; line-height: 1.6; white-space: pre-wrap;">{{ $body }}</div>
    </div>

    <!-- Metadata table -->
    <table role="presentation" class="meta-table" cellpadding="0" cellspacing="0">
        <tr>
            <td>Course Code</td>
            <td><strong>{{ $course->course_code }}</strong></td>
        </tr>
        <tr>
            <td>Instructor / Lecturer</td>
            <td>{{ $sender->full_name ?? $sender->name }}</td>
        </tr>
        <tr>
            <td>Sent To</td>
            <td>{{ $recipient->first_name }} {{ $recipient->last_name }} ({{ $recipient->student_id ?? 'Student' }})</td>
        </tr>
    </table>

    <div class="button-wrapper">
        <a href="{{ config('app.url', url('/')) }}/courses/{{ $course->id }}" class="btn-primary" target="_blank">
            Open Course in LMS &rarr;
        </a>
    </div>

    <p style="font-size: 13px; color: #64748b; margin-top: 25px; line-height: 1.5;">
        To reply or inquire about this course message, please use the LMS Mailbox or contact your instructor directly.
    </p>
@endsection
