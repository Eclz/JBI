@extends('layouts.app')

@section('title', 'Edit Fee Structure')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">Edit Fee Structure</h1>
                    <p class="text-muted">Update fee structure details</p>
                </div>
                <div>
                    <a href="{{ route('admin.fees.structures.show', $feeStructure) }}" class="btn btn-outline-secondary me-2">
                        <i class="fas fa-eye me-2"></i>View Structure
                    </a>
                    <a href="{{ route('admin.fees.structures.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Structures
                    </a>
                </div>
            </div>

            @if($errors->any())
                <div class="alert alert-danger">
                    <h6>Please correct the following errors:</h6>
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Fee Structure Details</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.fees.structures.update', $feeStructure) }}" method="POST" id="feeStructureForm">
                                @csrf
                                @method('PUT')

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Fee Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                                   id="name" name="name" value="{{ old('name', $feeStructure->name) }}" required>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="type" class="form-label">Fee Type <span class="text-danger">*</span></label>
                                            <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                                                <option value="">Select Fee Type</option>
                                                <option value="tuition" {{ old('type', $feeStructure->type) == 'tuition' ? 'selected' : '' }}>Tuition</option>
                                                <option value="registration" {{ old('type', $feeStructure->type) == 'registration' ? 'selected' : '' }}>Registration</option>
                                                <option value="library" {{ old('type', $feeStructure->type) == 'library' ? 'selected' : '' }}>Library</option>
                                                <option value="laboratory" {{ old('type', $feeStructure->type) == 'laboratory' ? 'selected' : '' }}>Laboratory</option>
                                                <option value="technology" {{ old('type', $feeStructure->type) == 'technology' ? 'selected' : '' }}>Technology</option>
                                                <option value="activity" {{ old('type', $feeStructure->type) == 'activity' ? 'selected' : '' }}>Activity</option>
                                                <option value="other" {{ old('type', $feeStructure->type) == 'other' ? 'selected' : '' }}>Other</option>
                                            </select>
                                            @error('type')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror"
                                              id="description" name="description" rows="3">{{ old('description', $feeStructure->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="amount" class="form-label">Amount ($) <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control @error('amount') is-invalid @enderror"
                                                   id="amount" name="amount" value="{{ old('amount', $feeStructure->amount) }}"
                                                   step="0.01" min="0" required>
                                            @error('amount')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="frequency" class="form-label">Frequency <span class="text-danger">*</span></label>
                                            <select class="form-select @error('frequency') is-invalid @enderror" id="frequency" name="frequency" required>
                                                <option value="">Select Frequency</option>
                                                <option value="one_time" {{ old('frequency', $feeStructure->frequency) == 'one_time' ? 'selected' : '' }}>One Time</option>
                                                <option value="semester" {{ old('frequency', $feeStructure->frequency) == 'semester' ? 'selected' : '' }}>Per Semester</option>
                                                <option value="monthly" {{ old('frequency', $feeStructure->frequency) == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                                <option value="annual" {{ old('frequency', $feeStructure->frequency) == 'annual' ? 'selected' : '' }}>Annual</option>
                                            </select>
                                            @error('frequency')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="academic_year_id" class="form-label">Academic Year <span class="text-danger">*</span></label>
                                            <select class="form-select @error('academic_year_id') is-invalid @enderror"
                                                    id="academic_year_id" name="academic_year_id" required>
                                                <option value="">Select Academic Year</option>
                                                @foreach($academicYears as $year)
                                                    <option value="{{ $year->id }}" {{ old('academic_year_id', $feeStructure->academic_year_id) == $year->id ? 'selected' : '' }}>
                                                        {{ $year->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('academic_year_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="semester_id" class="form-label">Semester</label>
                                            <select class="form-select @error('semester_id') is-invalid @enderror"
                                                    id="semester_id" name="semester_id">
                                                <option value="">All Semesters</option>
                                                @foreach($semesters as $semester)
                                                    <option value="{{ $semester->id }}" {{ old('semester_id', $feeStructure->semester_id) == $semester->id ? 'selected' : '' }}>
                                                        {{ $semester->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('semester_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="due_date" class="form-label">Due Date</label>
                                            <input type="date" class="form-control @error('due_date') is-invalid @enderror"
                                                   id="due_date" name="due_date" value="{{ old('due_date', $feeStructure->due_date?->format('Y-m-d')) }}">
                                            @error('due_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="late_fee_amount" class="form-label">Late Fee Amount ($)</label>
                                            <input type="number" class="form-control @error('late_fee_amount') is-invalid @enderror"
                                                   id="late_fee_amount" name="late_fee_amount" value="{{ old('late_fee_amount', $feeStructure->late_fee_amount) }}"
                                                   step="0.01" min="0">
                                            @error('late_fee_amount')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="late_fee_days" class="form-label">Late Fee Grace Period (Days)</label>
                                            <input type="number" class="form-control @error('late_fee_days') is-invalid @enderror"
                                                   id="late_fee_days" name="late_fee_days" value="{{ old('late_fee_days', $feeStructure->late_fee_days) }}"
                                                   min="1" max="365">
                                            <div class="form-text">Number of days after due date before late fee applies</div>
                                            @error('late_fee_days')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" id="is_mandatory"
                                                   name="is_mandatory" value="1" {{ old('is_mandatory', $feeStructure->is_mandatory) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_mandatory">
                                                Mandatory Fee
                                            </label>
                                            <div class="form-text">Students must pay this fee to continue enrollment</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" id="is_active"
                                                   name="is_active" value="1" {{ old('is_active', $feeStructure->is_active) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_active">
                                                Active
                                            </label>
                                            <div class="form-text">Only active fee structures can be used for new invoices</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('admin.fees.structures.show', $feeStructure) }}" class="btn btn-secondary">Cancel</a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Update Fee Structure
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title mb-0">Usage Information</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <strong>Fee Records:</strong>
                                <span class="badge bg-info">{{ $feeStructure->feeRecords()->count() }}</span>
                            </div>
                            <div class="mb-3">
                                <strong>Total Collected:</strong>
                                <span class="text-success">{{ $currencyCode }} {{ number_format($feeStructure->feeRecords()->sum('paid_amount'), 2) }}</span>
                            </div>
                            <div class="mb-3">
                                <strong>Outstanding:</strong>
                                <span class="text-warning">{{ $currencyCode }} {{ number_format($feeStructure->feeRecords()->sum('balance_amount'), 2) }}</span>
                            </div>

                            @if($feeStructure->feeRecords()->count() > 0)
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <strong>Note:</strong> This fee structure is being used by existing fee records.
                                    Changes may affect existing invoices.
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="card mt-3">
                        <div class="card-header">
                            <h6 class="card-title mb-0">Help & Tips</h6>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <i class="fas fa-info-circle text-info me-2"></i>
                                    Changes to amount will not affect existing fee records
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-calendar text-warning me-2"></i>
                                    Updating due date affects future invoices only
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-toggle-on text-success me-2"></i>
                                    Deactivating prevents new invoice generation
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('feeStructureForm');

    // Add confirmation for significant changes
    form.addEventListener('submit', function(e) {
        const isActive = document.getElementById('is_active').checked;
        const originalActive = {{ $feeStructure->is_active ? 'true' : 'false' }};

        if (originalActive && !isActive) {
            if (!confirm('You are about to deactivate this fee structure. This will prevent new invoices from being generated. Are you sure?')) {
                e.preventDefault();
            }
        }
    });
});
</script>
@endpush
