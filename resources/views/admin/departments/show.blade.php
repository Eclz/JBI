@extends('layouts.app')

@section('title', 'Department Details - ' . $department->name)

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">{{ $department->name }}</h1>
            <p class="mb-0 text-muted">Department Code: <strong>{{ $department->code }}</strong></p>
        </div>
        <div>
            <a href="{{ route('admin.departments.edit', $department) }}" class="btn btn-warning">
                <i class="fa fa-edit"></i> Edit Department
            </a>
            <a href="{{ route('admin.faculty-staff.create') }}?department={{ $department->id }}" class="btn btn-success">
                <i class="fa fa-user-plus"></i> Add Faculty Staff
            </a>
            <a href="{{ route('admin.departments.index') }}" class="btn btn-secondary">
                <i class="fa fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    <!-- Status Alert -->
    @if(!$department->is_active)
        <div class="alert alert-warning" role="alert">
            <i class="fa fa-exclamation-triangle"></i>
            This department is currently inactive and not visible to students and faculty.
        </div>
    @endif

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Courses</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['course_count'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fa fa-book fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Active Courses</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['active_courses'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fa fa-check-circle fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Faculty Members</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['faculty_count'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fa fa-chalkboard-teacher fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Students</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['student_count'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fa fa-user-graduate fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Department Information -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Department Information</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Department Name:</strong><br>
                        {{ $department->name }}
                    </div>

                    <div class="mb-3">
                        <strong>Department Code:</strong><br>
                        {{ $department->code }}
                    </div>

                    <div class="mb-3">
                        <strong>Faculty:</strong><br>
                        @if($department->faculty)
                            <a href="{{ route('admin.faculties.show', $department->faculty) }}" class="text-decoration-none">
                                {{ $department->faculty->name }}
                            </a>
                        @else
                            <span class="text-danger font-italic"><i class="fa fa-exclamation-triangle"></i> No faculty assigned</span>
                        @endif
                    </div>

                    @if($department->description)
                        <div class="mb-3">
                            <strong>Description:</strong><br>
                            {{ $department->description }}
                        </div>
                    @endif

                    <div class="mb-3">
                        <strong>Status:</strong><br>
                        <span class="badge bg-{{ $department->is_active ? 'success' : 'secondary' }}">
                            {{ $department->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>

                    @if($department->location)
                        <div class="mb-3">
                            <strong>Location:</strong><br>
                            <i class="fa fa-map-marker-alt text-muted"></i> {{ $department->location }}
                        </div>
                    @endif

                    @if($department->phone || $department->email)
                        <div class="mb-3">
                            <strong>Contact Information:</strong><br>
                            @if($department->phone)
                                <div><i class="fa fa-phone text-muted"></i> {{ $department->phone }}</div>
                            @endif
                            @if($department->email)
                                <div><i class="fa fa-envelope text-muted"></i> {{ $department->email }}</div>
                            @endif
                        </div>
                    @endif

                    <div class="mb-0">
                        <strong>Created:</strong><br>
                        <small class="text-muted">{{ $department->created_at->format('M d, Y \a\t g:i A') }}</small>
                    </div>
                </div>
            </div>

            <!-- Head of Department -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Head of Department</h6>
                    @if($availableFacultyMembers->count() > 0)
                        <button type="button" class="btn btn-sm btn-outline-primary" data-toggle="modal" data-target="#assignHeadModal">
                            <i class="fa fa-user-cog"></i> {{ $department->headOfDepartment ? 'Change' : 'Assign' }}
                        </button>
                    @endif
                </div>
                <div class="card-body">
                    @if($department->headOfDepartment)
                        <div class="d-flex align-items-center">
                            <div class="mr-3">
                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                    {{ substr($department->headOfDepartment->first_name ?? $department->headOfDepartment->name, 0, 1) }}
                                </div>
                            </div>
                            <div>
                                <h6 class="mb-1">{{ $department->headOfDepartment->first_name }} {{ $department->headOfDepartment->last_name }}</h6>
                                <p class="mb-1 text-muted">{{ $department->headOfDepartment->email }}</p>
                                @if($department->headOfDepartment->facultyProfile)
                                    <small class="text-muted">{{ $department->headOfDepartment->facultyProfile->designation ?? 'Faculty Member' }}</small>
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="text-center py-3">
                            <i class="fa fa-user-slash fa-2x text-gray-300 mb-2"></i>
                            <p class="text-muted mb-0">No head of department assigned</p>
                            @if($availableFacultyMembers->count() == 0)
                                <small class="text-muted">Add faculty members to this department first</small>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Faculty Members and Courses -->
        <div class="col-lg-8">
            <!-- Faculty Members -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Faculty Members</h6>
                    <div>
                        <a href="{{ route('admin.faculty-staff.index') }}?department={{ $department->id }}" class="btn btn-sm btn-info">
                            <i class="fa fa-list"></i> Manage All
                        </a>
                        <a href="{{ route('admin.faculty-staff.create') }}?department={{ $department->id }}" class="btn btn-sm btn-primary">
                            <i class="fa fa-plus"></i> Add Faculty
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($department->facultyMembers->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Designation</th>
                                        <th>Employment Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($department->facultyMembers as $faculty)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="mr-2">
                                                        <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center" style="width: 30px; height: 30px; font-size: 12px;">
                                                            {{ substr($faculty->user->first_name ?? $faculty->user->name, 0, 1) }}
                                                        </div>
                                                    </div>
                                                    <div>
                                                        {{ $faculty->user->first_name }} {{ $faculty->user->last_name }}
                                                        @if($department->headOfDepartment && $department->headOfDepartment->id === $faculty->user->id)
                                                            <span class="badge bg-warning text-dark ml-1">Head</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ $faculty->user->email }}</td>
                                            <td>{{ $faculty->designation ?? 'Faculty Member' }}</td>
                                            <td>
                                                <span class="badge bg-{{ $faculty->employment_status === 'full_time' ? 'success' : ($faculty->employment_status === 'part_time' ? 'warning text-dark' : 'info text-dark') }}">
                                                    {{ ucfirst(str_replace('_', ' ', $faculty->employment_status ?? 'active')) }}
                                                </span>
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.faculty-staff.show', $faculty) }}" class="btn btn-sm btn-info">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.faculty-staff.edit', $faculty) }}" class="btn btn-sm btn-warning">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fa fa-chalkboard-teacher fa-3x text-gray-300 mb-3"></i>
                            <h6 class="text-gray-600">No faculty members found</h6>
                            <p class="text-muted">This department doesn't have any faculty members yet.</p>
                            <a href="{{ route('admin.faculty-staff.create') }}?department={{ $department->id }}" class="btn btn-primary">
                                <i class="fa fa-plus"></i> Add First Faculty Member
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Courses -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Courses</h6>
                    <a href="{{ route('admin.courses.create') }}?department={{ $department->id }}" class="btn btn-sm btn-primary">
                        <i class="fa fa-plus"></i> Add Course
                    </a>
                </div>
                <div class="card-body">
                    @if($department->courses->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Code</th>
                                        <th>Name</th>
                                        <th>Credits</th>
                                        <th>Semester</th>
                                        <th>Enrollments</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($department->courses as $course)
                                        <tr>
                                            <td>{{ $course->code }}</td>
                                            <td>{{ $course->name }}</td>
                                            <td>{{ $course->credits }}</td>
                                            <td>{{ $course->semester->name ?? 'N/A' }}</td>
                                            <td>{{ $course->enrollments->count() }}</td>
                                            <td>
                                                <span class="badge bg-{{ $course->is_active ? 'success' : 'secondary' }}">
                                                    {{ $course->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.courses.show', $course) }}" class="btn btn-sm btn-info">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.courses.edit', $course) }}" class="btn btn-sm btn-warning">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fa fa-book fa-3x text-gray-300 mb-3"></i>
                            <h6 class="text-gray-600">No courses found</h6>
                            <p class="text-muted">This department doesn't have any courses yet.</p>
                            <a href="{{ route('admin.courses.create') }}?department={{ $department->id }}" class="btn btn-primary">
                                <i class="fa fa-plus"></i> Add First Course
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Assign Head Modal -->
@if($availableFacultyMembers->count() > 0)
<div class="modal fade" id="assignHeadModal" tabindex="-1" role="dialog" aria-labelledby="assignHeadModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.departments.assign-head', $department) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="assignHeadModalLabel">
                        {{ $department->headOfDepartment ? 'Change' : 'Assign' }} Head of Department
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="head_of_department_id">Select Faculty Member</label>
                        <select class="form-control" id="head_of_department_id" name="head_of_department_id" required>
                            <option value="">Choose a faculty member...</option>
                            @foreach($availableFacultyMembers as $faculty)
                                <option value="{{ $faculty->id }}" {{ $department->head_of_department_id == $faculty->id ? 'selected' : '' }}>
                                    {{ $faculty->first_name }} {{ $faculty->last_name }}
                                    @if($faculty->facultyProfile)
                                        ({{ $faculty->facultyProfile->designation ?? 'Faculty Member' }})
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="alert alert-info">
                        <i class="fa fa-info-circle"></i>
                        Only faculty members assigned to this department can be selected as head.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        {{ $department->headOfDepartment ? 'Change' : 'Assign' }} Head
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Handle status toggle
    $('.toggle-status').on('click', function() {
        var button = $(this);
        var url = button.data('url');

        $.post(url, {
            _token: '{{ csrf_token() }}'
        })
        .done(function(response) {
            if (response.success) {
                button.removeClass('btn-success btn-danger')
                      .addClass('btn-' + response.status_class)
                      .text(response.status_text);

                // Show success message
                showAlert('success', response.message);
            }
        })
        .fail(function() {
            showAlert('error', 'Failed to update status.');
        });
    });

    function showAlert(type, message) {
        var alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        var alertHtml = '<div class="alert ' + alertClass + ' alert-dismissible fade show" role="alert">' +
                       message +
                       '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                       '<span aria-hidden="true">&times;</span>' +
                       '</button>' +
                       '</div>';

        $('.container-fluid').prepend(alertHtml);

        // Auto dismiss after 5 seconds
        setTimeout(function() {
            $('.alert').alert('close');
        }, 5000);
    }
});
</script>
@endpush
