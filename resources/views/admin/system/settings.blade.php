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

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card h-100 border-{{ $admissionWindow['isOpen'] ? 'success' : 'warning' }}">
            <div class="card-body d-flex align-items-center gap-3">
                <i class="bi bi-person-lines-fill fs-2 text-{{ $admissionWindow['isOpen'] ? 'success' : 'warning' }}"></i>
                <div>
                    <div class="small text-muted">Admission applications</div>
                    <div class="fw-bold text-capitalize">{{ $admissionWindow['status'] }}</div>
                    <div class="small">Prospective students applying to JBI</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        @php($courseRegistrationOpen = $currentSemester?->is_registration_open ?? false)
        <div class="card h-100 border-{{ $courseRegistrationOpen ? 'success' : 'secondary' }}">
            <div class="card-body d-flex align-items-center gap-3">
                <i class="bi bi-journal-check fs-2 text-{{ $courseRegistrationOpen ? 'success' : 'secondary' }}"></i>
                <div>
                    <div class="small text-muted">Semester registration</div>
                    <div class="fw-bold">{{ $courseRegistrationOpen ? 'Open' : 'Closed' }}</div>
                    <div class="small">Admitted students enrolling in courses</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100 border-primary">
            <div class="card-body d-flex align-items-center gap-3">
                <i class="bi bi-clock-history fs-2 text-primary"></i>
                <div>
                    <div class="small text-muted">System time</div>
                    <div class="fw-bold">{{ $admissionWindow['now']->format('d M Y, H:i') }}</div>
                    <div class="small">South African Standard Time</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="alert alert-light border mb-4">
    <strong>Admission</strong> is the application and approval process for prospective students.
    <strong>Registration</strong> happens after admission, when students pay required fees and enrol in semester courses.
    Semester registration dates are managed under <a href="{{ route('admin.semesters.index') }}">Semesters</a>.
</div>

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
                               value="{{ old('app_email', $settings->get('app_email')->value ?? 'info@jbiuniversity.com') }}" required>
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
                            <option value="Africa/Johannesburg" {{ old('timezone', $settings->get('timezone')->value ?? 'Africa/Johannesburg') === 'Africa/Johannesburg' ? 'selected' : '' }}>South Africa Standard Time (SAST)</option>
                            <option value="Africa/Kampala" {{ old('timezone', $settings->get('timezone')->value ?? '') === 'Africa/Kampala' ? 'selected' : '' }}>East Africa Time (Kampala)</option>
                            <option value="Africa/Nairobi" {{ old('timezone', $settings->get('timezone')->value ?? '') === 'Africa/Nairobi' ? 'selected' : '' }}>East Africa Time (Nairobi)</option>
                            <option value="UTC" {{ old('timezone', $settings->get('timezone')->value ?? '') === 'UTC' ? 'selected' : '' }}>UTC</option>
                        </select>
                        <small class="text-muted">All registration windows and system dates use this timezone.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Operating Region</label>
                        <select name="operating_region" id="operating_region" class="form-select" required>
                            @foreach($currencyRegions as $code => $region)
                                <option value="{{ $code }}" data-default-currency="{{ $region['default'] }}" {{ old('operating_region', $settings->get('operating_region')->value ?? 'southern_africa') === $code ? 'selected' : '' }}>{{ $region['label'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold"><i class="bi bi-currency-exchange me-1 text-primary"></i>Default Currency</label>
                        <select name="default_currency" id="default_currency" class="form-select" required>
                            @foreach($supportedCurrencies as $code => $name)
                                <option value="{{ $code }}" {{ old('default_currency', $settings->get('default_currency')->value ?? 'ZAR') === $code ? 'selected' : '' }}>{{ $code }} — {{ $name }}</option>
                            @endforeach
                        </select>
                    </div>

                    @php
                        $savedCurrencies = $settings->get('accepted_currencies')->typed_value ?? ['ZAR', 'USD'];
                        $selectedCurrencies = old('accepted_currencies', is_array($savedCurrencies) ? $savedCurrencies : ['ZAR', 'USD']);
                    @endphp
                    <div class="mb-3">
                        <label class="form-label">Accepted Currencies</label>
                        <select name="accepted_currencies[]" class="form-select" multiple size="7" required>
                            @foreach($supportedCurrencies as $code => $name)
                                <option value="{{ $code }}" {{ in_array($code, $selectedCurrencies, true) ? 'selected' : '' }}>{{ $code }} — {{ $name }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted">Hold Ctrl (Windows) or Command (Mac) to select several currencies. The default currency must also be selected.</small>
                    </div>

                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" name="maintenance_mode" id="maintenance_mode" value="1"
                               {{ old('maintenance_mode', $settings->get('maintenance_mode')->value ?? false) ? 'checked' : '' }}>
                        <label class="form-check-label" for="maintenance_mode">
                            Maintenance Mode
                        </label>
                        <small class="d-block text-muted">System will be unavailable to users when enabled</small>
                    </div>

                    <hr class="my-4">
                    <h6 class="text-primary mb-3"><i class="bi bi-person-lines-fill me-2"></i>Admission Application Window</h6>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" name="admission_enabled" id="admission_enabled" value="1"
                               {{ filter_var(old('admission_enabled', $settings->get('admission_enabled')->value ?? $settings->get('registration_enabled')->value ?? true), FILTER_VALIDATE_BOOLEAN) ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold" for="admission_enabled">
                            Accept New Admission Applications
                        </label>
                        <small class="d-block text-muted">Allows prospective students to create an applicant account and submit an application.</small>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Applications Open</label>
                            <input type="datetime-local" name="admission_open_at" class="form-control"
                                   value="{{ old('admission_open_at', $settings->get('admission_open_at')->value ?? $settings->get('registration_open_at')->value ?? '') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Applications Close</label>
                            <input type="datetime-local" name="admission_close_at" class="form-control"
                                   value="{{ old('admission_close_at', $settings->get('admission_close_at')->value ?? $settings->get('registration_close_at')->value ?? '') }}">
                        </div>
                    </div>
                    <div class="alert alert-info py-2 small">
                        New applicant accounts and applications are accepted only within this window. Closing admissions does not remove existing applications or prevent administrators from reviewing them.
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
