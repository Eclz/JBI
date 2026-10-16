@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="mb-4">
        <a href="{{ route('faculty.dean.student-issues.index') }}" class="text-decoration-none text-muted mb-2 d-inline-block">
            <i class="bi bi-arrow-left me-1"></i> Back to Student Issues
        </a>
        <h1 class="h3 mb-2" style="color: #1e293b; font-weight: 600;">Report Student Issue</h1>
        <p class="text-muted">Log an academic, disciplinary, or welfare concern regarding a student.</p>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <form action="{{ route('faculty.dean.student-issues.store') }}" method="POST">
                @csrf

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="student_id" class="form-label fw-medium">Student <span class="text-danger">*</span></label>
                        <select name="student_id" id="student_id" class="form-select @error('student_id') is-invalid @enderror" required>
                            <option value="">Select a Student</option>
                            @foreach($students as $student)
                                <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>
                                    {{ $student->full_name }} ({{ $student->email }})
                                </option>
                            @endforeach
                        </select>
                        @error('student_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="issue_type" class="form-label fw-medium">Issue Type <span class="text-danger">*</span></label>
                        <select name="issue_type" id="issue_type" class="form-select @error('issue_type') is-invalid @enderror" required>
                            <option value="">Select Issue Category</option>
                            <option value="Academic" {{ old('issue_type') == 'Academic' ? 'selected' : '' }}>Academic / Performance</option>
                            <option value="Disciplinary" {{ old('issue_type') == 'Disciplinary' ? 'selected' : '' }}>Disciplinary / Conduct</option>
                            <option value="Welfare" {{ old('issue_type') == 'Welfare' ? 'selected' : '' }}>Welfare / Personal</option>
                            <option value="Other" {{ old('issue_type') == 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('issue_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="mb-4">
                    <label for="description" class="form-label fw-medium">Detailed Description of the Issue <span class="text-danger">*</span></label>
                    <textarea name="description" id="description" rows="6" class="form-control @error('description') is-invalid @enderror" placeholder="Describe the incident, context, and any relevant background information..." required>{{ old('description') }}</textarea>
                    @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i> Log Issue
                    </button>
                    <a href="{{ route('faculty.dean.student-issues.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
