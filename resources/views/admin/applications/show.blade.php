@extends('layouts.app')

@section('title', 'Application #' . ($application->application_number ?? $application->id) . ' - Admissions')

@section('content')
<div class="container-fluid py-3">
    <!-- Top Bar Navigation & Header -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-white overflow-hidden" style="border-radius: 12px;">
                <div class="card-body p-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <a href="{{ route('admin.applications.index') }}" class="btn btn-sm btn-outline-secondary" style="border-radius: 6px;">
                                <i class="bi bi-arrow-left me-1"></i>Back
                            </a>
                            <span class="badge bg-primary-subtle text-primary fw-bold text-uppercase px-2.5 py-1.5" style="border-radius: 6px; letter-spacing: 0.5px;">
                                {{ ucfirst($application->type) }} Application
                            </span>
                            <span class="badge 
                                @if($application->status === 'admitted') bg-success 
                                @elseif($application->status === 'approved') bg-info text-dark
                                @elseif($application->status === 'rejected') bg-danger 
                                @else bg-warning text-dark @endif px-3 py-1.5" style="border-radius: 6px;">
                                <i class="bi @if($application->status === 'admitted') bi-mortarboard-fill @elseif($application->status === 'rejected') bi-x-circle-fill @else bi-hourglass-split @endif me-1"></i>
                                Status: {{ ucfirst($application->status) }}
                            </span>
                        </div>
                        <h3 class="fw-bold text-dark mb-0">
                            {{ $application->first_name }} {{ $application->last_name }}
                            <span class="text-muted fs-6 fw-normal ms-2">({{ $application->application_number ?? ('#' . $application->id) }})</span>
                        </h3>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        @if($application->status !== 'rejected' && $application->status !== 'admitted')
                            <button type="button" class="btn btn-outline-danger btn-sm px-3 py-2 fw-semibold" data-bs-toggle="modal" data-bs-target="#rejectModal" style="border-radius: 8px;">
                                <i class="bi bi-x-circle me-1"></i>Reject Application
                            </button>
                        @endif
                        <a href="{{ route('admin.applications.index') }}" class="btn btn-secondary btn-sm px-3 py-2 fw-semibold" style="border-radius: 8px;">
                            <i class="bi bi-list-ul me-1"></i>All Applications
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 8px;">
            <i class="bi bi-check-circle-fill me-2 fs-5"></i>
            <span>{{ session('success') }}</span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 8px;">
            <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
            <span>{{ session('error') }}</span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="alert alert-info">Process in order: verify payment → verify uploaded documents → assign programme → confirm admission. Each completed step unlocks the next.</div>
    @php $revisions = \Illuminate\Support\Facades\DB::table('application_revisions')->where('application_id', $application->id)->latest('id')->get(); @endphp
    @if($revisions->isNotEmpty())
        <details class="card p-3 mb-3"><summary>Previous submissions and decisions ({{ $revisions->count() }})</summary>
            @foreach($revisions as $revision)
                @php $snapshot = json_decode($revision->snapshot, true); @endphp
                <p class="mt-2 mb-1">Saved {{ $revision->created_at }} · Status: {{ ucfirst($snapshot['status'] ?? 'pending') }} · Review: {{ $snapshot['review_notes'] ?? 'No decision note' }} · Payment: {{ $snapshot['payment_status'] ?? 'pending' }}</p>
            @endforeach
        </details>
    @endif

    <!-- SECTION 1: Personal Information & Application Details (TOP SECTION) -->
    <div class="row g-3 mb-4">
        <!-- Personal Information & Bio Data -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
                <div class="card-header bg-white border-bottom border-primary border-2 py-3">
                    <h5 class="mb-0 text-primary fw-bold">
                        <i class="bi bi-person-badge-fill me-2"></i>Personal Information & Bio Data
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6 col-sm-6">
                            <span class="text-muted small d-block">Full Legal Name</span>
                            <strong class="text-dark fs-6">{{ $application->first_name }} {{ $application->last_name }}</strong>
                        </div>
                        <div class="col-md-6 col-sm-6">
                            <span class="text-muted small d-block">Email Address</span>
                            <span class="text-dark fw-medium"><i class="bi bi-envelope text-primary me-1"></i>{{ $application->email }}</span>
                        </div>
                        <div class="col-md-6 col-sm-6">
                            <span class="text-muted small d-block">Phone Number</span>
                            <span class="text-dark fw-medium"><i class="bi bi-telephone text-primary me-1"></i>{{ $application->phone }}</span>
                        </div>
                        <div class="col-md-6 col-sm-6">
                            <span class="text-muted small d-block">Date of Birth & Gender</span>
                            <span class="text-dark fw-medium">
                                {{ $application->date_of_birth ? $application->date_of_birth->format('M d, Y') : 'Not provided' }}
                                <span class="badge bg-light text-dark border ms-1">{{ ucfirst($application->gender ?? 'Unspecified') }}</span>
                            </span>
                        </div>
                        <div class="col-md-12">
                            <span class="text-muted small d-block">Residential Address</span>
                            <span class="text-dark fw-medium">{{ $application->address ?: 'Not provided' }}</span>
                        </div>

                        <!-- Prior Education Info -->
                        <div class="col-12 pt-2">
                            <h6 class="text-uppercase text-secondary fw-semibold small mb-2" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                                <i class="bi bi-mortarboard me-1"></i>Academic Background & Prior Qualifications
                            </h6>
                            <hr class="mt-0 mb-3">
                        </div>
                        <div class="col-md-6 col-sm-6">
                            <span class="text-muted small d-block">Previous School / Institution</span>
                            <span class="text-dark fw-medium">{{ $application->previous_school ?: 'Not provided' }}</span>
                        </div>
                        <div class="col-md-6 col-sm-6">
                            <span class="text-muted small d-block">Prior Qualification & GPA</span>
                            <span class="text-dark fw-medium">
                                {{ $application->previous_qualification ?: 'Not provided' }}
                                @if($application->previous_gpa)
                                    <span class="badge bg-secondary-subtle text-dark ms-1">GPA: {{ $application->previous_gpa }}</span>
                                @endif
                            </span>
                        </div>

                        @if($application->type === 'student' && ($application->guardian_name || $application->guardian_phone))
                        <div class="col-12 pt-2">
                            <h6 class="text-uppercase text-secondary fw-semibold small mb-2" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                                <i class="bi bi-people me-1"></i>Guardian Details
                            </h6>
                            <hr class="mt-0 mb-3">
                        </div>
                        <div class="col-md-6 col-sm-6">
                            <span class="text-muted small d-block">Guardian Name</span>
                            <span class="text-dark fw-medium">{{ $application->guardian_name }} ({{ $application->guardian_relationship ?? 'Guardian' }})</span>
                        </div>
                        <div class="col-md-6 col-sm-6">
                            <span class="text-muted small d-block">Guardian Contact</span>
                            <span class="text-dark fw-medium">{{ $application->guardian_phone }} {{ $application->guardian_email ? '| ' . $application->guardian_email : '' }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Application Details Card -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
                <div class="card-header bg-white border-bottom border-primary border-2 py-3">
                    <h5 class="mb-0 text-primary fw-bold">
                        <i class="bi bi-info-circle-fill me-2"></i>Application Details
                    </h5>
                </div>
                <div class="card-body p-4 d-flex flex-column justify-content-between">
                    <ul class="list-group list-group-flush mb-3">
                        <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                            <span class="text-muted small">Application Number</span>
                            <code class="fw-bold fs-6 text-dark bg-light px-2 py-1 rounded">{{ $application->application_number ?? ('#' . $application->id) }}</code>
                        </li>
                        <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                            <span class="text-muted small">Submitted On</span>
                            <span class="small fw-semibold text-dark">{{ $application->created_at->format('M d, Y H:i') }}</span>
                        </li>
                        <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                            <span class="text-muted small">Payment Status</span>
                            @if($step1Complete)
                                <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Verified & Approved</span>
                            @elseif($application->payment_proof)
                                <span class="badge bg-info text-dark"><i class="bi bi-clock me-1"></i>Receipt Uploaded</span>
                            @else
                                <span class="badge bg-warning text-dark"><i class="bi bi-exclamation-circle me-1"></i>Payment Pending</span>
                            @endif
                        </li>
                        <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                            <span class="text-muted small">Documents Status</span>
                            @if($step2Complete)
                                <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Previewed & Verified</span>
                            @else
                                <span class="badge bg-secondary"><i class="bi bi-hourglass-split me-1"></i>Pending Review</span>
                            @endif
                        </li>
                        <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                            <span class="text-muted small">Assigned Programme</span>
                            @if($step3Complete)
                                <span class="badge bg-success text-truncate" style="max-width: 180px;">{{ $application->programRecord->name ?? $application->program }}</span>
                            @else
                                <span class="badge bg-secondary">Not yet assigned</span>
                            @endif
                        </li>
                        @if($application->student_number)
                        <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                            <span class="text-muted small">Official Student No.</span>
                            <span class="badge bg-primary fs-6">{{ $application->student_number }}</span>
                        </li>
                        @endif
                    </ul>

                    <!-- Readiness Summary Gauge -->
                    @php
                        $score = $approvalReadiness['score'] ?? 0;
                        $scoreBg = $score >= 80 ? 'success' : ($score >= 50 ? 'warning' : 'danger');
                    @endphp
                    <div class="bg-light p-3 rounded text-center">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="small fw-bold text-secondary">Sequential Progression</span>
                            <span class="badge bg-{{ $scoreBg }}">{{ $score }}% Ready</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-{{ $scoreBg }}" role="progressbar" style="width: {{ $score }}%" aria-valuenow="{{ $score }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SECTION 2: ADMISSION PROCESSING WIZARD (STRICT SEQUENTIAL ORDER) -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm overflow-hidden" style="border-radius: 14px;">
                <div class="card-header bg-white border-bottom py-3">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <h4 class="fw-bold text-dark mb-0">
                                <i class="bi bi-diagram-3-fill text-primary me-2"></i>Admission Processing Wizard
                            </h4>
                            <p class="text-muted small mb-0">Strict Sequential Order: Payment &rarr; Document Preview &rarr; Programme Assignment &rarr; Admission Confirmation</p>
                        </div>
                        <span class="badge bg-primary px-3 py-2 fs-6">
                            Active Step: {{ $currentStep }} of 4
                        </span>
                    </div>
                </div>

                <!-- Wizard Steps Stepper Navigation -->
                <div class="bg-light p-3 border-bottom">
                    <ul class="nav nav-pills nav-fill gap-2" id="admissionWizardTabs" role="tablist">
                        <!-- Step 1: Payment -->
                        <li class="nav-item" role="presentation">
                            <button class="nav-link py-3 text-start position-relative border {{ $currentStep === 1 ? 'active shadow-sm' : '' }} {{ $step1Complete ? 'border-success bg-white' : '' }}" 
                                    id="step1-tab" 
                                    data-bs-toggle="pill" 
                                    data-bs-target="#step1-pane" 
                                    type="button" 
                                    role="tab" 
                                    aria-controls="step1-pane" 
                                    aria-selected="{{ $currentStep === 1 ? 'true' : 'false' }}"
                                    style="border-radius: 10px;">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center me-3 {{ $step1Complete ? 'bg-success text-white' : ($currentStep === 1 ? 'bg-primary text-white' : 'bg-secondary text-white') }}" style="width: 38px; height: 38px; min-width: 38px;">
                                        @if($step1Complete)
                                            <i class="bi bi-check-lg fs-5"></i>
                                        @else
                                            <i class="bi bi-credit-card fs-6"></i>
                                        @endif
                                    </div>
                                    <div class="text-truncate">
                                        <div class="small text-uppercase fw-bold text-muted" style="font-size: 0.7rem;">Step 1</div>
                                        <div class="fw-bold {{ $currentStep === 1 ? 'text-white' : 'text-dark' }}">Payment Approval</div>
                                        <span class="badge {{ $step1Complete ? 'bg-success' : 'bg-warning text-dark' }} mt-1" style="font-size: 0.7rem;">
                                            {{ $step1Complete ? 'Verified & Approved' : 'Action Required' }}
                                        </span>
                                    </div>
                                </div>
                            </button>
                        </li>

                        <!-- Step 2: Document Preview -->
                        <li class="nav-item" role="presentation">
                            <button class="nav-link py-3 text-start position-relative border {{ $currentStep === 2 ? 'active shadow-sm' : '' }} {{ $step2Complete ? 'border-success bg-white' : '' }} {{ !$step1Complete ? 'disabled opacity-50' : '' }}" 
                                    id="step2-tab" 
                                    data-bs-toggle="pill" 
                                    data-bs-target="#step2-pane" 
                                    type="button" 
                                    role="tab" 
                                    aria-controls="step2-pane" 
                                    aria-selected="{{ $currentStep === 2 ? 'true' : 'false' }}"
                                    {{ !$step1Complete ? 'disabled' : '' }}
                                    style="border-radius: 10px;">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center me-3 {{ $step2Complete ? 'bg-success text-white' : ($currentStep === 2 ? 'bg-primary text-white' : 'bg-secondary text-white') }}" style="width: 38px; height: 38px; min-width: 38px;">
                                        @if($step2Complete)
                                            <i class="bi bi-check-lg fs-5"></i>
                                        @elseif(!$step1Complete)
                                            <i class="bi bi-lock-fill fs-6"></i>
                                        @else
                                            <i class="bi bi-file-earmark-check fs-6"></i>
                                        @endif
                                    </div>
                                    <div class="text-truncate">
                                        <div class="small text-uppercase fw-bold text-muted" style="font-size: 0.7rem;">Step 2</div>
                                        <div class="fw-bold {{ $currentStep === 2 ? 'text-white' : 'text-dark' }}">Document Preview</div>
                                        <span class="badge {{ $step2Complete ? 'bg-success' : (!$step1Complete ? 'bg-secondary' : 'bg-warning text-dark') }} mt-1" style="font-size: 0.7rem;">
                                            {{ $step2Complete ? 'Verified' : (!$step1Complete ? 'Locked' : 'Review Required') }}
                                        </span>
                                    </div>
                                </div>
                            </button>
                        </li>

                        <!-- Step 3: Program Assignment -->
                        <li class="nav-item" role="presentation">
                            <button class="nav-link py-3 text-start position-relative border {{ $currentStep === 3 ? 'active shadow-sm' : '' }} {{ $step3Complete ? 'border-success bg-white' : '' }} {{ !$step2Complete ? 'disabled opacity-50' : '' }}" 
                                    id="step3-tab" 
                                    data-bs-toggle="pill" 
                                    data-bs-target="#step3-pane" 
                                    type="button" 
                                    role="tab" 
                                    aria-controls="step3-pane" 
                                    aria-selected="{{ $currentStep === 3 ? 'true' : 'false' }}"
                                    {{ !$step2Complete ? 'disabled' : '' }}
                                    style="border-radius: 10px;">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center me-3 {{ $step3Complete ? 'bg-success text-white' : ($currentStep === 3 ? 'bg-primary text-white' : 'bg-secondary text-white') }}" style="width: 38px; height: 38px; min-width: 38px;">
                                        @if($step3Complete)
                                            <i class="bi bi-check-lg fs-5"></i>
                                        @elseif(!$step2Complete)
                                            <i class="bi bi-lock-fill fs-6"></i>
                                        @else
                                            <i class="bi bi-journal-bookmark-fill fs-6"></i>
                                        @endif
                                    </div>
                                    <div class="text-truncate">
                                        <div class="small text-uppercase fw-bold text-muted" style="font-size: 0.7rem;">Step 3</div>
                                        <div class="fw-bold {{ $currentStep === 3 ? 'text-white' : 'text-dark' }}">Program Assignment</div>
                                        <span class="badge {{ $step3Complete ? 'bg-success' : (!$step2Complete ? 'bg-secondary' : 'bg-warning text-dark') }} mt-1" style="font-size: 0.7rem;">
                                            {{ $step3Complete ? 'Assigned' : (!$step2Complete ? 'Locked' : 'Pending Selection') }}
                                        </span>
                                    </div>
                                </div>
                            </button>
                        </li>

                        <!-- Step 4: Admission Confirmation -->
                        <li class="nav-item" role="presentation">
                            <button class="nav-link py-3 text-start position-relative border {{ $currentStep === 4 ? 'active shadow-sm' : '' }} {{ $step4Complete ? 'border-success bg-white' : '' }} {{ !$step3Complete ? 'disabled opacity-50' : '' }}" 
                                    id="step4-tab" 
                                    data-bs-toggle="pill" 
                                    data-bs-target="#step4-pane" 
                                    type="button" 
                                    role="tab" 
                                    aria-controls="step4-pane" 
                                    aria-selected="{{ $currentStep === 4 ? 'true' : 'false' }}"
                                    {{ !$step3Complete ? 'disabled' : '' }}
                                    style="border-radius: 10px;">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center me-3 {{ $step4Complete ? 'bg-success text-white' : ($currentStep === 4 ? 'bg-primary text-white' : 'bg-secondary text-white') }}" style="width: 38px; height: 38px; min-width: 38px;">
                                        @if($step4Complete)
                                            <i class="bi bi-check-lg fs-5"></i>
                                        @elseif(!$step3Complete)
                                            <i class="bi bi-lock-fill fs-6"></i>
                                        @else
                                            <i class="bi bi-mortarboard-fill fs-6"></i>
                                        @endif
                                    </div>
                                    <div class="text-truncate">
                                        <div class="small text-uppercase fw-bold text-muted" style="font-size: 0.7rem;">Step 4</div>
                                        <div class="fw-bold {{ $currentStep === 4 ? 'text-white' : 'text-dark' }}">Admission Confirmation</div>
                                        <span class="badge {{ $step4Complete ? 'bg-success' : (!$step3Complete ? 'bg-secondary' : 'bg-primary text-white') }} mt-1" style="font-size: 0.7rem;">
                                            {{ $step4Complete ? 'Admitted' : (!$step3Complete ? 'Locked' : 'Ready to Admit') }}
                                        </span>
                                    </div>
                                </div>
                            </button>
                        </li>
                    </ul>
                </div>

                <!-- Wizard Panels Content -->
                <div class="card-body p-4">
                    <div class="tab-content" id="admissionWizardContent">

                        <!-- ========================================== -->
                        <!-- STEP 1 PANE: PAYMENT VERIFICATION          -->
                        <!-- ========================================== -->
                        <div class="tab-pane fade {{ $currentStep === 1 ? 'show active' : '' }}" id="step1-pane" role="tabpanel" aria-labelledby="step1-tab">
                            <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4 flex-wrap gap-2">
                                <div>
                                    <h4 class="fw-bold text-dark mb-1">
                                        <i class="bi bi-credit-card-2-front text-primary me-2"></i>Step 1: Registration Fee Payment Verification
                                    </h4>
                                    <p class="text-muted small mb-0">Registration fee must be received and verified before the application proceeds to document verification.</p>
                                </div>
                                @if($step1Complete)
                                    <span class="badge bg-success py-2 px-3 fs-6">
                                        <i class="bi bi-check-circle-fill me-1"></i>STEP 1 COMPLETE
                                    </span>
                                @endif
                            </div>

                            @php
                                $admissionFeeAmount = \App\Services\AdmissionPaymentService::getAdmissionFeeAmount($application, $registrationFeeCurrency);
                            @endphp

                            <!-- Payment Overview Card -->
                            <div class="row g-4 mb-4">
                                <div class="col-md-5">
                                    <div class="card border bg-light h-100" style="border-radius: 10px;">
                                        <div class="card-body p-4">
                                            <h6 class="text-uppercase text-secondary fw-bold small mb-3">Registration Fee Required</h6>
                                            <h2 class="fw-bold text-success mb-2">{{ $registrationFeeCurrency }} {{ number_format($admissionFeeAmount, 2) }}</h2>
                                            <div class="small text-muted mb-3">Non-refundable student registration fee. Applicant selected {{ $registrationFeeCurrency }}.</div>
                                            <hr>
                                            <div class="mb-2"><strong>Payment Reference:</strong> <code class="bg-white border px-2 py-1 rounded text-primary">{{ $application->payment_ref ?: 'None' }}</code></div>
                                            <div class="mb-0"><strong>Applicant:</strong> {{ $application->first_name }} {{ $application->last_name }}</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-7">
                                    @if($step1Complete)
                                        <!-- Already Verified -->
                                        <div class="card border-success border-2 bg-success bg-opacity-10 h-100" style="border-radius: 10px;">
                                            <div class="card-body p-4">
                                                <div class="d-flex align-items-center mb-3">
                                                    <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px;">
                                                        <i class="bi bi-shield-check fs-4"></i>
                                                    </div>
                                                    <div>
                                                        <h5 class="fw-bold text-success mb-1">Payment Successfully Verified & Approved</h5>
                                                        <div class="text-muted small">
                                                            Verified on {{ $application->payment_verified_at ? $application->payment_verified_at->format('M d, Y H:i') : 'Recorded' }}
                                                            @if($application->paymentVerifier)
                                                                by {{ $application->paymentVerifier->name }}
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>

                                                <p class="small text-dark mb-4">
                                                    The registration fee payment for reference <code>{{ $application->payment_ref }}</code> is locked in and synced to Finance. You are now authorized to proceed to <strong>Step 2: Document Preview</strong>.
                                                </p>

                                                <div class="d-flex gap-2 flex-wrap">
                                                    @if($application->payment_proof)
                                                        @php
                                                            $proofUrl = route('applications.files.show', [$application, 'receipt']);
                                                            $proofExt = strtolower(pathinfo($application->payment_proof, PATHINFO_EXTENSION));
                                                        @endphp
                                                        <button type="button" class="btn btn-outline-success btn-sm preview-doc-btn fw-bold"
                                                                data-url="{{ $proofUrl }}"
                                                                data-title="Payment Receipt Proof"
                                                                data-ext="{{ $proofExt }}">
                                                            <i class="bi bi-eye me-1"></i>View Uploaded Receipt
                                                        </button>
                                                    @endif
                                                    <button type="button" class="btn btn-primary btn-sm fw-bold px-3 switch-tab-btn" data-target-tab="#step2-tab">
                                                        Proceed to Step 2: Document Preview <i class="bi bi-arrow-right ms-1"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <!-- Not Yet Verified: Form to Verify Uploaded Receipt or Offline Cash -->
                                        <div class="card border h-100" style="border-radius: 10px;">
                                            <div class="card-body p-4">
                                                @if($application->payment_proof)
                                                    @php
                                                        $proofUrl = route('applications.files.show', [$application, 'receipt']);
                                                        $proofExt = strtolower(pathinfo($application->payment_proof, PATHINFO_EXTENSION));
                                                    @endphp
                                                    <div class="alert alert-info border-0 shadow-sm d-flex align-items-center mb-3">
                                                        <i class="bi bi-receipt fs-2 text-info me-3"></i>
                                                        <div>
                                                            <strong class="d-block text-dark">Payment Receipt Uploaded by Applicant</strong>
                                                            <span class="small text-muted">Uploaded on {{ $application->payment_uploaded_at ? $application->payment_uploaded_at->format('M d, Y H:i') : 'Recent' }}. Preview the receipt below before confirming.</span>
                                                        </div>
                                                    </div>

                                                    <div class="d-flex gap-2 mb-4">
                                                        <button type="button" class="btn btn-outline-primary btn-sm preview-doc-btn fw-bold"
                                                                data-url="{{ $proofUrl }}"
                                                                data-title="Applicant Payment Proof"
                                                                data-ext="{{ $proofExt }}">
                                                            <i class="bi bi-eye me-1"></i>Preview Uploaded Receipt
                                                        </button>
                                                        <a href="{{ $proofUrl }}" target="_blank" download class="btn btn-outline-secondary btn-sm">
                                                            <i class="bi bi-download me-1"></i>Download
                                                        </a>
                                                    </div>

                                                    <form action="{{ route('admin.applications.verify-payment', $application->id) }}" method="POST">
                                                        @csrf
                                                        <div class="mb-3">
                                                            <label class="form-label small fw-bold text-dark">Verification Notes (Optional)</label>
                                                            <input type="text" name="notes" class="form-control form-control-sm" placeholder="e.g. Bank slip confirmed and reconciled against bank statement">
                                                        </div>
                                                        <button type="submit" class="btn btn-success fw-bold px-4 py-2 confirm-submit" data-confirm="Confirm that you have reviewed the payment receipt and approve this payment?">
                                                            <i class="bi bi-check-circle-fill me-1"></i>Approve & Confirm Payment Receipt
                                                        </button>
                                                    </form>
                                                @else
                                                    <!-- No Receipt Uploaded - Offline/Direct Payment Option -->
                                                    <div class="alert alert-warning border-0 shadow-sm mb-3">
                                                        <div class="fw-bold text-dark"><i class="bi bi-clock-history me-1"></i>No Payment Receipt Uploaded by Applicant Yet</div>
                                                        <div class="small text-muted">The applicant has not uploaded a payment receipt through their student portal. If the applicant paid in cash, bank draft, or wire transfer directly at the finance office, enter the transaction reference below to verify.</div>
                                                    </div>

                                                    <form action="{{ route('admin.applications.verify-payment', $application->id) }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="confirm_manual_payment" value="1">
                                                        <div class="mb-3">
                                                            <label class="form-label small fw-bold text-dark">Direct Receipt / Transaction Reference <span class="text-danger">*</span></label>
                                                            <input type="text" name="offline_receipt" class="form-control form-control-sm" placeholder="e.g. CASH-REC-2026-0012 or STANBIC-TX-9981" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label small fw-bold text-dark">Verification Remark</label>
                                                            <input type="text" name="notes" class="form-control form-control-sm" placeholder="e.g. Paid in cash at finance office">
                                                        </div>
                                                        <button type="submit" class="btn btn-primary fw-bold px-4 py-2 confirm-submit" data-confirm="Record and verify direct payment for this applicant?">
                                                            <i class="bi bi-cash-coin me-1"></i>Verify Direct / Offline Payment
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- ========================================== -->
                        <!-- STEP 2 PANE: DOCUMENT PREVIEW              -->
                        <!-- ========================================== -->
                        <div class="tab-pane fade {{ $currentStep === 2 ? 'show active' : '' }}" id="step2-pane" role="tabpanel" aria-labelledby="step2-tab">
                            <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4 flex-wrap gap-2">
                                <div>
                                    <h4 class="fw-bold text-dark mb-1">
                                        <i class="bi bi-file-earmark-check text-primary me-2"></i>Step 2: Document Preview Against Application Details
                                    </h4>
                                    <p class="text-muted small mb-0">Cross-reference submitted academic transcripts, certificates, and ID documents against applicant details.</p>
                                </div>
                                @if($step2Complete)
                                    <span class="badge bg-success py-2 px-3 fs-6">
                                        <i class="bi bi-check-circle-fill me-1"></i>STEP 2 COMPLETE
                                    </span>
                                @endif
                            </div>

                            @if(!$step1Complete)
                                <div class="alert alert-danger border-0 shadow-sm p-4 text-center my-4" style="border-radius: 10px;">
                                    <div class="rounded-circle bg-danger text-white d-inline-flex align-items-center justify-content-center mb-2" style="width: 50px; height: 50px;">
                                        <i class="bi bi-lock-fill fs-4"></i>
                                    </div>
                                    <h5 class="fw-bold text-danger mb-1">Step 2 is Locked</h5>
                                    <p class="text-muted small mx-auto mb-3" style="max-width: 500px;">
                                        Strict Admission Order: Admission application fee payment must be received and approved in <strong>Step 1</strong> before applicant documents can be previewed or verified.
                                    </p>
                                    <button type="button" class="btn btn-sm btn-outline-danger switch-tab-btn" data-target-tab="#step1-tab">
                                        <i class="bi bi-arrow-left me-1"></i>Go to Step 1: Payment Approval
                                    </button>
                                </div>
                            @else
                                <!-- Comparative Side-by-Side Verification Layout -->
                                <div class="row g-4 mb-4">
                                    <!-- Left Column: Applicant Bio Data & Credentials to Check Against -->
                                    <div class="col-lg-5">
                                        <div class="card border h-100" style="border-radius: 10px;">
                                            <div class="card-header bg-light py-2.5">
                                                <h6 class="fw-bold text-dark mb-0"><i class="bi bi-person-lines-fill me-2 text-primary"></i>Applicant Details to Match</h6>
                                            </div>
                                            <div class="card-body p-3">
                                                <div class="mb-3 pb-2 border-bottom">
                                                    <span class="text-muted small d-block">Candidate Full Name</span>
                                                    <strong class="text-dark">{{ $application->first_name }} {{ $application->last_name }}</strong>
                                                </div>
                                                <div class="mb-3 pb-2 border-bottom">
                                                    <span class="text-muted small d-block">Date of Birth & Gender</span>
                                                    <span class="text-dark fw-medium">
                                                        {{ $application->date_of_birth ? $application->date_of_birth->format('M d, Y') : 'N/A' }} 
                                                        ({{ ucfirst($application->gender ?? 'N/A') }})
                                                    </span>
                                                </div>
                                                <div class="mb-3 pb-2 border-bottom">
                                                    <span class="text-muted small d-block">Previous School / College</span>
                                                    <span class="text-dark fw-medium">{{ $application->previous_school ?: 'Not specified' }}</span>
                                                </div>
                                                <div class="mb-3 pb-2 border-bottom">
                                                    <span class="text-muted small d-block">Claimed Qualification</span>
                                                    <span class="text-dark fw-medium">{{ $application->previous_qualification ?: 'Not specified' }}</span>
                                                </div>
                                                <div class="mb-3 pb-2 border-bottom">
                                                    <span class="text-muted small d-block">Reported GPA / Results</span>
                                                    <span class="badge bg-secondary-subtle text-dark">{{ $application->previous_gpa ?: 'N/A' }}</span>
                                                </div>
                                                <div class="mb-0">
                                                    <span class="text-muted small d-block">Preferred Intake Programmes</span>
                                                    <span class="small text-dark">{{ $programChoices->pluck('name')->join(', ') ?: 'Under review' }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Right Column: Submitted Document Grid -->
                                    <div class="col-lg-7">
                                        <div class="card border h-100" style="border-radius: 10px;">
                                            <div class="card-header bg-light py-2.5 d-flex justify-content-between align-items-center">
                                                <h6 class="fw-bold text-dark mb-0">
                                                    <i class="bi bi-file-earmark-text-fill me-2 text-primary"></i>Submitted Supporting Documents
                                                </h6>
                                                <span class="badge bg-primary">
                                                    {{ is_array($application->documents) ? count($application->documents) : 0 }} Document(s)
                                                </span>
                                            </div>
                                            <div class="card-body p-3">
                                                @if(is_array($application->documents) && count($application->documents) > 0)
                                                    <div class="row g-2">
                                                        @foreach($application->documents as $docIndex => $docItem)
                                                            @php
                                                                $docPath = is_array($docItem) ? ($docItem['path'] ?? null) : (is_string($docItem) ? $docItem : null);
                                                                $fileUrl = $docPath ? route('applications.files.show', [$application, $docIndex]) : null;
                                                                $ext = $docPath ? strtolower(pathinfo($docPath, PATHINFO_EXTENSION)) : '';
                                                                $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                                                $isPdf = $ext === 'pdf';
                                                            @endphp
                                                            @if($docPath)
                                                                <div class="col-md-6">
                                                                    <div class="card border p-3 text-center bg-light h-100" style="border-radius: 8px;">
                                                                        <div class="fs-2 mb-1">
                                                                            @if($isImage)
                                                                                <i class="bi bi-file-earmark-image text-primary"></i>
                                                                            @elseif($isPdf)
                                                                                <i class="bi bi-file-earmark-pdf text-danger"></i>
                                                                            @else
                                                                                <i class="bi bi-file-earmark-word text-info"></i>
                                                                            @endif
                                                                        </div>
                                                                        <strong class="text-truncate d-block small mb-1">Document #{{ $loop->iteration }}</strong>
                                                                        <span class="badge bg-secondary-subtle text-dark text-uppercase small mb-2" style="font-size: 0.65rem;">{{ $ext ?: 'FILE' }}</span>
                                                                        <div class="d-flex justify-content-center gap-1.5 mt-auto">
                                                                            <button type="button" class="btn btn-sm btn-primary py-1 px-2.5 preview-doc-btn"
                                                                                    data-url="{{ $fileUrl }}"
                                                                                    data-title="Document #{{ $loop->iteration }} Preview"
                                                                                    data-ext="{{ $ext }}" style="border-radius: 5px;">
                                                                                <i class="bi bi-eye me-1"></i>Preview
                                                                            </button>
                                                                            <a href="{{ $fileUrl }}" target="_blank" download class="btn btn-sm btn-outline-secondary py-1 px-2" style="border-radius: 5px;">
                                                                                <i class="bi bi-download"></i>
                                                                            </a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <div class="text-center py-4 text-muted">
                                                        <i class="bi bi-folder-x fs-1 d-block mb-2"></i>
                                                        No supporting documents were attached with this application.
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Document Verification Confirmation Card -->
                                <div class="card border-0 shadow-sm" style="border-radius: 10px; background-color: #f8fafc;">
                                    <div class="card-body p-4">
                                        @if($step2Complete)
                                            <div class="alert alert-success border-0 mb-3 d-flex align-items-center">
                                                <i class="bi bi-check-circle-fill fs-3 text-success me-3"></i>
                                                <div>
                                                    <strong class="d-block text-dark">Documents Successfully Verified Against Application Details</strong>
                                                    <span class="small text-muted">
                                                        Verified on {{ $application->documents_verified_at ? $application->documents_verified_at->format('M d, Y H:i') : 'Recent' }}
                                                        @if($application->documentsVerifier)
                                                            by {{ $application->documentsVerifier->name }}
                                                        @endif
                                                    </span>
                                                    @if($application->documents_verified_notes)
                                                        <div class="small text-dark mt-1"><strong>Note:</strong> {{ $application->documents_verified_notes }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                                <button type="button" class="btn btn-outline-secondary btn-sm switch-tab-btn" data-target-tab="#step1-tab">
                                                    <i class="bi bi-arrow-left me-1"></i>Back to Step 1
                                                </button>
                                                <button type="button" class="btn btn-primary fw-bold px-4 py-2 switch-tab-btn" data-target-tab="#step3-tab">
                                                    Proceed to Step 3: Program Assignment <i class="bi bi-arrow-right ms-1"></i>
                                                </button>
                                            </div>
                                        @else
                                            <form action="{{ route('admin.applications.verify-documents', $application->id) }}" method="POST">
                                                @csrf
                                                <h6 class="fw-bold text-dark mb-2"><i class="bi bi-check2-circle text-primary me-2"></i>Verify Applicant Documents</h6>
                                                <p class="text-muted small mb-3">Please certify that the applicant's submitted certificates, transcripts, and bio-data match the details entered on this application.</p>
                                                
                                                <div class="mb-3">
                                                    <label class="form-label small fw-bold text-dark">Verification Notes (Optional)</label>
                                                    <textarea name="notes" class="form-control form-control-sm" rows="2" placeholder="e.g. Verified against secondary leaving certificate and national ID. Qualifications approved."></textarea>
                                                </div>

                                                <div class="form-check mb-3">
                                                    <input class="form-check-input" type="checkbox" id="confirm_doc_check" required>
                                                    <label class="form-check-label small text-dark fw-medium" for="confirm_doc_check">
                                                        I confirm that I have previewed and checked the uploaded documents against the application details and verified their authenticity.
                                                    </label>
                                                </div>

                                                <div class="d-flex justify-content-between align-items-center">
                                                    <button type="button" class="btn btn-outline-secondary btn-sm switch-tab-btn" data-target-tab="#step1-tab">
                                                        <i class="bi bi-arrow-left me-1"></i>Back to Step 1
                                                    </button>
                                                    <button type="submit" class="btn btn-success fw-bold px-4 py-2">
                                                        <i class="bi bi-shield-check me-1"></i>Verify Documents & Proceed to Step 3
                                                    </button>
                                                </div>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- ========================================== -->
                        <!-- STEP 3 PANE: PROGRAM ASSIGNMENT            -->
                        <!-- ========================================== -->
                        <div class="tab-pane fade {{ $currentStep === 3 ? 'show active' : '' }}" id="step3-pane" role="tabpanel" aria-labelledby="step3-tab">
                            <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4 flex-wrap gap-2">
                                <div>
                                    <h4 class="fw-bold text-dark mb-1">
                                        <i class="bi bi-journal-bookmark text-primary me-2"></i>Step 3: Programme Assignment
                                    </h4>
                                    <p class="text-muted small mb-0">Assign the official university programme from the applicant's preferred choices or department offerings.</p>
                                </div>
                                @if($step3Complete)
                                    <span class="badge bg-success py-2 px-3 fs-6">
                                        <i class="bi bi-check-circle-fill me-1"></i>STEP 3 COMPLETE
                                    </span>
                                @endif
                            </div>

                            @if(!$step1Complete || !$step2Complete)
                                <div class="alert alert-danger border-0 shadow-sm p-4 text-center my-4" style="border-radius: 10px;">
                                    <div class="rounded-circle bg-danger text-white d-inline-flex align-items-center justify-content-center mb-2" style="width: 50px; height: 50px;">
                                        <i class="bi bi-lock-fill fs-4"></i>
                                    </div>
                                    <h5 class="fw-bold text-danger mb-1">Step 3 is Locked</h5>
                                    <p class="text-muted small mx-auto mb-3" style="max-width: 500px;">
                                        Strict Admission Order: Step 1 (Payment) and Step 2 (Document Preview & Verification) must be completed before a university programme can be assigned.
                                    </p>
                                    @if(!$step1Complete)
                                        <button type="button" class="btn btn-sm btn-outline-danger switch-tab-btn" data-target-tab="#step1-tab">
                                            <i class="bi bi-arrow-left me-1"></i>Go to Step 1: Payment Approval
                                        </button>
                                    @else
                                        <button type="button" class="btn btn-sm btn-outline-danger switch-tab-btn" data-target-tab="#step2-tab">
                                            <i class="bi bi-arrow-left me-1"></i>Go to Step 2: Document Preview
                                        </button>
                                    @endif
                                </div>
                            @else
                                <!-- Current Assigned Programme Card -->
                                @if($step3Complete)
                                    <div class="card border-success border-2 bg-success bg-opacity-10 mb-4" style="border-radius: 10px;">
                                        <div class="card-body p-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
                                            <div>
                                                <span class="badge bg-success mb-2 px-2.5 py-1 text-uppercase">Assigned University Programme</span>
                                                <h3 class="fw-bold text-dark mb-1">{{ $application->programRecord->name ?? $application->program }}</h3>
                                                <div class="text-muted small">
                                                    <span class="me-3"><strong>Code:</strong> <code>{{ $application->programRecord->code ?? 'N/A' }}</code></span>
                                                    <span class="me-3"><strong>Level:</strong> {{ $application->programRecord->level->name ?? 'Degree' }}</span>
                                                    <span><strong>Department:</strong> {{ $application->programRecord->department->name ?? 'Academic Dept' }}</span>
                                                </div>
                                            </div>
                                            <div class="text-md-end">
                                                <span class="badge {{ ($application->programRecord->is_full ?? false) ? 'bg-danger' : 'bg-success' }} px-3 py-2 fs-6">
                                                    {{ $application->programRecord->available_slots ?? 50 }} Slots Available
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="alert alert-warning border-0 shadow-sm d-flex align-items-center mb-4">
                                        <i class="bi bi-exclamation-triangle-fill fs-3 text-warning me-3"></i>
                                        <div>
                                            <strong class="d-block text-dark">No University Programme Assigned Yet</strong>
                                            <span class="small text-muted">Review the applicant's submitted choices below and click <strong>"Select & Assign"</strong> or choose a programme from the dropdown to assign their official university programme.</span>
                                        </div>
                                    </div>
                                @endif

                                <!-- Preferred Choices Table -->
                                <div class="card border mb-4 overflow-hidden" style="border-radius: 10px;">
                                    <div class="card-header bg-white border-bottom py-3">
                                        <h6 class="fw-bold text-dark mb-0">
                                            <i class="bi bi-list-ol me-2 text-primary"></i>Applicant's Submitted Programme Preferences
                                        </h6>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Preference Rank</th>
                                                    <th>Programme Code</th>
                                                    <th>Programme Name</th>
                                                    <th>Department</th>
                                                    <th>Available Slots</th>
                                                    <th class="text-end">Assign Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if(isset($programChoices) && count($programChoices) > 0)
                                                    @foreach($programChoices as $rankIndex => $choiceProg)
                                                        @php
                                                            $isAssigned = $application->program_id == $choiceProg->id;
                                                            $rankLabel = match($rankIndex) {
                                                                0 => '1st Choice (Primary)',
                                                                1 => '2nd Choice',
                                                                2 => '3rd Choice',
                                                                3 => '4th Choice',
                                                                default => ($rankIndex + 1) . 'th Choice'
                                                            };
                                                        @endphp
                                                        <tr class="{{ $isAssigned ? 'table-success bg-opacity-25' : '' }}">
                                                            <td>
                                                                <span class="badge {{ $rankIndex === 0 ? 'bg-primary' : 'bg-secondary' }}">
                                                                    {{ $rankLabel }}
                                                                </span>
                                                            </td>
                                                            <td><code class="fw-bold bg-light px-2 py-1 rounded text-dark">{{ $choiceProg->code }}</code></td>
                                                            <td>
                                                                <strong class="text-dark">{{ $choiceProg->name }}</strong>
                                                                <div class="small text-muted">{{ $choiceProg->level->name ?? 'Degree' }}</div>
                                                            </td>
                                                            <td>{{ $choiceProg->department->name ?? 'N/A' }}</td>
                                                            <td>
                                                                <span class="badge {{ $choiceProg->is_full ? 'bg-danger' : 'bg-success' }} px-2 py-1">
                                                                    {{ $choiceProg->available_slots }} / {{ $choiceProg->capacity }} Slots Left
                                                                </span>
                                                            </td>
                                                            <td class="text-end">
                                                                @if($isAssigned)
                                                                    <span class="badge bg-success px-3 py-1.5 fs-6">
                                                                        <i class="bi bi-check-circle-fill me-1"></i>ASSIGNED
                                                                    </span>
                                                                @else
                                                                    <form action="{{ route('admin.applications.update-program', $application->id) }}" method="POST" class="d-inline">
                                                                        @csrf
                                                                        <input type="hidden" name="program_id" value="{{ $choiceProg->id }}">
                                                                        <button type="submit" class="btn btn-sm btn-outline-primary fw-bold assign-btn" data-program-name="{{ $choiceProg->name }}">
                                                                            <i class="bi bi-person-check me-1"></i>Select & Assign
                                                                        </button>
                                                                    </form>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @else
                                                    <tr>
                                                        <td colspan="6" class="text-center text-muted py-3">No specific program choice records found. Please use the dropdown below to assign a programme.</td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- Manual Dropdown Selector -->
                                    <div class="p-3 bg-light border-top">
                                        <form action="{{ route('admin.applications.update-program', $application->id) }}" method="POST">
                                            @csrf
                                            <div class="row align-items-center g-2">
                                                <div class="col-md-7">
                                                    <label class="form-label small fw-bold mb-1 text-dark">Assign Any University Programme:</label>
                                                    <select name="program_id" class="form-select form-select-sm" required>
                                                        <option value="">Choose University Programme...</option>
                                                        @foreach($allPrograms as $prog)
                                                            <option value="{{ $prog->id }}" {{ $application->program_id == $prog->id ? 'selected' : '' }}>
                                                                {{ $prog->name }} ({{ $prog->code }}) &mdash; {{ $prog->available_slots }} slots available
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-5 d-flex align-items-end gap-2 mt-md-4">
                                                    <button type="submit" class="btn btn-sm btn-primary fw-bold px-3" style="border-radius: 6px;">
                                                        <i class="bi bi-save me-1"></i>Confirm Programme Assignment
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between align-items-center">
                                    <button type="button" class="btn btn-outline-secondary btn-sm switch-tab-btn" data-target-tab="#step2-tab">
                                        <i class="bi bi-arrow-left me-1"></i>Back to Step 2
                                    </button>
                                    @if($step3Complete)
                                        <button type="button" class="btn btn-primary fw-bold px-4 py-2 switch-tab-btn" data-target-tab="#step4-tab">
                                            Proceed to Step 4: Admission Confirmation <i class="bi bi-arrow-right ms-1"></i>
                                        </button>
                                    @endif
                                </div>
                            @endif
                        </div>

                        <!-- ========================================== -->
                        <!-- STEP 4 PANE: ADMISSION CONFIRMATION        -->
                        <!-- ========================================== -->
                        <div class="tab-pane fade {{ $currentStep === 4 ? 'show active' : '' }}" id="step4-pane" role="tabpanel" aria-labelledby="step4-tab">
                            <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4 flex-wrap gap-2">
                                <div>
                                    <h4 class="fw-bold text-dark mb-1">
                                        <i class="bi bi-mortarboard text-primary me-2"></i>Step 4: Admission Confirmation
                                    </h4>
                                    <p class="text-muted small mb-0">Final stage: Confirm admission, issue official Student Number, activate account, and send Admission Letter.</p>
                                </div>
                                @if($step4Complete)
                                    <span class="badge bg-success py-2 px-3 fs-6">
                                        <i class="bi bi-mortarboard-fill me-1"></i>OFFICIALLY ADMITTED
                                    </span>
                                @endif
                            </div>

                            @if(!$canConfirmAdmission && !$step4Complete)
                                <div class="alert alert-danger border-0 shadow-sm p-4 text-center my-4" style="border-radius: 10px;">
                                    <div class="rounded-circle bg-danger text-white d-inline-flex align-items-center justify-content-center mb-2" style="width: 50px; height: 50px;">
                                        <i class="bi bi-lock-fill fs-4"></i>
                                    </div>
                                    <h5 class="fw-bold text-danger mb-1">Step 4 is Locked</h5>
                                    <p class="text-muted small mx-auto mb-3" style="max-width: 500px;">
                                        Strict Admission Order: You cannot confirm admission before all prerequisite steps are verified and completed.
                                    </p>
                                    <div class="d-inline-flex flex-column align-items-start bg-white p-3 border rounded shadow-sm text-start mb-3">
                                        <div class="small mb-1">
                                            Step 1: Payment &mdash; 
                                            @if($step1Complete)
                                                <span class="text-success fw-bold"><i class="bi bi-check-circle me-1"></i>Verified</span>
                                            @else
                                                <span class="text-danger fw-bold"><i class="bi bi-x-circle me-1"></i>Missing</span>
                                            @endif
                                        </div>
                                        <div class="small mb-1">
                                            Step 2: Document Preview &mdash; 
                                            @if($step2Complete)
                                                <span class="text-success fw-bold"><i class="bi bi-check-circle me-1"></i>Verified</span>
                                            @else
                                                <span class="text-danger fw-bold"><i class="bi bi-x-circle me-1"></i>Missing</span>
                                            @endif
                                        </div>
                                        <div class="small">
                                            Step 3: Programme Assignment &mdash; 
                                            @if($step3Complete)
                                                <span class="text-success fw-bold"><i class="bi bi-check-circle me-1"></i>Assigned</span>
                                            @else
                                                <span class="text-danger fw-bold"><i class="bi bi-x-circle me-1"></i>Missing</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div>
                                        @if(!$step1Complete)
                                            <button type="button" class="btn btn-sm btn-outline-danger switch-tab-btn" data-target-tab="#step1-tab">
                                                Go to Step 1: Payment
                                            </button>
                                        @elseif(!$step2Complete)
                                            <button type="button" class="btn btn-sm btn-outline-danger switch-tab-btn" data-target-tab="#step2-tab">
                                                Go to Step 2: Document Preview
                                            </button>
                                        @else
                                            <button type="button" class="btn btn-sm btn-outline-danger switch-tab-btn" data-target-tab="#step3-tab">
                                                Go to Step 3: Programme Assignment
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            @elseif($step4Complete)
                                <!-- Admitted Success Banner -->
                                <div class="card border-0 shadow-sm p-4 text-center my-3 bg-light" style="border-radius: 12px; border-top: 4px solid #198754 !important;">
                                    <div class="card-body">
                                        <div class="rounded-circle bg-success text-white d-inline-flex align-items-center justify-content-center shadow mb-3" style="width: 70px; height: 70px;">
                                            <i class="bi bi-mortarboard-fill fs-2"></i>
                                        </div>
                                        <h3 class="fw-bold text-success mb-2">Student Officially Admitted! 🎉</h3>
                                        <p class="text-muted mx-auto mb-4" style="max-width: 580px;">
                                            Admission process completed. All 4 stages (Payment, Document Verification, Programme Assignment, and Admission Confirmation) have been successfully fulfilled.
                                        </p>
                                        <div class="d-inline-flex flex-column align-items-start bg-white p-3 text-start border shadow-sm mb-4" style="border-radius: 10px; width: 100%; max-width: 480px;">
                                            <div class="small mb-1 text-dark"><strong>Student Reg No:</strong> <span class="badge bg-primary fs-6">{{ $application->student_number }}</span></div>
                                            <div class="small mb-1 text-dark"><strong>Admission No:</strong> <span class="badge bg-secondary fs-6">{{ $application->admission_number }}</span></div>
                                            <div class="small mb-1 text-dark"><strong>Programme:</strong> {{ $application->programRecord->name ?? $application->program }}</div>
                                            <div class="small text-dark"><strong>Admitted On:</strong> {{ $application->admitted_at ? $application->admitted_at->format('M d, Y H:i') : 'Recorded' }}</div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <!-- Ready to Confirm Admission Form -->
                                <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; background: linear-gradient(135deg, #ffffff 0%, #f0fdf4 100%); border-top: 4px solid #198754 !important;">
                                    <div class="card-body p-4">
                                        <div class="d-flex align-items-center mb-4">
                                            <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center me-3 shadow-sm" style="width: 54px; height: 54px;">
                                                <i class="bi bi-check2-all fs-2"></i>
                                            </div>
                                            <div>
                                                <h4 class="fw-bold text-success mb-1">All Prerequisites Satisfied!</h4>
                                                <p class="text-muted small mb-0">Payment is approved, documents are verified against application details, and official programme is assigned.</p>
                                            </div>
                                        </div>

                                        <!-- Verification Summary Checklist -->
                                        <div class="row g-3 mb-4">
                                            <div class="col-md-4">
                                                <div class="card border-0 bg-white p-3 shadow-sm h-100" style="border-radius: 8px;">
                                                    <div class="text-success small fw-bold mb-1"><i class="bi bi-check-circle-fill me-1"></i>Step 1: Payment Approved</div>
                                                    <div class="text-dark fw-bold fs-6">{{ $registrationFeeCurrency }} {{ number_format($admissionFeeAmount, 2) }}</div>
                                                    <small class="text-muted">Ref: {{ $application->payment_ref }}</small>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="card border-0 bg-white p-3 shadow-sm h-100" style="border-radius: 8px;">
                                                    <div class="text-success small fw-bold mb-1"><i class="bi bi-check-circle-fill me-1"></i>Step 2: Documents Verified</div>
                                                    <div class="text-dark fw-bold fs-6">Credentials Certified</div>
                                                    <small class="text-muted">Verified by {{ $application->documentsVerifier?->name ?? 'Admissions Staff' }}</small>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="card border-0 bg-white p-3 shadow-sm h-100" style="border-radius: 8px;">
                                                    <div class="text-success small fw-bold mb-1"><i class="bi bi-check-circle-fill me-1"></i>Step 3: Programme Assigned</div>
                                                    <div class="text-dark fw-bold fs-6 text-truncate">{{ $application->programRecord->name ?? $application->program }}</div>
                                                    <small class="text-muted">Dept: {{ $application->programRecord->department->name ?? 'Academic' }}</small>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Final Action Form -->
                                        <form action="{{ route('admin.applications.approve', $application->id) }}" method="POST">
                                            @csrf
                                            <div class="mb-3">
                                                <label for="admission_notes" class="form-label small fw-bold text-dark">Official Admission Notes / Special Conditions (Optional)</label>
                                                <textarea name="notes" id="admission_notes" class="form-control" rows="2" placeholder="e.g. Admitted on Merit for Academic Intake 2026/2027. Full-time study."></textarea>
                                            </div>

                                            <div class="form-check mb-4">
                                                <input class="form-check-input" type="checkbox" id="confirm_admission_check" required>
                                                <label class="form-check-label small text-dark fw-semibold" for="confirm_admission_check">
                                                    I hereby certify and confirm the official admission of this candidate to JBI University.
                                                </label>
                                            </div>

                                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                                <button type="button" class="btn btn-outline-secondary btn-sm switch-tab-btn" data-target-tab="#step3-tab">
                                                    <i class="bi bi-arrow-left me-1"></i>Back to Step 3
                                                </button>
                                                <button type="submit" class="btn btn-success btn-lg fw-bold px-4 py-2.5 confirm-submit" data-confirm="Approve and officially admit this student? Official Student Number will be issued.">
                                                    <i class="bi bi-mortarboard-fill me-2"></i>Confirm & Officially Admit Student
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @endif
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- REJECT APPLICATION MODAL -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 12px;">
            <form action="{{ route('admin.applications.reject', $application->id) }}" method="POST">
                @csrf
                <div class="modal-header bg-danger text-white py-3">
                    <h5 class="modal-title fw-bold"><i class="bi bi-x-circle me-2"></i>Reject Application</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="small text-muted mb-3">Rejecting this application will notify the applicant and mark their status as Not Admitted. Please provide a clear reason.</p>
                    <div class="mb-3">
                        <label for="rejection_notes" class="form-label small fw-bold text-dark">Reason for Rejection <span class="text-danger">*</span></label>
                        <textarea name="notes" id="rejection_notes" class="form-control" rows="3" required placeholder="Specify why the application cannot be accepted (e.g. Did not meet minimum GPA requirements, incomplete credentials)..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2 px-4">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger btn-sm fw-bold confirm-submit" data-confirm="Are you sure you want to reject this application?">
                        <i class="bi bi-x-circle me-1"></i>Confirm Rejection
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ================================================================================== -->
<!-- FLICKER-FREE DOCUMENT PREVIEW MODAL OVERLAY (Vanilla Overlay, No Bootstrap JS)    -->
<!-- ================================================================================== -->
<style>
    #adminDocPreviewBackdrop {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.65);
        z-index: 1055;
        display: none;
    }
    #adminDocPreviewBackdrop.show { display: block; }

    #adminDocPreviewModal {
        position: fixed;
        inset: 0;
        z-index: 1060;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 1.5rem;
    }
    #adminDocPreviewModal.show { display: flex; }

    #adminDocPreviewModal .modal-dialog {
        width: 100%;
        max-width: 1100px;
        margin: 0;
    }
</style>

<div id="adminDocPreviewBackdrop"></div>
<div id="adminDocPreviewModal" tabindex="-1" aria-hidden="true" role="dialog" aria-modal="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content border-0 shadow-lg overflow-hidden" style="border-radius: 12px;">
            <div class="modal-header text-white py-3 px-4" style="background: linear-gradient(135deg, #0f2942 0%, #1e3a8a 100%);">
                <div class="d-flex align-items-center">
                    <i class="bi bi-file-earmark-pdf fs-4 me-2 text-warning"></i>
                    <div>
                        <h5 class="modal-title fw-bold mb-0 text-white" id="adminDocPreviewTitle">Document Preview</h5>
                        <small class="text-white-50" id="adminDocPreviewSubtitle">Official Applicant Document</small>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a id="adminDocOpenNewTabBtn" href="#" target="_blank" class="btn btn-sm btn-outline-light px-3 py-1.5" style="border-radius: 6px;">
                        <i class="bi bi-box-arrow-up-right me-1"></i>Open Full Screen
                    </a>
                    <a id="adminDocDownloadHeaderBtn" href="#" download class="btn btn-sm btn-warning fw-bold px-3 py-1.5 text-dark" style="border-radius: 6px;">
                        <i class="bi bi-download me-1"></i>Download
                    </a>
                    <button type="button" class="btn-close btn-close-white ms-2" id="adminDocPreviewCloseBtn" aria-label="Close"></button>
                </div>
            </div>
            <div class="modal-body p-0 bg-light position-relative" style="height: 75vh; min-height: 580px; overflow: hidden; background-color: #0f172a !important;">
                <div id="adminDocPreviewContainer" class="w-100 h-100 position-relative" style="overflow: hidden;">
                    <div class="position-absolute top-50 start-50 translate-middle text-primary fw-bold">Loading document preview...</div>
                </div>
            </div>
            <div class="modal-footer bg-white py-2.5 px-4 d-flex justify-content-between align-items-center">
                <small class="text-muted"><i class="bi bi-shield-check text-success me-1"></i>Verified Applicant Document Record</small>
                <button type="button" class="btn btn-secondary px-4 fw-bold" id="adminDocPreviewCloseBtn2" style="border-radius: 6px;">Close Preview</button>
            </div>
        </div>
    </div>
</div>

<script>
    function resetAdminDocPreviewContainer() {
        const container = document.getElementById('adminDocPreviewContainer');
        if (container) {
            container.innerHTML = '<div class="position-absolute top-50 start-50 translate-middle text-primary fw-bold">Loading document preview...</div>';
        }
    }

    function closeAdminDocPreview() {
        const modalEl = document.getElementById('adminDocPreviewModal');
        const backdropEl = document.getElementById('adminDocPreviewBackdrop');
        if (!modalEl || !modalEl.classList.contains('show')) {
            return;
        }
        modalEl.classList.remove('show');
        backdropEl.classList.remove('show');
        document.body.style.overflow = '';
        resetAdminDocPreviewContainer();
    }

    function openAdminDocPreview(url, title, ext) {
        if (!url) return;

        document.getElementById('adminDocPreviewTitle').innerText = title || 'Document Preview';
        document.getElementById('adminDocPreviewSubtitle').innerText = 'Format: ' + (ext || 'Document').toUpperCase();

        document.getElementById('adminDocOpenNewTabBtn').href = url;
        document.getElementById('adminDocDownloadHeaderBtn').href = url;

        const container = document.getElementById('adminDocPreviewContainer');
        const cleanExt = (ext || '').toLowerCase().trim();

        if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(cleanExt)) {
            const img = document.createElement('img');
            img.src = url;
            img.className = 'img-fluid rounded shadow';
            img.style.maxHeight = '100%';
            img.style.maxWidth = '100%';
            img.style.objectFit = 'contain';

            const wrap = document.createElement('div');
            wrap.className = 'w-100 h-100 d-flex align-items-center justify-content-center p-3';
            wrap.style.background = '#0f172a';
            wrap.appendChild(img);

            container.innerHTML = '';
            container.appendChild(wrap);
        } else if (cleanExt === 'pdf') {
            const iframe = document.createElement('iframe');
            iframe.src = url + '#toolbar=1&navpanes=0&scrollbar=1';
            iframe.className = 'w-100 h-100';
            iframe.style.minHeight = '580px';
            iframe.style.border = 'none';
            iframe.style.display = 'block';
            iframe.style.width = '100%';
            iframe.style.height = '100%';
            iframe.style.background = '#ffffff';

            container.innerHTML = '';
            container.appendChild(iframe);
        } else {
            container.innerHTML = '<div class="w-100 h-100 d-flex flex-column align-items-center justify-content-center p-5 text-center" style="background: #f8fafc;"><i class="bi bi-file-earmark-word fs-1 text-primary d-block mb-3"></i><h5 class="text-dark fw-bold mb-2">Preview Not Available Inline</h5><p class="text-muted fs-6 mb-4">Direct inline preview is not supported for <strong>' + cleanExt.toUpperCase() + '</strong> files.</p><div class="d-flex gap-2"><a href="' + url + '" target="_blank" class="btn btn-outline-primary px-4 py-2 fw-bold" style="border-radius: 6px;"><i class="bi bi-box-arrow-up-right me-2"></i>Open File</a><a href="' + url + '" download class="btn btn-primary px-4 py-2 fw-bold" style="border-radius: 6px;"><i class="bi bi-download me-2"></i>Download File</a></div></div>';
        }

        const modalEl = document.getElementById('adminDocPreviewModal');
        const backdropEl = document.getElementById('adminDocPreviewBackdrop');

        if (modalEl.classList.contains('show')) return;
        modalEl.classList.add('show');
        backdropEl.classList.add('show');
        document.body.style.overflow = 'hidden';
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Portal modal and backdrop directly under <body> to prevent transformed-parent positioning issues
        var previewModalEl = document.getElementById('adminDocPreviewModal');
        var previewBackdropEl = document.getElementById('adminDocPreviewBackdrop');
        if (previewModalEl && previewModalEl.parentElement !== document.body) {
            document.body.appendChild(previewBackdropEl);
            document.body.appendChild(previewModalEl);
        }

        // Preview button click listeners
        document.querySelectorAll('.preview-doc-btn').forEach(function (btn) {
            btn.addEventListener('click', function () {
                openAdminDocPreview(this.dataset.url, this.dataset.title, this.dataset.ext);
            });
        });

        // Close handlers
        const closeBtn1 = document.getElementById('adminDocPreviewCloseBtn');
        const closeBtn2 = document.getElementById('adminDocPreviewCloseBtn2');
        const backdropEl = document.getElementById('adminDocPreviewBackdrop');
        if (closeBtn1) closeBtn1.addEventListener('click', closeAdminDocPreview);
        if (closeBtn2) closeBtn2.addEventListener('click', closeAdminDocPreview);
        if (backdropEl) backdropEl.addEventListener('click', closeAdminDocPreview);

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') closeAdminDocPreview();
        });

        // Switch tab buttons (e.g. Next / Previous buttons)
        document.querySelectorAll('.switch-tab-btn').forEach(function (btn) {
            btn.addEventListener('click', function () {
                const targetTabSelector = this.dataset.targetTab;
                const tabEl = document.querySelector(targetTabSelector);
                if (tabEl && !tabEl.hasAttribute('disabled')) {
                    const tabTrigger = new bootstrap.Tab(tabEl);
                    tabTrigger.show();
                    window.scrollTo({ top: document.getElementById('admissionWizardTabs').offsetTop - 20, behavior: 'smooth' });
                }
            });
        });

        // Confirm submit handlers
        document.querySelectorAll('.confirm-submit').forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                if (!confirm(this.dataset.confirm || 'Are you sure?')) {
                    e.preventDefault();
                }
            });
        });

        // Assign program confirmation
        document.querySelectorAll('.assign-btn').forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                const name = this.dataset.programName || 'this programme';
                if (!confirm('Assign ' + name + ' to this applicant?')) {
                    e.preventDefault();
                }
            });
        });
    });
</script>
@endsection