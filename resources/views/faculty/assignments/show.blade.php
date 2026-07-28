@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1" style="color: #1e293b; font-weight: 600;">{{ $assignment->title }}</h1>
            <p class="text-muted mb-0">{{ $assignment->course->code }} - {{ $assignment->course->name }}</p>
        </div>
        <div class="btn-group">
            <a href="{{ route('faculty.assignments.edit', $assignment) }}" class="btn btn-outline-primary">
                <i class="bi bi-pencil me-2"></i>Edit
            </a>
            <a href="{{ route('faculty.assignments.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background-color: #dbeafe;">
                                <i class="bi bi-people-fill" style="font-size: 1.5rem; color: #3b82f6;"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h3 class="mb-0" style="color: #1e293b; font-weight: 700;">{{ $submittedCount }}</h3>
                            <p class="text-muted mb-0 small">Submitted</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background-color: #dcfce7;">
                                <i class="bi bi-check2-circle" style="font-size: 1.5rem; color: #22c55e;"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h3 class="mb-0" style="color: #1e293b; font-weight: 700;">{{ $gradedCount }}</h3>
                            <p class="text-muted mb-0 small">Graded</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background-color: #fef3c7;">
                                <i class="bi bi-star-fill" style="font-size: 1.5rem; color: #f59e0b;"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h3 class="mb-0" style="color: #1e293b; font-weight: 700;">{{ number_format($averageScore ?? 0, 1) }}</h3>
                            <p class="text-muted mb-0 small">Average Score</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background-color: #e0e7ff;">
                                <i class="bi bi-trophy-fill" style="font-size: 1.5rem; color: #6366f1;"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h3 class="mb-0" style="color: #1e293b; font-weight: 700;">{{ $assignment->max_score }}</h3>
                            <p class="text-muted mb-0 small">Max Score</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Submissions Table -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">Student Submissions</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead style="background-color: #f8fafc;">
                                <tr>
                                    <th style="padding: 1rem;">Student</th>
                                    <th style="padding: 1rem;">Submitted</th>
                                    <th style="padding: 1rem;">Score</th>
                                    <th style="padding: 1rem;">Status</th>
                                    <th style="padding: 1rem;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($assignment->submissions as $submission)
                                <tr>
                                    <td class="align-middle" style="padding: 1rem;">
                                        <div class="fw-semibold" style="color: #1e293b;">{{ $submission->student->name }}</div>
                                        <small class="text-muted">{{ $submission->student->email }}</small>
                                    </td>
                                    <td class="align-middle" style="padding: 1rem;">
                                        @if($submission->submitted_at)
                                            {{ \Carbon\Carbon::parse($submission->submitted_at)->format('M d, Y h:i A') }}
                                        @else
                                            <span class="text-muted">Not submitted</span>
                                        @endif
                                    </td>
                                    <td class="align-middle" style="padding: 1rem;">
                                        @if($submission->score !== null)
                                            <span class="fw-semibold">{{ $submission->score }}/{{ $assignment->max_score }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="align-middle" style="padding: 1rem;">
                                        @if($submission->score !== null)
                                            <span class="badge bg-success">Graded</span>
                                        @elseif($submission->submitted_at)
                                            <span class="badge bg-warning">Pending</span>
                                        @else
                                            <span class="badge bg-secondary">Not Submitted</span>
                                        @endif
                                    </td>
                                    <td class="align-middle" style="padding: 1rem;">
                                        @if($submission->submitted_at)
                                            <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#gradeModal{{ $submission->id }}">
                                                <i class="bi bi-pencil-square"></i> Grade
                                            </button>

                                            <!-- Grade Modal -->
                                            <div class="modal fade" id="gradeModal{{ $submission->id }}" tabindex="-1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <form action="{{ route('faculty.assignments.grade', [$assignment, $submission]) }}" method="POST">
                                                            @csrf
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Grade Submission</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="mb-3">
                                                                    <label class="form-label fw-semibold">Student</label>
                                                                    <p>{{ $submission->student->name }}</p>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="score{{ $submission->id }}" class="form-label fw-semibold">Score</label>
                                                                    <input type="number" class="form-control" id="score{{ $submission->id }}" name="score" value="{{ $submission->score }}" min="0" max="{{ $assignment->max_score }}" step="0.01" required>
                                                                    <small class="text-muted">Max: {{ $assignment->max_score }}</small>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="feedback{{ $submission->id }}" class="form-label fw-semibold">Feedback</label>
                                                                    <textarea class="form-control" id="feedback{{ $submission->id }}" name="feedback" rows="4">{{ $submission->feedback }}</textarea>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                                <button type="submit" class="btn btn-primary">Save Grade</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <i class="bi bi-inbox" style="font-size: 3rem; color: #cbd5e1;"></i>
                                        <p class="text-muted mt-3">No submissions yet</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Assignment Details Sidebar -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0 fw-semibold">Assignment Details</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="text-muted small">Course</label>
                        <p class="mb-0 fw-semibold">{{ $assignment->course->code }} - {{ $assignment->course->name }}</p>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted small">Due Date</label>
                        <p class="mb-0 fw-semibold">{{ \Carbon\Carbon::parse($assignment->due_date)->format('M d, Y h:i A') }}</p>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted small">Maximum Score</label>
                        <p class="mb-0 fw-semibold">{{ $assignment->max_score }}</p>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted small">Submission Type</label>
                        <p class="mb-0 fw-semibold">{{ ucfirst($assignment->submission_type) }}</p>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted small">Description</label>
                        <p class="mb-0">{{ $assignment->description }}</p>
                    </div>

                    @if($assignment->instructions)
                    <div class="mb-3">
                        <label class="text-muted small">Instructions</label>
                        <p class="mb-0">{{ $assignment->instructions }}</p>
                    </div>
                    @endif

                    @if($assignment->file_path)
                    <div class="mb-3">
                        <label class="text-muted small">Attachment</label>
                        <div>
                            <a href="{{ Storage::url($assignment->file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-download me-1"></i> Download
                            </a>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
