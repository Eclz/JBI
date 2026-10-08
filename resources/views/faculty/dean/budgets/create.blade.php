@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="mb-4">
        <a href="{{ route('faculty.dean.budgets.index') }}" class="text-decoration-none text-muted mb-2 d-inline-block">
            <i class="bi bi-arrow-left me-1"></i> Back to Budget Requests
        </a>
        <h1 class="h3 mb-2" style="color: #1e293b; font-weight: 600;">Create Budget Request</h1>
        <p class="text-muted">Submit a new financial request for department resources, equipment, or events.</p>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <form action="{{ route('faculty.dean.budgets.store') }}" method="POST">
                @csrf

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="department_id" class="form-label fw-medium">Department <span class="text-danger">*</span></label>
                        <select name="department_id" id="department_id" class="form-select @error('department_id') is-invalid @enderror" required>
                            <option value="">Select a Department</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>
                                    {{ $dept->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('department_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="title" class="form-label fw-medium">Request Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" placeholder="e.g. Science Lab Equipment Upgrade" required>
                        @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                @if($facultyMembers->isNotEmpty())
                    <div class="mb-3">
                        <label class="form-label fw-medium">Faculty Members</label>
                        <select class="form-select" disabled>
                            @foreach($facultyMembers as $member)
                                <option>{{ $member->full_name ?: $member->name }} ({{ $member->email }})</option>
                            @endforeach
                        </select>
                        <div class="form-text">Budget requests are submitted by you as dean and scoped to the selected department.</div>
                    </div>
                @endif

                <div class="mb-3">
                    <label for="amount" class="form-label fw-medium">Requested Amount ({{ $currencyCode }}) <span class="text-danger">*</span></label>
                    <div class="input-group" style="max-width: 300px;">
                        <span class="input-group-text">{{ $currencyCode }}</span>
                        <input type="number" name="amount" id="amount" class="form-control @error('amount') is-invalid @enderror" value="{{ old('amount') }}" step="0.01" min="0.01" required>
                    </div>
                    @error('amount') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                </div>

                <div class="mb-4">
                    <label for="description" class="form-label fw-medium">Detailed Description & Justification <span class="text-danger">*</span></label>
                    <textarea name="description" id="description" rows="6" class="form-control @error('description') is-invalid @enderror" placeholder="Provide a detailed breakdown of costs and justify why this budget is required..." required>{{ old('description') }}</textarea>
                    @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i> Save as Draft
                    </button>
                    <a href="{{ route('faculty.dean.budgets.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
