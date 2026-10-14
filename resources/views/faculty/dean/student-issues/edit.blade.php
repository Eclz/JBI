@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="mb-4">
        <a href="{{ route('faculty.dean.student-issues.index') }}" class="text-decoration-none text-muted mb-2 d-inline-block">
            <i class="bi bi-arrow-left me-1"></i> Back to Student Issues
        </a>
        <h1 class="h3 mb-2" style="color: #1e293b; font-weight: 600;">Edit Student Issue</h1>
        <p class="text-muted">Update issue details, status, or log actions taken.</p>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <form action="{{ route('faculty.dean.student-issues.update', $studentIssue) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="student_id" class="form-label fw-medium">Student <span class="text-danger">*</span></label>
                        <select name="student_id" id="student_id" class="form-select @error('student_id') is-invalid @enderror" required>
                            <option value="">Select a Student</option>
                            @foreach($students as $student)
                                <option value="{{ $student->id }}" {{ old('student_id', $studentIssue->student_id) == $student->id ? 'selected' : '' }}>
                                    {{ $student->full_name }} ({{ $student->email }})
                                </option>
                            @endforeach
                        </select>
                        @error('student_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="issue_type" class="form-label fw-medium">Issue Type <span class="text-danger">*</span></label>
                        <select name="issue_type" id="issue_type" class="form-select @error('issue_type') is-invalid @enderror" required>
                            <option value="Academic" {{ old('issue_type', $studentIssue->issue_type) == 'Academic' ? 'selected' : '' }}>Academic / Performance</option>
                            <option value="Disciplinary" {{ old('issue_type', $studentIssue->issue_type) == 'Disciplinary' ? 'selected' : '' }}>Disciplinary / Conduct</option>
                            <option value="Welfare" {{ old('issue_type', $studentIssue->issue_type) == 'Welfare' ? 'selected' : '' }}>Welfare / Personal</option>
                            <option value="Other" {{ old('issue_type', $studentIssue->issue_type) == 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('issue_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="status" class="form-label fw-medium">Current Status <span class="text-danger">*</span></label>
                    <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" style="max-width: 250px;" required>
                        <option value="Open" {{ old('status', $studentIssue->status) == 'Open' ? 'selected' : '' }}>Open</option>
                        <option value="In Progress" {{ old('status', $studentIssue->status) == 'In Progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="Resolved" {{ old('status', $studentIssue->status) == 'Resolved' ? 'selected' : '' }}>Resolved</option>
                        <option value="Escalated" {{ old('status', $studentIssue->status) == 'Escalated' ? 'selected' : '' }}>Escalated</option>
                    </select>
                    @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-4">
                    <label for="description" class="form-label fw-medium">Detailed Description <span class="text-danger">*</span></label>
                    <textarea name="description" id="description" rows="4" class="form-control @error('description') is-invalid @enderror" required>{{ old('description', $studentIssue->description) }}</textarea>
                    @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-4">
                    <label for="action_taken" class="form-label fw-medium">Action Taken</label>
                    <textarea name="action_taken" id="action_taken" rows="4" class="form-control @error('action_taken') is-invalid @enderror" placeholder="Log any disciplinary measures, counseling provided, or actions taken to resolve the issue...">{{ old('action_taken', $studentIssue->action_taken) }}</textarea>
                    @error('action_taken') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i> Update Issue
                    </button>
                    <a href="{{ route('faculty.dean.student-issues.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
