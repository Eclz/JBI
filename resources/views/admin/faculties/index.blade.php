@extends('layouts.app')

@section('title', 'Faculty Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">Faculty Management</h1>
                    <p class="text-muted">Manage academic faculties (e.g., Faculty of Social Sciences, Faculty of Engineering)</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.faculty-staff.index') }}" class="btn btn-outline-primary">
                        <i class="bi bi-people"></i> Manage Faculty Staff
                    </a>
                    <a href="{{ route('admin.faculties.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-lg"></i> Add New Faculty
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
                                    <small>Total Faculties</small>
                                </div>
                                <i class="bi bi-building fs-1 opacity-50"></i>
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
                <div class="col-md-2">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="mb-0">{{ $stats['with_dean'] }}</h4>
                                    <small>With Dean</small>
                                </div>
                                <i class="bi bi-person-badge fs-1 opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="mb-0">{{ $stats['total_departments'] }}</h4>
                                    <small>Total Departments</small>
                                </div>
                                <i class="bi bi-diagram-3 fs-1 opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="card-title mb-3">Quick Actions</h6>
                            <div class="d-flex flex-wrap gap-2">
                                <a href="{{ route('admin.faculty-staff.create') }}" class="btn btn-sm btn-outline-success">
                                    <i class="bi bi-person-plus"></i> Add Faculty Staff
                                </a>
                                <a href="{{ route('admin.departments.index') }}" class="btn btn-sm btn-outline-info">
                                    <i class="bi bi-diagram-3"></i> Manage Departments
                                </a>
                                <a href="{{ route('admin.courses.index') }}" class="btn btn-sm btn-outline-warning">
                                    <i class="bi bi-book"></i> Manage Courses
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters and Search -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.faculties.index') }}">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="search" class="form-label">Search</label>
                                <input type="text" class="form-control" id="search" name="search"
                                       value="{{ request('search') }}" placeholder="Search by name, code, or description...">
                            </div>
                            <div class="col-md-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="">All Status</option>
                                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="sort" class="form-label">Sort By</label>
                                <select class="form-select" id="sort" name="sort">
                                    <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Name</option>
                                    <option value="code" {{ request('sort') == 'code' ? 'selected' : '' }}>Code</option>
                                    <option value="created_at" {{ request('sort') == 'created_at' ? 'selected' : '' }}>Created Date</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-outline-primary">
                                        <i class="bi bi-search"></i> Filter
                                    </button>
                                    <a href="{{ route('admin.faculties.index') }}" class="btn btn-outline-secondary">
                                        <i class="bi bi-x-lg"></i> Clear
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Faculties Table -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Faculties List</h5>
                </div>
                <div class="card-body">
                    @if($faculties->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Code</th>
                                        <th>Name</th>
                                        <th>Dean</th>
                                        <th>Departments</th>
                                        <th>Contact</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($faculties as $faculty)
                                        <tr>
                                            <td>
                                                <span class="badge bg-secondary">{{ $faculty->code }}</span>
                                            </td>
                                            <td>
                                                <div>
                                                    <strong>{{ $faculty->name }}</strong>
                                                    @if($faculty->description)
                                                        <br><small class="text-muted">{{ Str::limit($faculty->description, 50) }}</small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                @if($faculty->dean)
                                                    <div class="d-flex align-items-center">
                                                        @if($faculty->dean->profile_picture)
                                                            <img src="{{ asset('storage/' . $faculty->dean->profile_picture) }}"
                                                                 alt="Dean" class="rounded-circle me-2" width="30" height="30">
                                                        @else
                                                            <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center me-2"
                                                                 style="width: 30px; height: 30px;">
                                                                <i class="bi bi-person text-white"></i>
                                                            </div>
                                                        @endif
                                                        <div>
                                                            <small class="fw-medium">{{ $faculty->dean->full_name }}</small>
                                                        </div>
                                                    </div>
                                                @else
                                                    <span class="text-muted">No Dean Assigned</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="badge bg-info">{{ $faculty->departments->count() }} Departments</span>
                                                    @if($faculty->departments->count() > 0)
                                                        <a href="{{ route('admin.departments.index', ['faculty' => $faculty->id]) }}"
                                                           class="btn btn-sm btn-outline-info" title="View Departments">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                @if($faculty->email)
                                                    <div><i class="bi bi-envelope"></i> {{ $faculty->email }}</div>
                                                @endif
                                                @if($faculty->phone)
                                                    <div><i class="bi bi-telephone"></i> {{ $faculty->phone }}</div>
                                                @endif
                                                @if($faculty->location)
                                                    <div><i class="bi bi-geo-alt"></i> {{ $faculty->location }}</div>
                                                @endif
                                            </td>
                                            <td>
                                                <button type="button"
                                                        class="btn btn-sm btn-outline-{{ $faculty->is_active ? 'success' : 'danger' }} toggle-status"
                                                        data-faculty-id="{{ $faculty->id }}"
                                                        data-current-status="{{ $faculty->is_active }}">
                                                    <i class="bi bi-{{ $faculty->is_active ? 'check-circle' : 'x-circle' }}"></i>
                                                    {{ $faculty->is_active ? 'Active' : 'Inactive' }}
                                                </button>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.faculties.show', $faculty) }}"
                                                       class="btn btn-sm btn-outline-info" title="View Details">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.faculty-staff.index', ['faculty' => $faculty->id]) }}"
                                                       class="btn btn-sm btn-outline-success" title="Manage Staff">
                                                        <i class="bi bi-people"></i>
                                                    </a>
                                                    <a href="{{ route('admin.faculties.edit', $faculty) }}"
                                                       class="btn btn-sm btn-outline-primary" title="Edit">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <form action="{{ route('admin.faculties.destroy', $faculty) }}"
                                                          method="POST" class="d-inline"
                                                          onsubmit="return confirm('Are you sure you want to delete this faculty?')">
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
                                Showing {{ $faculties->firstItem() }} to {{ $faculties->lastItem() }} of {{ $faculties->total() }} results
                            </div>
                            {{ $faculties->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-building display-1 text-muted"></i>
                            <h4 class="mt-3">No Faculties Found</h4>
                            <p class="text-muted">Start by creating your first faculty.</p>
                            <a href="{{ route('admin.faculties.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus-lg"></i> Add New Faculty
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
            const facultyId = this.dataset.facultyId;
            const currentStatus = this.dataset.currentStatus === '1';

            fetch(`/admin/faculties/${facultyId}/toggle-status`, {
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
