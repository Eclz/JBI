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
        @php
            $courseRegistrationOpen = $currentSemester?->is_registration_open ?? false;
        @endphp
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
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label fw-semibold mb-0">Accepted Currencies</label>
                            <div class="btn-group btn-group-sm" role="group">
                                <button type="button" class="btn btn-outline-primary" id="selectCommonCurrencies">Select common</button>
                                <button type="button" class="btn btn-outline-secondary" id="clearCurrencies">Clear</button>
                            </div>
                        </div>
                        <div class="border rounded p-3 bg-light" style="max-height: 300px; overflow-y: auto;">
                            <div class="row g-2">
                                @foreach($supportedCurrencies as $code => $name)
                                    <div class="col-md-6">
                                        <label class="currency-option d-flex align-items-center gap-2 bg-white border rounded p-2 w-100" for="currency_{{ $code }}" style="cursor: pointer;">
                                            <input class="form-check-input currency-checkbox mt-0" type="checkbox"
                                                   name="accepted_currencies[]" id="currency_{{ $code }}" value="{{ $code }}"
                                                   {{ in_array($code, $selectedCurrencies, true) ? 'checked' : '' }}>
                                            <span class="fw-bold text-primary" style="min-width: 42px;">{{ $code }}</span>
                                            <span class="small text-muted">{{ $name }}</span>
                                            <span class="badge bg-primary ms-auto default-currency-badge d-none">Default</span>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @error('accepted_currencies')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        <small class="text-muted">Tick every currency JBI will accept. The selected default currency is automatically included.</small>
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

                    <div class="mb-3">
                        <label class="form-label fw-semibold"><i class="bi bi-file-earmark-check me-1 text-primary"></i>Configured Exam Types</label>
                        <input type="text" name="exam_types" class="form-control"
                               value="{{ old('exam_types', $settings->get('exam_types')->value ?? 'Midterm, Final, Quiz, Assignment, Practical, Test, Mock Exam, Supplementary') }}"
                               placeholder="e.g. Midterm, Final, Quiz, Assignment, Practical, Test, Mock Exam">
                        <small class="text-muted d-block mt-1">Comma-separated list of examination types available for faculty to select when scheduling course exams.</small>
                    </div>
                </div>
            </div>

            <!-- Academic Term Settings -->
            <div class="card mt-4 border-info">
                <div class="card-header bg-info bg-opacity-10">
                    <h5 class="card-title mb-0 text-info-emphasis">
                        <i class="bi bi-calendar-range me-2"></i>Current Academic Term
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Current Academic Year</label>
                            <select name="current_academic_year_id" id="current_academic_year_id" class="form-select">
                                <option value="">Select Academic Year</option>
                                @foreach($academicYears as $year)
                                    <option value="{{ $year->id }}" {{ $year->is_current ? 'selected' : '' }}>
                                        {{ $year->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Current Semester</label>
                            <select name="current_semester_id" id="current_semester_id" class="form-select">
                                <option value="">Select Semester</option>
                                @foreach($academicYears as $year)
                                    <optgroup label="{{ $year->name }}">
                                        @foreach($year->semesters as $sem)
                                            <option value="{{ $sem->id }}" 
                                                data-start="{{ $sem->start_date ? $sem->start_date->format('Y-m-d') : '' }}"
                                                data-end="{{ $sem->end_date ? $sem->end_date->format('Y-m-d') : '' }}"
                                                data-reg-start="{{ $sem->registration_start ? $sem->registration_start->format('Y-m-d') : '' }}"
                                                data-reg-end="{{ $sem->registration_end ? $sem->registration_end->format('Y-m-d') : '' }}"
                                                {{ $sem->is_current ? 'selected' : '' }}>
                                                {{ $sem->name }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div id="semester_dates_section">
                        <hr>
                        <h6 class="mb-3">Semester Dates</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Semester Start Date</label>
                                <input type="date" name="semester_start_date" id="semester_start_date" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Semester End (Closure) Date</label>
                                <input type="date" name="semester_end_date" id="semester_end_date" class="form-control">
                                <small class="text-muted">Semester auto-closes on this date.</small>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Enrollment Start Date</label>
                                <input type="date" name="semester_registration_start" id="semester_registration_start" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-danger fw-bold">Enrollment Deadline</label>
                                <input type="date" name="semester_registration_end" id="semester_registration_end" class="form-control border-danger">
                                <small class="text-danger">Students cannot enroll after this date.</small>
                            </div>
                        </div>

                        <div class="mb-3 d-none" id="reason_for_change_container">
                            <label class="form-label text-warning fw-bold">Reason for Date Change</label>
                            <textarea name="reason_for_change" id="reason_for_change" class="form-control border-warning" rows="2" placeholder="Required when modifying active dates (will notify users)..."></textarea>
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const defaultCurrency = document.getElementById('default_currency');
    const currencyCheckboxes = Array.from(document.querySelectorAll('.currency-checkbox'));
    const commonCurrencies = ['ZAR', 'USD', 'EUR', 'GBP'];

    function syncDefaultCurrency() {
        document.querySelectorAll('.default-currency-badge').forEach(badge => badge.classList.add('d-none'));
        const checkbox = document.getElementById('currency_' + defaultCurrency.value);
        if (checkbox) {
            checkbox.checked = true;
            checkbox.closest('.currency-option').querySelector('.default-currency-badge').classList.remove('d-none');
        }
    }

    defaultCurrency.addEventListener('change', syncDefaultCurrency);
    document.getElementById('selectCommonCurrencies').addEventListener('click', function () {
        currencyCheckboxes.forEach(checkbox => checkbox.checked = commonCurrencies.includes(checkbox.value));
        syncDefaultCurrency();
    });
    document.getElementById('clearCurrencies').addEventListener('click', function () {
        currencyCheckboxes.forEach(checkbox => checkbox.checked = false);
        syncDefaultCurrency();
    });
    currencyCheckboxes.forEach(checkbox => checkbox.addEventListener('change', syncDefaultCurrency));
    syncDefaultCurrency();

    // Academic Term Settings Logic
    const currentSemesterSelect = document.getElementById('current_semester_id');
    const dateInputs = ['semester_start_date', 'semester_end_date', 'semester_registration_start', 'semester_registration_end'];
    const originalDates = {};
    const reasonContainer = document.getElementById('reason_for_change_container');
    const reasonInput = document.getElementById('reason_for_change');

    function populateSemesterDates() {
        if (!currentSemesterSelect.value) return;
        const selectedOption = currentSemesterSelect.options[currentSemesterSelect.selectedIndex];
        
        dateInputs.forEach(id => {
            const input = document.getElementById(id);
            const dataKey = id.replace('semester_', '').replace('_date', '');
            
            // Map the data attributes
            let val = '';
            if (id === 'semester_start_date') val = selectedOption.dataset.start;
            if (id === 'semester_end_date') val = selectedOption.dataset.end;
            if (id === 'semester_registration_start') val = selectedOption.dataset.regStart;
            if (id === 'semester_registration_end') val = selectedOption.dataset.regEnd;
            
            input.value = val;
            originalDates[id] = val; // Store original values to detect changes
        });
        
        checkDateModifications();
    }

    function checkDateModifications() {
        let isModified = false;
        dateInputs.forEach(id => {
            const input = document.getElementById(id);
            if (input.value !== originalDates[id]) {
                isModified = true;
            }
        });

        if (isModified) {
            reasonContainer.classList.remove('d-none');
            reasonInput.setAttribute('required', 'required');
        } else {
            reasonContainer.classList.add('d-none');
            reasonInput.removeAttribute('required');
        }
    }

    currentSemesterSelect.addEventListener('change', populateSemesterDates);
    dateInputs.forEach(id => {
        document.getElementById(id).addEventListener('change', checkDateModifications);
    });

    // Initialize on page load
    populateSemesterDates();
    
    // Form confirmation
    document.querySelector('form').addEventListener('submit', function(e) {
        if (!reasonContainer.classList.contains('d-none')) {
            if (!confirm('You are about to modify the active semester dates. Users will be notified with the provided reason. Proceed?')) {
                e.preventDefault();
            }
        }
    });
});
</script>
@endpush
