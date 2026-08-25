@extends('layouts.app')

@section('title', 'System Settings')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0 text-primary">
            <i class="bi bi-gear me-2"></i>System Settings
        </h1>
        <p class="text-muted mb-0">Configure system-wide settings and preferences</p>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<form action="{{ route('admin.settings.update') }}" method="POST">
    @csrf
    @method('PUT')

    <div class="row">
        <!-- General Settings -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-info-circle me-2"></i>General Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Application Name <span class="text-danger">*</span></label>
                        <input type="text" name="app_name" class="form-control @error('app_name') is-invalid @enderror"
                               value="{{ old('app_name', $settings->get('app_name')->value ?? 'JBI University') }}" required>
                        @error('app_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Contact Email <span class="text-danger">*</span></label>
                        <input type="email" name="app_email" class="form-control @error('app_email') is-invalid @enderror"
                               value="{{ old('app_email', $settings->get('app_email')->value ?? 'info@jbiuniversity.edu') }}" required>
                        @error('app_email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Contact Phone</label>
                        <input type="text" name="app_phone" class="form-control"
                               value="{{ old('app_phone', $settings->get('app_phone')->value ?? '') }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <textarea name="app_address" class="form-control" rows="3">{{ old('app_address', $settings->get('app_address')->value ?? '') }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="app_description" class="form-control" rows="3">{{ old('app_description', $settings->get('app_description')->value ?? '') }}</textarea>
                        <small class="text-muted">Brief description of the institution</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- System Settings -->
        <div class="col-lg-6 mb-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-sliders me-2"></i>System Configuration
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Timezone</label>
                        <select name="timezone" class="form-select">
                            <option value="UTC" {{ old('timezone', $settings->get('timezone')->value ?? 'UTC') == 'UTC' ? 'selected' : '' }}>UTC</option>
                            <option value="America/New_York" {{ old('timezone', $settings->get('timezone')->value ?? '') == 'America/New_York' ? 'selected' : '' }}>Eastern Time (US)</option>
                            <option value="America/Chicago" {{ old('timezone', $settings->get('timezone')->value ?? '') == 'America/Chicago' ? 'selected' : '' }}>Central Time (US)</option>
                            <option value="America/Denver" {{ old('timezone', $settings->get('timezone')->value ?? '') == 'America/Denver' ? 'selected' : '' }}>Mountain Time (US)</option>
                            <option value="America/Los_Angeles" {{ old('timezone', $settings->get('timezone')->value ?? '') == 'America/Los_Angeles' ? 'selected' : '' }}>Pacific Time (US)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark"><i class="bi bi-currency-exchange me-1 text-primary"></i>System Default Currency</label>
                        <input type="text" name="default_currency" class="form-control form-control-lg text-uppercase fw-bold" maxlength="5"
                               value="{{ old('default_currency', $settings->get('default_currency')->value ?? 'USD') }}" placeholder="USD" required>
                        <small class="text-muted d-block mt-1">3 to 5-letter global currency code (e.g. <strong>USD</strong>, <strong>EUR</strong>, <strong>GBP</strong>, <strong>UGX</strong>, <strong>KES</strong>). Applies dynamically across all fees, invoices, receipts, and finance modules.</small>
                    </div>

                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" name="maintenance_mode" id="maintenance_mode" value="1"
                               {{ old('maintenance_mode', $settings->get('maintenance_mode')->value ?? false) ? 'checked' : '' }}>
                        <label class="form-check-label" for="maintenance_mode">
                            Maintenance Mode
                        </label>
                        <small class="d-block text-muted">System will be unavailable to users when enabled</small>
                    </div>

                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" name="registration_enabled" id="registration_enabled" value="1"
                               {{ old('registration_enabled', $settings->get('registration_enabled')->value ?? true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="registration_enabled">
                            Allow User Registration
                        </label>
                        <small class="d-block text-muted">Users can self-register for accounts</small>
                    </div>
                </div>
            </div>

            <!-- Academic Settings -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-mortarboard me-2"></i>Academic Configuration
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Max Students Per Course</label>
                        <input type="number" name="max_students_per_course" class="form-control" min="1"
                               value="{{ old('max_students_per_course', $settings->get('max_students_per_course')->value ?? 50) }}">
                        <small class="text-muted">Default enrollment limit for courses</small>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Academic Year Start</label>
                            <input type="date" name="academic_year_start" class="form-control"
                                   value="{{ old('academic_year_start', $settings->get('academic_year_start')->value ?? '') }}">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Academic Year End</label>
                            <input type="date" name="academic_year_end" class="form-control"
                                   value="{{ old('academic_year_end', $settings->get('academic_year_end')->value ?? '') }}">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold"><i class="bi bi-file-earmark-check me-1 text-primary"></i>Configured Exam Types</label>
                        <input type="text" name="exam_types" class="form-control"
                               value="{{ old('exam_types', $settings->get('exam_types')->value ?? 'Midterm, Final, Quiz, Assignment, Practical, Test, Mock Exam, Supplementary') }}"
                               placeholder="e.g. Midterm, Final, Quiz, Assignment, Practical, Test, Mock Exam">
                        <small class="text-muted d-block mt-1">Comma-separated list of examination types available for faculty to select when scheduling course exams.</small>
                    </div>
                </div>
            </div>

            <!-- Admissions & Payments -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-cash-coin me-2"></i>Admissions & Payments
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Registration Fee Structure</label>
                        <select name="registration_fee_structure_id" class="form-select">
                            <option value="">Select Registration Fee</option>
                            @foreach($feeStructures ?? [] as $structure)
                                <option value="{{ $structure->id }}"
                                    {{ (string) old('registration_fee_structure_id', $settings->get('registration_fee_structure_id')->value ?? '') === (string) $structure->id ? 'selected' : '' }}>
                                    {{ $structure->name }} ({{ strtoupper($structure->type) }}) - {{ $settings->get('default_currency')->value ?? 'USD' }} {{ number_format($structure->amount, 2) }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">This fee must be paid before activation and admission numbers are issued.</small>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Registration Payment Deadline (days)</label>
                            <input type="number" name="registration_payment_days" class="form-control" min="1" max="365"
                                   value="{{ old('registration_payment_days', $settings->get('registration_payment_days')->value ?? 14) }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tuition Minimum % for Enrollment</label>
                            <input type="number" name="tuition_min_percent" class="form-control" min="0" max="100" step="0.01"
                                   value="{{ old('tuition_min_percent', $settings->get('tuition_min_percent')->value ?? 0) }}">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tuition Payment Deadline (days after registration)</label>
                        <input type="number" name="tuition_payment_days" class="form-control" min="1" max="365"
                               value="{{ old('tuition_payment_days', $settings->get('tuition_payment_days')->value ?? 30) }}">
                        <small class="text-muted">Students must meet the tuition % by this deadline to remain active.</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check me-2"></i>Save Settings
            </button>
            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </div>
</form>
@endsection
