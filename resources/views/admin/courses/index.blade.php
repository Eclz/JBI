@extends('layouts.app')

@section('title', 'Course Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Course Management</h4>
                    <a href="{{ route('admin.courses.create') }}" class="btn btn-primary">
                        <i class="fa fa-plus"></i> Add New Course
                    </a>
                </div>
                <div class="card-body">
                    <!-- Search and Filter -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <input type="text" class="form-control" id="searchInput" placeholder="Search courses...">
                        </div>
                        <div class="col-md-2">
                            <select class="form-control" id="departmentFilter">
                                <option value="">All Departments</option>
                                @foreach($departments as $department)
                                    <option value="{{ $department->id }}">{{ $department->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-control" id="semesterFilter">
                                <option value="">All Semesters</option>
                                @foreach($semesters as $semester)
                                    <option value="{{ $semester->id }}">{{ $semester->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-control" id="statusFilter">
                                <option value="">All Status</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="completed">Completed</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-outline-secondary" id="resetFilters">Reset</button>
                        </div>
                        <div class="col-md-1">
                            <button class="btn btn-outline-success" onclick="exportCourses()">
                                <i class="fa fa-download"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Courses Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Course Code</th>
                                    <th>Course Name</th>
                                    <th>Department</th>
                                    <th>Instructor</th>
                                    <th>Credits</th>
                                    <th>Enrolled</th>
                                    <th>Capacity</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($courses as $course)
                                <tr>
                                    <td><strong>{{ $course->course_code }}</strong></td>
                                    <td>{{ $course->name }}</td>
                                    <td>{{ $course->department->name ?? 'N/A' }}</td>
                                    <td>
                                        @if($course->instructor)
                                            <div class="d-flex align-items-center">
                                                @if($course->instructor->avatar)
                                                    <img src="{{ asset('storage/' . $course->instructor->avatar) }}"
                                                         class="rounded-circle me-2" width="24" height="24">
                                                @endif
                                                {{ $course->instructor->name }}
                                            </div>
                                        @else
                                            <span class="text-muted">Not Assigned</span>
                                        @endif
                                    </td>
                                    <td>{{ $course->credits }}</td>
                                    <td>
                                        <span class="badge bg-info">
                                            {{ $course->enrolled_count ?? 0 }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($course->capacity || $course->max_students)
                                            <span class="badge bg-{{ ($course->enrolled_count >= ($course->capacity ?? $course->max_students)) ? 'danger' : 'success' }}">
                                                {{ $course->capacity ?? $course->max_students }}
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">Unlimited</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $course->status == 'active' ? 'success' : ($course->status == 'completed' ? 'primary' : 'secondary') }}">
                                            {{ ucfirst($course->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.courses.show', $course) }}"
                                               class="btn btn-sm btn-outline-info" title="View">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.courses.edit', $course) }}"
                                               class="btn btn-sm btn-outline-primary" title="Edit">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <a href="{{ route('admin.courses.enrollments', $course) }}"
                                               class="btn btn-sm btn-outline-success" title="Enrollments">
                                                <i class="fa fa-users"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger"
                                                    onclick="confirmDelete({{ $course->id }})" title="Delete">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4">
                                        <i class="fa fa-book fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">No courses found</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div>
                            Showing {{ $courses->firstItem() ?? 0 }} to {{ $courses->lastItem() ?? 0 }}
                            of {{ $courses->total() }} results
                        </div>
                        {{ $courses->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this course? This action cannot be undone and will affect all enrolled students.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
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
function confirmDelete(courseId) {
    const form = document.getElementById('deleteForm');
    form.action = `/admin/courses/${courseId}`;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

function exportCourses() {

}

// Search and filter functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const departmentFilter = document.getElementById('departmentFilter');
    const semesterFilter = document.getElementById('semesterFilter');
    const statusFilter = document.getElementById('statusFilter');
    const resetButton = document.getElementById('resetFilters');

    function applyFilters() {
        const searchTerm = searchInput.value.toLowerCase();
        const departmentId = departmentFilter.value;
        const semesterId = semesterFilter.value;
        const status = statusFilter.value;

        const rows = document.querySelectorAll('tbody tr');

        rows.forEach(row => {
            const courseCode = row.cells[0]?.textContent.toLowerCase() || '';
            const courseName = row.cells[1]?.textContent.toLowerCase() || '';
            const department = row.cells[2]?.textContent || '';
            const rowStatus = row.cells[7]?.textContent.toLowerCase() || '';

            const matchesSearch = courseCode.includes(searchTerm) || courseName.includes(searchTerm);
            const matchesDepartment = !departmentId || department.includes(departmentId);
            const matchesStatus = !status || rowStatus.includes(status);

            row.style.display = matchesSearch && matchesDepartment && matchesStatus ? '' : 'none';
        });
    }

    searchInput.addEventListener('input', applyFilters);
    departmentFilter.addEventListener('change', applyFilters);
    semesterFilter.addEventListener('change', applyFilters);
    statusFilter.addEventListener('change', applyFilters);

    resetButton.addEventListener('click', function() {
        searchInput.value = '';
        departmentFilter.value = '';
        semesterFilter.value = '';
        statusFilter.value = '';
        applyFilters();
    });
});
</script>
@endpush
