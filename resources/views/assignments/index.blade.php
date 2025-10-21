@extends('layouts.app')

@section('title', 'Assignments')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0 text-primary">
            <i class="bi bi-clipboard-check me-2"></i>Assignments
        </h1>
        <p class="text-muted mb-0">
            @if(Auth::user()->role === 'faculty')
                Manage course assignments and submissions
            @else
                View and submit your assignments
            @endif
        </p>
    </div>
    @if(Auth::user()->role === 'faculty')
        <a href="{{ route('assignments.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-2"></i>Create Assignment
        </a>
    @endif
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('assignments.index') }}" class="row g-3">
            <div class="col-md-4">
                <label for="course" class="form-label">Course</label>
                <select class="form-select" id="course" name="course">
                    <option value="">All Courses</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" 
                                {{ request('course') == $course->id ? 'selected' : '' }}>
                            {{ $course->code }} - {{ $course->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="type" class="form-label">Type</label>
                <select class="form-select" id="type" name="type">
                    <option value="">All Types</option>
                    <option value="assignment" {{ request('type') == 'assignment' ? 'selected' : '' }}>Assignment</option>
                    <option value="quiz" {{ request('type') == 'quiz' ? 'selected' : '' }}>Quiz</option>
                    <option value="exam" {{ request('type') == 'exam' ? 'selected' : '' }}>Exam</option>
                    <option value="project" {{ request('type') == 'project' ? 'selected' : '' }}>Project</option>
                    <option value="presentation" {{ request('type') == 'presentation' ? 'selected' : '' }}>Presentation</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status">
                    <option value="">All Statuses</option>
                    <option value="upcoming" {{ request('status') == 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-outline-primary me-2">
                    <i class="bi bi-search"></i> Filter
                </button>
                <a href="{{ route('assignments.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-x-lg"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Assignments List -->
<div class="row">
    @forelse($assignments as $assignment)
        @php
            $dueDate = \Carbon\Carbon::parse($assignment->due_date);
            $isOverdue = $dueDate->isPast();
            $isDueSoon = $dueDate->diffInDays(now()) <= 3 && !$isOverdue;
            
            $userSubmission = null;
            if (Auth::user()->role === 'student') {
                $userSubmission = $assignment->submissions()->where('student_id', Auth::id())->first();
            }
        @endphp
        
        <div class="col-lg-6 col-xl-4 mb-4">
            <div class="card h-100 {{ $isOverdue ? 'border-danger' : ($isDueSoon ? 'border-warning' : '') }}">
                <div class="card-header d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1">
                        <h6 class="card-title mb-1">
                            <a href="{{ route('assignments.show', $assignment) }}" class="text-decoration-none">
                                {{ $assignment->title }}
                            </a>
                        </h6>
                        <small class="text-muted">{{ $assignment->course->code }} - {{ $assignment->course->name }}</small>
                    </div>
                    <span class="badge bg-{{ $assignment->type === 'exam' ? 'danger' : ($assignment->type === 'quiz' ? 'warning' : 'info') }}">
                        {{ ucfirst($assignment->type) }}
                    </span>
                </div>
                
                <div class="card-body">
                    <p class="card-text text-muted">{{ Str::limit($assignment->description, 100) }}</p>
                    
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <small class="text-muted">Points</small>
                            <div class="fw-bold">{{ $assignment->points }}</div>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Weight</small>
                            <div class="fw-bold">{{ $assignment->weight }}%</div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <small class="text-muted">Due Date</small>
                        <div class="{{ $isOverdue ? 'text-danger' : ($isDueSoon ? 'text-warning' : '') }}">
                            {{ $dueDate->format('M d, Y g:i A') }}
                            @if($isOverdue)
                                <i class="bi bi-exclamation-triangle ms-1"></i>
                            @elseif($isDueSoon)
                                <i class="bi bi-clock ms-1"></i>
                            @endif
                        </div>
                        <small class="text-muted">{{ $dueDate->diffForHumans() }}</small>
                    </div>
                    
                    @if(Auth::user()->role === 'student')
                        @if($userSubmission)
                            <div class="alert alert-success py-2">
                                <i class="bi bi-check-circle me-2"></i>
                                <strong>Submitted</strong>
                                @if($userSubmission->grade !== null)
                                    <br><small>Grade: {{ $userSubmission->grade }}/{{ $assignment->points }}</small>
                                @endif
                            </div>
                        @elseif($isOverdue)
                            <div class="alert alert-danger py-2">
                                <i class="bi bi-x-circle me-2"></i>
                                <strong>Overdue</strong>
                            </div>
                        @else
                            <div class="alert alert-warning py-2">
                                <i class="bi bi-clock me-2"></i>
                                <strong>Pending Submission</strong>
                            </div>
                        @endif
                    @else
                        @php
                            $submissionCount = $assignment->submissions()->count();
                            $enrollmentCount = $assignment->course->enrollments()->count();
                        @endphp
                        <div class="mb-2">
                            <small class="text-muted">Submissions</small>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar" role="progressbar" 
                                     style="width: {{ $enrollmentCount > 0 ? ($submissionCount / $enrollmentCount) * 100 : 0 }}%">
                                </div>
                            </div>
                            <small class="text-muted">{{ $submissionCount }}/{{ $enrollmentCount }} submitted</small>
                        </div>
                    @endif
                </div>
                
                <div class="card-footer bg-transparent">
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">
                            {{ $assignment->course->instructor->first_name ?? 'TBA' }} 
                            {{ $assignment->course->instructor->last_name ?? '' }}
                        </small>
                        <a href="{{ route('assignments.show', $assignment) }}" class="btn btn-sm btn-outline-primary">
                            View Details
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="text-center py-5">
                <i class="bi bi-clipboard-check display-1 text-muted"></i>
                <h4 class="mt-3">No assignments found</h4>
                <p class="text-muted">
                    @if(Auth::user()->role === 'faculty')
                        You haven't created any assignments yet.
                    @else
                        No assignments match your current filters.
                    @endif
                </p>
                @if(Auth::user()->role === 'faculty')
                    <a href="{{ route('assignments.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-lg me-2"></i>Create First Assignment
                    </a>
                @endif
            </div>
        </div>
    @endforelse
</div>

<!-- Pagination -->
@if($assignments->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $assignments->links() }}
    </div>
@endif
@endsection

@push('styles')
<style>
.card.border-danger {
    border-color: #dc3545 !important;
    border-width: 2px;
}

.card.border-warning {
    border-color: #ffc107 !important;
    border-width: 2px;
}

.progress {
    background-color: #e9ecef;
}
</style>
@endpush
