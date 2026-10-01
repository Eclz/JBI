@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="mb-4">
        <a href="{{ route('faculty.dean.budgets.index') }}" class="text-decoration-none text-muted mb-2 d-inline-block">
            <i class="bi bi-arrow-left me-1"></i> Back to Budget Requests
        </a>
        <h1 class="h3 mb-2" style="color: #1e293b; font-weight: 600;">Edit Budget Request</h1>
        <p class="text-muted">Modify the budget draft before submission.</p>
    </div>

    @if($budget->status === 'Rejected')
        <div class="alert alert-danger mb-4">
            <h6 class="alert-heading fw-bold"><i class="bi bi-exclamation-triangle me-1"></i> Returned by Finance Team</h6>
            <p class="mb-0">{{ $budget->finance_notes }}</p>
            <hr>
            <small class="mb-0">Please update the request based on the feedback above before resubmitting.</small>
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <form action="{{ route('faculty.dean.budgets.update', $budget) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="department_id" class="form-label fw-medium">Department <span class="text-danger">*</span></label>
                        <select name="department_id" id="department_id" class="form-select @error('department_id') is-invalid @enderror" required>
                            <option value="">Select a Department</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ old('department_id', $budget->department_id) == $dept->id ? 'selected' : '' }}>
                                    {{ $dept->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('department_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="title" class="form-label fw-medium">Request Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $budget->title) }}" required>
                        @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="amount" class="form-label fw-medium">Requested Amount (GHS) <span class="text-danger">*</span></label>
                    <div class="input-group" style="max-width: 300px;">
                        <span class="input-group-text">₵</span>
                        <input type="number" name="amount" id="amount" class="form-control @error('amount') is-invalid @enderror" value="{{ old('amount', $budget->amount) }}" step="0.01" min="0.01" required>
                    </div>
                    @error('amount') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                </div>

                <div class="mb-4">
                    <label for="description" class="form-label fw-medium">Detailed Description & Justification <span class="text-danger">*</span></label>
                    <textarea name="description" id="description" rows="6" class="form-control @error('description') is-invalid @enderror" required>{{ old('description', $budget->description) }}</textarea>
                    @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i> Update Request
                    </button>
                    <a href="{{ route('faculty.dean.budgets.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
