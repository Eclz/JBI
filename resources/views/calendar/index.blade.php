@extends('layouts.app')

@section('title', 'Academic Calendar')

@section('content')
<div class="container-fluid px-4 py-4">
    @if(Auth::user()->isStudent())
        @include('partials.student-header-bar')
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold text-dark text-uppercase mb-0">
                <i class="bi bi-calendar-week text-primary me-2"></i>ACADEMIC CALENDAR & KEY EVENTS
            </h5>
            <p class="text-muted small mb-0">Academic Year {{ $currentYear?->name ?? '2026/2027' }} Schedule & Exam Dates</p>
        </div>
    </div>

    <div class="row g-4">
        <!-- Upcoming Examinations Card -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3 border-bottom border-danger border-2 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0 text-danger"><i class="bi bi-pencil-square me-2"></i>UPCOMING EXAMINATIONS</h6>
                    <span class="badge bg-danger px-3 py-1">{{ $upcomingExams->count() }} SCHEDULED</span>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush small">
                        @forelse($upcomingExams as $exam)
                            <div class="list-group-item p-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-bold text-primary">{{ $exam->course?->code }} - {{ $exam->title }}</span>
                                    <span class="badge bg-light text-dark border"><i class="bi bi-clock me-1"></i>{{ $exam->duration_minutes ?? 120 }} Mins</span>
                                </div>
                                <div class="text-muted small">
                                    <i class="bi bi-calendar-event me-1"></i>{{ $exam->start_time ? $exam->start_time->format('M d, Y h:i A') : 'TBA' }}
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4 text-muted">No upcoming exams scheduled.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Upcoming Course Assignments Card -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3 border-bottom border-primary border-2 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0 text-primary"><i class="bi bi-journal-text me-2"></i>COURSEWORK & ASSIGNMENTS</h6>
                    <span class="badge bg-primary px-3 py-1">{{ $upcomingAssignments->count() }} DUE</span>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush small">
                        @forelse($upcomingAssignments as $assign)
                            <div class="list-group-item p-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-bold text-dark">{{ $assign->course?->code }} - {{ $assign->title }}</span>
                                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary">{{ $assign->max_score ?? 100 }} Marks</span>
                                </div>
                                <div class="text-muted small">
                                    <i class="bi bi-hourglass-split me-1 text-danger"></i>Due Date: {{ $assign->due_date ? $assign->due_date->format('M d, Y h:i A') : 'TBA' }}
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4 text-muted">No upcoming assignment deadlines.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
