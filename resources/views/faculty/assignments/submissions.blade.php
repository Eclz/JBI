@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1" style="color: #1e293b; font-weight: 600;">{{ $assignment->title }} - Submissions</h1>
            <p class="text-muted mb-0">{{ $assignment->course->code }} &mdash; {{ $assignment->course->name }}</p>
        </div>
        <div class="btn-group">
            <a href="{{ route('faculty.assignments.show', $assignment) }}" class="btn btn-outline-primary">
                <i class="bi bi-eye me-2"></i>Assignment Details
            </a>
            <a href="{{ route('faculty.assignments.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back to Assignments
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Submissions List Card -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-semibold text-dark">
                <i class="bi bi-file-earmark-check me-2 text-primary"></i>Student Submissions
            </h5>
            <span class="badge bg-primary fs-6">{{ $submissions->total() }} Total</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead style="background-color: #f8fafc;">
                        <tr>
                            <th style="padding: 1rem;">Student</th>
                            <th style="padding: 1rem;">Submitted At</th>
                            <th style="padding: 1rem;">Score</th>
                            <th style="padding: 1rem;">Status</th>
                            <th style="padding: 1rem;">Attachment</th>
                            <th style="padding: 1rem;" class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($submissions as $submission)
                        <tr>
                            <td style="padding: 1rem;">
                                <div class="fw-semibold text-dark">{{ $submission->student->name }}</div>
                                <small class="text-muted">{{ $submission->student->email }}</small>
                            </td>
                            <td style="padding: 1rem;">
                                @if($submission->submitted_at)
                                    <span class="fw-medium text-dark">{{ \Carbon\Carbon::parse($submission->submitted_at)->format('M d, Y h:i A') }}</span>
                                @else
                                    <span class="text-muted">Not submitted</span>
                                @endif
                            </td>
                            <td style="padding: 1rem;">
                                @if($submission->score !== null)
                                    <span class="fw-bold text-success">{{ $submission->score }} / {{ $assignment->max_score }}</span>
                                @else
                                    <span class="text-muted">&mdash;</span>
                                @endif
                            </td>
                            <td style="padding: 1rem;">
                                @if($submission->score !== null)
                                    <span class="badge bg-success">Graded</span>
                                @elseif($submission->submitted_at)
                                    <span class="badge bg-warning text-dark">Pending Grading</span>
                                @else
                                    <span class="badge bg-secondary">Not Submitted</span>
                                @endif
                            </td>
                            <td style="padding: 1rem;">
                                @if($submission->file_path)
                                    <a href="{{ Storage::url($submission->file_path) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                                        <i class="bi bi-paperclip me-1"></i>Download
                                    </a>
                                @else
                                    <span class="text-muted small">No file</span>
                                @endif
                            </td>
                            <td style="padding: 1rem;" class="text-end">
                                @if($submission->submitted_at)
                                    <button type="button" class="btn btn-sm btn-primary fw-semibold"
                                            data-bs-toggle="modal"
                                            data-bs-target="#gradeSubmissionModal"
                                            data-action="{{ route('faculty.assignments.submissions.grade', [$assignment, $submission]) }}"
                                            data-student="{{ $submission->student->name }}"
                                            data-score="{{ $submission->score }}"
                                            data-feedback="{{ $submission->feedback ?? '' }}"
                                            data-file="{{ $submission->file_path ? Storage::url($submission->file_path) : '' }}">
                                        <i class="bi bi-pencil-square me-1"></i>{{ $submission->score !== null ? 'Re-Grade' : 'Grade' }}
                                    </button>
                                @else
                                    <span class="text-muted small">&mdash;</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                                <p class="text-muted mt-3 mb-0">No submissions found for this assignment.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($submissions->hasPages())
        <div class="card-footer bg-white border-top py-3">
            {{ $submissions->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Single Clean Grade Submission Modal (Outside Table) -->
<div class="modal fade" id="gradeSubmissionModal" tabindex="-1" aria-labelledby="gradeSubmissionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form action="#" method="POST" id="gradeSubmissionForm">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold" id="gradeSubmissionModalLabel">
                        <i class="bi bi-pencil-square me-2"></i>Grade Submission
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label text-muted small mb-1">Student Name</label>
                        <h6 class="fw-bold text-dark mb-0" id="modalStudentName">&mdash;</h6>
                    </div>

                    <div class="mb-3" id="modalFileSection" style="display: none;">
                        <label class="form-label text-muted small mb-1">Submitted Attachment</label>
                        <div>
                            <a href="#" class="btn btn-sm btn-outline-primary" target="_blank" id="modalFileLink">
                                <i class="bi bi-download me-1"></i>Download Student File
                            </a>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="modalScore" class="form-label fw-semibold">Score (Out of {{ $assignment->max_score }}) *</label>
                        <input type="number" class="form-control form-control-lg" id="modalScore" name="score" min="0" max="{{ $assignment->max_score }}" step="0.01" required>
                    </div>

                    <div class="mb-3">
                        <label for="modalFeedback" class="form-label fw-semibold">Instructor Feedback</label>
                        <textarea class="form-control" id="modalFeedback" name="feedback" rows="4" placeholder="Optional comments, corrections or feedback for the student..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-bold px-4">Save Grade</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const gradeSubmissionModal = document.getElementById('gradeSubmissionModal');
    if (gradeSubmissionModal) {
        gradeSubmissionModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            if (!button) return;

            const form = document.getElementById('gradeSubmissionForm');
            form.action = button.getAttribute('data-action') || '#';
            document.getElementById('modalStudentName').textContent = button.getAttribute('data-student') || '';
            document.getElementById('modalScore').value = button.getAttribute('data-score') || '';
            document.getElementById('modalFeedback').value = button.getAttribute('data-feedback') || '';

            const fileUrl = button.getAttribute('data-file');
            const fileSection = document.getElementById('modalFileSection');
            const fileLink = document.getElementById('modalFileLink');
            if (fileUrl && fileSection && fileLink) {
                fileSection.style.display = 'block';
                fileLink.href = fileUrl;
            } else if (fileSection) {
                fileSection.style.display = 'none';
            }
        });
    }
});
</script>
@endpush
@endsection
