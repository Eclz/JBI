@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="bi bi-file-earmark-person me-2"></i>
                            Application Details - {{ $application->first_name }} {{ $application->last_name }}
                        </h4>
                        <span class="badge
                            @if($application->status === 'pending') bg-warning
                            @elseif($application->status === 'approved') bg-info
                            @elseif($application->status === 'rejected') bg-danger
                            @elseif($application->status === 'admitted') bg-success
                            @else bg-secondary @endif fs-6">
                            {{ ucfirst($application->status) }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Left Column -->
                        <div class="col-md-8">
                            <!-- Personal Information -->
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0"><i class="bi bi-person me-2"></i>Personal Information</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>Full Name:</strong> {{ $application->first_name }} {{ $application->last_name }}</p>
                                            <p><strong>Email:</strong> {{ $application->email }}</p>
                                            <p><strong>Phone:</strong> {{ $application->phone }}</p>
                                            <p><strong>Date of Birth:</strong> {{ $application->date_of_birth ? $application->date_of_birth->format('M d, Y') : 'Not provided' }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Gender:</strong> {{ ucfirst($application->gender ?? 'Not specified') }}</p>
                                            <p><strong>Address:</strong><br>{{ $application->address }}</p>
                                            <p><strong>Type:</strong> <span class="badge bg-primary">{{ ucfirst($application->type) }}</span></p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Academic Information -->
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0"><i class="bi bi-mortarboard me-2"></i>Academic Information</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>Program:</strong> {{ $application->program->name ?? $application->program ?? 'Not specified' }}</p>
                                            <p><strong>Program Level:</strong> {{ $application->program->level->name ?? 'Not specified' }}</p>
                                            <p><strong>Department:</strong> {{ $application->program->department->name ?? 'Not specified' }}</p>
                                            <p><strong>Previous Institution:</strong> {{ $application->previous_school }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            @if($application->admission_number)
                                            <p><strong>Admission Number:</strong> <span class="badge bg-success">{{ $application->admission_number }}</span></p>
                                            @endif
                                            @if($application->student_number)
                                            <p><strong>Student Number:</strong> <span class="badge bg-info">{{ $application->student_number }}</span></p>
                                            @endif
                                            <p><strong>Qualifications:</strong><br>
                                                <small class="text-muted">{{ $application->previous_qualification ?? 'Not provided' }}</small>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Guardian Information (for students) -->
                            @if($application->type === 'student')
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0"><i class="bi bi-people me-2"></i>Guardian Information</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>Guardian Name:</strong> {{ $application->guardian_name }}</p>
                                            <p><strong>Guardian Phone:</strong> {{ $application->guardian_phone }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Guardian Email:</strong> {{ $application->guardian_email ?? 'Not provided' }}</p>
                                            <p><strong>Relationship:</strong> {{ $application->guardian_relationship ?? 'Not specified' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif

                            <!-- Documents -->
                            @if($application->documents && is_array($application->documents) && count($application->documents) > 0)
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0"><i class="bi bi-file-earmark-text me-2"></i>Submitted Documents</h5>
                                </div>
                                <div class="card-body">
                                    @foreach($application->documents as $index => $document)
                                        <a href="{{ asset('storage/' . $document) }}" target="_blank" class="btn btn-outline-primary btn-sm me-2 mb-2">
                                            <i class="bi bi-download me-1"></i>Document {{ $index + 1 }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                            @endif

                            <!-- Payment Information -->
                            @if($application->status === 'approved' || $application->status === 'admitted')
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0"><i class="bi bi-credit-card me-2"></i>Payment Information</h5>
                                </div>
                                <div class="card-body">
                                    @if($application->payment_proof)
                                        <p><strong>Payment Proof:</strong> <span class="badge bg-success">Submitted</span></p>
                                        <a href="{{ asset('storage/' . $application->payment_proof) }}" target="_blank" class="btn btn-outline-primary btn-sm">
                                            <i class="bi bi-eye me-1"></i>View Payment Proof
                                        </a>
                                        @if($application->payment_verified_at)
                                            <p class="mt-2"><strong>Verified At:</strong> {{ $application->payment_verified_at->format('M d, Y H:i') }}</p>
                                        @else
                                            <span class="badge bg-warning ms-2">Pending Verification</span>
                                        @endif
                                    @else
                                        <p class="text-muted">Payment proof not yet submitted</p>
                                    @endif
                                </div>
                            </div>
                            @endif

                            <!-- Notes -->
                            @if($application->review_notes)
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0"><i class="bi bi-sticky me-2"></i>Reviewer Notes</h5>
                                </div>
                                <div class="card-body">
                                    <p>{{ $application->review_notes }}</p>
                                </div>
                            </div>
                            @endif
                        </div>

                        <!-- Right Column - Actions -->
                        <div class="col-md-4">
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Application Details</h5>
                                </div>
                                <div class="card-body">
                                    <p><strong>Application ID:</strong> {{ $application->id }}</p>
                                    <p><strong>Submitted:</strong> {{ $application->created_at->format('M d, Y H:i') }}</p>
                                    <p><strong>Last Updated:</strong> {{ $application->updated_at->format('M d, Y H:i') }}</p>
                                    @if($application->reviewed_at)
                                    <p><strong>Reviewed:</strong> {{ $application->reviewed_at->format('M d, Y H:i') }}</p>
                                    @endif
                                </div>
                            </div>

                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0"><i class="bi bi-check2-square me-2"></i>Approval Readiness</h5>
                                </div>
                                <div class="card-body">
                                    @php
                                        $readiness = $approvalReadiness ?? ['score' => 0, 'ready' => false, 'checks' => []];
                                        $readinessColor = $readiness['ready'] ? 'success' : ($readiness['score'] >= 60 ? 'warning' : 'danger');
                                    @endphp
                                    <div class="mb-2">
                                        <span class="badge bg-{{ $readinessColor }}">{{ $readiness['score'] }}%</span>
                                    </div>
                                    <ul class="list-group list-group-flush">
                                        @foreach(($readiness['weighted_checks'] ?? []) as $check)
                                            <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                                <span>{{ $check['label'] }}</span>
                                                @if($check['passed'])
                                                    <span class="badge bg-success">OK</span>
                                                @else
                                                    <span class="badge bg-danger">Missing ({{ $check['weight'] }}%)</span>
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>

                            <!-- Actions -->
                            @if($application->status === 'pending')
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0"><i class="bi bi-gear me-2"></i>Actions</h5>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('admin.applications.approve', $application->id) }}" method="POST" class="mb-3">
                                        @csrf
                                        <div class="mb-3">
                                            <label for="approval_notes" class="form-label">Approval Notes (Optional)</label>
                                            <textarea name="notes" id="approval_notes" class="form-control" rows="3"></textarea>
                                        </div>
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" name="force_approve" id="force_approve" value="1">
                                            <label class="form-check-label" for="force_approve">
                                                Force approval if checklist is incomplete
                                            </label>
                                        </div>
                                        <button type="submit" class="btn btn-success w-100" onclick="return confirm('Approve this application?')">
                                            <i class="bi bi-check-circle me-1"></i>Approve
                                        </button>
                                    </form>

                                    <form action="{{ route('admin.applications.reject', $application->id) }}" method="POST">
                                        @csrf
                                        <div class="mb-3">
                                            <label for="rejection_notes" class="form-label">Rejection Reason <span class="text-danger">*</span></label>
                                            <textarea name="notes" id="rejection_notes" class="form-control" rows="3" required></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-danger w-100" onclick="return confirm('Reject this application?')">
                                            <i class="bi bi-x-circle me-1"></i>Reject
                                        </button>
                                    </form>
                                </div>
                            </div>
                            @elseif($application->status === 'approved' && $application->payment_proof && !$application->payment_verified_at)
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0"><i class="bi bi-check2-square me-2"></i>Verify Payment</h5>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('admin.applications.verify-payment', $application->id) }}" method="POST">
                                        @csrf
                                        <div class="mb-3">
                                            <label for="verify_notes" class="form-label">Verification Notes (Optional)</label>
                                            <textarea name="notes" id="verify_notes" class="form-control" rows="3"></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-primary w-100" onclick="return confirm('Verify payment and send admission letter?')">
                                            <i class="bi bi-check-circle me-1"></i>Verify Payment & Send Admission Letter
                                        </button>
                                    </form>
                                </div>
                            </div>
                            @endif

                            <div class="card">
                                <div class="card-body">
                                    <a href="{{ route('admin.applications.index') }}" class="btn btn-outline-secondary w-100">
                                        <i class="bi bi-arrow-left me-1"></i>Back to Applications
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
