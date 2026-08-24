@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">Student Details</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.students.index') }}">Students</a></li>
                            <li class="breadcrumb-item active">{{ $student->name }}</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    @if(auth()->user()->hasPermission('students', 'edit'))
                    <a href="{{ route('admin.students.edit', $student) }}" class="btn btn-primary">
                        <i class="fas fa-edit"></i> Edit Student
                    </a>
                    @endif
                    @if(auth()->user()->hasPermission('enrollments', 'create'))
                    <a href="{{ route('admin.students.enroll-course', $student) }}" class="btn btn-success">
                        <i class="fas fa-plus"></i> Enroll in Course
                    </a>
                    @endif
                </div>
            </div>

            <div class="row">
                <!-- Student Information -->
                <div class="col-lg-4">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-user"></i> Personal Information
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="text-center mb-3">
                                @if($student->profile_picture)
                                    <img src="{{ asset('storage/' . $student->profile_picture) }}"
                                         alt="{{ $student->name }}"
                                         class="rounded-circle"
                                         width="100" height="100">
                                @else
                                    <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center"
                                         style="width: 100px; height: 100px;">
                                        <span class="text-white fs-2">{{ $student->initials }}</span>
                                    </div>
                                @endif
                                <h4 class="mt-2 mb-0">{{ $student->name }}</h4>
                                <p class="text-muted">{{ $student->studentProfile->admission_number ?? 'N/A' }}</p>
                            </div>

                            <table class="table table-sm">
                                <tr>
                                    <td><strong>Email:</strong></td>
                                    <td>{{ $student->email }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Phone:</strong></td>
                                    <td>{{ $student->phone ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Date of Birth:</strong></td>
                                    <td>{{ $student->date_of_birth ? $student->date_of_birth->format('M d, Y') : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Gender:</strong></td>
                                    <td>{{ ucfirst($student->gender ?? 'N/A') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Address:</strong></td>
                                    <td>{{ $student->address ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        @if($student->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-danger">Inactive</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Academic Information -->
                    @if($student->studentProfile)
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-graduation-cap"></i> Academic Information
                            </h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>Department:</strong></td>
                                    <td>
                                        @if($student->studentProfile->department)
                                            @if(is_object($student->studentProfile->department))
                                                {{ $student->studentProfile->department->name ?? 'N/A' }}
                                            @else
                                                {{ $student->studentProfile->department }}
                                            @endif
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Program:</strong></td>
                                    <td>{{ $student->studentProfile->program ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Specialization:</strong></td>
                                    <td>{{ $student->studentProfile->specialization ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Current Semester:</strong></td>
                                    <td>{{ $student->studentProfile->current_semester ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Current GPA:</strong></td>
                                    <td>{{ number_format($student->studentProfile->current_gpa ?? 0, 2) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Cumulative GPA:</strong></td>
                                    <td>{{ number_format($student->studentProfile->cumulative_gpa ?? 0, 2) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Credits Earned:</strong></td>
                                    <td>{{ $student->studentProfile->total_credits_earned ?? 0 }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Admission Date:</strong></td>
                                    <td>{{ $student->studentProfile->admission_date ? $student->studentProfile->admission_date->format('M d, Y') : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Academic Status:</strong></td>
                                    <td>
                                        @php
                                            $status = $student->studentProfile->status ?? 'active';
                                            $badgeClass = match($status) {
                                                'active' => 'bg-success',
                                                'inactive' => 'bg-secondary',
                                                'graduated' => 'bg-primary',
                                                'suspended' => 'bg-danger',
                                                'dropped' => 'bg-warning',
                                                default => 'bg-secondary'
                                            };
                                        @endphp
                                        <span class="badge {{ $badgeClass }}">{{ ucfirst($status) }}</span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Main Content -->
                <div class="col-lg-8">
                    <!-- Navigation Tabs -->
                    <ul class="nav nav-tabs mb-4" id="studentTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="courses-tab" data-bs-toggle="tab" data-bs-target="#courses" type="button" role="tab">
                                <i class="fas fa-book"></i> Enrolled Courses
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="grades-tab" data-bs-toggle="tab" data-bs-target="#grades" type="button" role="tab">
                                <i class="fas fa-chart-line"></i> Grades
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="attendance-tab" data-bs-toggle="tab" data-bs-target="#attendance" type="button" role="tab">
                                <i class="fas fa-calendar-check"></i> Attendance
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="fees-tab" data-bs-toggle="tab" data-bs-target="#fees" type="button" role="tab">
                                <i class="fas fa-dollar-sign"></i> Fees
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="notes-tab" data-bs-toggle="tab" data-bs-target="#notes" type="button" role="tab">
                                <i class="fas fa-sticky-note"></i> Notes
                            </button>
                        </li>
                    </ul>

                    <!-- Tab Content -->
                    <div class="tab-content" id="studentTabsContent">
                        <!-- Enrolled Courses -->
                        <div class="tab-pane fade show active" id="courses" role="tabpanel">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Enrolled Courses</h5>
                                </div>
                                <div class="card-body">
                                    @if($student->enrolledCourses->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Course Code</th>
                                                        <th>Course Name</th>
                                                        <th>Department</th>
                                                        <th>Credits</th>
                                                        <th>Instructor</th>
                                                        <th>Status</th>
                                                        <th>Enrollment Date</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($student->enrolledCourses as $course)
                                                        <tr>
                                                            <td><strong>{{ $course->code }}</strong></td>
                                                            <td>{{ $course->name }}</td>
                                                            <td>{{ $course->department->name ?? 'N/A' }}</td>
                                                            <td>{{ $course->credits }}</td>
                                                            <td>{{ $course->instructor->name ?? 'N/A' }}</td>
                                                            <td>
                                                                @php
                                                                    $status = $course->pivot->status ?? 'enrolled';
                                                                    $badgeClass = match($status) {
                                                                        'enrolled' => 'bg-success',
                                                                        'pending' => 'bg-warning',
                                                                        'dropped' => 'bg-danger',
                                                                        'completed' => 'bg-primary',
                                                                        default => 'bg-secondary'
                                                                    };
                                                                @endphp
                                                                <span class="badge {{ $badgeClass }}">{{ ucfirst($status) }}</span>
                                                            </td>
                                                            <td>{{ $course->pivot->enrollment_date ? \Carbon\Carbon::parse($course->pivot->enrollment_date)->format('M d, Y') : 'N/A' }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="text-center py-4">
                                            <i class="fas fa-book fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">No courses enrolled yet.</p>
                                            <a href="{{ route('admin.students.enroll-course', $student) }}" class="btn btn-primary">
                                                <i class="fas fa-plus"></i> Enroll in Course
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Grades -->
                        <div class="tab-pane fade" id="grades" role="tabpanel">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Academic Grades</h5>
                                </div>
                                <div class="card-body">
                                    @if($student->grades->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Course</th>
                                                        <th>Grade</th>
                                                        <th>Points</th>
                                                        <th>Credits</th>
                                                        <th>Date</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($student->grades as $grade)
                                                        <tr>
                                                            <td>{{ $grade->course->name ?? 'N/A' }}</td>
                                                            <td>
                                                                <span class="badge bg-primary">{{ $grade->grade }}</span>
                                                            </td>
                                                            <td>{{ $grade->points }}</td>
                                                            <td>{{ $grade->course->credits ?? 0 }}</td>
                                                            <td>{{ $grade->created_at->format('M d, Y') }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="text-center py-4">
                                            <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">No grades recorded yet.</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Attendance -->
                        <div class="tab-pane fade" id="attendance" role="tabpanel">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Attendance Records</h5>
                                </div>
                                <div class="card-body">
                                    @if($student->attendanceRecords->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Course</th>
                                                        <th>Date</th>
                                                        <th>Status</th>
                                                        <th>Notes</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($student->attendanceRecords as $attendance)
                                                        <tr>
                                                            <td>{{ $attendance->course->name ?? 'N/A' }}</td>
                                                            <td>{{ \Carbon\Carbon::parse($attendance->date)->format('M d, Y') }}</td>
                                                            <td>
                                                                @php
                                                                    $status = $attendance->status;
                                                                    $badgeClass = match($status) {
                                                                        'present' => 'bg-success',
                                                                        'absent' => 'bg-danger',
                                                                        'late' => 'bg-warning',
                                                                        'excused' => 'bg-info',
                                                                        default => 'bg-secondary'
                                                                    };
                                                                @endphp
                                                                <span class="badge {{ $badgeClass }}">{{ ucfirst($status) }}</span>
                                                            </td>
                                                            <td>{{ $attendance->notes ?? '-' }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="text-center py-4">
                                            <i class="fas fa-calendar-check fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">No attendance records found.</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Fees -->
                        <div class="tab-pane fade" id="fees" role="tabpanel">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Fee Records</h5>
                                </div>
                                <div class="card-body">
                                    @if($student->feeRecords->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Description</th>
                                                        <th>Amount</th>
                                                        <th>Due Date</th>
                                                        <th>Status</th>
                                                        <th>Payment Date</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($student->feeRecords as $fee)
                                                        <tr>
                                                            <td>{{ $fee->description ?? 'N/A' }}</td>
                                                            <td>{{ $currencyCode }} {{ number_format($fee->amount, 2) }}</td>
                                                            <td>{{ $fee->due_date ? \Carbon\Carbon::parse($fee->due_date)->format('M d, Y') : 'N/A' }}</td>
                                                            <td>
                                                                @php
                                                                    $status = $fee->status;
                                                                    $badgeClass = match($status) {
                                                                        'paid' => 'bg-success',
                                                                        'pending' => 'bg-warning',
                                                                        'overdue' => 'bg-danger',
                                                                        'cancelled' => 'bg-secondary',
                                                                        default => 'bg-secondary'
                                                                    };
                                                                @endphp
                                                                <span class="badge {{ $badgeClass }}">{{ ucfirst($status) }}</span>
                                                            </td>
                                                            <td>{{ $fee->payment_date ? \Carbon\Carbon::parse($fee->payment_date)->format('M d, Y') : '-' }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="text-center py-4">
                                            <i class="fas fa-dollar-sign fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">No fee records found.</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="tab-pane fade" id="notes" role="tabpanel">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="card-title mb-0">Student Notes</h5>
                                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addNoteModal">
                                        <i class="fas fa-plus"></i> Add Note
                                    </button>
                                </div>
                                <div class="card-body">
                                    @if($student->studentNotes->count() > 0)
                                        <div class="timeline">
                                            @foreach($student->studentNotes as $note)
                                                <div class="timeline-item mb-4">
                                                    <div class="timeline-marker">
                                                        <div class="timeline-marker-icon bg-{{ $note->type_badge }}">
                                                            <i class="fas fa-sticky-note"></i>
                                                        </div>
                                                    </div>
                                                    <div class="timeline-content">
                                                        <div class="card">
                                                            <div class="card-header py-2">
                                                                <div class="d-flex justify-content-between align-items-center">
                                                                    <div>
                                                                        <span class="badge bg-{{ $note->type_badge }}">{{ ucfirst($note->type) }}</span>
                                                                        <span class="badge bg-{{ $note->priority_badge }}">{{ ucfirst($note->priority) }}</span>
                                                                        @if($note->is_private)
                                                                            <span class="badge bg-dark">Private</span>
                                                                        @endif
                                                                    </div>
                                                                    <small class="text-muted">
                                                                        {{ $note->noted_at->format('M d, Y g:i A') }}
                                                                    </small>
                                                                </div>
                                                            </div>
                                                            <div class="card-body py-2">
                                                                <p class="mb-2">{{ $note->note }}</p>
                                                                <small class="text-muted">
                                                                    <i class="fas fa-user"></i> {{ $note->createdBy->name }}
                                                                </small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="text-center py-4">
                                            <i class="fas fa-sticky-note fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">No notes recorded yet.</p>
                                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addNoteModal">
                                                <i class="fas fa-plus"></i> Add First Note
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Note Modal -->
<div class="modal fade" id="addNoteModal" tabindex="-1" aria-labelledby="addNoteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('admin.students.notes.add', $student) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="addNoteModalLabel">Add Student Note</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="type" class="form-label">Note Type</label>
                                <select class="form-select" id="type" name="type" required>
                                    <option value="">Select Type</option>
                                    <option value="general">General</option>
                                    <option value="academic">Academic</option>
                                    <option value="disciplinary">Disciplinary</option>
                                    <option value="counseling">Counseling</option>
                                    <option value="medical">Medical</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="priority" class="form-label">Priority</label>
                                <select class="form-select" id="priority" name="priority" required>
                                    <option value="">Select Priority</option>
                                    <option value="low">Low</option>
                                    <option value="medium" selected>Medium</option>
                                    <option value="high">High</option>
                                    <option value="urgent">Urgent</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="note" class="form-label">Note Content</label>
                        <textarea class="form-control" id="note" name="note" rows="5" required placeholder="Enter your note here..."></textarea>
                        <div class="form-text">Maximum 2000 characters</div>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_private" name="is_private" value="1">
                            <label class="form-check-label" for="is_private">
                                Mark as Private (only visible to administrators)
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Note
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #dee2e6;
}

.timeline-item {
    position: relative;
}

.timeline-marker {
    position: absolute;
    left: -22px;
    top: 0;
}

.timeline-marker-icon {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 12px;
}

.timeline-content {
    margin-left: 20px;
}

.bg-general { background-color: #6c757d !important; }
.bg-academic { background-color: #0d6efd !important; }
.bg-disciplinary { background-color: #dc3545 !important; }
.bg-counseling { background-color: #0dcaf0 !important; }
.bg-medical { background-color: #fd7e14 !important; }
</style>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Character counter for note textarea
    const noteTextarea = document.getElementById('note');
    if (noteTextarea) {
        noteTextarea.addEventListener('input', function() {
            const maxLength = 2000;
            const currentLength = this.value.length;
            const remaining = maxLength - currentLength;

            let helpText = this.parentNode.querySelector('.form-text');
            if (remaining < 100) {
                helpText.textContent = `${remaining} characters remaining`;
                helpText.className = 'form-text text-warning';
            } else {
                helpText.textContent = 'Maximum 2000 characters';
                helpText.className = 'form-text';
            }

            if (remaining < 0) {
                helpText.textContent = `${Math.abs(remaining)} characters over limit`;
                helpText.className = 'form-text text-danger';
            }
        });
    }
});
</script>
@endsection
