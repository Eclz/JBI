@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-2">
                    <li class="breadcrumb-item"><a href="{{ route('faculty.exams.index') }}">Exams</a></li>
                    <li class="breadcrumb-item active">{{ $exam->title }}</li>
                </ol>
            </nav>
            <h1 class="h3 mb-1" style="color: #1e293b; font-weight: 600;">{{ $exam->title }}</h1>
            <p class="text-muted mb-0">{{ $exam->course->code }} - {{ $exam->course->name }}</p>
        </div>
        <div>
            <a href="{{ route('faculty.exams.edit', $exam) }}" class="btn btn-outline-primary me-2">
                <i class="bi bi-pencil me-2"></i>Edit Exam
            </a>
            <form action="{{ route('faculty.exams.destroy', $exam) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this exam?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger">
                    <i class="bi bi-trash me-2"></i>Delete
                </button>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body text-white p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div style="background: rgba(255,255,255,0.2); padding: 12px; border-radius: 12px;">
                            <i class="bi bi-people" style="font-size: 1.5rem;"></i>
                        </div>
                    </div>
                    <div style="font-size: 2rem; font-weight: 700; margin-bottom: 0.25rem;">{{ $statistics['total_attempts'] ?? 0 }}</div>
                    <div style="opacity: 0.9; font-size: 0.875rem;">Total Attempts</div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <div class="card-body text-white p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div style="background: rgba(255,255,255,0.2); padding: 12px; border-radius: 12px;">
                            <i class="bi bi-check-circle" style="font-size: 1.5rem;"></i>
                        </div>
                    </div>
                    <div style="font-size: 2rem; font-weight: 700; margin-bottom: 0.25rem;">{{ $statistics['completed'] ?? 0 }}</div>
                    <div style="opacity: 0.9; font-size: 0.875rem;">Graded</div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                <div class="card-body text-white p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div style="background: rgba(255,255,255,0.2); padding: 12px; border-radius: 12px;">
                            <i class="bi bi-hourglass-split" style="font-size: 1.5rem;"></i>
                        </div>
                    </div>
                    <div style="font-size: 2rem; font-weight: 700; margin-bottom: 0.25rem;">{{ $statistics['in_progress'] ?? 0 }}</div>
                    <div style="opacity: 0.9; font-size: 0.875rem;">In Progress</div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                <div class="card-body text-white p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div style="background: rgba(255,255,255,0.2); padding: 12px; border-radius: 12px;">
                            <i class="bi bi-trophy" style="font-size: 1.5rem;"></i>
                        </div>
                    </div>
                    <div style="font-size: 2rem; font-weight: 700; margin-bottom: 0.25rem;">{{ number_format($statistics['average_score'] ?? 0, 1) }}</div>
                    <div style="opacity: 0.9; font-size: 0.875rem;">Average Score</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <!-- Student Attempts -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h5 class="mb-0" style="color: #1e293b; font-weight: 600;">Student Attempts</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead style="background-color: #f8fafc;">
                                <tr>
                                    <th style="color: #64748b; font-weight: 600; font-size: 0.75rem; text-transform: uppercase; padding: 1rem;">Student</th>
                                    <th style="color: #64748b; font-weight: 600; font-size: 0.75rem; text-transform: uppercase; padding: 1rem;">Status</th>
                                    <th style="color: #64748b; font-weight: 600; font-size: 0.75rem; text-transform: uppercase; padding: 1rem;">Marks</th>
                                    <th style="color: #64748b; font-weight: 600; font-size: 0.75rem; text-transform: uppercase; padding: 1rem;">Submitted</th>
                                    <th style="color: #64748b; font-weight: 600; font-size: 0.75rem; text-transform: uppercase; padding: 1rem;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($exam->attempts as $attempt)
                                <tr>
                                    <td class="align-middle" style="padding: 1rem;">
                                        <div class="fw-semibold" style="color: #1e293b;">{{ $attempt->user->name }}</div>
                                        <small class="text-muted">{{ $attempt->user->email }}</small>
                                    </td>
                                    <td class="align-middle" style="padding: 1rem;">
                                        @if($attempt->status === 'in_progress')
                                            <span class="badge bg-warning">In Progress</span>
                                        @elseif($attempt->status === 'submitted')
                                            <span class="badge bg-info">Submitted</span>
                                        @elseif($attempt->status === 'graded')
                                            <span class="badge bg-success">Graded</span>
                                        @endif
                                    </td>
                                    <td class="align-middle" style="padding: 1rem;">
                                        @if($attempt->status === 'graded')
                                            <span class="fw-semibold" style="color: #1e293b;">{{ $attempt->marks_obtained }}/{{ $exam->total_marks }}</span>
                                        @else
                                            <span class="text-muted">Not graded</span>
                                        @endif
                                    </td>
                                    <td class="align-middle" style="padding: 1rem;">
                                        @if($attempt->submitted_at)
                                            <span style="color: #1e293b;">{{ $attempt->submitted_at->format('M d, Y H:i') }}</span>
                                        @else
                                            <span class="text-muted">Not submitted</span>
                                        @endif
                                    </td>
                                    <td class="align-middle" style="padding: 1rem;">
                                        @if(in_array($attempt->status, ['submitted', 'graded'], true))
                                            <a href="{{ route('faculty.exams.attempts.show', ['exam' => $exam->id, 'attempt' => $attempt->id]) }}"
                                               class="btn btn-sm btn-outline-primary me-2">
                                                <i class="bi bi-eye me-1"></i>View Submission
                                            </a>
                                        @endif
                                        @if($attempt->status === 'submitted')
                                            <button type="button"
                                                    class="btn btn-sm btn-primary"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#gradeModal"
                                                    data-action="{{ route('faculty.exams.attempts.grade', ['exam' => $exam->id, 'attempt' => $attempt->id]) }}"
                                                    data-student="{{ e($attempt->user->name) }}"
                                                    data-marks="{{ $attempt->marks_obtained }}"
                                                    data-feedback="{{ e($attempt->feedback ?? '') }}"
                                                    data-submission-url="{{ $attempt->submission_file_url ? Storage::url($attempt->submission_file_url) : '' }}"
                                                    data-answers='@json($attempt->answers ?? "")'>
                                                <i class="bi bi-pencil-square me-1"></i>Grade
                                            </button>
                                        @elseif($attempt->status === 'graded')
                                            <button type="button"
                                                    class="btn btn-sm btn-outline-secondary"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#gradeModal"
                                                    data-action="{{ route('faculty.exams.attempts.grade', ['exam' => $exam->id, 'attempt' => $attempt->id]) }}"
                                                    data-student="{{ e($attempt->user->name) }}"
                                                    data-marks="{{ $attempt->marks_obtained }}"
                                                    data-feedback="{{ e($attempt->feedback ?? '') }}"
                                                    data-submission-url="{{ $attempt->submission_file_url ? Storage::url($attempt->submission_file_url) : '' }}"
                                                    data-answers='@json($attempt->answers ?? "")'>
                                                <i class="bi bi-eye me-1"></i>View Grade
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="bi bi-inbox" style="font-size: 3rem; opacity: 0.3;"></i>
                                            <p class="mt-3 mb-0">No student attempts yet</p>
                                        </div>
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
            <!-- Exam Details -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h5 class="mb-0" style="color: #1e293b; font-weight: 600;">Exam Details</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted d-block mb-1">Exam Type</small>
                        <span class="badge bg-primary">{{ ucfirst($exam->exam_type) }}</span>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block mb-1">Start Time</small>
                        <strong style="color: #1e293b;">{{ $exam->start_time->format('M d, Y H:i') }}</strong>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block mb-1">End Time</small>
                        <strong style="color: #1e293b;">{{ $exam->end_time->format('M d, Y H:i') }}</strong>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block mb-1">Duration</small>
                        <strong style="color: #1e293b;">{{ $exam->duration_minutes }} minutes</strong>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block mb-1">Total Marks</small>
                        <strong style="color: #1e293b;">{{ $exam->total_marks }}</strong>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block mb-1">Passing Marks</small>
                        <strong style="color: #1e293b;">{{ $exam->passing_marks }}</strong>
                    </div>
                    @if($exam->required_payment)
                    <div class="mb-3">
                        <small class="text-muted d-block mb-1">Required Payment</small>
                        <strong style="color: #1e293b;">{{ $currencyCode }} {{ number_format($exam->required_payment, 2) }}</strong>
                    </div>
                    @endif
                    <div class="mb-3">
                        <small class="text-muted d-block mb-1">Online Editor</small>
                        <strong style="color: #1e293b;">{{ $exam->allow_online_editor ? 'Enabled' : 'Disabled' }}</strong>
                    </div>
                    @if($exam->exam_paper_url)
                    <div class="mb-3">
                        <small class="text-muted d-block mb-1">Question Paper</small>
                        <a href="{{ Storage::url($exam->exam_paper_url) }}" class="btn btn-sm btn-outline-primary" target="_blank">
                            <i class="bi bi-download me-1"></i>Download
                        </a>
                    </div>
                    @endif
                    @if($exam->answer_booklet_url)
                    <div class="mb-3">
                        <small class="text-muted d-block mb-1">Answer Booklet</small>
                        <a href="{{ Storage::url($exam->answer_booklet_url) }}" class="btn btn-sm btn-outline-primary" target="_blank">
                            <i class="bi bi-download me-1"></i>Download
                        </a>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Score Distribution -->
            @if($statistics['total_attempts'] > 0)
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h5 class="mb-0" style="color: #1e293b; font-weight: 600;">Score Distribution</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted d-block mb-1">Highest Score</small>
                        <strong style="color: #10b981; font-size: 1.5rem;">{{ $statistics['highest_score'] ?? 0 }}</strong>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block mb-1">Average Score</small>
                        <strong style="color: #3b82f6; font-size: 1.5rem;">{{ number_format($statistics['average_score'] ?? 0, 1) }}</strong>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block mb-1">Lowest Score</small>
                        <strong style="color: #ef4444; font-size: 1.5rem;">{{ $statistics['lowest_score'] ?? 0 }}</strong>
                    </div>
                </div>
            </div>
            @endif
        </div>
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
                        <label class="form-label">Marks Obtained (out of {{ $exam->total_marks }})</label>
                        <input type="number" name="marks_obtained" class="form-control" id="gradeMarks"
                               min="0" max="{{ $exam->total_marks }}" step="0.5" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Feedback</label>
                        <textarea name="feedback" class="form-control" rows="4" id="gradeFeedback"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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
