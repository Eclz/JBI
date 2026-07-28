@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-2">
                    <li class="breadcrumb-item"><a href="{{ route('faculty.exams.index') }}">Exams</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('faculty.exams.show', $exam) }}">{{ $exam->title }}</a></li>
                    <li class="breadcrumb-item active">Attempts</li>
                </ol>
            </nav>
            <h1 class="h3 mb-1" style="color: #1e293b; font-weight: 600;">Exam Attempts</h1>
            <p class="text-muted mb-0">{{ $exam->title }} - {{ $exam->course->code }}</p>
        </div>
        <div>
            <a href="{{ route('faculty.exams.show', $exam) }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back to Exam
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 py-3">
            <h5 class="mb-0" style="color: #1e293b; font-weight: 600;">All Student Attempts</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                        <tr>
                            <th style="color: #64748b; font-weight: 600; padding: 1rem;">Student</th>
                            <th style="color: #64748b; font-weight: 600; padding: 1rem;">Email</th>
                            <th style="color: #64748b; font-weight: 600; padding: 1rem;">Started At</th>
                            <th style="color: #64748b; font-weight: 600; padding: 1rem;">Status</th>
                            <th style="color: #64748b; font-weight: 600; padding: 1rem;">Marks</th>
                            <th style="color: #64748b; font-weight: 600; padding: 1rem;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attempts as $attempt)
                            <tr>
                                <td style="padding: 1rem; color: #1e293b;">
                                    <div class="fw-semibold">{{ $attempt->user->name }}</div>
                                    <small class="text-muted">{{ $attempt->user->studentProfile->admission_number ?? 'N/A' }}</small>
                                </td>
                                <td style="padding: 1rem; color: #64748b;">{{ $attempt->user->email }}</td>
                                <td style="padding: 1rem; color: #64748b;">
                                    {{ $attempt->started_at ? $attempt->started_at->format('M d, Y h:i A') : 'Not started' }}
                                </td>
                                <td style="padding: 1rem;">
                                    @if($attempt->status === 'graded')
                                        <span class="badge" style="background: #10b981; color: white; padding: 0.5rem 0.75rem; border-radius: 6px;">Graded</span>
                                    @elseif($attempt->status === 'submitted')
                                        <span class="badge" style="background: #3b82f6; color: white; padding: 0.5rem 0.75rem; border-radius: 6px;">Submitted</span>
                                    @elseif($attempt->status === 'in_progress')
                                        <span class="badge" style="background: #f59e0b; color: white; padding: 0.5rem 0.75rem; border-radius: 6px;">In Progress</span>
                                    @else
                                        <span class="badge" style="background: #6b7280; color: white; padding: 0.5rem 0.75rem; border-radius: 6px;">{{ ucfirst($attempt->status) }}</span>
                                    @endif
                                </td>
                                <td style="padding: 1rem; color: #1e293b;">
                                    @if($attempt->status === 'graded')
                                        <span class="fw-semibold">{{ $attempt->marks_obtained ?? 0 }}/{{ $exam->total_marks }}</span>
                                        <small class="text-muted d-block">{{ number_format(($attempt->marks_obtained / $exam->total_marks) * 100, 1) }}%</small>
                                    @else
                                        <span class="text-muted">Not graded</span>
                                    @endif
                                </td>
                                <td style="padding: 1rem;">
                                    @if(in_array($attempt->status, ['submitted', 'graded']))
                                        <a href="{{ route('faculty.exams.attempts.show', [$exam, $attempt]) }}"
                                           class="btn btn-sm btn-outline-primary me-2">
                                            <i class="bi bi-eye me-1"></i>
                                            View Submission
                                        </a>
                                        <button type="button"
                                                class="btn btn-sm btn-primary"
                                                data-bs-toggle="modal"
                                                data-bs-target="#gradeModal"
                                                data-action="{{ route('faculty.exams.attempts.grade', [$exam, $attempt]) }}"
                                                data-student="{{ e($attempt->user->name) }}"
                                                data-marks="{{ $attempt->marks_obtained }}"
                                                data-feedback="{{ e($attempt->feedback ?? '') }}"
                                                data-submission-url="{{ $attempt->submission_file_url ? Storage::url($attempt->submission_file_url) : '' }}"
                                                data-answers='@json($attempt->answers ?? "")'>
                                            <i class="bi bi-pencil-square me-1"></i>
                                            {{ $attempt->status === 'graded' ? 'Update Grade' : 'Grade' }}
                                        </button>

                                        @if($attempt->submission_file_url)
                                            <a href="{{ Storage::url($attempt->submission_file_url) }}" class="btn btn-sm btn-outline-secondary ms-2" target="_blank">
                                                <i class="bi bi-download"></i>
                                            </a>
                                        @endif
                                    @else
                                        <span class="text-muted small">Waiting for submission</span>
                                    @endif
                                </td>
                            </tr>

                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <i class="bi bi-inbox" style="font-size: 3rem; color: #cbd5e1;"></i>
                                    <p class="text-muted mt-3 mb-0">No attempts yet</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($attempts->hasPages())
            <div class="card-footer bg-white border-0 py-3">
                {{ $attempts->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Grading Modal -->
<div class="modal fade" id="gradeModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="#" method="POST" id="gradeForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Grade Exam - <span id="gradeStudentName"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3" id="submissionSection" style="display: none;">
                        <label class="form-label">Submitted Answer File</label>
                        <div>
                            <a href="#" class="btn btn-sm btn-outline-primary" target="_blank" id="submissionLink">
                                <i class="bi bi-download me-1"></i>Download
                            </a>
                        </div>
                    </div>
                    <div class="mb-3" id="answersSection" style="display: none;">
                        <label class="form-label">Student Answers</label>
                        <div class="border rounded p-3 bg-light" id="studentAnswers"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Marks Obtained</label>
                        <input type="number" name="marks_obtained" class="form-control" id="gradeMarks"
                               min="0" max="{{ $exam->total_marks }}" step="0.5" required>
                        <small class="text-muted">Out of {{ $exam->total_marks }} marks</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Feedback</label>
                        <textarea name="feedback" class="form-control" rows="4" id="gradeFeedback"></textarea>
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

@push('scripts')
<script>
    const gradeModal = document.getElementById('gradeModal');
    gradeModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        if (!button) {
            return;
        }

        const form = document.getElementById('gradeForm');
        const studentName = button.getAttribute('data-student') || '';
        const marks = button.getAttribute('data-marks') || '';
        const feedback = button.getAttribute('data-feedback') || '';
        const submissionUrl = button.getAttribute('data-submission-url') || '';
        const answersJson = button.getAttribute('data-answers') || '""';

        document.getElementById('gradeStudentName').textContent = studentName;
        form.action = button.getAttribute('data-action') || '#';
        document.getElementById('gradeMarks').value = marks;
        document.getElementById('gradeFeedback').value = feedback;

        const submissionSection = document.getElementById('submissionSection');
        const submissionLink = document.getElementById('submissionLink');
        if (submissionUrl) {
            submissionLink.href = submissionUrl;
            submissionSection.style.display = 'block';
        } else {
            submissionSection.style.display = 'none';
        }

        const answersSection = document.getElementById('answersSection');
        const answersContainer = document.getElementById('studentAnswers');
        let answers = '';
        try {
            answers = JSON.parse(answersJson) || '';
        } catch (error) {
            answers = '';
        }

        if (answers) {
            answersContainer.innerHTML = answers;
            answersSection.style.display = 'block';
        } else {
            answersContainer.textContent = '';
            answersSection.style.display = 'none';
        }
    });
</script>
@endpush
@endsection
