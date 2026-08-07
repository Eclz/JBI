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

                            <!-- Applied Courses / Programme Choices & Live Slots Tracker -->
                            <div class="card mb-4 border-0 shadow-sm overflow-hidden" style="border-radius: 10px;">
                                <div class="card-header bg-white border-bottom border-primary border-2 py-3 d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0 text-primary fw-bold">
                                        <i class="bi bi-journal-bookmark-fill me-2"></i>Applicant's Course & Programme Choices
                                    </h5>
                                    <span class="badge bg-primary px-3 py-2" style="border-radius: 6px;">
                                        {{ isset($programChoices) ? count($programChoices) : 1 }} Choice(s) Submitted
                                    </span>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Preference Rank</th>
                                                    <th>Code</th>
                                                    <th>Programme Name</th>
                                                    <th>Faculty / Department</th>
                                                    <th>Available Slots Tracker</th>
                                                    <th class="text-end">Assign Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if(isset($programChoices) && count($programChoices) > 0)
                                                    @foreach($programChoices as $rankIndex => $choiceProg)
                                                        @php
                                                            $isAssigned = ($application->program_id == $choiceProg->id) || ($application->program === $choiceProg->name);
                                                            $rankLabel = match($rankIndex) {
                                                                0 => '1st Choice (Primary)',
                                                                1 => '2nd Choice',
                                                                2 => '3rd Choice',
                                                                3 => '4th Choice',
                                                                4 => '5th Choice',
                                                                5 => '6th Choice',
                                                                default => ($rankIndex + 1) . 'th Choice'
                                                            };
                                                        @endphp
                                                        <tr class="{{ $isAssigned ? 'table-success bg-opacity-10' : '' }}">
                                                            <td>
                                                                <span class="badge {{ $rankIndex === 0 ? 'bg-primary' : 'bg-secondary' }}">
                                                                    {{ $rankLabel }}
                                                                </span>
                                                            </td>
                                                            <td><code class="fw-bold bg-light px-2 py-1 rounded text-dark">{{ $choiceProg->code }}</code></td>
                                                            <td>
                                                                <strong class="text-dark">{{ $choiceProg->name }}</strong>
                                                                <div class="small text-muted">{{ $choiceProg->level->name ?? 'N/A' }}</div>
                                                            </td>
                                                            <td>{{ $choiceProg->department->name ?? 'N/A' }}</td>
                                                            <td>
                                                                <div class="d-flex align-items-center">
                                                                    <div class="me-2">
                                                                        <span class="badge {{ $choiceProg->is_full ? 'bg-danger' : 'bg-success' }} px-2 py-1">
                                                                            <i class="bi bi-people-fill me-1"></i>
                                                                            {{ $choiceProg->available_slots }} / {{ $choiceProg->capacity }} Slots Left
                                                                        </span>
                                                                    </div>
                                                                    <small class="text-muted">({{ $choiceProg->enrolled_count }} enrolled)</small>
                                                                </div>
                                                            </td>
                                                            <td class="text-end">
                                                                @if($isAssigned)
                                                                    <span class="badge bg-success px-3 py-1 fs-6">
                                                                        <i class="bi bi-check-circle-fill me-1"></i>ASSIGNED TO APPLICANT
                                                                    </span>
                                                                @else
                                                                    <form action="{{ route('admin.applications.update-program', $application->id) }}" method="POST" class="d-inline">
                                                                        @csrf
                                                                        <input type="hidden" name="program_id" value="{{ $choiceProg->id }}">
                                                                        <button type="submit" class="btn btn-sm btn-outline-primary fw-bold" onclick="return confirm('Assign {{ $choiceProg->name }} to this applicant?')">
                                                                            <i class="bi bi-person-check me-1"></i>Select & Assign
                                                                        </button>
                                                                    </form>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @else
                                                    <tr>
                                                        <td colspan="6" class="text-center text-muted py-3">
                                                            Primary Programme: <strong>{{ $application->programRecord->name ?? $application->program ?? 'Not specified' }}</strong>
                                                        </td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- Admin Change Course Selector Form -->
                                    <div class="p-3 bg-light border-top">
                                        <form action="{{ route('admin.applications.update-program', $application->id) }}" method="POST">
                                            @csrf
                                            <div class="row align-items-center g-2">
                                                <div class="col-md-7">
                                                    <label class="form-label small fw-bold mb-1 text-dark">Assign Different University Programme for Applicant:</label>
                                                    <select name="program_id" class="form-select form-select-sm" required>
                                                        <option value="">Select University Programme...</option>
                                                        @foreach($allPrograms as $prog)
                                                            <option value="{{ $prog->id }}" {{ $application->program_id == $prog->id ? 'selected' : '' }}>
                                                                {{ $prog->name }} ({{ $prog->code }}) &mdash; {{ $prog->available_slots }} slots available
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-5 d-flex align-items-end gap-2 mt-md-4">
                                                    <button type="submit" class="btn btn-sm btn-primary fw-bold px-3" style="border-radius: 6px;">
                                                        <i class="bi bi-save me-1"></i>Update Assigned Programme
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
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
                                            <p><strong>Assigned Program:</strong> <span class="badge bg-primary fs-6">{{ $application->programRecord->name ?? $application->program ?? 'Not specified' }}</span></p>
                                            <p><strong>Program Level:</strong> {{ $application->programRecord->level->name ?? 'Not specified' }}</p>
                                            <p><strong>Department:</strong> {{ $application->programRecord->department->name ?? 'Not specified' }}</p>
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
                            <div class="card mb-3 border-0 shadow-sm">
                                <div class="card-header bg-white border-bottom border-primary border-2 py-3">
                                    <h5 class="mb-0 text-primary fw-bold"><i class="bi bi-file-earmark-text me-2"></i>Submitted Applicant Documents ({{ count($application->documents) }})</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        @foreach($application->documents as $index => $document)
                                            @php
                                                $fileUrl = asset('storage/' . $document);
                                                $ext = strtolower(pathinfo($document, PATHINFO_EXTENSION));
                                                $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                                $isPdf = $ext === 'pdf';
                                            @endphp
                                            <div class="col-md-6 col-lg-4">
                                                <div class="card h-100 border p-3 text-center bg-light">
                                                    <div class="mb-2 fs-1 text-primary">
                                                        @if($isImage)
                                                            <i class="bi bi-file-earmark-image"></i>
                                                        @elseif($isPdf)
                                                            <i class="bi bi-file-earmark-pdf text-danger"></i>
                                                        @else
                                                            <i class="bi bi-file-earmark-word text-info"></i>
                                                        @endif
                                                    </div>
                                                    <h6 class="fw-bold mb-1 text-truncate">Document {{ $index + 1 }}</h6>
                                                    <small class="text-muted d-block mb-3 text-uppercase">{{ $ext ?: 'FILE' }} Document</small>

                                                    <div class="d-flex justify-content-center gap-2">
                                                        <button type="button" class="btn btn-sm btn-primary" onclick="openAdminDocPreview('{{ $fileUrl }}', 'Document {{ $index + 1 }}', '{{ $ext }}')">
                                                            <i class="bi bi-eye me-1"></i>Preview
                                                        </button>
                                                        <a href="{{ $fileUrl }}" target="_blank" download class="btn btn-sm btn-outline-secondary">
                                                            <i class="bi bi-download"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            @endif

                            <!-- Document Preview Modal -->
                            <div class="modal fade" id="adminDocPreviewModal" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-xl modal-dialog-centered">
                                    <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
                                        <div class="modal-header bg-dark text-white py-3">
                                            <h5 class="modal-title fw-bold" id="adminDocPreviewTitle"><i class="bi bi-eye me-2"></i>Document Preview</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body p-0 text-center bg-light" style="min-height: 550px;">
                                            <div id="adminDocPreviewContainer" class="w-100 h-100 d-flex align-items-center justify-content-center py-4">
                                                <div class="spinner-border text-primary" role="status"></div>
                                            </div>
                                        </div>
                                        <div class="modal-footer bg-light">
                                            <a id="adminDocDownloadBtn" href="#" target="_blank" download class="btn btn-primary fw-bold">
                                                <i class="bi bi-download me-1"></i>Download Original File
                                            </a>
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <script>
                                function openAdminDocPreview(url, title, ext) {
                                    document.getElementById('adminDocPreviewTitle').innerHTML = '<i class="bi bi-eye me-2"></i>' + title;
                                    document.getElementById('adminDocDownloadBtn').href = url;
                                    const container = document.getElementById('adminDocPreviewContainer');

                                    if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(ext.toLowerCase())) {
                                        container.innerHTML = '<img src="' + url + '" class="img-fluid rounded shadow-sm" style="max-height: 550px; object-fit: contain;">';
                                    } else if (ext.toLowerCase() === 'pdf') {
                                        container.innerHTML = '<iframe src="' + url + '" class="w-100" style="height: 580px; border: none;"></iframe>';
                                    } else {
                                        container.innerHTML = '<div class="p-5 text-center"><i class="bi bi-file-earmark-word fs-1 text-primary d-block mb-3"></i><p class="text-muted">Direct preview not available for ' + ext.toUpperCase() + ' files.</p><a href="' + url + '" target="_blank" class="btn btn-primary"><i class="bi bi-download me-1"></i>Download to View File</a></div>';
                                    }

                                    const modal = new bootstrap.Modal(document.getElementById('adminDocPreviewModal'));
                                    modal.show();
                                }
                            </script>

                            <!-- Payment Information -->
                            @if($application->status === 'approved' || $application->status === 'admitted')
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0"><i class="bi bi-credit-card me-2"></i>Payment Information</h5>
                                </div>
                                <div class="card-body">
                                    @if($application->payment_proof)
                                        <p><strong>Payment Proof:</strong> <span class="badge bg-success">Submitted</span></p>
                                        @php
                                            $proofUrl = asset('storage/' . $application->payment_proof);
                                            $proofExt = strtolower(pathinfo($application->payment_proof, PATHINFO_EXTENSION));
                                        @endphp
                                        <button type="button" class="btn btn-primary btn-sm me-2" onclick="openAdminDocPreview('{{ $proofUrl }}', 'Payment Proof Receipt', '{{ $proofExt }}')">
                                            <i class="bi bi-eye me-1"></i>Preview Payment Proof
                                        </button>
                                        <a href="{{ $proofUrl }}" target="_blank" class="btn btn-outline-secondary btn-sm">
                                            <i class="bi bi-download me-1"></i>Download
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
