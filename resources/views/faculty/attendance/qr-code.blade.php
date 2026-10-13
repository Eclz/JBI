@extends('layouts.app')

@section('title', 'Attendance Check-in Code')

@section('content')
<div class="mb-4">
    <a href="{{ route('faculty.courses.attendance.show', $course) }}" class="text-decoration-none small">
        <i class="bi bi-arrow-left me-1"></i>Back to attendance
    </a>
    <h1 class="h3 mt-2 mb-0">Check-in Code</h1>
    <p class="text-muted mb-0">{{ $course->code }} - {{ $course->name }} &middot; {{ now()->format('D, M d Y') }}</p>
</div>

<div class="card text-center">
    <div class="card-body py-5">
        @if($qrCode)
            <div class="d-inline-block p-3 bg-white border rounded mb-3">{!! $qrCode !!}</div>
        @endif
        <div class="text-muted small mb-1">Today's check-in code</div>
        <div class="display-4 fw-bold font-monospace">{{ $checkInCode }}</div>
        <p class="text-muted mt-3 mb-0">This code changes daily and is valid for today's session only.</p>
    </div>
</div>
@endsection
