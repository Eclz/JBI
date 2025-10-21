@extends('layouts.app')

@section('title', 'Course Assignments - ' . $course->name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-1">Course Assignments</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.courses.index') }}">Courses</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.courses.show', $course) }}">{{ $course->name }}</a></li>
                            <li class="breadcrumb-item active">Assignments</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <a href="{{ route('admin.courses.show', $course) }}" class="btn btn-outline-secondary me-2">
                        <i class="bi bi-arrow-left"></i> Back to Course
                    </a>
                    <a href="#" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Create Assignment
                    </a>
                </div>
            </div>

            <!-- Course Info -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h5 class="card-title mb-1">{{ $course->name }}</h5>
                            <p class="text-muted mb-0">{{ $course->code }} • {{ $course->credits }} Credits</p>
                        </div>
                        <div class="col-md-4 text-end">
                            <span class="badge bg-primary fs-6">{{ $assignments->total() }} Assignments</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Assignments List -->
            @if($assignments->count() > 0)
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col">
                                <h5 class="card-title mb-0">Assignments</h5>
                            </div>
                            <div class="col-auto">
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-outline-secondary active" data-filter="all">All</button>
                                    <button type="button" class="btn btn-outline-secondary" data-filter="active">Active</button>
                                    <button type="button" class="btn btn-outline-secondary" data-filter="past-due">Past Due</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Assignment</th>
                                        <th>Due Date</th>
                                        <th>Points</th>
                                        <th>Submissions</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($assignments as $assignment)
                                        <tr class="assignment-row"
                                            data-status="{{ $assignment->due_date->isPast() ? 'past-due' : 'active' }}">
                                            <td>
                                                <div>
                                                    <h6 class="mb-1">{{ $assignment->title }}</h6>
                                                    @if($assignment->description)
                                                        <small class="text-muted">{{ Str::limit($assignment->description, 80) }}</small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    <span class="fw-medium">{{ $assignment->due_date->format('M d, Y') }}</span>
                                                    <br>
                                                    <small class="text-muted">{{ $assignment->due_date->format('g:i A') }}</small>
                                                </div>
                                                @if($assignment->due_date->isPast())
                                                    <span class="badge bg-danger mt-1">Past Due</span>
                                                @elseif($assignment->due_date->isToday())
                                                    <span class="badge bg-warning mt-1">Due Today</span>
                                                @elseif($assignment->due_date->diffInDays() <= 3)
                                                    <span class="badge bg-info mt-1">Due Soon</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="fw-medium">{{ $assignment->max_points }}</span>
                                                <small class="text-muted">pts</small>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="me-2">
                                                        <span class="fw-medium">{{ $assignment->submissions_count }}</span>
                                                        <small class="text-muted">/ {{ $course->enrollments->where('status', 'enrolled')->count() }}</small>
                                                    </div>
                                                    <div class="progress" style="width: 60px; height: 6px;">
                                                        @php
                                                            $totalStudents = $course->enrollments->where('status', 'enrolled')->count();
                                                            $percentage = $totalStudents > 0 ? ($assignment->submissions_count / $totalStudents) * 100 : 0;
                                                        @endphp
                                                        <div class="progress-bar" style="width: {{ $percentage }}%"></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @if($assignment->is_published)
                                                    <span class="badge bg-success">Published</span>
                                                @else
                                                    <span class="badge bg-secondary">Draft</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="#" class="btn btn-outline-primary" title="View Assignment">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <a href="#" class="btn btn-outline-info" title="View Submissions">
                                                        <i class="bi bi-file-earmark-text"></i>
                                                    </a>
                                                    <a href="#" class="btn btn-outline-warning" title="Edit Assignment">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-outline-danger" title="Delete Assignment">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        {{ $assignments->links() }}
                    </div>
                </div>
            @else
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-clipboard-check display-1 text-muted"></i>
                        <h5 class="mt-3">No Assignments Yet</h5>
                        <p class="text-muted">Create your first assignment to get started.</p>
                        <a href="#" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-2"></i>Create First Assignment
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Filter assignments
document.querySelectorAll('[data-filter]').forEach(button => {
    button.addEventListener('click', function() {
        const filter = this.dataset.filter;

        // Update active button
        document.querySelectorAll('[data-filter]').forEach(btn => btn.classList.remove('active'));
        this.classList.add('active');

        // Filter rows
        document.querySelectorAll('.assignment-row').forEach(row => {
            if (filter === 'all') {
                row.style.display = '';
            } else {
                const status = row.dataset.status;
                row.style.display = status === filter ? '' : 'none';
            }
        });
    });
});
</script>
@endpush

@push('styles')
<style>
.progress {
    border-radius: 10px;
}

.table td {
    vertical-align: middle;
}

.btn-group-sm .btn {
    padding: 0.25rem 0.5rem;
}

.assignment-row:hover {
    background-color: rgba(0, 123, 255, 0.05);
}
</style>
@endpush
