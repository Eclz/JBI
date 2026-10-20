@extends('layouts.app')

@section('title', 'Student Applications')

@section('content')
@if(auth()->user()->hasPermission('applications', 'view') || auth()->user()->hasPermission('hr_onboarding', 'view'))
<div class="mx-4 mt-3"><a class="btn btn-outline-primary" href="{{ route('onboarding.index') }}">Onboarding: next actions and setup</a></div>
@endif
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Student Applications</h3>
                    <div class="card-tools">
                        <span class="badge badge-warning">{{ $applications->total() }} Total</span>
                    </div>
                </div>

                <!-- Filters -->
                <div class="card-body border-bottom">
                    <form method="GET" action="{{ route('admin.applications.index') }}" class="row g-3">
                        @if(request()->filled('status'))
                            <input type="hidden" name="status" value="{{ request('status') }}">
                        @endif
                        <div class="col-md-4">
                            <label for="search" class="form-label">Search Here</label>
                            <input type="text" name="search" class="form-control" placeholder="Search by name, email, phone..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-4">
                            <label for="type" class="form-label">Type</label>
                            <select name="type" id="type" class="form-select">
                                <option value="">All Types</option>
                                <option value="student" {{ request('type') === 'student' ? 'selected' : '' }}>Student</option>
                                <option value="faculty" {{ request('type') === 'faculty' ? 'selected' : '' }}>Faculty</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="program_id" class="form-label">Program</label>
                            <select name="program_id" id="program_id" class="form-select">
                                <option value="">All Programs</option>
                                @foreach($programs as $program)
                                    <option value="{{ $program->id }}" {{ request('program_id') == $program->id ? 'selected' : '' }}>
                                        {{ $program->name }} @if($program->level) ({{ $program->level->name }}) @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">Filter</button>
                            <a href="{{ route('admin.applications.index') }}" class="btn btn-secondary">Clear</a>
                        </div>
                    </form>
                    <div class="border-top mt-3 pt-3">
                        <ul class="nav nav-tabs">
                            <li class="nav-item">
                                <a class="nav-link {{ request()->filled('status') ? '' : 'active' }}" href="{{ route('admin.applications.index', request()->except('status')) }}">All</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request('status') === 'pending' ? 'active' : '' }}" href="{{ route('admin.applications.index', array_merge(request()->except('status'), ['status' => 'pending'])) }}">Pending</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request('status') === 'under_review' ? 'active' : '' }}" href="{{ route('admin.applications.index', array_merge(request()->except('status'), ['status' => 'under_review'])) }}">Under Review</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request('status') === 'approved' ? 'active' : '' }}" href="{{ route('admin.applications.index', array_merge(request()->except('status'), ['status' => 'approved'])) }}">Approved</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request('status') === 'admitted' ? 'active' : '' }}" href="{{ route('admin.applications.index', array_merge(request()->except('status'), ['status' => 'admitted'])) }}">Admitted</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request('status') === 'rejected' ? 'active' : '' }}" href="{{ route('admin.applications.index', array_merge(request()->except('status'), ['status' => 'rejected'])) }}">Rejected</a>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="card-body p-0">
                    @if($applications->count() > 0)
                        <form method="POST" action="{{ route('admin.applications.bulk-approve') }}">
                            @csrf
                            <div class="p-3 border-bottom bg-light d-flex flex-wrap gap-2 align-items-center">
                                <button type="submit" class="btn btn-sm btn-success">
                                    <i class="fas fa-check-double"></i> Bulk Approve Selected
                                </button>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="only_ready" id="bulkOnlyReady" value="1" checked>
                                    <label class="form-check-label" for="bulkOnlyReady">Only readiness-qualified</label>
                                </div>

                                <span class="small text-muted d-none d-lg-inline border-start ps-2" style="font-size: 0.75rem;">
                                    <i class="fas fa-shield-alt text-primary me-1"></i>Payment, documents and programme assignment are required.
                                </span>
                                <input type="text" class="form-control form-control-sm" name="notes" style="max-width: 260px;" placeholder="Optional bulk note">
                            </div>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th style="width: 40px;">
                                            <input type="checkbox" id="selectAllApps">
                                        </th>
                                        <th>Applicant</th>
                                        <th>Type</th>
                                        <th>Program</th>
                                        <th>Department</th>
                                        <th>Applied Date</th>
                                        <th>Status</th>
                                        <th>Readiness</th>
                                        <th>Payment Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($applications as $application)
                                        @php
                                            $isStudent = ($application->type === 'student');
                                            $isPaymentApproved = $application->isPaymentApproved();
                                            $hasCourseAssigned = $application->hasAssignedCourse();
                                            $canApprove = !$isStudent || ($isPaymentApproved && $hasCourseAssigned);

                                            $missingBlockers = [];
                                            if ($isStudent && !$hasCourseAssigned) $missingBlockers[] = 'No Course Assigned';
                                            if ($isStudent && !$isPaymentApproved) $missingBlockers[] = 'Payment Not Approved';
                                            $blockerText = implode(' & ', $missingBlockers);
                                        @endphp
                                        <tr>
                                            <td>
                                                @if($application->status === 'pending')
                                                    <input type="checkbox" class="app-checkbox" name="application_ids[]" value="{{ $application->id }}">
                                                @endif
                                            </td>
                                            <td>
                                                <div>
                                                    <div class="fw-bold">{{ $application->first_name }} {{ $application->last_name }}</div>
                                                    <small class="text-muted">{{ $application->email }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary">
                                                    {{ ucfirst($application->type) }}
                                                </span>
                                            </td>
                                            <td>
                                                <div>
                                                    @if($application->program_id && ($application->programRecord || $application->program))
                                                        <strong class="text-dark">{{ $application->programRecord->name ?? $application->program }}</strong>
                                                        <span class="badge bg-success-subtle text-success border border-success-subtle d-inline-block mt-0.5" style="font-size: 0.68rem;">Assigned</span>
                                                    @elseif($application->type === 'student')
                                                        <span class="badge bg-warning text-dark fw-bold" style="font-size: 0.75rem;">
                                                            <i class="bi bi-clock-history me-1"></i>Pending Manual Assignment
                                                        </span>
                                                    @else
                                                        <strong class="text-dark">{{ $application->department ?? 'N/A' }}</strong>
                                                    @endif

                                                    @if(is_array($application->program_choices) && count($application->program_choices) > 0)
                                                        <div class="mt-1">
                                                            <span class="badge bg-info text-dark" style="font-size: 0.7rem;">
                                                                <i class="bi bi-list-stars me-1"></i>{{ count($application->program_choices) }} Applied Choice(s)
                                                            </span>
                                                        </div>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>{{ $application->programRecord->department->name ?? 'N/A' }}</td>
                                            <td>{{ $application->created_at->format('M j, Y') }}</td>
                                            <td>
                                                @php
                                                    $statusColors = [
                                                        'pending' => 'warning',
                                                        'approved' => 'success',
                                                        'admitted' => 'success',
                                                        'rejected' => 'danger',
                                                        'payment_pending' => 'info',
                                                        'completed' => 'success'
                                                    ];
                                                    $color = $statusColors[$application->status] ?? 'secondary';
                                                @endphp
                                                <span class="badge bg-{{ $color }}">{{ ucfirst(str_replace('_', ' ', $application->status)) }}</span>
                                            </td>
                                            <td>
                                                @php
                                                    $readiness = $application->approval_readiness ?? ['score' => 0, 'ready' => false];
                                                    $readinessColor = $readiness['ready'] ? 'success' : ($readiness['score'] >= 60 ? 'warning' : 'danger');
                                                @endphp
                                                <span class="badge bg-{{ $readinessColor }}">
                                                    {{ $readiness['score'] }}%
                                                </span>
                                            </td>
                                            <td>
                                                @if($isPaymentApproved)
                                                    <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Approved</span>
                                                @elseif($application->payment_proof || $application->payment_status === 'uploaded')
                                                    <span class="badge bg-info text-dark"><i class="fas fa-clock me-1"></i>Uploaded</span>
                                                @elseif($isStudent)
                                                    <span class="badge bg-danger"><i class="fas fa-times-circle me-1"></i>Unpaid</span>
                                                @else
                                                    <span class="badge bg-secondary">N/A</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.applications.show', $application->id) }}"
                                                       class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-eye"></i> View
                                                    </a>
                                                    @if($application->status === 'pending')
                                                        @if($canApprove)
                                                            <button type="button" class="btn btn-sm btn-outline-success"
                                                                    onclick="approveApplication({{ $application->id }})">
                                                                <i class="fas fa-check"></i> Approve
                                                            </button>
                                                        @else
                                                            <button type="button" class="btn btn-sm btn-outline-secondary"
                                                                    onclick="alertApprovalLocked('{{ addslashes($blockerText) }}', {{ $application->id }})"
                                                                    title="Strictly Locked: {{ $blockerText }}">
                                                                <i class="fas fa-lock"></i> Locked
                                                            </button>
                                                        @endif
                                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                                                onclick="rejectApplication({{ $application->id }})">
                                                                <i class="fas fa-times"></i> Reject
                                                        </button>
                                                    @elseif($application->status === 'payment_pending' && $application->payment_proof)
                                                        <button type="button" class="btn btn-sm btn-outline-success"
                                                                onclick="verifyPayment({{ $application->id }})">
                                                            <i class="fas fa-check-circle"></i> Verify Payment
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        </form>

                        <div class="card-footer">
                            {{ $applications->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No applications found</h5>
                            <p class="text-muted">No applications match your current filters or no applications have been submitted yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Approval Modal -->
<div class="modal fade" id="approvalModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-user-check me-2 text-success"></i>Approve & Admit Student</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="approvalForm" method="POST">
                @csrf
                <input type="hidden" name="force_approve" id="forceApproveField" value="0">
                <div class="modal-body">
                    <div class="alert alert-success py-2 px-3 small mb-3">
                        <i class="fas fa-check-circle me-1"></i>Requirements verified: Student has verified admission payment and an assigned university programme.
                    </div>
                    <p class="mb-3">Are you sure you want to approve this application? The student will be officially admitted, an official Student Number issued, and admission credentials sent.</p>
                    <div class="mb-3">
                        <label for="approval_notes" class="form-label small fw-bold">Admission Notes (Optional)</label>
                        <textarea name="notes" id="approval_notes" class="form-control form-control-sm" rows="3"
                                  placeholder="Add any notes about this admission approval..."></textarea>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success"><i class="fas fa-check me-1"></i>Approve & Admit Student</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Rejection Modal -->
<div class="modal fade" id="rejectionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reject Application</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="rejectionForm" method="POST">
                @csrf
                <div class="modal-body">
                    <p>Are you sure you want to reject this application? The applicant will be notified via email.</p>
                    <div class="mb-3">
                        <label for="rejection_notes" class="form-label">Reason for Rejection</label>
                        <textarea name="notes" id="rejection_notes" class="form-control" rows="3"
                                  placeholder="Please provide a reason for rejection..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject Application</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
function alertApprovalLocked(reason, applicationId) {
    if (confirm("STRICT ADMISSIONS REQUIREMENT:\n\nThis student cannot be approved or admitted because:\n• " + reason + "\n\nAdmissions rule: Student must have verified payment and an assigned course before approval.\n\nWould you like to open this application now to assign a course or verify payment?")) {
        window.location.href = `/admin/applications/${applicationId}`;
    }
}

function approveApplication(applicationId) {
    const form = document.getElementById('approvalForm');
    const forceApproveField = document.getElementById('forceApproveField');
    const forceApproveCheckbox = document.getElementById('forceApproveCheckbox');
    form.action = `/admin/applications/${applicationId}/approve`;
    if (forceApproveField) forceApproveField.value = '0';
    if (forceApproveCheckbox) {
        forceApproveCheckbox.checked = false;
        forceApproveCheckbox.onchange = function () {
            forceApproveField.value = this.checked ? '1' : '0';
        };
    }
    new bootstrap.Modal(document.getElementById('approvalModal')).show();
}

function rejectApplication(applicationId) {
    const form = document.getElementById('rejectionForm');
    form.action = `/admin/applications/${applicationId}/reject`;
    new bootstrap.Modal(document.getElementById('rejectionModal')).show();
}

function verifyPayment(applicationId) {
    if(confirm('Are you sure you want to verify this payment and complete the admission process?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/applications/${applicationId}/verify-payment`;

        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);

        document.body.appendChild(form);
        form.submit();
    }
}

document.addEventListener('DOMContentLoaded', function () {
    const selectAll = document.getElementById('selectAllApps');
    const checkboxes = document.querySelectorAll('.app-checkbox');
    selectAll?.addEventListener('change', function () {
        checkboxes.forEach(cb => { cb.checked = selectAll.checked; });
    });

    // Persist Only readiness-qualified state
    const bulkOnlyReady = document.getElementById('bulkOnlyReady');
    if (bulkOnlyReady) {
        const savedState = sessionStorage.getItem('bulkOnlyReady');
        if (savedState !== null) {
            bulkOnlyReady.checked = savedState === 'true';
        }
        bulkOnlyReady.addEventListener('change', function () {
            sessionStorage.setItem('bulkOnlyReady', this.checked);
        });
    }

    // Persist Force incomplete state
    const bulkForceApprove = document.getElementById('bulkForceApprove');
    if (bulkForceApprove) {
        const savedState = sessionStorage.getItem('bulkForceApprove');
        if (savedState !== null) {
            bulkForceApprove.checked = savedState === 'true';
        }
        bulkForceApprove.addEventListener('change', function () {
            sessionStorage.setItem('bulkForceApprove', this.checked);
        });
    }
});
</script>
@endsection
