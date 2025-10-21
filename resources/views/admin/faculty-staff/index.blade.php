@extends('layouts.app')

@section('title', 'Faculty Staff Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">Faculty Staff Management</h1>
                    <p class="text-muted">Manage individual faculty members and teaching staff</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.faculties.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-building"></i> Manage Faculties
                    </a>
                    <a href="{{ route('admin.faculty-staff.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-lg"></i> Add Faculty Member
                    </a>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-2">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="mb-0">{{ $stats['total'] }}</h4>
                                    <small>Total Faculty</small>
                                </div>
                                <i class="bi bi-people fs-1 opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="mb-0">{{ $stats['active'] }}</h4>
                                    <small>Active</small>
                                </div>
                                <i class="bi bi-check-circle fs-1 opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card bg-danger text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="mb-0">{{ $stats['inactive'] }}</h4>
                                    <small>Inactive</small>
                                </div>
                                <i class="bi bi-x-circle fs-1 opacity-50"></i>
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
                                    <small>Full-time</small>
                                </div>
                                <i class="bi bi-briefcase fs-1 opacity-50"></i>
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
                                    <small>Part-time</small>
                                </div>
                                <i class="bi bi-clock fs-1 opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters and Search -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.faculty-staff.index') }}">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label for="search" class="form-label">Search</label>
                                <input type="text" class="form-control" id="search" name="search"
                                       value="{{ request('search') }}" placeholder="Search faculty members...">
                            </div>
                            <div class="col-md-2">
                                <label for="faculty" class="form-label">Faculty</label>
                                <select class="form-select" id="faculty" name="faculty">
                                    <option value="">All Faculties</option>
                                    @foreach($faculties as $faculty)
                                        <option value="{{ $faculty->id }}" {{ request('faculty') == $faculty->id ? 'selected' : '' }}>
                                            {{ $faculty->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="department" class="form-label">Department</label>
                                <select class="form-select" id="department" name="department">
                                    <option value="">All Departments</option>
                                    @foreach($departments as $department)
                                        <option value="{{ $department->id }}" {{ request('department') == $department->id ? 'selected' : '' }}>
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
                                </select>
                            </div>
                            <div class="col-md-1">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-flex gap-1">
                                    <button type="submit" class="btn btn-outline-primary">
                                        <i class="bi bi-search"></i>
                                    </button>
                                    <a href="{{ route('admin.faculty-staff.index') }}" class="btn btn-outline-secondary">
                                        <i class="bi bi-x-lg"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Faculty Staff Table -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Faculty Staff Members</h5>
                </div>
                <div class="card-body">
                    @if($facultyStaff->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Employee ID</th>
                                        <th>Name</th>
                                        <th>Department</th>
                                        <th>Position</th>
                                        <th>Employment</th>
                                        <th>Contact</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($facultyStaff as $staff)
                                        <tr>
                                            <td>
                                                <span class="badge bg-secondary">
                                                    {{ $staff->facultyProfile->employee_id ?? 'N/A' }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if($staff->profile_picture)
                                                        <img src="{{ $staff->profile_picture_url }}"
                                                             alt="Profile" class="rounded-circle me-2" width="40" height="40">
                                                    @else
                                                        <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center me-2"
                                                             style="width: 40px; height: 40px;">
                                                            <i class="bi bi-person text-white"></i>
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <div class="fw-medium">{{ $staff->name }}</div>
                                                        <small class="text-muted">{{ $staff->email }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @if($staff->facultyProfile && $staff->facultyProfile->department)
                                                    <div>
                                                        <strong>{{ $staff->facultyProfile->department->name }}</strong>
                                                        @if($staff->facultyProfile->department->faculty)
                                                            <br><small class="text-muted">{{ $staff->facultyProfile->department->faculty->name }}</small>
                                                        @endif
                                                    </div>
                                                @else
                                                    <span class="text-muted">No Department</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($staff->facultyProfile)
                                                    <div>{{ $staff->facultyProfile->position ?? 'Faculty Member' }}</div>
                                                    @if($staff->facultyProfile->specialization)
                                                        <small class="text-muted">{{ $staff->facultyProfile->specialization }}</small>
                                                    @endif
                                                @else
                                                    <span class="text-muted">No Profile</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($staff->facultyProfile)
                                                    <span class="badge bg-{{ $staff->facultyProfile->employment_type === 'full_time' ? 'primary' : 'info' }}">
                                                        {{ ucfirst(str_replace('_', ' ', $staff->facultyProfile->employment_type)) }}
                                                    </span>
                                                    <br>
                                                    <span class="badge bg-{{ $staff->facultyProfile->employment_status === 'active' ? 'success' : 'warning' }}">
                                                        {{ ucfirst(str_replace('_', ' ', $staff->facultyProfile->employment_status)) }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <div><i class="bi bi-telephone"></i> {{ $staff->phone }}</div>
                                                @if($staff->facultyProfile && $staff->facultyProfile->years_of_experience)
                                                    <small class="text-muted">{{ $staff->facultyProfile->years_of_experience }} years exp.</small>
                                                @endif
                                            </td>
                                            <td>
                                                <button type="button"
                                                        class="btn btn-sm btn-outline-{{ $staff->is_active ? 'success' : 'danger' }} toggle-status"
                                                        data-staff-id="{{ $staff->id }}"
                                                        data-current-status="{{ $staff->is_active }}">
                                                    <i class="bi bi-{{ $staff->is_active ? 'check-circle' : 'x-circle' }}"></i>
                                                    {{ $staff->is_active ? 'Active' : 'Inactive' }}
                                                </button>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.faculty-staff.show', $staff) }}"
                                                       class="btn btn-sm btn-outline-info" title="View Details">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.faculty-staff.edit', $staff) }}"
                                                       class="btn btn-sm btn-outline-primary" title="Edit">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <form action="{{ route('admin.faculty-staff.destroy', $staff) }}"
                                                          method="POST" class="d-inline"
                                                          onsubmit="return confirm('Are you sure you want to delete this faculty member?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <div>
                                Showing {{ $facultyStaff->firstItem() }} to {{ $facultyStaff->lastItem() }} of {{ $facultyStaff->total() }} results
                            </div>
                            {{ $facultyStaff->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-people display-1 text-muted"></i>
                            <h4 class="mt-3">No Faculty Members Found</h4>
                            <p class="text-muted">Start by adding your first faculty member.</p>
                            <a href="{{ route('admin.faculty-staff.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus-lg"></i> Add Faculty Member
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle status toggle
    document.querySelectorAll('.toggle-status').forEach(button => {
        button.addEventListener('click', function() {
            const staffId = this.dataset.staffId;
            const currentStatus = this.dataset.currentStatus === '1';

            fetch(`/admin/faculty-staff/${staffId}/toggle-status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update button appearance
                    this.className = `btn btn-sm btn-outline-${data.status_class} toggle-status`;
                    this.innerHTML = `<i class="bi bi-${data.is_active ? 'check-circle' : 'x-circle'}"></i> ${data.status_text}`;
                    this.dataset.currentStatus = data.is_active ? '1' : '0';

                    // Show success message
                    showAlert('success', data.message);
                } else {
                    showAlert('danger', data.error || 'Failed to update status');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('danger', 'An error occurred while updating status');
            });
        });
    });

    // Handle department filtering based on faculty selection
    const facultySelect = document.getElementById('faculty');
    const departmentSelect = document.getElementById('department');

    if (facultySelect && departmentSelect) {
        facultySelect.addEventListener('change', function() {
            const facultyId = this.value;

            // Reset department options
            departmentSelect.innerHTML = '<option value="">All Departments</option>';

            if (facultyId) {
                // Filter departments by selected faculty
                @foreach($departments as $department)
                    if ('{{ $department->faculty_id }}' === facultyId) {
                        const option = document.createElement('option');
                        option.value = '{{ $department->id }}';
                        option.textContent = '{{ $department->name }}';
                        departmentSelect.appendChild(option);
                    }
                @endforeach
            } else {
                // Show all departments
                @foreach($departments as $department)
                    const option = document.createElement('option');
                    option.value = '{{ $department->id }}';
                    option.textContent = '{{ $department->name }}';
                    departmentSelect.appendChild(option);
                @endforeach
            }
        });
    }
});

function showAlert(type, message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

    const container = document.querySelector('.container-fluid');
    container.insertBefore(alertDiv, container.firstChild);

    // Auto dismiss after 5 seconds
    setTimeout(() => {
        alertDiv.remove();
    }, 5000);
}
</script>
@endpush
