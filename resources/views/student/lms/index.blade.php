@extends('layouts.app')

@section('title', 'My Learning')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">My Learning</h2>
            <p class="text-muted mb-0">Track progress, deadlines, and study activity across all enrolled courses.</p>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">Enrolled Courses</div>
                    <div class="h3 mb-0">{{ $summary['total_courses'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">Average Progress</div>
                    <div class="h3 mb-0">{{ $summary['avg_progress'] }}%</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">Completed Items</div>
                    <div class="h3 mb-0">{{ $summary['total_completed'] }}/{{ $summary['total_items'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">Due In 7 Days</div>
                    <div class="h3 mb-0">{{ $summary['due_7_days'] }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white">
            <h5 class="mb-0">Upcoming Learning Tasks (Next 7 Days)</h5>
        </div>
        <div class="card-body p-0">
            <ul class="list-group list-group-flush">
                @forelse($upcomingItems->take(8) as $item)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fw-semibold">{{ $item['title'] }}</div>
                            <div class="small text-muted">{{ $item['type'] }} • {{ $item['course'] ?? 'Course' }}</div>
                        </div>
                        <div class="text-end">
                            <div class="small text-muted">{{ \Carbon\Carbon::parse($item['due_at'])->format('M d, Y H:i') }}</div>
                            <a href="{{ $item['link'] }}" class="btn btn-sm btn-outline-primary mt-1">Open</a>
                        </div>
                    </li>
                @empty
                    <li class="list-group-item text-muted">No assignment, quiz, or exam deadlines in the next 7 days.</li>
                @endforelse
            </ul>
        </div>
    </div>

    <div class="row g-4">
        @forelse($courses as $entry)
            @php
                $course = $entry['course'];
                $progress = $entry['progress'];
                $lastActivity = $entry['last_activity'];
                $isCompleted = $progress['total'] > 0 && $progress['percent'] >= 100;
            @endphp
            <div class="col-md-6 col-xl-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body d-flex flex-column">
                        <div class="mb-2 text-muted small">{{ $course->semester->name ?? 'No Semester' }}</div>
                        <div class="d-flex justify-content-between align-items-start">
                            <h5 class="mb-1">{{ $course->name }}</h5>
                            @if($isCompleted)
                                <span class="badge bg-success">Completed</span>
                            @endif
                        </div>
                        <div class="text-muted small mb-3">{{ $course->code ?? $course->course_code }} • {{ $course->instructor->full_name ?? $course->instructor->name ?? 'Instructor TBA' }}</div>

                        <div class="mb-2 d-flex justify-content-between">
                            <span class="small text-muted">Progress</span>
                            <span class="small fw-semibold">{{ $progress['percent'] }}%</span>
                        </div>
                        <div class="progress mb-3" style="height: 10px;">
                            <div class="progress-bar" role="progressbar" style="width: {{ $progress['percent'] }}%"></div>
                        </div>

                        <div class="small text-muted mb-3">
                            {{ $progress['completed'] }} / {{ $progress['total'] }} learning items completed
                        </div>
                        <div class="small text-muted mb-3">
                            Last activity: {{ $lastActivity ? $lastActivity->diffForHumans() : 'No activity yet' }}
                        </div>

                        <div class="mt-auto">
                            @if($isCompleted)
                                <div class="d-grid gap-2">
                                    <a href="{{ route('student.lms.show', $course) }}" class="btn btn-outline-primary">Review Course</a>
                                    <a href="{{ route('student.lms.certificate', $course) }}" class="btn btn-success">View Certificate</a>
                                </div>
                            @else
                                <a href="{{ route('student.lms.show', $course) }}" class="btn btn-primary w-100">Continue Learning</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info mb-0">No enrolled courses yet. Enroll in courses to start learning.</div>
            </div>
        @endforelse
    </div>
</div>
@endsection
