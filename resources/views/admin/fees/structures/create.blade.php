@extends('layouts.app')

@section('title', 'Create Fee Structure')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">Create Fee Structure</h1>
                    <p class="text-muted">Set up a new fee structure for students</p>
                </div>
                <div>
                    <a href="{{ route('admin.fees.structures.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Fee Structures
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
                            <form action="{{ route('admin.fees.structures.store') }}" method="POST" id="feeStructureForm">
                                @csrf

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Fee Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                                   id="name" name="name" value="{{ old('name') }}" required>
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
                                                <option value="tuition" {{ old('type') == 'tuition' ? 'selected' : '' }}>Tuition</option>
                                                <option value="library" {{ old('type') == 'library' ? 'selected' : '' }}>Library</option>
                                                <option value="laboratory" {{ old('type') == 'laboratory' ? 'selected' : '' }}>Laboratory</option>
                                                <option value="technology" {{ old('type') == 'technology' ? 'selected' : '' }}>Technology</option>
                                                <option value="activity" {{ old('type') == 'activity' ? 'selected' : '' }}>Activity</option>
                                                <option value="other" {{ old('type') == 'other' ? 'selected' : '' }}>Other</option>
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
                                              id="description" name="description" rows="3">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="amount" class="form-label">Amount ($) <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control @error('amount') is-invalid @enderror"
                                                   id="amount" name="amount" value="{{ old('amount') }}"
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
                                                <option value="one_time" {{ old('frequency') == 'one_time' ? 'selected' : '' }}>One Time</option>
                                                <option value="semester" {{ old('frequency') == 'semester' ? 'selected' : '' }}>Per Semester</option>
                                                <option value="monthly" {{ old('frequency') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                                <option value="annual" {{ old('frequency') == 'annual' ? 'selected' : '' }}>Annual</option>
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
                                                    <option value="{{ $year->id }}" {{ old('academic_year_id') == $year->id ? 'selected' : '' }}>
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
                                                    <option value="{{ $semester->id }}" {{ old('semester_id') == $semester->id ? 'selected' : '' }}>
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
                                                   id="due_date" name="due_date" value="{{ old('due_date') }}">
                                            @error('due_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="late_fee_amount" class="form-label">Late Fee Amount ($)</label>
                                            <input type="number" class="form-control @error('late_fee_amount') is-invalid @enderror"
                                                   id="late_fee_amount" name="late_fee_amount" value="{{ old('late_fee_amount') }}"
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
                                                   id="late_fee_days" name="late_fee_days" value="{{ old('late_fee_days') }}"
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
                                                   name="is_mandatory" value="1" {{ old('is_mandatory') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_mandatory">
                                                Mandatory Fee
                                            </label>
                                            <div class="form-text">Students must pay this fee to continue enrollment</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" id="is_active"
                                                   name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_active">
                                                Active
                                            </label>
                                            <div class="form-text">Only active fee structures can be used for new invoices</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('admin.fees.structures.index') }}" class="btn btn-secondary">Cancel</a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Create Fee Structure
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title mb-0">Fee Structure Preview</h6>
                        </div>
                        <div class="card-body">
                            <div id="preview-content">
                                <p class="text-muted">Fill out the form to see a preview of the fee structure.</p>
                            </div>
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
                                    <strong>Fee Types:</strong> Choose the appropriate category for better organization
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-calendar text-warning me-2"></i>
                                    <strong>Due Date:</strong> Set a specific due date or leave blank for flexible payment
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-exclamation-triangle text-danger me-2"></i>
                                    <strong>Late Fees:</strong> Encourage timely payments with late fee penalties
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check-circle text-success me-2"></i>
                                    <strong>Mandatory:</strong> Mark as mandatory for required fees like tuition
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
    const previewContent = document.getElementById('preview-content');

    function updatePreview() {
        const formData = new FormData(form);
        const name = formData.get('name') || 'Untitled Fee';
        const type = formData.get('type') || 'Not specified';
        const amount = formData.get('amount') || '0.00';
        const frequency = formData.get('frequency') || 'Not specified';
        const dueDate = formData.get('due_date') || 'No due date';
        const lateFee = formData.get('late_fee_amount') || '0.00';
        const isMandatory = formData.get('is_mandatory') ? 'Yes' : 'No';
        const isActive = formData.get('is_active') ? 'Yes' : 'No';

        previewContent.innerHTML = `
            <div class="mb-3">
                <h6 class="text-primary">${name}</h6>
                <span class="badge bg-info">${type.charAt(0).toUpperCase() + type.slice(1)}</span>
            </div>
            <table class="table table-sm">
                <tr>
                    <td><strong>Amount:</strong></td>
                    <td>$${parseFloat(amount).toFixed(2)}</td>
                </tr>
                <tr>
                    <td><strong>Frequency:</strong></td>
                    <td>${frequency.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())}</td>
                </tr>
                <tr>
                    <td><strong>Due Date:</strong></td>
                    <td>${dueDate !== 'No due date' ? new Date(dueDate).toLocaleDateString() : dueDate}</td>
                </tr>
                <tr>
                    <td><strong>Late Fee:</strong></td>
                    <td>$${parseFloat(lateFee).toFixed(2)}</td>
                </tr>
                <tr>
                    <td><strong>Mandatory:</strong></td>
                    <td>${isMandatory}</td>
                </tr>
                <tr>
                    <td><strong>Status:</strong></td>
                    <td>
                        <span class="badge ${isActive === 'Yes' ? 'bg-success' : 'bg-danger'}">
                            ${isActive === 'Yes' ? 'Active' : 'Inactive'}
                        </span>
                    </td>
                </tr>
            </table>
        `;
    }

    // Update preview on form changes
    form.addEventListener('input', updatePreview);
    form.addEventListener('change', updatePreview);

    // Initial preview update
    updatePreview();
});
</script>
@endpush
