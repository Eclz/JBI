@extends('layouts.app')

@section('title', 'Course Enrollments - ' . $course->name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-1">Course Enrollments</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.courses.index') }}">Courses</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.courses.show', $course) }}">{{ $course->name }}</a></li>
                            <li class="breadcrumb-item active">Enrollments</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <a href="{{ route('admin.courses.show', $course) }}" class="btn btn-outline-secondary me-2">
                        <i class="bi bi-arrow-left"></i> Back to Course
                    </a>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#enrollStudentModal">
                        <i class="bi bi-person-plus"></i> Enroll Student
                    </button>
                </div>
            </div>

            <!-- Course Info Card -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h5 class="card-title mb-1">{{ $course->name }}</h5>
                            <p class="text-muted mb-2">{{ $course->code }}</p>
                            <p class="mb-0">{{ $course->description }}</p>
                        </div>
                        <div class="col-md-4">
                            <div class="row text-center">
                                <div class="col-4">
                                    <div class="border-end">
                                        <h4 class="mb-0 text-primary">{{ $enrollments->total() }}</h4>
                                        <small class="text-muted">Total Enrolled</small>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="border-end">
                                        <h4 class="mb-0 text-success">{{ $course->credits }}</h4>
                                        <small class="text-muted">Credits</small>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <h4 class="mb-0 text-info">{{ $course->capacity ?? 'Unlimited' }}</h4>
                                    <small class="text-muted">Capacity</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Enrollments Table -->
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h5 class="card-title mb-0">Enrolled Students</h5>
                        </div>
                        <div class="col-auto">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Search students..." id="searchInput">
                                <button class="btn btn-outline-secondary" type="button">
                                    <i class="bi bi-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($enrollments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Student</th>
                                        <th>Student ID</th>
                                        <th>Department</th>
                                        <th>Enrollment Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($enrollments as $enrollment)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="{{ $enrollment->student->profile_picture_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($enrollment->student->name) . '&background=007bff&color=fff' }}"
                                                         alt="{{ $enrollment->student->name }}"
                                                         class="rounded-circle me-2"
                                                         width="32" height="32">
                                                    <div>
                                                        <div class="fw-medium">{{ $enrollment->student->name }}</div>
                                                        <small class="text-muted">{{ $enrollment->student->email }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark">
                                                    {{ $enrollment->student->studentProfile->student_id ?? 'N/A' }}
                                                </span>
                                            </td>
                                            <td>
                                                {{ $enrollment->student->studentProfile->department->name ?? 'N/A' }}
                                            </td>
                                            <td>
                                                {{ $enrollment->enrollment_date->format('M d, Y') }}
                                            </td>
                                            <td>
                                                @switch($enrollment->status)
                                                    @case('enrolled')
                                                        <span class="badge bg-success">Enrolled</span>
                                                        @break
                                                    @case('dropped')
                                                        <span class="badge bg-danger">Dropped</span>
                                                        @break
                                                    @case('completed')
                                                        <span class="badge bg-primary">Completed</span>
                                                        @break
                                                    @default
                                                        <span class="badge bg-secondary">{{ ucfirst($enrollment->status) }}</span>
                                                @endswitch
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('admin.students.show', $enrollment->student) }}"
                                                       class="btn btn-outline-primary" title="View Student">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    @if($enrollment->status === 'enrolled')
                                                        <button type="button" class="btn btn-outline-danger"
                                                                onclick="confirmDrop({{ $enrollment->id }})" title="Drop Student">
                                                            <i class="bi bi-person-dash"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="card-footer">
                            {{ $enrollments->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-people display-1 text-muted"></i>
                            <h5 class="mt-3">No Students Enrolled</h5>
                            <p class="text-muted">This course doesn't have any enrolled students yet.</p>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#enrollStudentModal">
                                <i class="bi bi-person-plus"></i> Enroll First Student
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Enroll Student Modal -->
<div class="modal fade" id="enrollStudentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('admin.courses.enroll-student', $course) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Enroll Student</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="student_id" class="form-label">Select Student</label>
                        <select class="form-select" id="student_id" name="student_id" required>
                            <option value="">Choose a student...</option>
                            @foreach($availableStudents as $student)
                                <option value="{{ $student->id }}">
                                    {{ $student->name }}
                                    ({{ $student->studentProfile->student_id ?? 'No ID' }}) -
                                    {{ $student->email }}
                                </option>
                            @endforeach
                        </select>
                        @if($availableStudents->count() === 0)
                            <div class="form-text text-warning">
                                <i class="bi bi-exclamation-triangle"></i>
                                No available students to enroll. All active students are already enrolled in this course.
                            </div>
                        @endif
                    </div>

                    <div class="mb-3">
                        <label for="enrollment_date" class="form-label">Enrollment Date</label>
                        <input type="date" class="form-control" id="enrollment_date" name="enrollment_date"
                               value="{{ date('Y-m-d') }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes (Optional)</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3"
                                  placeholder="Any additional notes about this enrollment..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" {{ $availableStudents->count() === 0 ? 'disabled' : '' }}>
                        <i class="bi bi-person-plus"></i> Enroll Student
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Drop Student Confirmation Modal -->
<div class="modal fade" id="dropStudentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Drop Student</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to drop this student from the course?</p>
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle"></i>
                    <strong>Warning:</strong> This action will change the student's enrollment status to "dropped".
                    Their grades and attendance records will be preserved.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="dropStudentForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-person-dash"></i> Drop Student
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function confirmDrop(enrollmentId) {
    const form = document.getElementById('dropStudentForm');
    form.action = `{{ route('admin.courses.show', $course) }}/enrollments/${enrollmentId}/drop`;

    const modal = new bootstrap.Modal(document.getElementById('dropStudentModal'));
    modal.show();
}

// Search functionality
document.getElementById('searchInput').addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const tableRows = document.querySelectorAll('tbody tr');

    tableRows.forEach(row => {
        const studentName = row.querySelector('td:first-child .fw-medium').textContent.toLowerCase();
        const studentEmail = row.querySelector('td:first-child small').textContent.toLowerCase();
        const studentId = row.querySelector('td:nth-child(2)').textContent.toLowerCase();

        if (studentName.includes(searchTerm) || studentEmail.includes(searchTerm) || studentId.includes(searchTerm)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});

// Enhanced select dropdown
document.addEventListener('DOMContentLoaded', function() {
    const studentSelect = document.getElementById('student_id');
    if (studentSelect) {
        // Add search functionality to select dropdown
        studentSelect.addEventListener('focus', function() {
            this.size = Math.min(this.options.length, 8);
        });

        studentSelect.addEventListener('blur', function() {
            this.size = 1;
        });
    }
});
</script>
@endpush

@push('styles')
<style>
.table td {
    vertical-align: middle;
}

.btn-group-sm .btn {
    padding: 0.25rem 0.5rem;
}

.modal-lg {
    max-width: 800px;
}

#student_id[size] {
    height: auto !important;
}

.form-select:focus {
    border-color: #86b7fe;
    outline: 0;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}
</style>
@endpush
