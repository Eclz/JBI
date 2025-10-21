@extends('layouts.app')

@section('title', 'Faculty Management')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1">Faculty Management</h2>
                    <p class="text-muted mb-0">Manage faculty members and their information</p>
                </div>
                <a href="{{ route('admin.faculties.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg"></i> Add New Faculty
                </a>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $stats['total'] }}</h4>
                            <p class="mb-0">Total Faculty</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-people fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $stats['active'] }}</h4>
                            <p class="mb-0">Active Faculty</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-person-check fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $stats['full_time'] }}</h4>
                            <p class="mb-0">Full-time</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-briefcase fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $stats['part_time'] }}</h4>
                            <p class="mb-0">Part-time</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-clock fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.faculties.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label for="search" class="form-label">Search Faculty</label>
                    <input type="text" class="form-control" id="search" name="search"
                           value="{{ request('search') }}" placeholder="Name, email, employee ID...">
                </div>
                <div class="col-md-2">
                    <label for="department" class="form-label">Department</label>
                    <select class="form-select" id="department" name="department">
                        <option value="">All Departments</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}"
                                    {{ request('department') == $department->id ? 'selected' : '' }}>
                                {{ $department->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="employment_status" class="form-label">Employment</label>
                    <select class="form-select" id="employment_status" name="employment_status">
                        <option value="">All Types</option>
                        <option value="active" {{ request('employment_status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('employment_status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="on_leave" {{ request('employment_status') == 'on_leave' ? 'selected' : '' }}>On Leave</option>
                        <option value="terminated" {{ request('employment_status') == 'terminated' ? 'selected' : '' }}>Terminated</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="bi bi-search"></i> Search
                        </button>
                        <a href="{{ route('admin.faculties.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-lg"></i> Clear
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Faculty List -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Faculty Members ({{ $faculty->total() }})</h5>
            <div class="d-flex gap-2">
                <div class="dropdown">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-sort-down"></i> Sort
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort' => 'name', 'order' => 'asc']) }}">Name (A-Z)</a></li>
                        <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort' => 'name', 'order' => 'desc']) }}">Name (Z-A)</a></li>
                        <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort' => 'created_at', 'order' => 'desc']) }}">Newest First</a></li>
                        <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort' => 'created_at', 'order' => 'asc']) }}">Oldest First</a></li>
                        <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort' => 'department', 'order' => 'asc']) }}">Department</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            @if($faculty->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Faculty</th>
                                <th>Employee ID</th>
                                <th>Department</th>
                                <th>Position</th>
                                <th>Employment</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($faculty as $member)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="me-3">
                                                @if($member->profile_picture)
                                                    <img src="{{ $member->profile_picture_url }}"
                                                         alt="{{ $member->name }}"
                                                         class="rounded-circle"
                                                         width="40" height="40">
                                                @else
                                                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center"
                                                         style="width: 40px; height: 40px;">
                                                        <span class="text-white fw-bold">
                                                            {{ strtoupper(substr($member->first_name, 0, 1) . substr($member->last_name, 0, 1)) }}
                                                        </span>
                                                    </div>
                                                @endif
                                            </div>
                                            <div>
                                                <div class="fw-semibold">{{ $member->name }}</div>
                                                <small class="text-muted">{{ $member->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">
                                            {{ $member->facultyProfile?->employee_id ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">
                                            {{ $member->facultyProfile?->department?->name ?? 'No Department' }}
                                        </span>
                                    </td>
                                    <td>{{ $member->facultyProfile?->position ?? 'N/A' }}</td>
                                    <td>
                                        @php
                                            $employmentType = $member->facultyProfile?->employment_type ?? 'N/A';
                                            $employmentStatus = $member->facultyProfile?->employment_status ?? 'N/A';
                                            $badgeClass = match($employmentStatus) {
                                                'active' => 'success',
                                                'inactive' => 'secondary',
                                                'on_leave' => 'warning',
                                                'terminated' => 'danger',
                                                default => 'secondary'
                                            };
                                        @endphp
                                        <div>
                                            <small class="text-muted">{{ ucfirst(str_replace('_', ' ', $employmentType)) }}</small><br>
                                            <span class="badge bg-{{ $badgeClass }}">
                                                {{ ucfirst(str_replace('_', ' ', $employmentStatus)) }}
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input status-toggle"
                                                   type="checkbox"
                                                   data-faculty-id="{{ $member->id }}"
                                                   {{ $member->is_active ? 'checked' : '' }}>
                                            <label class="form-check-label">
                                                <span class="status-text">{{ $member->is_active ? 'Active' : 'Inactive' }}</span>
                                            </label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.faculty.show', $member) }}"
                                               class="btn btn-sm btn-outline-primary" title="View Details">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.faculty.edit', $member) }}"
                                               class="btn btn-sm btn-outline-warning" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button type="button"
                                                    class="btn btn-sm btn-outline-danger delete-faculty"
                                                    data-faculty-id="{{ $member->id }}"
                                                    data-faculty-name="{{ $member->name }}"
                                                    title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="card-footer">
                    {{ $faculty->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-people fs-1 text-muted"></i>
                    <h5 class="mt-3 text-muted">No Faculty Members Found</h5>
                    <p class="text-muted">
                        @if(request()->hasAny(['search', 'department', 'status', 'employment_status']))
                            No faculty members match your search criteria.
                        @else
                            Start by adding your first faculty member.
                        @endif
                    </p>
                    @if(!request()->hasAny(['search', 'department', 'status', 'employment_status']))
                        <a href="{{ route('admin.faculties.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-lg"></i> Add First Faculty Member
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete <strong id="facultyName"></strong>?</p>
                <p class="text-danger"><small>This action cannot be undone.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Faculty</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Status toggle functionality
    document.querySelectorAll('.status-toggle').forEach(function(toggle) {
        toggle.addEventListener('change', function() {
            const facultyId = this.dataset.facultyId;
            const statusText = this.closest('td').querySelector('.status-text');

            fetch(`/admin/faculty/${facultyId}/toggle-status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    statusText.textContent = data.status_text;
                    // Show success message
                    showAlert('success', data.message);
                } else {
                    // Revert toggle state
                    this.checked = !this.checked;
                    showAlert('danger', data.error || 'Failed to update status');
                }
            })
            .catch(error => {
                // Revert toggle state
                this.checked = !this.checked;
                showAlert('danger', 'An error occurred while updating status');
                console.error('Error:', error);
            });
        });
    });

    // Delete faculty functionality
    document.querySelectorAll('.delete-faculty').forEach(function(button) {
        button.addEventListener('click', function() {
            const facultyId = this.dataset.facultyId;
            const facultyName = this.dataset.facultyName;

            document.getElementById('facultyName').textContent = facultyName;
            document.getElementById('deleteForm').action = `/admin/faculty/${facultyId}`;

            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        });
    });

    // Alert function
    function showAlert(type, message) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

        const container = document.querySelector('.container-fluid');
        container.insertBefore(alertDiv, container.firstChild);

        // Auto-dismiss after 5 seconds
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 5000);
    }
});
</script>
@endpush
