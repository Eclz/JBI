@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="mb-4">
        <a href="{{ route('faculty.dean.partnerships.index') }}" class="text-decoration-none text-muted mb-2 d-inline-block">
            <i class="bi bi-arrow-left me-1"></i> Back to Partnerships
        </a>
        <h1 class="h3 mb-2" style="color: #1e293b; font-weight: 600;">Edit Partnership Details</h1>
        <p class="text-muted">Update the relationship status and objectives.</p>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <form action="{{ route('faculty.dean.partnerships.update', $partnership) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="organization_name" class="form-label fw-medium">Organization / Entity Name <span class="text-danger">*</span></label>
                        <input type="text" name="organization_name" id="organization_name" class="form-control @error('organization_name') is-invalid @enderror" value="{{ old('organization_name', $partnership->organization_name) }}" required>
                        @error('organization_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="partnership_type" class="form-label fw-medium">Partnership Type <span class="text-danger">*</span></label>
                        <select name="partnership_type" id="partnership_type" class="form-select @error('partnership_type') is-invalid @enderror" required>
                            <option value="Industry" {{ old('partnership_type', $partnership->partnership_type) == 'Industry' ? 'selected' : '' }}>Industry / Corporate</option>
                            <option value="Donor" {{ old('partnership_type', $partnership->partnership_type) == 'Donor' ? 'selected' : '' }}>Donor / NGO</option>
                            <option value="Alumni" {{ old('partnership_type', $partnership->partnership_type) == 'Alumni' ? 'selected' : '' }}>Alumni Group</option>
                            <option value="Government" {{ old('partnership_type', $partnership->partnership_type) == 'Government' ? 'selected' : '' }}>Government / Agency</option>
                            <option value="Other" {{ old('partnership_type', $partnership->partnership_type) == 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('partnership_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <label for="funding_amount" class="form-label fw-medium">Expected Funding / Endowment (GHS)</label>
                        <div class="input-group">
                            <span class="input-group-text">₵</span>
                            <input type="number" name="funding_amount" id="funding_amount" class="form-control @error('funding_amount') is-invalid @enderror" value="{{ old('funding_amount', $partnership->funding_amount) }}" step="0.01" min="0">
                        </div>
                        @error('funding_amount') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="status" class="form-label fw-medium">Status <span class="text-danger">*</span></label>
                        <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                            <option value="Pending" {{ old('status', $partnership->status) == 'Pending' ? 'selected' : '' }}>Pending</option>
                            <option value="Active" {{ old('status', $partnership->status) == 'Active' ? 'selected' : '' }}>Active</option>
                            <option value="Concluded" {{ old('status', $partnership->status) == 'Concluded' ? 'selected' : '' }}>Concluded</option>
                        </select>
                        @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="mb-4">
                    <label for="objectives" class="form-label fw-medium">Strategic Objectives & Outcomes <span class="text-danger">*</span></label>
                    <textarea name="objectives" id="objectives" rows="6" class="form-control @error('objectives') is-invalid @enderror" required>{{ old('objectives', $partnership->objectives) }}</textarea>
                    @error('objectives') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i> Update Partnership
                    </button>
                    <a href="{{ route('faculty.dean.partnerships.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
