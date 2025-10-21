@extends('layouts.app')

@section('title', 'Pending Applications')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Pending Applications</h3>
                    <div class="card-tools">
                        <span class="badge badge-warning">{{ $applications->total() }} Total</span>
                    </div>
                </div>

                <!-- Filters -->
                <div class="card-body border-bottom">
                    <form method="GET" action="{{ route('admin.applications.index') }}" class="row g-3">
                        <div class="col-md-3">
                            <label for="role" class="form-label">Role</label>
                            <select name="role" id="role" class="form-select">
                                <option value="">All Roles</option>
                                <option value="student" {{ request('role') === 'student' ? 'selected' : '' }}>Student</option>
                                <option value="faculty" {{ request('role') === 'faculty' ? 'selected' : '' }}>Faculty</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="department_id" class="form-label">Department</label>
                            <select name="department_id" id="department_id" class="form-select">
                                <option value="">All Departments</option>
                                @foreach($departments as $department)
                                    <option value="{{ $department->id }}" {{ request('department_id') == $department->id ? 'selected' : '' }}>
                                        {{ $department->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="application_status" class="form-label">Status</label>
                            <select name="application_status" id="application_status" class="form-select">
                                <option value="">All Statuses</option>
                                <option value="submitted" {{ request('application_status') === 'submitted' ? 'selected' : '' }}>Submitted</option>
                                <option value="under_review" {{ request('application_status') === 'under_review' ? 'selected' : '' }}>Under Review</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="email_verified" class="form-label">Email Status</label>
                            <select name="email_verified" id="email_verified" class="form-select">
                                <option value="">All</option>
                                <option value="yes" {{ request('email_verified') === 'yes' ? 'selected' : '' }}>Verified</option>
                                <option value="no" {{ request('email_verified') === 'no' ? 'selected' : '' }}>Not Verified</option>
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
                        <div class="table-responsive">
                            <table class="table table-striped table-hover mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Applicant</th>
                                        <th>Role</th>
                                        <th>Department</th>
                                        <th>Program/Position</th>
                                        <th>Applied Date</th>
                                        <th>Email Status</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($applications as $application)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="{{ $application->profile_picture_url }}"
                                                         alt="Profile" class="rounded-circle me-2"
                                                         style="width: 40px; height: 40px;">
                                                    <div>
                                                        <div class="fw-bold">{{ $application->full_name }}</div>
                                                        <small class="text-muted">{{ $application->email }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $application->role === 'student' ? 'primary' : 'success' }}">
                                                    {{ ucfirst($application->role) }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($application->studentProfile)
                                                    {{ $application->studentProfile->department->name ?? 'N/A' }}
                                                @elseif($application->facultyProfile)
                                                    {{ $application->facultyProfile->department->name ?? 'N/A' }}
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td>
                                                @if($application->studentProfile)
                                                    {{ $application->studentProfile->program }}
                                                    @if($application->studentProfile->specialization)
                                                        <br><small class="text-muted">{{ $application->studentProfile->specialization }}</small>
                                                    @endif
                                                @elseif($application->facultyProfile)
                                                    {{ $application->facultyProfile->position }}
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td>{{ $application->created_at->format('M j, Y') }}</td>
                                            <td>
                                                @if($application->hasVerifiedEmail())
                                                    <span class="badge bg-success">Verified</span>
                                                @else
                                                    <span class="badge bg-warning">Not Verified</span>
                                                @endif
                                            </td>
                                            <td>
                                                @php
                                                    $status = 'submitted';
                                                    if($application->studentProfile) {
                                                        $status = $application->studentProfile->application_status;
                                                    } elseif($application->facultyProfile) {
                                                        $status = $application->facultyProfile->application_status ?? 'submitted';
                                                    }
                                                @endphp
                                                <span class="badge bg-info">{{ ucfirst($status) }}</span>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.applications.show', $application->id) }}"
                                                       class="btn btn-sm btn-outline-primary">
                                                        <i class="fa fa-eye"></i> View
                                                    </a>
                                                    @if($application->hasVerifiedEmail())
                                                        <button type="button" class="btn btn-sm btn-outline-success"
                                                                onclick="approveApplication({{ $application->id }})">
                                                            <i class="fa fa-check"></i> Approve
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                                                onclick="rejectApplication({{ $application->id }})">
                                                            <i class="fa fa-times"></i> Reject
                                                        </button>
                                                    @else
                                                        <span class="text-muted small">Email verification required</span>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="card-footer">
                            {{ $applications->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fa fa-inbox fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No pending applications found</h5>
                            <p class="text-muted">All applications have been processed or no new applications have been submitted.</p>
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
                <div class="modal-body">
                    <p>Are you sure you want to approve this application? The applicant will receive an email with their login credentials.</p>
                    <div class="mb-3">
                        <label for="approval_notes" class="form-label">Notes (Optional)</label>
                        <textarea name="notes" id="approval_notes" class="form-control" rows="3"
                                  placeholder="Add any notes about the approval..."></textarea>
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
    form.action = `/admin/applications/${applicationId}/approve`;
    new bootstrap.Modal(document.getElementById('approvalModal')).show();
}

function rejectApplication(applicationId) {
    const form = document.getElementById('rejectionForm');
    form.action = `/admin/applications/${applicationId}/reject`;
    new bootstrap.Modal(document.getElementById('rejectionModal')).show();
}
</script>
@endsection
