@extends('layouts.app')

@section('title', 'Admission Status - JBI University')

@section('content')
<div class="container py-4">
    <!-- Welcome Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm text-white" style="background: linear-gradient(135deg, #1e3a8a 0%, #3b5bdb 100%); border-radius: 12px;">
                <div class="card-body p-4">
                    <h3 class="fw-bold mb-1">Welcome, {{ $student->first_name }}! 👋</h3>
                    <p class="mb-0 text-white-50">This is your student portal. Follow the steps below to complete your admission.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Dashboard Navigation Tabs -->
    <div class="row mb-4">
        <div class="col-12">
            <ul class="nav nav-pills" id="dashboard-tabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active px-4 py-2 fw-semibold me-2" id="main-dashboard-tab" data-bs-toggle="pill" data-bs-target="#main-dashboard" type="button" role="tab" aria-controls="main-dashboard" aria-selected="true" style="border-radius: 8px;">
                        <i class="bi bi-speedometer2 me-2"></i>Dashboard
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link px-4 py-2 fw-semibold me-2" id="my-applications-tab" data-bs-toggle="pill" data-bs-target="#my-applications" type="button" role="tab" aria-controls="my-applications" aria-selected="false" style="border-radius: 8px;">
                        <i class="bi bi-file-earmark-text me-2"></i>My Applications
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link px-4 py-2 fw-semibold" id="available-programmes-tab" data-bs-toggle="pill" data-bs-target="#available-programmes" type="button" role="tab" aria-controls="available-programmes" aria-selected="false" style="border-radius: 8px;">
                        <i class="bi bi-journal-bookmark-fill me-2"></i>Available Programmes
                    </button>
                </li>
            </ul>
        </div>
    </div>

    <!-- Tab Contents -->
    <div class="tab-content" id="dashboard-tabs-content">
        
        <!-- TAB 1: DASHBOARD -->
        <div class="tab-pane fade show active" id="main-dashboard" role="tabpanel" aria-labelledby="main-dashboard-tab">
            
            <!-- Application Progress Stepper -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm py-4 px-3">
                        <div class="d-flex justify-content-around text-center flex-wrap">
                            <!-- Step 1: Register -->
                            <div class="d-flex flex-column align-items-center mb-3 mb-md-0" style="min-width: 120px;">
                                <div class="rounded-circle d-flex align-items-center justify-content-center bg-success text-white mb-2" style="width: 40px; height: 40px;">
                                    <i class="bi bi-person-check fs-5"></i>
                                </div>
                                <span class="small fw-semibold text-success">Account Created</span>
                            </div>

                            <!-- Line -->
                            <div class="d-none d-md-block align-self-center border-top flex-grow-1 mx-2" style="max-width: 80px; height: 2px;"></div>

                            <!-- Step 2: Submit Profile -->
                            @php
                                $step2Success = isset($application);
                                $step2Active = !$step2Success;
                            @endphp
                            <div class="d-flex flex-column align-items-center mb-3 mb-md-0" style="min-width: 120px;">
                                <div class="rounded-circle d-flex align-items-center justify-content-center {{ $step2Success ? 'bg-success text-white' : 'bg-primary text-white' }} mb-2" style="width: 40px; height: 40px;">
                                    <i class="bi bi-file-earmark-text fs-5"></i>
                                </div>
                                <span class="small fw-semibold {{ $step2Success ? 'text-success' : 'text-primary' }}">Submit Profile</span>
                            </div>

                            <!-- Line -->
                            <div class="d-none d-md-block align-self-center border-top flex-grow-1 mx-2" style="max-width: 80px; height: 2px;"></div>

                            <!-- Step 3: Under Review (Academic Verification) -->
                            @php
                                $step3Success = isset($application) && $application->status !== 'pending';
                                $step3Active = isset($application) && $application->status === 'pending';
                            @endphp
                            <div class="d-flex flex-column align-items-center mb-3 mb-md-0" style="min-width: 120px;">
                                <div class="rounded-circle d-flex align-items-center justify-content-center {{ $step3Success ? 'bg-success text-white' : ($step3Active ? 'bg-primary text-white' : 'bg-light text-muted') }} mb-2" style="width: 40px; height: 40px;">
                                    <i class="bi bi-clock-history fs-5"></i>
                                </div>
                                <span class="small fw-semibold {{ $step3Success ? 'text-success' : ($step3Active ? 'text-primary' : 'text-muted') }}">Under Review</span>
                            </div>

                            <!-- Line -->
                            <div class="d-none d-md-block align-self-center border-top flex-grow-1 mx-2" style="max-width: 80px; height: 2px;"></div>

                            <!-- Step 4: Fee Payment -->
                            @php
                                $step4Success = isset($application) && ($application->payment_status === 'verified' || $application->status === 'admitted');
                                $step4Active = isset($application) && $application->status === 'approved' && in_array($application->payment_status, ['pending', 'rejected', 'uploaded']);
                            @endphp
                            <div class="d-flex flex-column align-items-center mb-3 mb-md-0" style="min-width: 120px;">
                                <div class="rounded-circle d-flex align-items-center justify-content-center {{ $step4Success ? 'bg-success text-white' : ($step4Active ? 'bg-primary text-white' : 'bg-light text-muted') }} mb-2" style="width: 40px; height: 40px;">
                                    <i class="bi bi-credit-card fs-5"></i>
                                </div>
                                <span class="small fw-semibold {{ $step4Success ? 'text-success' : ($step4Active ? 'text-primary' : 'text-muted') }}">Fee Payment</span>
                            </div>

                            <!-- Line -->
                            <div class="d-none d-md-block align-self-center border-top flex-grow-1 mx-2" style="max-width: 80px; height: 2px;"></div>

                            <!-- Step 5: Admitted -->
                            <div class="d-flex flex-column align-items-center" style="min-width: 120px;">
                                <div class="rounded-circle d-flex align-items-center justify-content-center bg-light text-muted mb-2" style="width: 40px; height: 40px;">
                                    <i class="bi bi-mortarboard fs-5"></i>
                                </div>
                                <span class="small fw-semibold text-muted">Fully Admitted</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dashboard Alerts / Notifications -->
            <div class="row justify-content-center">
                <div class="col-lg-12">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert" style="border-radius: 8px;">
                            <i class="bi bi-check-circle-fill me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert" style="border-radius: 8px;">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <strong>Please fix the errors below:</strong>
                            <ul class="mb-0 mt-1 small">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <!-- CASE 1: No Application Submitted (Submit Profile) -->
                    @if(!$application)
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-white border-bottom py-3">
                                <h5 class="mb-0 fw-bold text-primary">
                                    <i class="bi bi-file-earmark-plus me-2"></i>Admission Application Form
                                </h5>
                            </div>
                            <div class="card-body p-4">
                                <div class="alert alert-info py-2 px-3 mb-4 small" style="border-radius: 8px;">
                                    <i class="bi bi-info-circle-fill me-2"></i>
                                    Please fill out the personal details, emergency contacts, and academic choices below to submit your admission application.
                                </div>

                                <form action="{{ route('applications.store') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="type" value="student">

                                    <!-- Personal Details & Contacts -->
                                    <div class="row g-3 mb-4">
                                        <div class="col-12">
                                            <h6 class="text-uppercase text-primary fw-semibold mb-2" style="font-size: 0.75rem;"><i class="bi bi-person-badge me-2"></i>Personal & Contact Details</h6>
                                            <hr class="mt-0 mb-3">
                                        </div>
                                        <div class="col-md-6 col-sm-6">
                                            <label class="form-label small fw-medium">Full Name (ReadOnly)</label>
                                            <input type="hidden" name="first_name" value="{{ $student->first_name }}">
                                            <input type="hidden" name="last_name" value="{{ $student->last_name }}">
                                            <input type="text" class="form-control form-control-sm bg-light" value="{{ $student->full_name }}" readonly>
                                        </div>
                                        <div class="col-md-6 col-sm-6">
                                            <label class="form-label small fw-medium">Email Address (ReadOnly)</label>
                                            <input type="email" class="form-control form-control-sm bg-light" name="email" value="{{ $student->email }}" readonly required>
                                        </div>
                                        <div class="col-md-6 col-sm-6">
                                            <label class="form-label small fw-medium">Phone Number (ReadOnly)</label>
                                            <input type="text" class="form-control form-control-sm bg-light" name="phone" value="{{ $student->phone }}" readonly required>
                                        </div>
                                        <div class="col-md-6 col-sm-6">
                                            <label for="date_of_birth" class="form-label small fw-medium">Date of Birth <span class="text-danger">*</span></label>
                                            <input type="date" class="form-control form-control-sm @error('date_of_birth') is-invalid @enderror" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth', $student->date_of_birth ? $student->date_of_birth->format('Y-m-d') : '') }}" required>
                                            @error('date_of_birth')
                                                <div class="invalid-feedback small">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 col-sm-6">
                                            <label for="gender" class="form-label small fw-medium">Gender <span class="text-danger">*</span></label>
                                            <select class="form-select form-select-sm @error('gender') is-invalid @enderror" id="gender" name="gender" required>
                                                <option value="">Select Gender</option>
                                                <option value="male" {{ old('gender', $student->gender) === 'male' ? 'selected' : '' }}>Male</option>
                                                <option value="female" {{ old('gender', $student->gender) === 'female' ? 'selected' : '' }}>Female</option>
                                                <option value="other" {{ old('gender', $student->gender) === 'other' ? 'selected' : '' }}>Other</option>
                                            </select>
                                            @error('gender')
                                                <div class="invalid-feedback small">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 col-sm-6">
                                            <label for="profile_picture" class="form-label small fw-medium">Profile Picture (Optional)</label>
                                            <input type="file" class="form-control form-control-sm @error('profile_picture') is-invalid @enderror" id="profile_picture" name="profile_picture" accept="image/*" onchange="previewProfilePicture(this, '')">
                                            @error('profile_picture')
                                                <div class="invalid-feedback small">{{ $message }}</div>
                                            @enderror
                                            <!-- Profile Picture Preview Card -->
                                            <div id="profile-picture-preview-card" class="card mt-3 border d-none overflow-hidden" style="max-width: 140px; border-radius: 8px;">
                                                <div class="position-relative">
                                                    <img id="profile-picture-preview-img" src="#" alt="Profile Preview" class="img-fluid" style="width: 140px; height: 140px; object-fit: cover;">
                                                    <button type="button" class="btn btn-danger btn-xs position-absolute top-0 end-0 m-1 rounded-circle p-1 d-flex align-items-center justify-content-center" onclick="removeProfilePicture('')" style="width: 22px; height: 22px; line-height: 1; border: none;">
                                                        <i class="bi bi-x fs-6"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <label for="address" class="form-label small fw-medium">Home Address <span class="text-danger">*</span></label>
                                            <textarea class="form-control form-control-sm @error('address') is-invalid @enderror" id="address" name="address" rows="2" required placeholder="123 Main St, City, Country">{{ old('address', $student->address) }}</textarea>
                                            @error('address')
                                                <div class="invalid-feedback small">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-12 mt-3">
                                            <h6 class="text-uppercase text-secondary fw-semibold mb-2" style="font-size: 0.75rem;"><i class="bi bi-telephone-fill me-2"></i>Emergency Contact Details</h6>
                                            <hr class="mt-0 mb-3">
                                        </div>
                                        <div class="col-md-6 col-sm-6">
                                            <label for="emergency_contact_name" class="form-label small fw-medium">Contact Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control form-control-sm @error('emergency_contact_name') is-invalid @enderror" id="emergency_contact_name" name="emergency_contact_name" value="{{ old('emergency_contact_name', $student->emergency_contact) }}" required placeholder="Jane Doe">
                                            @error('emergency_contact_name')
                                                <div class="invalid-feedback small">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 col-sm-6">
                                            <label for="emergency_contact_phone" class="form-label small fw-medium">Contact Phone <span class="text-danger">*</span></label>
                                            <input type="tel" class="form-control form-control-sm @error('emergency_contact_phone') is-invalid @enderror" id="emergency_contact_phone" name="emergency_contact_phone" value="{{ old('emergency_contact_phone', $student->emergency_phone) }}" required placeholder="+1 (555) 000-0000">
                                            @error('emergency_contact_phone')
                                                <div class="invalid-feedback small">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Course Choices -->
                                    <div class="row mb-4">
                                        <div class="col-12">
                                            <h6 class="text-uppercase text-primary fw-semibold mb-2" style="font-size: 0.75rem;"><i class="bi bi-list-stars me-2"></i>Course/Program Choices (Select up to 6 alternatives)</h6>
                                            <hr class="mt-0 mb-3">
                                        </div>
                                        <div class="row g-3">
                                            <div class="col-md-4 col-sm-6">
                                                <label for="program_id_1" class="form-label small fw-medium">Choice 1 (Primary Choice) <span class="text-danger">*</span></label>
                                                <select class="form-select form-select-sm program-choice-select @error('program_id_1') is-invalid @enderror" id="program_id_1" name="program_id_1" required>
                                                    <option value="">Select Primary Program</option>
                                                    @foreach($programs as $program)
                                                        <option value="{{ $program->id }}" {{ old('program_id_1') == $program->id ? 'selected' : '' }}>
                                                            {{ $program->name }} @if($program->level) ({{ $program->level->name }}) @endif
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('program_id_1')
                                                    <div class="invalid-feedback small">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-4 col-sm-6">
                                                <label for="program_id_2" class="form-label small fw-medium">Choice 2 (Alternative)</label>
                                                <select class="form-select form-select-sm program-choice-select @error('program_id_2') is-invalid @enderror" id="program_id_2" name="program_id_2">
                                                    <option value="">Select Alternate Program 2</option>
                                                    @foreach($programs as $program)
                                                        <option value="{{ $program->id }}" {{ old('program_id_2') == $program->id ? 'selected' : '' }}>
                                                            {{ $program->name }} @if($program->level) ({{ $program->level->name }}) @endif
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-4 col-sm-6">
                                                <label for="program_id_3" class="form-label small fw-medium">Choice 3 (Alternative)</label>
                                                <select class="form-select form-select-sm program-choice-select @error('program_id_3') is-invalid @enderror" id="program_id_3" name="program_id_3">
                                                    <option value="">Select Alternate Program 3</option>
                                                    @foreach($programs as $program)
                                                        <option value="{{ $program->id }}" {{ old('program_id_3') == $program->id ? 'selected' : '' }}>
                                                            {{ $program->name }} @if($program->level) ({{ $program->level->name }}) @endif
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-4 col-sm-6">
                                                <label for="program_id_4" class="form-label small fw-medium">Choice 4 (Alternative)</label>
                                                <select class="form-select form-select-sm program-choice-select @error('program_id_4') is-invalid @enderror" id="program_id_4" name="program_id_4">
                                                    <option value="">Select Alternate Program 4</option>
                                                    @foreach($programs as $program)
                                                        <option value="{{ $program->id }}" {{ old('program_id_4') == $program->id ? 'selected' : '' }}>
                                                            {{ $program->name }} @if($program->level) ({{ $program->level->name }}) @endif
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-4 col-sm-6">
                                                <label for="program_id_5" class="form-label small fw-medium">Choice 5 (Alternative)</label>
                                                <select class="form-select form-select-sm program-choice-select @error('program_id_5') is-invalid @enderror" id="program_id_5" name="program_id_5">
                                                    <option value="">Select Alternate Program 5</option>
                                                    @foreach($programs as $program)
                                                        <option value="{{ $program->id }}" {{ old('program_id_5') == $program->id ? 'selected' : '' }}>
                                                            {{ $program->name }} @if($program->level) ({{ $program->level->name }}) @endif
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-4 col-sm-6">
                                                <label for="program_id_6" class="form-label small fw-medium">Choice 6 (Alternative)</label>
                                                <select class="form-select form-select-sm program-choice-select @error('program_id_6') is-invalid @enderror" id="program_id_6" name="program_id_6">
                                                    <option value="">Select Alternate Program 6</option>
                                                    @foreach($programs as $program)
                                                        <option value="{{ $program->id }}" {{ old('program_id_6') == $program->id ? 'selected' : '' }}>
                                                            {{ $program->name }} @if($program->level) ({{ $program->level->name }}) @endif
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Academic Background Details -->
                                    <div class="row g-3 mb-4">
                                        <div class="col-12">
                                            <h6 class="text-uppercase text-primary fw-semibold mb-2" style="font-size: 0.75rem;"><i class="bi bi-mortarboard-fill me-2"></i>Academic Profile Details</h6>
                                            <hr class="mt-0 mb-3">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="previous_school" class="form-label small fw-medium">Previous School/College <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control form-control-sm @error('previous_school') is-invalid @enderror" id="previous_school" name="previous_school" value="{{ old('previous_school') }}" required placeholder="e.g. Lincoln High School">
                                            @error('previous_school')
                                                <div class="invalid-feedback small">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label for="previous_qualification" class="form-label small fw-medium">Previous Qualification <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control form-control-sm @error('previous_qualification') is-invalid @enderror" id="previous_qualification" name="previous_qualification" value="{{ old('previous_qualification') }}" required placeholder="e.g. High School Diploma">
                                            @error('previous_qualification')
                                                <div class="invalid-feedback small">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label for="previous_gpa" class="form-label small fw-medium">Previous GPA (0-4 Scale) <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control form-control-sm @error('previous_gpa') is-invalid @enderror" id="previous_gpa" name="previous_gpa" value="{{ old('previous_gpa') }}" min="0" max="4" step="0.01" required placeholder="3.50">
                                            @error('previous_gpa')
                                                <div class="invalid-feedback small">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-12">
                                            <label for="documents" class="form-label small fw-medium">Supporting Documents (Transcripts, Certificates) <span class="text-danger">*</span></label>
                                            <input type="file" class="form-control form-control-sm @error('documents') is-invalid @enderror" id="documents" name="documents[]" multiple required accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" onchange="previewDocuments(this, '')">
                                            <div class="form-text small">Upload multiple files. Accepted: PDF, DOC, DOCX, JPG, PNG. Max: 5MB per file.</div>
                                            @error('documents')
                                                <div class="invalid-feedback small">{{ $message }}</div>
                                            @enderror
                                            <!-- Previews -->
                                            <div id="documents-previews-container" class="row g-2 mt-3 d-none"></div>
                                        </div>
                                        <div class="col-12">
                                            <label for="application_notes" class="form-label small fw-medium">Personal Statement / Cover Note</label>
                                            <textarea class="form-control form-control-sm" id="application_notes" name="application_notes" rows="4" placeholder="Briefly describe your objectives..."></textarea>
                                        </div>
                                    </div>

                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-primary" style="border-radius: 8px;">
                                            <i class="bi bi-send-fill me-2"></i>Submit Admission Application
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif

                    <!-- CASE 2: Application Submitted, Under Review (Academic Verification) -->
                    @if(isset($application) && $application->status === 'pending' && $application->payment_status === 'pending')
                        <div class="card border-0 shadow-sm mb-4 text-center py-5" style="border-radius: 12px;">
                            <div class="card-body">
                                <div class="mb-4">
                                    <span class="spinner-border text-primary" style="width: 3.5rem; height: 3.5rem;" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </span>
                                </div>
                                <h4 class="fw-bold mb-2 text-dark">Step 3: Application Under Review</h4>
                                <p class="text-muted mx-auto mb-4" style="max-width: 520px;">
                                    Your admission request <strong>#{{ $application->application_number }}</strong> has been submitted successfully. The academic admissions board is currently verifying your profile and credentials. Once approved, you will proceed to the Admission Fee Payment step.
                                </p>
                                <div class="d-inline-flex flex-column align-items-start bg-light p-3 text-start border" style="border-radius: 10px; width: 100%; max-width: 480px;">
                                    <div class="small mb-1.5 text-dark"><strong>Submitted On:</strong> {{ $application->created_at->format('M d, Y h:i A') }}</div>
                                    <div class="small mb-1.5 text-dark"><strong>Primary Program:</strong> {{ $application->programRecord->name ?? $application->program }}</div>
                                    <div class="small text-dark"><strong>Academic Status:</strong> <span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split me-1"></i>Under Admissions Board Review</span></div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- CASE 3: Application Approved, Fee Payment Portal -->
                    @if(isset($application) && $application->status === 'approved' && in_array($application->payment_status, ['pending', 'rejected']))
                        @php
                            $levelName = strtolower($application->programRecord->level->name ?? '');
                            if (str_contains($levelName, 'bachelor') || str_contains($levelName, 'degree')) {
                                $feeAmount = 150;
                            } elseif (str_contains($levelName, 'master') || str_contains($levelName, 'doctor') || str_contains($levelName, 'phd')) {
                                $feeAmount = 250;
                            } else {
                                $feeAmount = 100;
                            }
                        @endphp
                        <div class="card border-0 shadow-sm mb-4 overflow-hidden" style="border-radius: 12px;">
                            <div class="card-header text-white py-3" style="background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%);">
                                <h5 class="mb-0 fw-bold"><i class="bi bi-credit-card me-2"></i>Step 4: Admission Fee Payment</h5>
                            </div>
                            <div class="card-body p-4">
                                <div class="alert alert-success py-2 px-3 mb-4 small" style="border-radius: 8px;">
                                    <i class="bi bi-check-circle-fill me-2"></i>
                                    <strong>Congratulations!</strong> Your academic profile has been verified and approved. Please pay the admission fee and upload the payment receipt below.
                                </div>

                                <!-- Course Brief Info & Fee Card -->
                                <div class="card border-0 bg-light mb-4" style="border-radius: 10px;">
                                    <div class="card-body p-4">
                                        <div class="row align-items-center">
                                            <div class="col-md-7 mb-3 mb-md-0">
                                                <h6 class="text-uppercase text-primary fw-semibold mb-2" style="font-size: 0.75rem; letter-spacing: 0.5px;">Applied Program Information</h6>
                                                <h4 class="fw-bold text-dark mb-1">{{ $application->programRecord->name ?? $application->program }}</h4>
                                                <p class="text-muted small mb-0">
                                                    <span class="me-3"><strong>Level:</strong> {{ $application->programRecord->level->name ?? 'N/A' }}</span>
                                                    <span class="me-3"><strong>Department:</strong> {{ $application->programRecord->department->name ?? 'N/A' }}</span>
                                                    <span><strong>Code:</strong> {{ $application->programRecord->code ?? 'N/A' }}</span>
                                                </p>
                                            </div>
                                            <div class="col-md-5 text-md-end border-start-md">
                                                <h6 class="text-uppercase text-secondary fw-semibold mb-1" style="font-size: 0.75rem; letter-spacing: 0.5px;">Admission Fee Amount</h6>
                                                <h2 class="fw-bold text-success mb-0">${{ number_format($feeAmount, 2) }} <span class="fs-6 fw-normal text-muted">USD</span></h2>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Payment Reference Indicator -->
                                <div class="alert alert-light border d-flex align-items-center justify-content-between p-3 mb-4 flex-wrap" style="border-radius: 8px;">
                                    <div class="d-flex align-items-center py-1">
                                        <i class="bi bi-qr-code-scan text-primary fs-3 me-3"></i>
                                        <div>
                                            <span class="text-muted small d-block">Your Payment Reference</span>
                                            <strong class="text-dark fs-5">{{ $application->payment_ref ?? 'None Generated' }}</strong>
                                        </div>
                                    </div>
                                    <div class="py-1">
                                        <form action="{{ route('applications.regenerate-payment-ref', $application) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-primary fw-medium px-3" style="border-radius: 6px;">
                                                <i class="bi bi-arrow-clockwise me-1"></i>Regenerate Reference
                                            </button>
                                        </form>
                                    </div>
                                </div>

                                <!-- Channels of Payment -->
                                <h6 class="fw-bold text-dark mb-3"><i class="bi bi-wallet2 me-2"></i>Select Payment Channel</h6>
                                <div class="row g-4 mb-4">
                                    <div class="col-md-6">
                                        <div class="nav flex-column nav-pills border p-2 h-100" id="v-pills-tab" role="tablist" aria-orientation="vertical" style="border-radius: 8px; background-color: #fafbfc;">
                                            <button class="nav-link active text-start py-2.5 px-3 mb-2 small fw-medium" id="v-pills-bank-tab" data-bs-toggle="pill" data-bs-target="#v-pills-bank" type="button" role="tab" aria-controls="v-pills-bank" aria-selected="true" style="border-radius: 6px;">
                                                <i class="bi bi-bank me-2"></i>Bank Transfer / Wire Transfer
                                            </button>
                                            <button class="nav-link text-start py-2.5 px-3 mb-2 small fw-medium" id="v-pills-card-tab" data-bs-toggle="pill" data-bs-target="#v-pills-card" type="button" role="tab" aria-controls="v-pills-card" aria-selected="false" style="border-radius: 6px;">
                                                <i class="bi bi-credit-card me-2"></i>Credit / Debit Card (Online)
                                            </button>
                                            <button class="nav-link text-start py-2.5 px-3 mb-2 small fw-medium" id="v-pills-paypal-tab" data-bs-toggle="pill" data-bs-target="#v-pills-paypal" type="button" role="tab" aria-controls="v-pills-paypal" aria-selected="false" style="border-radius: 6px;">
                                                <i class="bi bi-paypal me-2"></i>PayPal
                                            </button>
                                            <button class="nav-link text-start py-2.5 px-3 small fw-medium" id="v-pills-mobile-tab" data-bs-toggle="pill" data-bs-target="#v-pills-mobile" type="button" role="tab" aria-controls="v-pills-mobile" aria-selected="false" style="border-radius: 6px;">
                                                <i class="bi bi-phone me-2"></i>Mobile Payment / Wallet
                                            </button>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="tab-content border p-3 bg-white h-100" id="v-pills-tabContent" style="border-radius: 8px; min-height: 180px;">
                                            <div class="tab-pane fade show active small" id="v-pills-bank" role="tabpanel" aria-labelledby="v-pills-bank-tab">
                                                <h6 class="fw-bold text-primary mb-2">Direct Bank Transfer</h6>
                                                <p class="text-muted mb-3" style="font-size: 0.8rem;">Transfer the exact amount to JBI's bank account. Input reference code in payment remarks.</p>
                                                <div class="mb-2"><strong>Bank Name:</strong> JBI National Bank</div>
                                                <div class="mb-2"><strong>Account Name:</strong> JBI Admissions Department</div>
                                                <div class="mb-2"><strong>Account Number:</strong> 1234-5678-9012</div>
                                                <div class="mb-0"><strong>Required Remarks:</strong> <code class="bg-light text-danger px-1 py-0.5 rounded">{{ $application->payment_ref }}</code></div>
                                            </div>

                                            <div class="tab-pane fade small" id="v-pills-card" role="tabpanel" aria-labelledby="v-pills-card-tab">
                                                <h6 class="fw-bold text-primary mb-2">Online Credit Card</h6>
                                                <p class="text-muted mb-3" style="font-size: 0.8rem;">Pay securely online using your Visa, Mastercard, or American Express.</p>
                                                <div class="d-grid gap-2 mt-4">
                                                    <button type="button" class="btn btn-sm btn-primary py-2" onclick="alert('Online credit card payment channel simulated successfully. Please upload the transaction confirmation receipt below.')" style="border-radius: 6px;">
                                                        <i class="bi bi-shield-check me-2"></i>Pay ${{ number_format($feeAmount, 2) }} Online
                                                    </button>
                                                </div>
                                            </div>

                                            <div class="tab-pane fade small" id="v-pills-paypal" role="tabpanel" aria-labelledby="v-pills-paypal-tab">
                                                <h6 class="fw-bold text-primary mb-2">PayPal Checkout</h6>
                                                <p class="text-muted mb-3" style="font-size: 0.8rem;">Sign in to your PayPal account to make a secure digital wallet payment.</p>
                                                <div class="d-grid gap-2 mt-4">
                                                    <button type="button" class="btn btn-sm btn-warning py-2" onclick="alert('PayPal checkout simulated successfully. Please upload the transaction receipt below.')" style="border-radius: 6px; background-color: #ffc439;">
                                                        <i class="bi bi-paypal me-2"></i>Pay with PayPal
                                                    </button>
                                                </div>
                                            </div>

                                            <div class="tab-pane fade small" id="v-pills-mobile" role="tabpanel" aria-labelledby="v-pills-mobile-tab">
                                                <h6 class="fw-bold text-primary mb-2">Mobile Payments</h6>
                                                <p class="text-muted mb-3" style="font-size: 0.8rem;">Scan QR code or use push notification to pay using Apple Pay, Google Pay, or local mobile wallets.</p>
                                                <div class="d-flex align-items-center justify-content-center p-3">
                                                    <span class="badge bg-secondary p-2"><i class="bi bi-phone me-1"></i>Google Pay / Apple Pay Enabled</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Receipt Upload Form -->
                                <div class="border-top pt-4">
                                    <h6 class="fw-bold text-dark mb-3"><i class="bi bi-cloud-arrow-up me-2"></i>Upload Payment Confirmation Receipt</h6>
                                    @if($application->payment_status === 'rejected')
                                        <div class="alert alert-danger py-2 px-3 small mb-3" style="border-radius: 6px;">
                                            <i class="bi bi-exclamation-triangle-fill me-1"></i>
                                            Your previous payment proof was rejected. Reason: {{ $application->payment_notes }}
                                        </div>
                                    @endif

                                    <form action="{{ route('applications.store-payment', $application->application_number) }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <div class="row g-3">
                                            <div class="col-md-9">
                                                <input type="file" class="form-control form-control-sm @error('payment_proof') is-invalid @enderror" id="payment_proof" name="payment_proof" required accept=".pdf,.jpg,.jpeg,.png">
                                                @error('payment_proof')
                                                    <div class="invalid-feedback small">{{ $message }}</div>
                                                @enderror
                                                <div class="form-text small">Upload PDF, transaction receipt image, or deposit slip. Max size: 5MB.</div>
                                            </div>
                                            <div class="col-md-3 d-grid">
                                                <button type="submit" class="btn btn-sm btn-success" style="border-radius: 6px;">
                                                    <i class="bi bi-upload me-2"></i>Submit Receipt
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- CASE 4: Payment Uploaded, Payment Verification Pending -->
                    @if(isset($application) && $application->status === 'approved' && $application->payment_status === 'uploaded')
                        <div class="card border-0 shadow-sm mb-4 text-center py-5" style="border-radius: 12px;">
                            <div class="card-body">
                                <div class="mb-4">
                                    <span class="spinner-border text-success" style="width: 3.5rem; height: 3.5rem;" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </span>
                                </div>
                                <h4 class="fw-bold text-success mb-2">Payment Receipt Uploaded</h4>
                                <p class="text-muted mx-auto mb-4" style="max-width: 520px;">
                                    Your payment receipt has been submitted successfully for application <strong>#{{ $application->application_number }}</strong>. The admissions department is currently verifying your payment. You will receive an email once your portal is fully activated.
                                </p>
                                <div class="d-inline-flex flex-column align-items-start bg-light p-3 text-start border" style="border-radius: 10px; width: 100%; max-width: 480px;">
                                    <div class="small mb-1.5 text-dark"><strong>Primary Program:</strong> {{ $application->programRecord->name ?? $application->program }}</div>
                                    <div class="small mb-1.5 text-dark"><strong>Uploaded On:</strong> {{ $application->payment_uploaded_at ? $application->payment_uploaded_at->format('M d, Y h:i A') : 'N/A' }}</div>
                                    <div class="small text-dark"><strong>Verification Status:</strong> <span class="badge bg-info"><i class="bi bi-hourglass-split me-1"></i>Receipt Verification Pending</span></div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- CASE 5: Application Rejected -->
                    @if(isset($application) && $application->status === 'rejected')
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-danger text-white py-3">
                                <h5 class="mb-0 fw-bold">
                                    <i class="bi bi-exclamation-octagon-fill me-2"></i>Application Disapproved
                                </h5>
                            </div>
                            <div class="card-body p-4 text-center">
                                <i class="bi bi-x-circle text-danger" style="font-size: 3.5rem;"></i>
                                <h4 class="fw-bold mt-3 mb-2">Admission Request Declined</h4>
                                <p class="text-muted mx-auto" style="max-width: 480px;">
                                    We regret to inform you that your admission application <strong>#{{ $application->application_number }}</strong> was rejected.
                                </p>
                                
                                @if($application->review_notes)
                                    <div class="bg-light border text-start p-3 mx-auto mb-4" style="border-radius: 8px; max-width: 500px;">
                                        <strong>Admissions Office Notes:</strong>
                                        <p class="mb-0 text-muted small mt-1">{{ $application->review_notes }}</p>
                                    </div>
                                @endif

                                <div class="alert alert-info d-inline-block small" style="border-radius: 8px;">
                                    <i class="bi bi-info-circle me-1"></i>You can submit a new application with corrected academic details below.
                                </div>

                                <div class="mt-2">
                                    <a href="{{ route('student.dashboard') }}" class="btn btn-outline-primary" onclick="event.preventDefault(); document.getElementById('reApplyForm').style.display='block'; this.style.display='none';">
                                        <i class="bi bi-arrow-clockwise me-2"></i>Create New Application
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Hidden re-apply form -->
                        <div id="reApplyForm" class="card border-0 shadow-sm mb-4" style="display: none;">
                            <div class="card-header bg-white border-bottom py-3">
                                <h5 class="mb-0 fw-bold text-primary">
                                    <i class="bi bi-file-earmark-plus me-2"></i>New Admission Application
                                </h5>
                            </div>
                            <div class="card-body p-4">
                                <form action="{{ route('applications.store') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="type" value="student">

                                    <div class="row g-3 mb-4">
                                        <div class="col-12">
                                            <h6 class="text-uppercase text-primary fw-semibold mb-2" style="font-size: 0.75rem;"><i class="bi bi-person-badge me-2"></i>Personal & Contact Details</h6>
                                            <hr class="mt-0 mb-3">
                                        </div>
                                        <div class="col-md-6 col-sm-6">
                                            <label class="form-label small fw-medium">Full Name (ReadOnly)</label>
                                            <input type="hidden" name="first_name" value="{{ $student->first_name }}">
                                            <input type="hidden" name="last_name" value="{{ $student->last_name }}">
                                            <input type="text" class="form-control form-control-sm bg-light" value="{{ $student->full_name }}" readonly>
                                        </div>
                                        <div class="col-md-6 col-sm-6">
                                            <label class="form-label small fw-medium">Email Address (ReadOnly)</label>
                                            <input type="email" class="form-control form-control-sm bg-light" name="email" value="{{ $student->email }}" readonly required>
                                        </div>
                                        <div class="col-md-6 col-sm-6">
                                            <label class="form-label small fw-medium">Phone Number (ReadOnly)</label>
                                            <input type="text" class="form-control form-control-sm bg-light" name="phone" value="{{ $student->phone }}" readonly required>
                                        </div>
                                        <div class="col-md-6 col-sm-6">
                                            <label for="date_of_birth_re" class="form-label small fw-medium">Date of Birth <span class="text-danger">*</span></label>
                                            <input type="date" class="form-control form-control-sm" id="date_of_birth_re" name="date_of_birth" value="{{ old('date_of_birth', $student->date_of_birth ? $student->date_of_birth->format('Y-m-d') : '') }}" required>
                                        </div>
                                        <div class="col-md-6 col-sm-6">
                                            <label for="gender_re" class="form-label small fw-medium">Gender <span class="text-danger">*</span></label>
                                            <select class="form-select form-select-sm" id="gender_re" name="gender" required>
                                                <option value="">Select Gender</option>
                                                <option value="male" {{ old('gender', $student->gender) === 'male' ? 'selected' : '' }}>Male</option>
                                                <option value="female" {{ old('gender', $student->gender) === 'female' ? 'selected' : '' }}>Female</option>
                                                <option value="other" {{ old('gender', $student->gender) === 'other' ? 'selected' : '' }}>Other</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 col-sm-6">
                                            <label for="profile_picture_re" class="form-label small fw-medium">Profile Picture (Optional)</label>
                                            <input type="file" class="form-control form-control-sm" id="profile_picture_re" name="profile_picture" accept="image/*" onchange="previewProfilePicture(this, '_re')">
                                            <div id="profile-picture-preview-card_re" class="card mt-3 border d-none overflow-hidden" style="max-width: 140px; border-radius: 8px;">
                                                <div class="position-relative">
                                                    <img id="profile-picture-preview-img_re" src="#" alt="Profile Preview" class="img-fluid" style="width: 140px; height: 140px; object-fit: cover;">
                                                    <button type="button" class="btn btn-danger btn-xs position-absolute top-0 end-0 m-1 rounded-circle p-1 d-flex align-items-center justify-content-center" onclick="removeProfilePicture('_re')" style="width: 22px; height: 22px; line-height: 1; border: none;">
                                                        <i class="bi bi-x fs-6"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <label for="address_re" class="form-label small fw-medium">Home Address <span class="text-danger">*</span></label>
                                            <textarea class="form-control form-control-sm" id="address_re" name="address" rows="2" required placeholder="123 Main St, City, Country">{{ old('address', $student->address) }}</textarea>
                                        </div>

                                        <div class="col-12 mt-3">
                                            <h6 class="text-uppercase text-secondary fw-semibold mb-2" style="font-size: 0.75rem;"><i class="bi bi-telephone-fill me-2"></i>Emergency Contact Details</h6>
                                            <hr class="mt-0 mb-3">
                                        </div>
                                        <div class="col-md-6 col-sm-6">
                                            <label for="emergency_contact_name_re" class="form-label small fw-medium">Contact Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control form-control-sm" id="emergency_contact_name_re" name="emergency_contact_name" value="{{ old('emergency_contact_name', $student->emergency_contact) }}" required placeholder="Jane Doe">
                                        </div>
                                        <div class="col-md-6 col-sm-6">
                                            <label for="emergency_contact_phone_re" class="form-label small fw-medium">Contact Phone <span class="text-danger">*</span></label>
                                            <input type="tel" class="form-control form-control-sm" id="emergency_contact_phone_re" name="emergency_contact_phone" value="{{ old('emergency_contact_phone', $student->emergency_phone) }}" required placeholder="+1 (555) 000-0000">
                                        </div>
                                    </div>

                                    <!-- Course Choices (Re-apply) -->
                                    <div class="row mb-4">
                                        <div class="col-12">
                                            <h6 class="text-uppercase text-primary fw-semibold mb-2" style="font-size: 0.75rem;"><i class="bi bi-list-stars me-2"></i>Course/Program Choices (Select up to 6 alternatives)</h6>
                                            <hr class="mt-0 mb-3">
                                        </div>
                                        <div class="row g-3">
                                            <div class="col-md-4 col-sm-6">
                                                <label for="program_id_1_re" class="form-label small fw-medium">Choice 1 <span class="text-danger">*</span></label>
                                                <select class="form-select form-select-sm program-choice-select" id="program_id_1_re" name="program_id_1" required>
                                                    <option value="">Select Primary Program</option>
                                                    @foreach($programs as $program)
                                                        <option value="{{ $program->id }}">
                                                            {{ $program->name }} @if($program->level) ({{ $program->level->name }}) @endif
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-4 col-sm-6">
                                                <label for="program_id_2_re" class="form-label small fw-medium">Choice 2</label>
                                                <select class="form-select form-select-sm program-choice-select" id="program_id_2_re" name="program_id_2">
                                                    <option value="">Select Alternate Program 2</option>
                                                    @foreach($programs as $program)
                                                        <option value="{{ $program->id }}">
                                                            {{ $program->name }} @if($program->level) ({{ $program->level->name }}) @endif
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-4 col-sm-6">
                                                <label for="program_id_3_re" class="form-label small fw-medium">Choice 3</label>
                                                <select class="form-select form-select-sm program-choice-select" id="program_id_3_re" name="program_id_3">
                                                    <option value="">Select Alternate Program 3</option>
                                                    @foreach($programs as $program)
                                                        <option value="{{ $program->id }}">
                                                            {{ $program->name }} @if($program->level) ({{ $program->level->name }}) @endif
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-4 col-sm-6">
                                                <label for="program_id_4_re" class="form-label small fw-medium">Choice 4</label>
                                                <select class="form-select form-select-sm program-choice-select" id="program_id_4_re" name="program_id_4">
                                                    <option value="">Select Alternate Program 4</option>
                                                    @foreach($programs as $program)
                                                        <option value="{{ $program->id }}">
                                                            {{ $program->name }} @if($program->level) ({{ $program->level->name }}) @endif
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-4 col-sm-6">
                                                <label for="program_id_5_re" class="form-label small fw-medium">Choice 5</label>
                                                <select class="form-select form-select-sm program-choice-select" id="program_id_5_re" name="program_id_5">
                                                    <option value="">Select Alternate Program 5</option>
                                                    @foreach($programs as $program)
                                                        <option value="{{ $program->id }}">
                                                            {{ $program->name }} @if($program->level) ({{ $program->level->name }}) @endif
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-4 col-sm-6">
                                                <label for="program_id_6_re" class="form-label small fw-medium">Choice 6</label>
                                                <select class="form-select form-select-sm program-choice-select" id="program_id_6_re" name="program_id_6">
                                                    <option value="">Select Alternate Program 6</option>
                                                    @foreach($programs as $program)
                                                        <option value="{{ $program->id }}">
                                                            {{ $program->name }} @if($program->level) ({{ $program->level->name }}) @endif
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Academic Info (Re-apply) -->
                                    <div class="row g-3 mb-4">
                                        <div class="col-12">
                                            <h6 class="text-uppercase text-primary fw-semibold mb-2" style="font-size: 0.75rem;"><i class="bi bi-mortarboard-fill me-2"></i>Academic Profile Details</h6>
                                            <hr class="mt-0 mb-3">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="previous_school_re" class="form-label small fw-medium">Previous School/College <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control form-control-sm" id="previous_school_re" name="previous_school" required placeholder="e.g. Lincoln High School">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="previous_qualification_re" class="form-label small fw-medium">Previous Qualification <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control form-control-sm" id="previous_qualification_re" name="previous_qualification" required placeholder="e.g. High School Diploma">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="previous_gpa_re" class="form-label small fw-medium">Previous GPA (0-4 Scale) <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control form-control-sm" id="previous_gpa_re" name="previous_gpa" min="0" max="4" step="0.01" required placeholder="3.50">
                                        </div>
                                        <div class="col-12">
                                            <label for="documents_re" class="form-label small fw-medium">Supporting Documents (Transcripts, Certificates) <span class="text-danger">*</span></label>
                                            <input type="file" class="form-control form-control-sm" id="documents_re" name="documents[]" multiple required accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" onchange="previewDocuments(this, '_re')">
                                            <div class="form-text small">Upload multiple files. Accepted: PDF, DOC, DOCX, JPG, PNG. Max: 5MB per file.</div>
                                            <div id="documents-previews-container_re" class="row g-2 mt-3 d-none"></div>
                                        </div>
                                        <div class="col-12">
                                            <label for="application_notes_re" class="form-label small fw-medium">Personal Statement / Cover Note</label>
                                            <textarea class="form-control form-control-sm" id="application_notes_re" name="application_notes" rows="4" placeholder="Briefly describe your objectives..."></textarea>
                                        </div>
                                    </div>

                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-primary" style="border-radius: 8px;">
                                            <i class="bi bi-send-fill me-2"></i>Submit Admission Application
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- TAB 2: MY APPLICATIONS -->
        <div class="tab-pane fade" id="my-applications" role="tabpanel" aria-labelledby="my-applications-tab">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-file-earmark-person me-2"></i>Submitted Academic Profiles</h5>
                </div>
                <div class="card-body p-4">
                    @if($application)
                        <div class="row g-4">
                            <!-- Basic details list -->
                            <div class="col-md-8">
                                <div class="table-responsive">
                                    <table class="table table-sm table-borderless align-middle mb-0">
                                        <tbody>
                                            <tr>
                                                <td class="fw-semibold text-muted" style="width: 200px;">Application Reference:</td>
                                                <td class="text-dark fw-bold">#{{ $application->application_number }}</td>
                                            </tr>
                                            <tr>
                                                <td class="fw-semibold text-muted">Submitted Date:</td>
                                                <td class="text-dark">{{ $application->created_at->format('M d, Y h:i A') }}</td>
                                            </tr>
                                            <tr>
                                                <td class="fw-semibold text-muted">Status:</td>
                                                <td>
                                                    @if($application->status === 'pending')
                                                        <span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split me-1"></i>Under Review (Academic Verification)</span>
                                                    @elseif($application->status === 'approved' && in_array($application->payment_status, ['pending', 'rejected']))
                                                        <span class="badge bg-info"><i class="bi bi-credit-card me-1"></i>Payment Pending</span>
                                                    @elseif($application->status === 'approved' && $application->payment_status === 'uploaded')
                                                        <span class="badge bg-warning text-dark"><i class="bi bi-clock me-1"></i>Payment Verification Pending</span>
                                                    @elseif($application->status === 'admitted')
                                                        <span class="badge bg-success"><i class="bi bi-check-all me-1"></i>Fully Admitted</span>
                                                    @elseif($application->status === 'rejected')
                                                        <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>Declined / Disapproved</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="fw-semibold text-muted">Previous School:</td>
                                                <td class="text-dark">{{ $application->previous_school }}</td>
                                            </tr>
                                            <tr>
                                                <td class="fw-semibold text-muted">Previous GPA:</td>
                                                <td class="text-dark">{{ number_format($application->previous_gpa, 2) }}</td>
                                            </tr>
                                            <tr>
                                                <td class="fw-semibold text-muted">Date of Birth:</td>
                                                <td class="text-dark">{{ $application->date_of_birth ? $application->date_of_birth->format('M d, Y') : 'Not Specified' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="fw-semibold text-muted">Gender:</td>
                                                <td class="text-dark">{{ ucfirst($application->gender) }}</td>
                                            </tr>
                                            <tr>
                                                <td class="fw-semibold text-muted">Home Address:</td>
                                                <td class="text-dark">{{ $application->address }}</td>
                                            </tr>
                                            <tr>
                                                <td class="fw-semibold text-muted">Emergency Contact:</td>
                                                <td class="text-dark">{{ $student->emergency_contact ?? 'N/A' }} ({{ $student->emergency_phone ?? 'N/A' }})</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Profile Image Preview Card -->
                            <div class="col-md-4 text-center border-start">
                                <h6 class="fw-bold text-muted mb-2">Profile Photo</h6>
                                @if($student->profile_picture)
                                    <div class="d-inline-block p-1 border rounded bg-white shadow-sm mb-3">
                                        <img src="{{ $student->profile_picture_url }}" alt="Student Picture" class="img-fluid" style="max-width: 150px; max-height: 150px; object-fit: cover; border-radius: 6px;">
                                    </div>
                                @else
                                    <div class="d-inline-flex align-items-center justify-content-center bg-light text-secondary border rounded" style="width: 130px; height: 130px;">
                                        <i class="bi bi-person-fill-exclamation fs-1"></i>
                                    </div>
                                    <p class="text-muted small mt-2">No photo uploaded.</p>
                                @endif
                            </div>
                        </div>

                        <!-- Chosen Program Alternatives -->
                        <div class="row mt-4 pt-3 border-top">
                            <div class="col-12">
                                <h6 class="fw-bold text-primary mb-3"><i class="bi bi-list-ol me-2"></i>Selected Course Priorities</h6>
                                <div class="list-group list-group-flush border rounded">
                                    <div class="list-group-item d-flex justify-content-between align-items-center bg-light">
                                        <span><strong>Choice 1 (Primary):</strong> {{ $application->programRecord->name ?? $application->program }}</span>
                                        <span class="badge bg-primary rounded-pill">1st Priority</span>
                                    </div>
                                    @foreach($programChoices as $choice)
                                        @if($choice->id !== $application->program_id)
                                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                                <span><strong>Choice {{ $loop->iteration }}:</strong> {{ $choice->name }} @if($choice->level) ({{ $choice->level->name }}) @endif</span>
                                                <span class="badge bg-secondary rounded-pill">{{ $loop->iteration }}nd Priority</span>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Uploaded Documents Preview -->
                        <div class="row mt-4 pt-3 border-top">
                            <div class="col-12">
                                <h6 class="fw-bold text-primary mb-3"><i class="bi bi-folder-fill me-2"></i>Uploaded Supporting Documents</h6>
                                @if(is_array($application->documents) && count($application->documents) > 0)
                                    <div class="row g-3">
                                        @foreach($application->documents as $doc)
                                            @php
                                                $fileName = basename($doc);
                                                $isImage = in_array(strtolower(pathinfo($doc, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif']);
                                            @endphp
                                            <div class="col-sm-6 col-md-4 col-lg-3">
                                                <div class="card h-100 border shadow-sm overflow-hidden" style="border-radius: 8px;">
                                                    @if($isImage)
                                                        <img src="{{ Storage::url($doc) }}" class="card-img-top" style="height: 110px; object-fit: cover;">
                                                    @else
                                                        <div class="card-img-top d-flex align-items-center justify-content-center bg-light text-secondary" style="height: 110px;">
                                                            <i class="bi bi-file-earmark-pdf fs-1"></i>
                                                        </div>
                                                    @endif
                                                    <div class="card-body p-2 d-flex flex-column justify-content-between">
                                                        <span class="text-truncate d-block small fw-semibold text-dark mb-2" title="{{ $fileName }}">{{ $fileName }}</span>
                                                        <a href="{{ Storage::url($doc) }}" target="_blank" class="btn btn-xs btn-primary w-100 text-center py-1 mt-auto" style="font-size: 0.75rem; border-radius: 4px;">
                                                            <i class="bi bi-eye me-1"></i>Preview / View
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-muted small">No documents uploaded.</p>
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-file-earmark-x text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-2 mb-0">No active application profiles found.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- TAB 3: AVAILABLE PROGRAMMES -->
        <div class="tab-pane fade" id="available-programmes" role="tabpanel" aria-labelledby="available-programmes-tab">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-journal-bookmark-fill me-2"></i>Academic Programmes Directory</h5>
                </div>
                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped align-middle small">
                            <thead class="table-light">
                                <tr>
                                    <th>Code</th>
                                    <th>Programme Name</th>
                                    <th>Level</th>
                                    <th>Department</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($programs as $prog)
                                    <tr>
                                        <td><code class="fw-bold bg-light px-1.5 py-0.5 rounded text-dark">{{ $prog->code ?? 'N/A' }}</code></td>
                                        <td class="fw-semibold text-dark">{{ $prog->name }}</td>
                                        <td>{{ $prog->level->name ?? 'N/A' }}</td>
                                        <td>{{ $prog->department->name ?? 'N/A' }}</td>
                                        <td class="text-end">
                                            @if(!$application)
                                                <button type="button" class="btn btn-xs btn-primary px-3 py-1" onclick="switchToDashboardTab()" style="font-size: 0.75rem; border-radius: 5px;">
                                                    <i class="bi bi-arrow-right-circle me-1"></i>Apply
                                                </button>
                                            @else
                                                <span class="text-muted small">Application active</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
// Switch active tab back to main dashboard
function switchToDashboardTab() {
    const mainTab = new bootstrap.Tab(document.getElementById('main-dashboard-tab'));
    mainTab.show();
}

// Prevent duplicate program selections in choice dropdowns
document.addEventListener('DOMContentLoaded', function() {
    const selectClasses = ['.program-choice-select'];
    
    selectClasses.forEach(selector => {
        const selects = document.querySelectorAll(selector);
        
        function enforceUniqueness() {
            const selectedValues = Array.from(selects)
                .map(s => s.value)
                .filter(val => val !== '');
            
            selects.forEach(s => {
                const currentValue = s.value;
                Array.from(s.options).forEach(option => {
                    if (option.value === '') return;
                    if (selectedValues.includes(option.value) && option.value !== currentValue) {
                        option.disabled = true;
                    } else {
                        option.disabled = false;
                    }
                });
            });
        }

        selects.forEach(select => {
            select.addEventListener('change', enforceUniqueness);
        });
        
        enforceUniqueness();
    });
});

// Profile Picture File Preview Code
function previewProfilePicture(input, suffix) {
    const previewCard = document.getElementById('profile-picture-preview-card' + suffix);
    const previewImg = document.getElementById('profile-picture-preview-img' + suffix);
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            previewCard.classList.remove('d-none');
        }
        reader.readAsDataURL(input.files[0]);
    } else {
        previewImg.src = '#';
        previewCard.classList.add('d-none');
    }
}

function removeProfilePicture(suffix) {
    const input = document.getElementById(suffix ? 'profile_picture_re' : 'profile_picture');
    const previewCard = document.getElementById('profile-picture-preview-card' + suffix);
    const previewImg = document.getElementById('profile-picture-preview-img' + suffix);
    
    input.value = '';
    previewImg.src = '#';
    previewCard.classList.add('d-none');
}

// Supporting Documents File Preview with Deletion List (using DataTransfer)
let documentsFileList = new DataTransfer();
let documentsFileListRe = new DataTransfer();

function previewDocuments(input, suffix) {
    const container = document.getElementById(suffix ? 'documents-previews-container_re' : 'documents-previews-container');
    container.innerHTML = '';
    
    if (!input.files || input.files.length === 0) {
        container.classList.add('d-none');
        return;
    }

    container.classList.remove('d-none');
    
    if (suffix === '_re') {
        documentsFileListRe = new DataTransfer();
        for (let i = 0; i < input.files.length; i++) {
            documentsFileListRe.items.add(input.files[i]);
        }
    } else {
        documentsFileList = new DataTransfer();
        for (let i = 0; i < input.files.length; i++) {
            documentsFileList.items.add(input.files[i]);
        }
    }

    renderDocumentPreviews(suffix);
}

function renderDocumentPreviews(suffix) {
    const container = document.getElementById(suffix ? 'documents-previews-container_re' : 'documents-previews-container');
    container.innerHTML = '';
    
    const fileList = suffix === '_re' ? documentsFileListRe : documentsFileList;
    const files = fileList.files;
    
    if (files.length === 0) {
        container.classList.add('d-none');
        return;
    }
    
    for (let i = 0; i < files.length; i++) {
        const file = files[i];
        const isImage = file.type.startsWith('image/');
        const col = document.createElement('div');
        col.className = 'col-sm-6 col-md-4 col-lg-3';
        
        let previewHtml = '';
        if (isImage) {
            previewHtml = `<img src="${URL.createObjectURL(file)}" class="card-img-top border-bottom" style="height: 100px; object-fit: cover;">`;
        } else {
            previewHtml = `
                <div class="card-img-top d-flex align-items-center justify-content-center bg-light text-secondary border-bottom" style="height: 100px;">
                    <i class="bi bi-file-earmark-pdf fs-1"></i>
                </div>
            `;
        }
        
        col.innerHTML = `
            <div class="card h-100 border overflow-hidden position-relative shadow-sm" style="border-radius: 8px;">
                ${previewHtml}
                <div class="card-body p-2">
                    <span class="text-truncate d-block small fw-semibold text-dark mb-2" title="${file.name}" style="font-size: 0.75rem;">${file.name}</span>
                    <span class="text-muted text-uppercase" style="font-size: 0.6rem;">${(file.size / 1024 / 1024).toFixed(2)} MB</span>
                </div>
                <button type="button" class="btn btn-danger btn-xs position-absolute top-0 end-0 m-1 rounded-circle p-1 d-flex align-items-center justify-content-center" onclick="removeDocument(${i}, '${suffix}')" style="width: 22px; height: 22px; line-height: 1; border: none; z-index: 10;">
                    <i class="bi bi-x fs-6"></i>
                </button>
            </div>
        `;
        container.appendChild(col);
    }
}

function removeDocument(index, suffix) {
    const input = document.getElementById(suffix ? 'documents_re' : 'documents');
    const oldFileList = suffix === '_re' ? documentsFileListRe : documentsFileList;
    const newDT = new DataTransfer();
    
    for (let i = 0; i < oldFileList.files.length; i++) {
        if (i !== index) {
            newDT.items.add(oldFileList.files[i]);
        }
    }
    
    if (suffix === '_re') {
        documentsFileListRe = newDT;
        input.files = documentsFileListRe.files;
    } else {
        documentsFileList = newDT;
        input.files = documentsFileList.files;
    }
    
    renderDocumentPreviews(suffix);
}
</script>
@endsection
