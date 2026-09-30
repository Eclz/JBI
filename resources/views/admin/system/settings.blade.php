@extends('layouts.app')

@section('title', 'System Settings')

@section('content')
<div class="container-fluid px-4 py-4">
<div class="mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h3 mb-1 fw-bold text-dark">System Settings</h1>
            <p class="text-muted mb-0">Manage your institution's configuration, academic periods, admissions, payments and system preferences.</p>
        </div>
    </div>

    <!-- Status Cards -->
    <div class="row g-3">
        <div class="col-md-4">
            <div class="card h-100 border-{{ $admissionWindow['isOpen'] ? 'success' : 'warning' }} shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <i class="bi bi-person-lines-fill fs-4 text-{{ $admissionWindow['isOpen'] ? 'success' : 'warning' }} me-2"></i>
                        <h6 class="mb-0 fw-bold">Admissions</h6>
                    </div>
                    <div class="h5 mb-1 text-{{ $admissionWindow['isOpen'] ? 'success' : 'warning' }} fw-bold text-uppercase">{{ $admissionWindow['status'] }}</div>
                    <div class="small text-muted">Prospective students applying to JBI</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            @php
                $courseRegistrationOpen = $currentSemester?->is_registration_open ?? false;
            @endphp
            <div class="card h-100 border-{{ $courseRegistrationOpen ? 'success' : 'secondary' }} shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <i class="bi bi-journal-check fs-4 text-{{ $courseRegistrationOpen ? 'success' : 'secondary' }} me-2"></i>
                        <h6 class="mb-0 fw-bold">Registration</h6>
                    </div>
                    <div class="h5 mb-1 text-{{ $courseRegistrationOpen ? 'success' : 'secondary' }} fw-bold text-uppercase">{{ $courseRegistrationOpen ? 'Open' : 'Closed' }}</div>
                    <div class="small text-muted">Admitted students enrolling in courses</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 border-primary shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <i class="bi bi-clock-history fs-4 text-primary me-2"></i>
                        <h6 class="mb-0 fw-bold">System Time</h6>
                    </div>
                    <div class="h5 mb-1 text-primary fw-bold">{{ $admissionWindow['now']->format('d M Y') }}</div>
                    <div class="small text-muted">{{ $admissionWindow['now']->format('H:i') }} — {{ old('timezone', $settings->get('timezone')->value ?? 'Africa/Johannesburg') }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<form action="{{ route('admin.settings.update') }}" method="POST" id="settingsForm">
    @csrf
    @method('PUT')

    <div class="row">
        <!-- Sticky Sidebar Navigation -->
        <div class="col-md-3 mb-4">
            <div class="sticky-top" style="top: 80px; z-index: 10;">
                <h6 class="text-muted fw-bold mb-3 ms-2">SETTINGS</h6>
                <div class="nav flex-column nav-pills" id="settings-tab" role="tablist" aria-orientation="vertical">
                    <button class="nav-link active text-start fw-semibold mb-1" id="tab-general" data-bs-toggle="pill" data-bs-target="#content-general" type="button" role="tab"><i class="bi bi-building me-2"></i>General</button>
                    <button class="nav-link text-start fw-semibold mb-1" id="tab-system" data-bs-toggle="pill" data-bs-target="#content-system" type="button" role="tab"><i class="bi bi-sliders me-2"></i>System</button>
                    <button class="nav-link text-start fw-semibold mb-1" id="tab-academic" data-bs-toggle="pill" data-bs-target="#content-academic" type="button" role="tab"><i class="bi bi-mortarboard me-2"></i>Academic</button>
                    <button class="nav-link text-start fw-semibold mb-1" id="tab-term" data-bs-toggle="pill" data-bs-target="#content-term" type="button" role="tab"><i class="bi bi-calendar-range me-2"></i>Academic Term</button>
                    <button class="nav-link text-start fw-semibold mb-1" id="tab-admissions" data-bs-toggle="pill" data-bs-target="#content-admissions" type="button" role="tab"><i class="bi bi-person-lines-fill me-2"></i>Admissions</button>
                    <button class="nav-link text-start fw-semibold mb-1" id="tab-payments" data-bs-toggle="pill" data-bs-target="#content-payments" type="button" role="tab"><i class="bi bi-cash-coin me-2"></i>Payments</button>
                </div>
            </div>
        </div>

        <!-- Settings Content -->
        <div class="col-md-9 mb-5 pb-5">
            <div class="tab-content" id="settings-tabContent">
                
                <!-- General Tab -->
                <div class="tab-pane fade show active" id="content-general" role="tabpanel">
                    <div class="mb-4 d-flex justify-content-between align-items-start">
                        <div>
                            <h3 class="h5 fw-bold mb-1">General Information</h3>
                            <p class="text-muted mb-0">Configure institution details and contact information.</p>
                        </div>
                        <div class="d-flex gap-2 flex-shrink-0">
                            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary fw-bold px-4"><i class="bi bi-check-lg me-1"></i> Save</button>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body p-4">
                            <h6 class="fw-bold mb-4 border-bottom pb-2">Institution Details</h6>
                            
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Application Name <span class="text-danger">*</span></label>
                                    <input type="text" name="app_name" class="form-control" value="{{ old('app_name', $settings->get('app_name')->value ?? 'JBI University') }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Contact Email <span class="text-danger">*</span></label>
                                    <input type="email" name="app_email" class="form-control" value="{{ old('app_email', $settings->get('app_email')->value ?? 'info@jbiuniversity.com') }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Contact Phone</label>
                                    <input type="text" name="app_phone" class="form-control" value="{{ old('app_phone', $settings->get('app_phone')->value ?? '') }}">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Address</label>
                                    <textarea name="app_address" class="form-control" rows="2">{{ old('app_address', $settings->get('app_address')->value ?? '') }}</textarea>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Description</label>
                                    <textarea name="app_description" class="form-control" rows="2">{{ old('app_description', $settings->get('app_description')->value ?? '') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- System Tab -->
                <div class="tab-pane fade" id="content-system" role="tabpanel">
                    <div class="mb-4 d-flex justify-content-between align-items-start">
                        <div>
                            <h3 class="h5 fw-bold mb-1">System Configuration</h3>
                            <p class="text-muted mb-0">Manage timezones, region, and currencies.</p>
                        </div>
                        <div class="d-flex gap-2 flex-shrink-0">
                            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary fw-bold px-4"><i class="bi bi-check-lg me-1"></i> Save</button>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body p-4">
                            <h6 class="fw-bold mb-4 border-bottom pb-2">Localization</h6>
                            
                            <div class="row g-4 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Timezone</label>
                                    <select name="timezone" class="form-select">
                                        <option value="Africa/Johannesburg" {{ old('timezone', $settings->get('timezone')->value ?? 'Africa/Johannesburg') === 'Africa/Johannesburg' ? 'selected' : '' }}>South Africa Standard Time (SAST)</option>
                                        <option value="Africa/Kampala" {{ old('timezone', $settings->get('timezone')->value ?? '') === 'Africa/Kampala' ? 'selected' : '' }}>East Africa Time (Kampala)</option>
                                        <option value="Africa/Nairobi" {{ old('timezone', $settings->get('timezone')->value ?? '') === 'Africa/Nairobi' ? 'selected' : '' }}>East Africa Time (Nairobi)</option>
                                        <option value="UTC" {{ old('timezone', $settings->get('timezone')->value ?? '') === 'UTC' ? 'selected' : '' }}>UTC</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Operating Region</label>
                                    <select name="operating_region" id="operating_region" class="form-select" required>
                                        @foreach($currencyRegions as $code => $region)
                                            <option value="{{ $code }}" data-default-currency="{{ $region['default'] }}" {{ old('operating_region', $settings->get('operating_region')->value ?? 'southern_africa') === $code ? 'selected' : '' }}>{{ $region['label'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                            <h6 class="fw-bold mb-4 border-bottom pb-2">Currencies</h6>
                            
                            <div class="mb-4">
                                <label class="form-label fw-semibold">Default Currency</label>
                                <div class="row">
                                    <div class="col-md-6">
                                        <select name="default_currency" id="default_currency" class="form-select" required>
                                            @foreach($supportedCurrencies as $code => $name)
                                                <option value="{{ $code }}" {{ old('default_currency', $settings->get('default_currency')->value ?? 'ZAR') === $code ? 'selected' : '' }}>{{ $code }} — {{ $name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="text-muted small mt-2"><i class="bi bi-check-circle-fill text-success me-1"></i> Default currency is automatically included as an accepted currency.</div>
                            </div>

                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label class="form-label fw-semibold mb-0">Accepted Currencies</label>
                                    <div>
                                        <button type="button" class="btn btn-sm btn-link text-decoration-none" id="selectCommonCurrencies">Select Common</button>
                                        <button type="button" class="btn btn-sm btn-link text-danger text-decoration-none" id="clearCurrencies">Clear All</button>
                                    </div>
                                </div>
                                
                                @php
                                    $savedCurrencies = $settings->get('accepted_currencies')->typed_value ?? ['ZAR', 'USD'];
                                    $selectedCurrencies = old('accepted_currencies', is_array($savedCurrencies) ? $savedCurrencies : ['ZAR', 'USD']);
                                @endphp
                                
                                <div class="row g-2" style="max-height: 250px; overflow-y: auto;">
                                    @foreach($supportedCurrencies as $code => $name)
                                        <div class="col-lg-3 col-md-4 col-sm-6">
                                            <label class="card h-100 cursor-pointer currency-card border-{{ in_array($code, $selectedCurrencies) ? 'primary' : 'light' }} shadow-sm" style="cursor:pointer;" for="currency_{{ $code }}">
                                                <div class="card-body p-3 d-flex align-items-center">
                                                    <div class="form-check m-0 w-100 d-flex justify-content-between align-items-center">
                                                        <div class="d-flex align-items-center">
                                                            <input class="form-check-input currency-checkbox me-2" type="checkbox" name="accepted_currencies[]" id="currency_{{ $code }}" value="{{ $code }}" {{ in_array($code, $selectedCurrencies, true) ? 'checked' : '' }}>
                                                            <span class="fw-bold">{{ $code }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card border-danger shadow-sm bg-danger bg-opacity-10 mb-4">
                        <div class="card-body p-4 d-flex align-items-start gap-3">
                            <i class="bi bi-exclamation-triangle-fill text-danger fs-3"></i>
                            <div>
                                <h6 class="fw-bold text-danger mb-1">Maintenance Mode</h6>
                                <p class="text-danger opacity-75 small mb-3">Temporarily prevent users from accessing the system.</p>
                                <div class="form-check form-switch fs-5">
                                    <input class="form-check-input" type="checkbox" name="maintenance_mode" id="maintenance_mode" value="1" {{ old('maintenance_mode', $settings->get('maintenance_mode')->value ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold text-dark fs-6" for="maintenance_mode">OFF / ON</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Academic Tab -->
                <div class="tab-pane fade" id="content-academic" role="tabpanel">
                    <div class="mb-4 d-flex justify-content-between align-items-start">
                        <div>
                            <h3 class="h5 fw-bold mb-1">Academic Configuration</h3>
                            <p class="text-muted mb-0">Configure academic years, courses and examinations.</p>
                        </div>
                        <div class="d-flex gap-2 flex-shrink-0">
                            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary fw-bold px-4"><i class="bi bi-check-lg me-1"></i> Save</button>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body p-4">
                            <h6 class="fw-bold mb-4 border-bottom pb-2">General Academic Settings</h6>
                            
                            <div class="row g-4 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Maximum Students per Course</label>
                                    <input type="number" name="max_students_per_course" class="form-control" min="1" value="{{ old('max_students_per_course', $settings->get('max_students_per_course')->value ?? 50) }}">
                                </div>
                            </div>

                            <h6 class="fw-bold mb-4 border-bottom pb-2">Academic Year</h6>
                            
                            <div class="row g-4 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Academic Year Start Date</label>
                                    <input type="date" name="academic_year_start" class="form-control" value="{{ old('academic_year_start', $settings->get('academic_year_start')->value ?? '') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Academic Year End Date</label>
                                    <input type="date" name="academic_year_end" class="form-control" value="{{ old('academic_year_end', $settings->get('academic_year_end')->value ?? '') }}">
                                </div>
                            </div>

                            <h6 class="fw-bold mb-4 border-bottom pb-2">Examinations</h6>
                            
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Configured Exam Types</label>
                                <input type="text" name="exam_types" class="form-control" value="{{ old('exam_types', $settings->get('exam_types')->value ?? 'Midterm, Final, Quiz, Assignment, Practical, Test, Mock Exam, Supplementary') }}">
                                <small class="text-muted mt-1 d-block">Comma-separated list of examination types available for faculty to select.</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Academic Term Tab -->
                <div class="tab-pane fade" id="content-term" role="tabpanel">
                    <div class="mb-4 d-flex justify-content-between align-items-start">
                        <div>
                            <h3 class="h5 fw-bold mb-1">Current Academic Term</h3>
                            <p class="text-muted mb-0">Manage the active academic period and enrollment windows.</p>
                        </div>
                        <div class="d-flex gap-2 flex-shrink-0">
                            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary fw-bold px-4"><i class="bi bi-check-lg me-1"></i> Save</button>
                        </div>
                    </div>

                    <div class="card border-info shadow-sm mb-4">
                        <div class="card-body p-4">
                            <h6 class="fw-bold text-info mb-4 border-bottom border-info pb-2"><i class="bi bi-calendar-event me-2"></i>Active Academic Period</h6>
                            
                            <div class="row g-4 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Academic Year</label>
                                    <select name="current_academic_year_id" id="current_academic_year_id" class="form-select bg-light">
                                        <option value="">Select Academic Year</option>
                                        @foreach($academicYears as $year)
                                            <option value="{{ $year->id }}" {{ $year->is_current ? 'selected' : '' }}>{{ $year->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Current Semester</label>
                                    <select name="current_semester_id" id="current_semester_id" class="form-select bg-light">
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
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm mb-4" id="semester_dates_section">
                        <div class="card-body p-4">
                            <h6 class="fw-bold mb-4 border-bottom pb-2">Semester Dates</h6>
                            <div class="row g-4 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Semester Start Date</label>
                                    <input type="date" name="semester_start_date" id="semester_start_date" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Semester End Date</label>
                                    <input type="date" name="semester_end_date" id="semester_end_date" class="form-control">
                                </div>
                            </div>

                            <h6 class="fw-bold mb-4 border-bottom pb-2">Enrollment Period</h6>
                            <div class="row g-4 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Enrollment Opens</label>
                                    <input type="date" name="semester_registration_start" id="semester_registration_start" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-danger"><i class="bi bi-exclamation-circle me-1"></i>Enrollment Deadline</label>
                                    <input type="date" name="semester_registration_end" id="semester_registration_end" class="form-control border-danger">
                                    <small class="text-danger d-block mt-1">Students cannot enroll after this date.</small>
                                </div>
                            </div>

                            <h6 class="fw-bold mb-4 border-bottom pb-2">Program Change Window</h6>
                            <div class="row g-4 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Program Change Opens</label>
                                    <input type="date" name="program_change_start" id="program_change_start" class="form-control" value="{{ old('program_change_start', $settings->get('program_change_start')->value ?? '') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold"><i class="bi bi-clock-history me-1"></i>Program Change Deadline</label>
                                    <input type="date" name="program_change_end" id="program_change_end" class="form-control" value="{{ old('program_change_end', $settings->get('program_change_end')->value ?? '') }}">
                                    <small class="text-muted d-block mt-1">Students cannot request program changes after this date.</small>
                                </div>
                            </div>

                            <div class="d-none mt-4 p-3 border border-warning rounded bg-warning bg-opacity-10" id="reason_for_change_container">
                                <label class="form-label text-warning-emphasis fw-bold"><i class="bi bi-info-circle me-2"></i>Reason for Date Change</label>
                                <textarea name="reason_for_change" id="reason_for_change" class="form-control border-warning" rows="2" placeholder="Required when modifying active dates (will notify users)..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Admissions Tab -->
                <div class="tab-pane fade" id="content-admissions" role="tabpanel">
                    <div class="mb-4 d-flex justify-content-between align-items-start">
                        <div>
                            <h3 class="h5 fw-bold mb-1">Admissions</h3>
                            <p class="text-muted mb-0">Manage prospective student application windows.</p>
                        </div>
                        <div class="d-flex gap-2 flex-shrink-0">
                            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary fw-bold px-4"><i class="bi bi-check-lg me-1"></i> Save</button>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body p-4">
                            <h6 class="fw-bold mb-4 border-bottom pb-2">Application Window</h6>
                            
                            <div class="alert alert-info py-3 mb-4">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-info-circle fs-4 me-3"></i>
                                    <div>
                                        New applicant accounts and applications are accepted only within this window. Closing admissions does not remove existing applications or prevent administrators from reviewing them.
                                    </div>
                                </div>
                            </div>

                            <div class="card border-{{ filter_var(old('admission_enabled', $settings->get('admission_enabled')->value ?? $settings->get('registration_enabled')->value ?? true), FILTER_VALIDATE_BOOLEAN) ? 'success' : 'secondary' }} bg-light mb-4" id="admission_card">
                                <div class="card-body">
                                    <div class="form-check form-switch fs-5 mb-0">
                                        <input class="form-check-input" type="checkbox" name="admission_enabled" id="admission_enabled" value="1" {{ filter_var(old('admission_enabled', $settings->get('admission_enabled')->value ?? $settings->get('registration_enabled')->value ?? true), FILTER_VALIDATE_BOOLEAN) ? 'checked' : '' }}>
                                        <label class="form-check-label fw-bold text-dark fs-6 ms-2" for="admission_enabled">Accept New Admission Applications</label>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Applications Open</label>
                                    <input type="datetime-local" name="admission_open_at" class="form-control" value="{{ old('admission_open_at', $settings->get('admission_open_at')->value ?? $settings->get('registration_open_at')->value ?? '') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Applications Close</label>
                                    <input type="datetime-local" name="admission_close_at" class="form-control" value="{{ old('admission_close_at', $settings->get('admission_close_at')->value ?? $settings->get('registration_close_at')->value ?? '') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payments Tab -->
                <div class="tab-pane fade" id="content-payments" role="tabpanel">
                    <div class="mb-4 d-flex justify-content-between align-items-start">
                        <div>
                            <h3 class="h5 fw-bold mb-1">Payments & Fees</h3>
                            <p class="text-muted mb-0">Configure registration fees and tuition deadlines.</p>
                        </div>
                        <div class="d-flex gap-2 flex-shrink-0">
                            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary fw-bold px-4"><i class="bi bi-check-lg me-1"></i> Save</button>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body p-4">
                            <h6 class="fw-bold mb-4 border-bottom pb-2">Registration</h6>
                            
                            <div class="row g-4 mb-4">
                                <div class="col-md-12">
                                    <label class="form-label fw-semibold">Registration Fee Structure</label>
                                    <select name="registration_fee_structure_id" class="form-select w-50">
                                        <option value="">Select Registration Fee</option>
                                        @foreach($feeStructures ?? [] as $structure)
                                            <option value="{{ $structure->id }}"
                                                {{ (string) old('registration_fee_structure_id', $settings->get('registration_fee_structure_id')->value ?? '') === (string) $structure->id ? 'selected' : '' }}>
                                                {{ $structure->name }} ({{ strtoupper($structure->type) }}) - {{ $settings->get('default_currency')->value ?? 'USD' }} {{ number_format($structure->amount, 2) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted d-block mt-1">This fee must be paid before activation and admission numbers are issued.</small>
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Registration Payment Deadline</label>
                                    <div class="input-group">
                                        <input type="number" name="registration_payment_days" class="form-control" min="1" max="365" value="{{ old('registration_payment_days', $settings->get('registration_payment_days')->value ?? 14) }}">
                                        <span class="input-group-text">days</span>
                                    </div>
                                </div>
                            </div>

                            <h6 class="fw-bold mb-4 border-bottom pb-2">Tuition</h6>

                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Minimum Tuition for Enrollment</label>
                                    <div class="input-group">
                                        <input type="number" name="tuition_min_percent" class="form-control" min="0" max="100" step="0.01" value="{{ old('tuition_min_percent', $settings->get('tuition_min_percent')->value ?? 0) }}">
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Tuition Payment Deadline</label>
                                    <div class="input-group">
                                        <input type="number" name="tuition_payment_days" class="form-control" min="1" max="365" value="{{ old('tuition_payment_days', $settings->get('tuition_payment_days')->value ?? 30) }}">
                                        <span class="input-group-text">days after registration</span>
                                    </div>
                                    <small class="text-muted d-block mt-1">Students must meet the tuition % by this deadline to remain active.</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</form>

<style>
/* Style the settings tabs */
.nav-pills .nav-link {
    color: var(--bs-gray-700);
    border-radius: 0.5rem;
    padding: 0.75rem 1rem;
    transition: all 0.2s;
}
.nav-pills .nav-link:hover {
    background-color: var(--bs-gray-100);
}
.nav-pills .nav-link.active {
    background-color: var(--bs-primary-bg-subtle);
    color: var(--bs-primary);
    border-left: 4px solid var(--bs-primary);
}
.currency-card.border-primary {
    background-color: var(--bs-primary-bg-subtle);
    border-width: 2px !important;
}
</style>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const defaultCurrency = document.getElementById('default_currency');
    const currencyCheckboxes = Array.from(document.querySelectorAll('.currency-checkbox'));
    const commonCurrencies = ['ZAR', 'USD', 'EUR', 'GBP'];

    function syncCurrencies() {
        // Toggle card borders based on checkbox state
        currencyCheckboxes.forEach(checkbox => {
            const card = checkbox.closest('.currency-card');
            if (checkbox.checked) {
                card.classList.remove('border-light');
                card.classList.add('border-primary');
            } else {
                card.classList.add('border-light');
                card.classList.remove('border-primary');
            }
        });

        // Ensure default currency is always checked
        const defCheckbox = document.getElementById('currency_' + defaultCurrency.value);
        if (defCheckbox) {
            defCheckbox.checked = true;
            const card = defCheckbox.closest('.currency-card');
            card.classList.remove('border-light');
            card.classList.add('border-primary');
        }
    }

    defaultCurrency.addEventListener('change', syncCurrencies);
    document.getElementById('selectCommonCurrencies').addEventListener('click', function () {
        currencyCheckboxes.forEach(checkbox => checkbox.checked = commonCurrencies.includes(checkbox.value));
        syncCurrencies();
    });
    document.getElementById('clearCurrencies').addEventListener('click', function () {
        currencyCheckboxes.forEach(checkbox => checkbox.checked = false);
        syncCurrencies();
    });
    currencyCheckboxes.forEach(checkbox => checkbox.addEventListener('change', syncCurrencies));
    syncCurrencies();

    // Admissions enabled toggle
    const admissionEnabled = document.getElementById('admission_enabled');
    const admissionCard = document.getElementById('admission_card');
    admissionEnabled.addEventListener('change', function() {
        if (this.checked) {
            admissionCard.classList.remove('border-secondary');
            admissionCard.classList.add('border-success');
        } else {
            admissionCard.classList.add('border-secondary');
            admissionCard.classList.remove('border-success');
        }
    });

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
