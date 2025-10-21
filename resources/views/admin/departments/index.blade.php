@extends('layouts.app')

@section('title', 'Department Management')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Department Management</h1>
            <p class="mb-0 text-muted">Manage university departments and their information</p>
        </div>
        <a href="{{ route('admin.departments.create') }}" class="btn btn-primary">
            <i class="fa fa-plus"></i> Add Department
        </a>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Departments</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fa fa-building fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Active Departments</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['active'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fa fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Inactive Departments</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['inactive'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fa fa-pause-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">With Department Head</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['with_head'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fa fa-user-tie fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filters & Search</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.departments.index') }}">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="search">Search</label>
                            <input type="text" class="form-control" id="search" name="search"
                                   value="{{ request('search') }}" placeholder="Search departments...">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select class="form-control" id="status" name="status">
                                <option value="">All Status</option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="sort_by">Sort By</label>
                            <select class="form-control" id="sort_by" name="sort_by">
                                <option value="name" {{ request('sort_by') === 'name' ? 'selected' : '' }}>Name</option>
                                <option value="code" {{ request('sort_by') === 'code' ? 'selected' : '' }}>Code</option>
                                <option value="created_at" {{ request('sort_by') === 'created_at' ? 'selected' : '' }}>Created Date</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <div class="d-flex">
                                <button type="submit" class="btn btn-primary mr-2">
                                    <i class="fa fa-search"></i> Search
                                </button>
                                <a href="{{ route('admin.departments.index') }}" class="btn btn-secondary">
                                    <i class="fa fa-times"></i> Clear
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Departments Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Departments List</h6>
        </div>
        <div class="card-body">
            @if($departments->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Name</th>
                                <th>Head of Department</th>
                                <th>Location</th>
                                <th>Contact</th>
                                <th>Courses</th>
                                <th>Faculty</th>
                                <th>Students</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($departments as $department)
                                <tr>
                                    <td>
                                        <span class="badge badge-secondary">{{ $department->code }}</span>
                                    </td>
                                    <td>
                                        <strong>{{ $department->name }}</strong>
                                        @if($department->description)
                                            <br><small class="text-muted">{{ Str::limit($department->description, 50) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($department->headOfDepartment)
                                            <div class="d-flex align-items-center">
                                                <div class="mr-2">
                                                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                                        {{ substr($department->headOfDepartment->name, 0, 1) }}
                                                    </div>
                                                </div>
                                                <div>
                                                    <div class="font-weight-bold">{{ $department->headOfDepartment->name }}</div>
                                                    <small class="text-muted">{{ $department->headOfDepartment->email }}</small>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-muted">Not assigned</span>
                                        @endif
                                    </td>
                                    <td>{{ $department->location ?: 'Not specified' }}</td>
                                    <td>
                                        @if($department->phone)
                                            <div><i class="fa fa-phone"></i> {{ $department->phone }}</div>
                                        @endif
                                        @if($department->email)
                                            <div><i class="fa fa-envelope"></i> {{ $department->email }}</div>
                                        @endif
                                        @if(!$department->phone && !$department->email)
                                            <span class="text-muted">Not provided</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-info">{{ $department->courses_count }}</span>
                                    </td>
                                    <td>
                                        <span class="badge badge-success">{{ $department->faculty_members_count }}</span>
                                    </td>
                                    <td>
                                        <span class="badge badge-warning">{{ $department->students_count }}</span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm toggle-status {{ $department->is_active ? 'btn-success' : 'btn-secondary' }}"
                                                data-id="{{ $department->id }}"
                                                data-status="{{ $department->is_active }}">
                                            <i class="fa fa-{{ $department->is_active ? 'check' : 'times' }}"></i>
                                            {{ $department->is_active ? 'Active' : 'Inactive' }}
                                        </button>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.departments.show', $department) }}"
                                               class="btn btn-sm btn-info" title="View">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.departments.edit', $department) }}"
                                               class="btn btn-sm btn-warning" title="Edit">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-danger delete-btn"
                                                    data-id="{{ $department->id }}"
                                                    data-name="{{ $department->name }}" title="Delete">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div>
                        Showing {{ $departments->firstItem() }} to {{ $departments->lastItem() }} of {{ $departments->total() }} results
                    </div>
                    <div>
                        {{ $departments->links() }}
                    </div>
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fa fa-building fa-3x text-gray-300 mb-3"></i>
                    <h5 class="text-gray-600">No departments found</h5>
                    <p class="text-muted">Start by creating your first department.</p>
                    <a href="{{ route('admin.departments.create') }}" class="btn btn-primary">
                        <i class="fa fa-plus"></i> Create Department
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the department <strong id="departmentName"></strong>?</p>
                <p class="text-danger"><small>This action cannot be undone.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Delete confirmation
    $('.delete-btn').click(function() {
        const id = $(this).data('id');
        const name = $(this).data('name');

        $('#departmentName').text(name);
        $('#deleteForm').attr('action', `/admin/departments/${id}`);
        $('#deleteModal').modal('show');
    });

    // Toggle status
    $('.toggle-status').click(function() {
        const button = $(this);
        const id = button.data('id');
        const currentStatus = button.data('status');

        button.prop('disabled', true);

        $.ajax({
            url: `/admin/departments/${id}/toggle-status`,
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    const newStatus = response.status;
                    button.data('status', newStatus);

                    if (newStatus) {
                        button.removeClass('btn-secondary').addClass('btn-success');
                        button.html('<i class="fa fa-check"></i> Active');
                    } else {
                        button.removeClass('btn-success').addClass('btn-secondary');
                        button.html('<i class="fa fa-times"></i> Inactive');
                    }

                    // Show success message
                    showAlert('success', response.message);
                } else {
                    showAlert('error', response.message);
                }
            },
            error: function() {
                showAlert('error', 'Failed to update department status.');
            },
            complete: function() {
                button.prop('disabled', false);
            }
        });
    });

    function showAlert(type, message) {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const alert = `
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        `;

        $('.container-fluid').prepend(alert);

        // Auto dismiss after 5 seconds
        setTimeout(function() {
            $('.alert').alert('close');
        }, 5000);
    }
});
</script>
@endpush
