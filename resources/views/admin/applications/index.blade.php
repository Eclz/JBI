@extends('layouts.app')

@section('title', 'Student Applications')

@section('content')
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
                        <div class="col-md-3">
                            <label for="status" class="form-label">Search Here</label>
                            <input type="text" name="search" class="form-control" placeholder="Search by name, email, phone..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <label for="status" class="form-label">Status</label>
                            <select name="status" id="status" class="form-select">
                                <option value="">All Statuses</option>
                                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                                <option value="payment_pending" {{ request('status') === 'payment_pending' ? 'selected' : '' }}>Payment Pending</option>
                                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="type" class="form-label">Type</label>
                            <select name="type" id="type" class="form-select">
                                <option value="">All Types</option>
                                <option value="student" {{ request('type') === 'student' ? 'selected' : '' }}>Student</option>
                                <option value="faculty" {{ request('type') === 'faculty' ? 'selected' : '' }}>Faculty</option>
                            </select>
                        </div>
                        <div class="col-md-3">
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
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="force_approve" id="bulkForceApprove" value="1">
                                    <label class="form-check-label" for="bulkForceApprove">Force incomplete</label>
                                </div>
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
                                                    <strong class="text-dark">{{ $application->programRecord->name ?? $application->program ?? 'N/A' }}</strong>
                                                    @if(is_array($application->program_choices) && count($application->program_choices) > 1)
                                                        <div class="mt-1">
                                                            <span class="badge bg-info text-dark" style="font-size: 0.7rem;">
                                                                <i class="bi bi-list-stars me-1"></i>{{ count($application->program_choices) }} Course Choices
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
                                                @if($application->payment_proof)
                                                    <span class="badge bg-success">Submitted</span>
                                                @elseif($application->status === 'payment_pending')
                                                    <span class="badge bg-warning">Awaiting Payment</span>
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
                                                        <button type="button" class="btn btn-sm btn-outline-success"
                                                                onclick="approveApplication({{ $application->id }})">
                                                            <i class="fas fa-check"></i> Approve
                                                        </button>
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
                <h5 class="modal-title">Approve Application</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="approvalForm" method="POST">
                @csrf
                <input type="hidden" name="force_approve" id="forceApproveField" value="0">
                <div class="modal-body">
                    <p>Are you sure you want to approve this application? The applicant will receive an email with their login credentials.</p>
                    <div class="mb-3">
                        <label for="approval_notes" class="form-label">Notes (Optional)</label>
                        <textarea name="notes" id="approval_notes" class="form-control" rows="3"
                                  placeholder="Add any notes about the approval..."></textarea>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="forceApproveCheckbox">
                        <label class="form-check-label" for="forceApproveCheckbox">
                            Force approve even when readiness checklist is incomplete
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Approve Application</button>
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
