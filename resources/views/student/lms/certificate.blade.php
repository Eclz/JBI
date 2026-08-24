@extends('layouts.app')

@section('title', 'Course Certificate')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">Course Completion Certificate</h2>
        <div class="d-flex gap-2">
            <a href="{{ route('student.lms.show', $course) }}" class="btn btn-outline-secondary">Back to Course</a>
            <button type="button" class="btn btn-primary" onclick="window.print()">Print Certificate</button>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-5 text-center">
            <img src="{{ asset('images/jbi-blue.webp') }}" alt="JBI University Logo" style="height: 72px;" class="mb-3">
            <h3 class="mb-1">JBI University</h3>
            <p class="text-muted mb-4">Certificate of Completion</p>

            <p class="mb-1 text-muted">This certifies that</p>
            <h1 class="display-6 mb-3">{{ $student->full_name ?? $student->name }}</h1>

            <p class="mb-1 text-muted">has successfully completed the course</p>
            <h4 class="mb-4">{{ $course->name }}</h4>

            <div class="row justify-content-center text-start mt-4">
                <div class="col-md-8">
                    <div class="d-flex justify-content-between border-bottom py-2">
                        <span class="text-muted">Course Code</span>
                        <strong>{{ $course->course_code ?? $course->code ?? 'N/A' }}</strong>
                    </div>
                    <div class="d-flex justify-content-between border-bottom py-2">
                        <span class="text-muted">Completion Score</span>
                        <strong>{{ $progress['percent'] }}%</strong>
                    </div>
                    <div class="d-flex justify-content-between border-bottom py-2">
                        <span class="text-muted">Completion Date</span>
                        <strong>{{ \Carbon\Carbon::parse($completionDate)->format('F d, Y') }}</strong>
                    </div>
                </div>
            </div>

            <div class="mt-5">
                <p class="text-muted mb-1">Issued by Academic Affairs</p>
                <p class="small text-muted mb-0">Reference: LMS-{{ $course->id }}-{{ $student->id }}-{{ \Carbon\Carbon::parse($completionDate)->format('Ymd') }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
