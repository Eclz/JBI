@extends('layouts.app')

@section('title', 'Student Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="mb-1">Student Management</h2>
                    <p class="text-muted mb-0">Manage student records and academic information</p>
                </div>
                <div class="d-flex gap-2">
                    @if(auth()->user()->hasPermission('students', 'create'))
                    <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#importModal">
                        <i class="bi bi-upload"></i> Import Students
                    </button>
                    <a href="{{ route('admin.students.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-lg"></i> Add New Student
                    </a>
                    @endif
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="bg-primary bg-opacity-10 rounded-3 p-3">
                                        <i class="bi bi-people-fill text-white fs-4"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-0 text-muted">Total Students</h6>
                                    <h4 class="mb-0">{{ $students->total() }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="bg-success bg-opacity-10 rounded-3 p-3">
                                        <i class="bi bi-check-circle-fill text-success fs-4"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-0 text-muted">Active Students</h6>
                                    <h4 class="mb-0">{{ $students->where('is_active', true)->count() }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="bg-info bg-opacity-10 rounded-3 p-3">
                                        <i class="bi bi-mortarboard-fill text-info fs-4"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-0 text-muted">New This Month</h6>
                                    <h4 class="mb-0">{{ $students->where('created_at', '>=', now()->startOfMonth())->count() }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="bg-warning bg-opacity-10 rounded-3 p-3">
                                        <i class="fa fa-building text-white fs-4"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-0 text-muted">Departments</h6>
                                    <h4 class="mb-0">{{ $departments->count() }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content Card -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h5 class="mb-0">Student Directory</h5>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex gap-2 justify-content-md-end">
                                <button class="btn btn-outline-secondary btn-sm" onclick="exportStudents()">
                                    <i class="bi bi-download"></i> Export
                                </button>
                                <button class="btn btn-outline-secondary btn-sm" id="resetFilters">
                                    <i class="bi bi-arrow-clockwise"></i> Reset
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Enhanced Search and Filter -->
                    <form method="GET" action="{{ route('admin.students.index') }}" id="filterForm">
                        <div class="row mb-4 col-12 align-items-center">
                            <div class="col-md-3">
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="bi bi-search text-muted"></i>
                                    </span>
                                    <input type="text" name="search" class="form-control border-start-0" id="searchInput"
                                           placeholder="Search by name, email, or ID..." value="{{ request('search') }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <select name="department" class="form-select" id="departmentFilter">
                                    <option value="">All Departments</option>
                                    @foreach($departments as $department)
                                        <option value="{{ $department->id }}" {{ request('department') == $department->id ? 'selected' : '' }}>{{ $department->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="status" class="form-select" id="statusFilter">
                                    <option value="">All Status</option>
                                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    <option value="graduated" {{ request('status') == 'graduated' ? 'selected' : '' }}>Graduated</option>
                                    <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                                    <option value="dropped" {{ request('status') == 'dropped' ? 'selected' : '' }}>Dropped</option>
                                </select>
                            </div>
                            <div class="col-md-1">
                                <select name="year_of_study" class="form-select" id="yearOfStudyFilter">
                                    <option value="">All Years</option>
                                    @for($i = 1; $i <= 4; $i++)
                                        <option value="{{ $i }}" {{ request('year_of_study') == $i ? 'selected' : '' }}>Yr {{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="semester" class="form-select" id="semesterFilter">
                                    <option value="">All Semesters</option>
                                    @for($i = 1; $i <= 8; $i++)
                                        <option value="{{ $i }}" {{ request('semester') == $i ? 'selected' : '' }}>Sem {{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-2 d-flex gap-2">
                                <button type="submit" class="btn btn-primary w-100 px-1">
                                    <i class="bi bi-search"></i> Filter
                                </button>
                                <a href="{{ route('admin.students.index') }}" class="btn btn-outline-secondary w-100 px-1">
                                    <i class="bi bi-arrow-clockwise"></i> Reset
                                </a>
                            </div>
                        </div>
                    </form>

                    <!-- Enhanced Students Table -->
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th class="border-0">
                                        <input type="checkbox" class="form-check-input" id="selectAll">
                                    </th>
                                    <th class="border-0">Student</th>
                                    <th class="border-0">Admission Details</th>
                                    <th class="border-0">Academic Info</th>
                                    <th class="border-0">Contact</th>
                                    <th class="border-0">Status</th>
                                    <th class="border-0">Performance</th>
                                    <th class="border-0 text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($students as $student)
                                <tr class="student-row">
                                    <td>
                                        <input type="checkbox" class="form-check-input student-checkbox"
                                               value="{{ $student->id }}">
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($student->profile_picture)
                                                <img src="{{ asset('storage/' . $student->profile_picture) }}"
                                                     class="rounded-circle me-3" width="40" height="40"
                                                     alt="{{ $student->name }}">
                                            @else
                                                <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-3"
                                                     style="width: 40px; height: 40px;">
                                                    <span class="text-white fw-bold">
                                                        {{ strtoupper(substr($student->first_name ?? $student->name, 0, 1)) }}{{ strtoupper(substr($student->last_name ?? '', 0, 1)) }}
                                                    </span>
                                                </div>
                                            @endif
                                            <div>
                                                <h6 class="mb-0">{{ $student->first_name }} {{ $student->last_name }}</h6>
                                                <small class="text-muted">{{ $student->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <span class="fw-semibold text-primary">{{ $student->studentProfile?->admission_number ?? 'N/A' }}</span>
                                            <br>
                                            <small class="text-muted">
                                                Admitted: {{ $student->studentProfile?->admission_date?->format('M d, Y') ?? 'N/A' }}
                                            </small>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <span class="fw-semibold">{{ $student->studentProfile?->department?->name ?? 'N/A' }}</span>
                                            <br>
                                            <small class="text-muted">
                                                {{ $student->studentProfile?->program ?? 'N/A' }}
                                                @if($student->studentProfile?->year_of_study)
                                                    • Yr {{ $student->studentProfile->year_of_study }}
                                                @endif
                                                @if($student->studentProfile?->current_semester)
                                                    • Sem {{ $student->studentProfile->current_semester }}
                                                @endif
                                            </small>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            @if($student->phone)
                                                <small class="d-block">
                                                    <i class="bi bi-telephone text-muted me-1"></i>
                                                    {{ $student->phone }}
                                                </small>
                                            @endif
                                            @if($student->date_of_birth)
                                                <small class="text-muted">
                                                    <i class="bi bi-calendar text-muted me-1"></i>
                                                    Age {{ \Carbon\Carbon::parse($student->date_of_birth)->age }}
                                                </small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @php
                                            // Determine status based on both user and student profile
                                            $userActive = $student->is_active;
                                            $profileStatus = $student->studentProfile?->status ?? 'unknown';

                                            if (!$userActive) {
                                                $badgeClass = 'danger';
                                                $statusText = 'Inactive';
                                                $statusIcon = 'x-circle';
                                            } elseif ($profileStatus === 'active') {
                                                $badgeClass = 'success';
                                                $statusText = 'Active';
                                                $statusIcon = 'check-circle';
                                            } elseif ($profileStatus === 'graduated') {
                                                $badgeClass = 'info';
                                                $statusText = 'Graduated';
                                                $statusIcon = 'mortarboard';
                                            } elseif ($profileStatus === 'suspended') {
                                                $badgeClass = 'warning';
                                                $statusText = 'Suspended';
                                                $statusIcon = 'pause-circle';
                                            } elseif ($profileStatus === 'dropped') {
                                                $badgeClass = 'secondary';
                                                $statusText = 'Dropped';
                                                $statusIcon = 'dash-circle';
                                            } else {
                                                $badgeClass = 'secondary';
                                                $statusText = 'Unknown';
                                                $statusIcon = 'question-circle';
                                            }
                                        @endphp

                                        <span class="badge bg-{{ $badgeClass }} d-flex align-items-center gap-1">
                                            <i class="bi bi-{{ $statusIcon }}"></i>
                                            {{ $statusText }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($student->studentProfile)
                                            <div>
                                                <div class="d-flex align-items-center gap-2 mb-1">
                                                    <span class="small fw-semibold">GPA:</span>
                                                    @php
                                                        $gpa = $student->studentProfile->current_gpa ?? 0;
                                                        $gpaClass = $gpa >= 3.5 ? 'success' : ($gpa >= 2.5 ? 'warning' : 'danger');
                                                    @endphp
                                                    <span class="badge bg-{{ $gpaClass }}-subtle text-{{ $gpaClass }}">
                                                        {{ number_format($gpa, 2) }}
                                                    </span>
                                                </div>
                                                <div class="progress" style="height: 4px;">
                                                    @php
                                                        $progress = $student->studentProfile->progress_percentage ?? 0;
                                                        $progressClass = $progress >= 75 ? 'success' : ($progress >= 50 ? 'warning' : 'danger');
                                                    @endphp
                                                    <div class="progress-bar bg-{{ $progressClass }}"
                                                         style="width: {{ $progress }}%"
                                                         title="Progress: {{ $progress }}%"></div>
                                                </div>
                                                <small class="text-muted">
                                                    {{ $student->studentProfile->total_credits_earned ?? 0 }}/{{ $student->studentProfile->total_credits_required ?? 120 }} credits
                                                </small>
                                            </div>
                                        @else
                                            <span class="text-muted small">No profile</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.students.show', $student) }}"
                                               class="btn btn-sm btn-outline-info" title="View Details">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            @if(auth()->user()->hasPermission('students', 'edit'))
                                            <a href="{{ route('admin.students.edit', $student) }}"
                                               class="btn btn-sm btn-outline-primary" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            @endif
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle"
                                                        data-bs-toggle="dropdown" title="More Actions">
                                                    <i class="bi bi-three-dots"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('admin.students.academic-record', $student) }}">
                                                            <i class="bi bi-file-text me-2"></i>Academic Record
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('admin.students.attendance', $student) }}">
                                                            <i class="bi bi-calendar-check me-2"></i>Attendance
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('admin.students.fees', $student) }}">
                                                            <i class="bi bi-credit-card me-2"></i>Fee Records
                                                        </a>
                                                    </li>
                                                    @if(auth()->user()->hasPermission('enrollments', 'create'))
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('admin.students.enroll-course', $student) }}">
                                                            <i class="bi bi-plus-circle me-2"></i>Enroll Course
                                                        </a>
                                                    </li>
                                                    @endif
                                                    @if(auth()->user()->hasPermission('students', 'delete'))
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <button type="button" class="dropdown-item text-danger"
                                                                onclick="confirmDelete({{ $student->id }})">
                                                            <i class="bi bi-trash me-2"></i>Delete
                                                        </button>
                                                    </li>
                                                    @endif
                                                </ul>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <div class="d-flex flex-column align-items-center">
                                            <i class="bi bi-people fs-1 text-muted mb-3"></i>
                                            <h5 class="text-muted">No students found</h5>
                                            <p class="text-muted mb-3">Get started by adding your first student</p>
                                            @if(auth()->user()->hasPermission('students', 'create'))
                                            <a href="{{ route('admin.students.create') }}" class="btn btn-primary">
                                                <i class="bi bi-plus-lg"></i> Add Student
                                            </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Enhanced Pagination -->
                    @if($students->hasPages())
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <div class="text-muted">
                                Showing {{ $students->firstItem() ?? 0 }} to {{ $students->lastItem() ?? 0 }}
                                of {{ $students->total() }} students
                            </div>
                            <div>
                                {{ $students->links() }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Actions Bar (Hidden by default) -->
<div id="bulkActionsBar" class="position-fixed bottom-0 start-50 translate-middle-x bg-primary text-white rounded-top px-4 py-3 shadow-lg" style="display: none; z-index: 1050;">
    <div class="d-flex align-items-center gap-3">
        <span id="selectedCount">0</span> students selected
        <div class="btn-group btn-group-sm">
            <button class="btn btn-light btn-sm" onclick="bulkAction('activate')">
                <i class="bi bi-check-circle"></i> Activate
            </button>
            <button class="btn btn-light btn-sm" onclick="bulkAction('deactivate')">
                <i class="bi bi-x-circle"></i> Deactivate
            </button>
            <button class="btn btn-light btn-sm" onclick="bulkAction('export')">
                <i class="bi bi-download"></i> Export
            </button>
        </div>
        <button class="btn btn-outline-light btn-sm" onclick="clearSelection()">
            <i class="bi bi-x"></i> Clear
        </button>
    </div>
</div>

<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Import Students</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.students.bulk-import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="importFile" class="form-label">Select CSV or Excel file</label>
                        <input type="file" class="form-control" id="importFile" name="file"
                               accept=".csv,.xlsx,.xls" required>
                        <div class="form-text">
                            File should contain columns: first_name, last_name, email, phone, department_id, program
                        </div>
                    </div>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Note:</strong> Duplicate emails will be skipped. A detailed import report will be provided.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Import Students</button>
                </div>
            </form>
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
                <div class="text-center">
                    <i class="bi bi-exclamation-triangle text-warning fs-1 mb-3"></i>
                    <h5>Are you sure?</h5>
                    <p class="text-muted">This will permanently delete the student and all associated records. This action cannot be undone.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash"></i> Delete Student
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Enhanced JavaScript functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const departmentFilter = document.getElementById('departmentFilter');
    const statusFilter = document.getElementById('statusFilter');
    const semesterFilter = document.getElementById('semesterFilter');
    const yearOfStudyFilter = document.getElementById('yearOfStudyFilter');
    const sortBy = document.getElementById('sortBy');
    const resetButton = document.getElementById('resetFilters');
    const selectAllCheckbox = document.getElementById('selectAll');
    const studentCheckboxes = document.querySelectorAll('.student-checkbox');
    const bulkActionsBar = document.getElementById('bulkActionsBar');
    const selectedCount = document.getElementById('selectedCount');

    // Search and filter functionality
    const filterForm = document.getElementById('filterForm');

    if (departmentFilter) {
        departmentFilter.addEventListener('change', function() {
            filterForm.submit();
        });
    }
    if (statusFilter) {
        statusFilter.addEventListener('change', function() {
            filterForm.submit();
        });
    }
    if (semesterFilter) {
        semesterFilter.addEventListener('change', function() {
            filterForm.submit();
        });
    }
    if (yearOfStudyFilter) {
        yearOfStudyFilter.addEventListener('change', function() {
            filterForm.submit();
        });
    }

    if (resetButton) {
        resetButton.addEventListener('click', function(e) {
            e.preventDefault();
            window.location.href = "{{ route('admin.students.index') }}";
        });
    }

    // Bulk selection functionality
    selectAllCheckbox.addEventListener('change', function() {
        const isChecked = this.checked;
        studentCheckboxes.forEach(checkbox => {
            checkbox.checked = isChecked;
        });
        updateBulkActionsBar();
    });

    studentCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateBulkActionsBar);
    });

    function updateBulkActionsBar() {
        const checkedBoxes = document.querySelectorAll('.student-checkbox:checked');
        const count = checkedBoxes.length;

        if (count > 0) {
            selectedCount.textContent = count;
            bulkActionsBar.style.display = 'block';
        } else {
            bulkActionsBar.style.display = 'none';
        }

        // Update select all checkbox state
        selectAllCheckbox.indeterminate = count > 0 && count < studentCheckboxes.length;
        selectAllCheckbox.checked = count === studentCheckboxes.length;
    }
});

// Utility functions
function confirmDelete(studentId) {
    const form = document.getElementById('deleteForm');
    form.action = `/admin/students/${studentId}`;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

function clearSelection() {
    document.querySelectorAll('.student-checkbox').forEach(checkbox => {
        checkbox.checked = false;
    });
    document.getElementById('selectAll').checked = false;
    document.getElementById('bulkActionsBar').style.display = 'none';
}

function bulkAction(action) {
    const selectedIds = Array.from(document.querySelectorAll('.student-checkbox:checked'))
        .map(checkbox => checkbox.value);

    if (selectedIds.length === 0) {
        alert('Please select at least one student.');
        return;
    }

    switch(action) {
        case 'activate':
            if (confirm(`Activate ${selectedIds.length} selected students?`)) {
                // Implement bulk activation
                console.log('Bulk activate:', selectedIds);
            }
            break;
        case 'deactivate':
            if (confirm(`Deactivate ${selectedIds.length} selected students?`)) {
                // Implement bulk deactivation
                console.log('Bulk deactivate:', selectedIds);
            }
            break;
        case 'export':
            // Implement bulk export
            console.log('Bulk export:', selectedIds);
            break;
    }
}

function exportStudents() {
    // Implement export functionality
    window.location.href = '/admin/students/export';
}
</script>
@endpush

@push('styles')
<style>
.table th {
    font-weight: 600;
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.student-row:hover {
    background-color: rgba(0, 123, 255, 0.05);
}

.progress {
    background-color: rgba(0, 0, 0, 0.1);
}

.badge {
    font-size: 0.75rem;
    font-weight: 500;
}

.btn-group .dropdown-toggle::after {
    margin-left: 0;
}

#bulkActionsBar {
    min-width: 400px;
}

.card {
    border-radius: 12px;
}

.input-group-text {
    background-color: #f8f9fa;
}

.table-responsive {
    border-radius: 8px;
}
</style>
@endpush
