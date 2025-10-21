@extends('layouts.app')

@section('title', $faculty->name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">{{ $faculty->name }}</h1>
                    <p class="text-muted">Faculty Code: {{ $faculty->code }}</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.faculties.edit', $faculty) }}" class="btn btn-primary">
                        <i class="bi bi-pencil"></i> Edit Faculty
                    </a>
                    <a href="{{ route('admin.faculties.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Back to Faculties
                    </a>
                </div>
            </div>

            <!-- Faculty Information -->
            <div class="row">
                <!-- Basic Information -->
                <div class="col-lg-8">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Faculty Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <dl class="row">
                                        <dt class="col-sm-4">Name:</dt>
                                        <dd class="col-sm-8">{{ $faculty->name }}</dd>

                                        <dt class="col-sm-4">Code:</dt>
                                        <dd class="col-sm-8">
                                            <span class="badge bg-secondary">{{ $faculty->code }}</span>
                                        </dd>

                                        <dt class="col-sm-4">Status:</dt>
                                        <dd class="col-sm-8">
                                            <span class="badge bg-{{ $faculty->is_active ? 'success' : 'danger' }}">
                                                {{ $faculty->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </dd>

                                        @if($faculty->dean)
                                        <dt class="col-sm-4">Dean:</dt>
                                        <dd class="col-sm-8">
                                            <a href="{{ route('admin.faculty-staff.show', $faculty->dean) }}" class="text-decoration-none">
                                                {{ $faculty->dean->name }}
                                            </a>
                                        </dd>
                                        @endif
                                    </dl>
                                </div>

                                <div class="col-md-6">
                                    <dl class="row">
                                        @if($faculty->email)
                                        <dt class="col-sm-4">Email:</dt>
                                        <dd class="col-sm-8">
                                            <a href="mailto:{{ $faculty->email }}">{{ $faculty->email }}</a>
                                        </dd>
                                        @endif

                                        @if($faculty->phone)
                                        <dt class="col-sm-4">Phone:</dt>
                                        <dd class="col-sm-8">{{ $faculty->phone }}</dd>
                                        @endif

                                        @if($faculty->location)
                                        <dt class="col-sm-4">Location:</dt>
                                        <dd class="col-sm-8">{{ $faculty->location }}</dd>
                                        @endif

                                        @if($faculty->website)
                                        <dt class="col-sm-4">Website:</dt>
                                        <dd class="col-sm-8">
                                            <a href="{{ $faculty->website }}" target="_blank" class="text-decoration-none">
                                                {{ $faculty->website }} <i class="bi bi-box-arrow-up-right"></i>
                                            </a>
                                        </dd>
                                        @endif
                                    </dl>
                                </div>
                            </div>

                            @if($faculty->description)
                            <div class="mt-3">
                                <h6>Description:</h6>
                                <p class="text-muted">{{ $faculty->description }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Statistics -->
                <div class="col-lg-4">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Statistics</h5>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-6 mb-3">
                                    <div class="border-end">
                                        <h4 class="text-primary mb-0">{{ $stats['departments_count'] }}</h4>
                                        <small class="text-muted">Departments</small>
                                    </div>
                                </div>
                                <div class="col-6 mb-3">
                                    <h4 class="text-success mb-0">{{ $stats['faculty_members_count'] }}</h4>
                                    <small class="text-muted">Faculty Members</small>
                                </div>
                                <div class="col-6">
                                    <div class="border-end">
                                        <h4 class="text-info mb-0">{{ $stats['students_count'] }}</h4>
                                        <small class="text-muted">Students</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <h4 class="text-warning mb-0">{{ $stats['courses_count'] }}</h4>
                                    <small class="text-muted">Courses</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Departments -->
            @if($faculty->departments->count() > 0)
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Departments ({{ $faculty->departments->count() }})</h5>
                    <a href="{{ route('admin.departments.create') }}?faculty_id={{ $faculty->id }}" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-plus"></i> Add Department
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Code</th>
                                    <th>Head</th>
                                    <th>Faculty Members</th>
                                    <th>Students</th>
                                    <th>Courses</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($faculty->departments as $department)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.departments.show', $department) }}" class="text-decoration-none">
                                            {{ $department->name }}
                                        </a>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $department->code }}</span>
                                    </td>
                                    <td>
                                        @if($department->head)
                                            {{ $department->head->name }}
                                        @else
                                            <span class="text-muted">No head assigned</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-success">{{ $department->faculty_members_count ?? 0 }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $department->students_count ?? 0 }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-warning">{{ $department->courses_count ?? 0 }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $department->is_active ? 'success' : 'danger' }}">
                                            {{ $department->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('admin.departments.show', $department) }}"
                                               class="btn btn-outline-info" title="View">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.departments.edit', $department) }}"
                                               class="btn btn-outline-primary" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @else
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="bi bi-building display-1 text-muted"></i>
                    <h5 class="mt-3">No Departments</h5>
                    <p class="text-muted">This faculty doesn't have any departments yet.</p>
                    <a href="{{ route('admin.departments.create') }}?faculty_id={{ $faculty->id }}" class="btn btn-primary">
                        <i class="bi bi-plus"></i> Add First Department
                    </a>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle status toggle
    document.querySelector('.toggle-status')?.addEventListener('click', function() {
        const facultyId = this.dataset.facultyId;
        const currentStatus = this.dataset.currentStatus === '1';

        if (confirm(`Are you sure you want to ${currentStatus ? 'deactivate' : 'activate'} this faculty?`)) {
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
                    location.reload();
                } else {
                    alert(data.error || 'Failed to update status');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while updating status');
            });
        }
    });
});
</script>
@endpush
