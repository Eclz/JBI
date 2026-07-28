@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Back Button -->
    <div class="mb-3">
        <a href="{{ route('student.assignments.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>Back to Assignments
        </a>
    </div>

    <div class="row">
        <!-- Assignment Details -->
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h4 class="mb-2 fw-bold" style="color: #212529;">{{ $assignment->title }}</h4>
                            <div class="d-flex gap-3 align-items-center">
                                <span class="badge" style="background-color: #e3f2fd; color: #1976d2;">
                                    {{ $assignment->course->code }} - {{ $assignment->course->name }}
                                </span>
                                <span class="badge bg-secondary text-capitalize">{{ $assignment->type }}</span>
                            </div>
                        </div>
                        <div class="text-end">
                            <div class="fs-3 fw-bold" style="color: #667eea;">{{ $assignment->max_points }}</div>
                            <div class="small text-muted">Points</div>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Due Date Warning -->
                    @if($assignment->is_overdue && (!$submission || $submission->status == 'draft'))
                        <div class="alert alert-danger mb-4">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <strong>This assignment is overdue!</strong>
                            @if($assignment->allow_late_submission)
                                Late submissions are accepted but may incur a penalty.
                            @else
                                Late submissions are not accepted.
                            @endif
                        </div>
                    @endif

                    <!-- Assignment Description -->
                    <div class="mb-4">
                        <h5 class="fw-semibold mb-3" style="color: #495057;">Description</h5>
                        <div class="text-muted" style="line-height: 1.8;">
                            {!! nl2br(e($assignment->description)) !!}
                        </div>
                    </div>

                    <!-- Instructions -->
                    @if($assignment->instructions)
                        <div class="mb-4">
                            <h5 class="fw-semibold mb-3" style="color: #495057;">Instructions</h5>
                            <div class="bg-light p-3 rounded" style="line-height: 1.8;">
                                {!! nl2br(e($assignment->instructions)) !!}
                            </div>
                        </div>
                    @endif

                    <!-- File Requirements -->
                    @if($assignment->allowed_file_types)
                        <div class="mb-4">
                            <h5 class="fw-semibold mb-3" style="color: #495057;">File Requirements</h5>
                            <div class="d-flex gap-2 flex-wrap">
                                @foreach($assignment->allowed_file_types as $fileType)
                                    <span class="badge bg-light text-dark">{{ strtoupper($fileType) }}</span>
                                @endforeach
                            </div>
                            @if($assignment->max_file_size)
                                <div class="small text-muted mt-2">
                                    Maximum file size: {{ number_format($assignment->max_file_size / 1024, 2) }} MB
                                </div>
                            @endif
                        </div>
                    @endif

                    <!-- Submission Form -->
                    @if(!$submission || $submission->status == 'draft')
                        @if(!$assignment->is_overdue || $assignment->allow_late_submission)
                            <div class="border-top pt-4 mt-4">
                                <h5 class="fw-semibold mb-3" style="color: #495057;">Submit Assignment</h5>
                                <form action="{{ route('student.assignments.submit', $assignment) }}" method="POST" enctype="multipart/form-data">
                                    @csrf

                                    <div class="mb-3">
                                        <label for="submission_text" class="form-label">Submission Text</label>
                                        <textarea name="submission_text" id="submission_text" class="form-control" rows="6"
                                            placeholder="Enter your submission text here...">{{ $submission->submission_text ?? '' }}</textarea>
                                    </div>

                                    <div class="mb-3">
                                        <label for="file" class="form-label">Upload File</label>
                                        <input type="file" name="file" id="file" class="form-control">
                                        @if($submission && $submission->file_path)
                                            <div class="small text-muted mt-2">
                                                Current file: <a href="{{ Storage::url($submission->file_path) }}" target="_blank">{{ basename($submission->file_path) }}</a>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="d-flex gap-2">
                                        <button type="submit" name="action" value="submit" class="btn btn-primary">
                                            <i class="bi bi-check-circle me-1"></i>Submit Assignment
                                        </button>
                                        <button type="submit" name="action" value="draft" class="btn btn-outline-secondary">
                                            <i class="bi bi-save me-1"></i>Save as Draft
                                        </button>
                                    </div>
                                </form>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Assignment Info -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="mb-0 fw-semibold" style="color: #495057;">Assignment Information</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="small text-muted mb-1">Due Date</div>
                        <div class="fw-semibold" style="color: {{ $assignment->is_overdue ? '#f44336' : '#495057' }};">
                            <i class="bi bi-calendar3 me-1"></i>{{ $assignment->due_date->format('M d, Y h:i A') }}
                        </div>
                    </div>

                    @if($assignment->available_from)
                        <div class="mb-3">
                            <div class="small text-muted mb-1">Available From</div>
                            <div class="fw-semibold" style="color: #495057;">
                                {{ $assignment->available_from->format('M d, Y h:i A') }}
                            </div>
                        </div>
                    @endif

                    <div class="mb-3">
                        <div class="small text-muted mb-1">Weight</div>
                        <div class="fw-semibold" style="color: #495057;">
                            {{ $assignment->weight_percentage }}% of final grade
                        </div>
                    </div>

                    @if($assignment->allow_late_submission)
                        <div class="mb-3">
                            <div class="small text-muted mb-1">Late Submission</div>
                            <div class="fw-semibold text-warning">
                                Allowed ({{ $assignment->late_penalty_per_day }}% penalty per day)
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Submission Status -->
            @if($submission)
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 py-3">
                        <h6 class="mb-0 fw-semibold" style="color: #495057;">Your Submission</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="small text-muted mb-1">Status</div>
                            @if($submission->status == 'graded')
                                <span class="badge bg-success">
                                    <i class="bi bi-check-circle me-1"></i>Graded
                                </span>
                            @elseif($submission->status == 'submitted')
                                <span class="badge bg-info text-white">
                                    <i class="bi bi-clock me-1"></i>Submitted
                                </span>
                            @else
                                <span class="badge bg-warning text-dark">
                                    <i class="bi bi-file-earmark me-1"></i>Draft
                                </span>
                            @endif
                        </div>

                        <div class="mb-3">
                            <div class="small text-muted mb-1">Submitted At</div>
                            <div class="fw-semibold" style="color: #495057;">
                                {{ $submission->submitted_at ? $submission->submitted_at->format('M d, Y h:i A') : 'Not submitted yet' }}
                            </div>
                        </div>

                        @if($submission->score !== null)
                            <div class="mb-3">
                                <div class="small text-muted mb-1">Score</div>
                                <div class="fs-4 fw-bold" style="color: #4caf50;">
                                    {{ $submission->score }} / {{ $assignment->max_points }}
                                </div>
                            </div>
                        @endif

                        @if($submission->feedback)
                            <div class="mb-3">
                                <div class="small text-muted mb-1">Feedback</div>
                                <div class="bg-light p-3 rounded small">
                                    {{ $submission->feedback }}
                                </div>
                            </div>
                        @endif

                        @if($submission->file_path)
                            <div>
                                <a href="{{ Storage::url($submission->file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary w-100">
                                    <i class="bi bi-download me-1"></i>Download Submission
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
