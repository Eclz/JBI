@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i><strong>Error:</strong> {{ $errors->first() }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h2 class="mb-1" style="color: #1a202c; font-weight: 700;">{{ $course->name }}</h2>
            <p class="text-muted mb-0">
                <span class="badge bg-primary me-2">{{ $course->course_code ?? $course->code }}</span>
                Attendance & Session Tracking (Regular & Rescheduled Lessons)
            </p>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-primary fw-bold px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#recordAttendanceModal">
                <i class="bi bi-check2-square me-2"></i>Take Attendance
            </button>
            <a href="{{ route('faculty.courses.attendance.qr', $course) }}" class="btn btn-outline-dark">
                <i class="bi bi-qr-code me-2"></i>QR Code
            </a>
            <a href="{{ route('faculty.attendance.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>All Courses
            </a>
        </div>
    </div>

    @php
        $total = $attendanceRecords->total();
        $present = (clone $attendanceRecords->getCollection())->where('status', 'present')->count();
        $late = (clone $attendanceRecords->getCollection())->where('status', 'late')->count();
        $absent = (clone $attendanceRecords->getCollection())->where('status', 'absent')->count();
        $excused = (clone $attendanceRecords->getCollection())->where('status', 'excused')->count();
    @endphp

    <div class="row g-3 mb-4">
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small fw-semibold text-uppercase">Total Marked</div>
                    <div class="h3 mb-0 fw-bold text-dark">{{ $total }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm border-start border-success border-4">
                <div class="card-body">
                    <div class="text-muted small fw-semibold text-uppercase">Present (Page)</div>
                    <div class="h3 mb-0 fw-bold text-success">{{ $present }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm border-start border-warning border-4">
                <div class="card-body">
                    <div class="text-muted small fw-semibold text-uppercase">Late (Page)</div>
                    <div class="h3 mb-0 fw-bold text-warning">{{ $late }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm border-start border-danger border-4">
                <div class="card-body">
                    <div class="text-muted small fw-semibold text-uppercase">Absent (Page)</div>
                    <div class="h3 mb-0 fw-bold text-danger">{{ $absent }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-clock-history me-2 text-primary"></i>Attendance History</h5>
            <span class="badge bg-light text-dark border">{{ $attendanceRecords->total() }} Logged Entries</span>
        </div>
        <div class="card-body p-0">
            @if($attendanceRecords->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead style="background-color: #f8fafc;">
                            <tr>
                                <th style="padding: 1rem;">Lesson Date</th>
                                <th style="padding: 1rem;">Student</th>
                                <th style="padding: 1rem;">Status</th>
                                <th style="padding: 1rem;">Session Time</th>
                                <th style="padding: 1rem;">Notes / Reschedule</th>
                                <th style="padding: 1rem;">Marked By</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($attendanceRecords as $record)
                                <tr>
                                    <td style="padding: 1rem;" class="fw-semibold text-dark">
                                        <i class="bi bi-calendar-event me-1 text-muted"></i>
                                        {{ $record->attendance_date?->format('M d, Y') ?? '-' }}
                                    </td>
                                    <td style="padding: 1rem;">
                                        <div class="fw-bold text-dark">{{ $record->student->full_name ?? $record->student->name ?? 'N/A' }}</div>
                                        <small class="text-muted">{{ $record->student->email ?? '' }}</small>
                                    </td>
                                    <td style="padding: 1rem;">
                                        @if($record->status === 'present')
                                            <span class="badge bg-success-subtle text-success border border-success px-2 py-1 rounded-pill">
                                                <i class="bi bi-check-circle me-1"></i>Present
                                            </span>
                                        @elseif($record->status === 'late')
                                            <span class="badge bg-warning-subtle text-warning border border-warning px-2 py-1 rounded-pill">
                                                <i class="bi bi-clock me-1"></i>Late
                                            </span>
                                        @elseif($record->status === 'absent')
                                            <span class="badge bg-danger-subtle text-danger border border-danger px-2 py-1 rounded-pill">
                                                <i class="bi bi-x-circle me-1"></i>Absent
                                            </span>
                                        @elseif($record->status === 'excused')
                                            <span class="badge bg-info-subtle text-info border border-info px-2 py-1 rounded-pill">
                                                <i class="bi bi-info-circle me-1"></i>Excused
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">{{ ucfirst($record->status) }}</span>
                                        @endif
                                    </td>
                                    <td style="padding: 1rem;">
                                        @if($record->class_start_time || $record->class_end_time)
                                            <small class="fw-semibold text-muted">
                                                {{ $record->class_start_time ? date('H:i', strtotime($record->class_start_time)) : '' }}
                                                {{ $record->class_end_time ? ' - ' . date('H:i', strtotime($record->class_end_time)) : '' }}
                                            </small>
                                        @elseif($record->check_in_time)
                                            <small class="text-muted">In: {{ $record->check_in_time->format('H:i') }}</small>
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    </td>
                                    <td style="padding: 1rem;">
                                        @if($record->notes)
                                            <span class="badge bg-light text-dark border fw-normal" title="{{ $record->notes }}">
                                                <i class="bi bi-journal-text me-1 text-primary"></i>{{ Str::limit($record->notes, 30) }}
                                            </span>
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    </td>
                                    <td style="padding: 1rem;">
                                        <small class="text-muted">{{ $record->markedBy->full_name ?? $record->markedBy->name ?? 'System' }}</small>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer bg-white border-0 py-3">
                    {{ $attendanceRecords->links() }}
                </div>
            @else
                <div class="p-5 text-center text-muted">
                    <i class="bi bi-calendar-x" style="font-size: 3rem; color: #cbd5e1;"></i>
                    <p class="mt-3 mb-3">No attendance records found for this course yet.</p>
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#recordAttendanceModal">
                        <i class="bi bi-plus-lg me-1"></i>Take First Attendance
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal: Take / Record Attendance -->
<div class="modal fade" id="recordAttendanceModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 14px; overflow: hidden;">
            <form action="{{ route('faculty.courses.attendance.store', $course) }}" method="POST">
                @csrf
                <div class="modal-header text-white py-3" style="background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);">
                    <div>
                        <h5 class="modal-title fw-bold"><i class="bi bi-check2-square me-2"></i>Record Course Attendance</h5>
                        <small class="text-white-50">{{ $course->course_code ?? $course->code }} - {{ $course->name }}</small>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <!-- Session & Reschedule Details -->
                    <div class="card bg-light border-0 mb-4" style="border-radius: 10px;">
                        <div class="card-body p-3">
                            <h6 class="fw-bold text-dark mb-3"><i class="bi bi-calendar-check me-2 text-primary"></i>Lesson / Session Details (Regular or Rescheduled)</h6>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-bold small text-uppercase">Lesson Date <span class="text-danger">*</span></label>
                                    <input type="date" name="date" class="form-control" value="{{ request('date', date('Y-m-d')) }}" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold small text-uppercase">Start Time (Optional)</label>
                                    <input type="time" name="class_start_time" class="form-control" value="{{ request('start_time', '09:00') }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold small text-uppercase">End Time (Optional)</label>
                                    <input type="time" name="class_end_time" class="form-control" value="{{ request('end_time', '11:00') }}">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-bold small text-uppercase">Session Remarks / Reschedule Notes</label>
                                    <input type="text" name="notes" class="form-control" placeholder="e.g. Regular Lecture, Rescheduled Make-up Class, Practical Lab, etc." value="{{ request('notes') }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Student Roster -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold text-dark mb-0">
                            <i class="bi bi-people me-2 text-primary"></i>Enrolled Students ({{ count($enrolledStudents ?? []) }})
                        </h6>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-sm btn-outline-success" onclick="markAll('present')">
                                <i class="bi bi-check-all me-1"></i>Mark All Present
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="markAll('absent')">
                                <i class="bi bi-x-lg me-1"></i>Mark All Absent
                            </button>
                        </div>
                    </div>

                    @if(isset($enrolledStudents) && count($enrolledStudents) > 0)
                        <div class="table-responsive border rounded" style="max-height: 400px; overflow-y: auto;">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light sticky-top">
                                    <tr>
                                        <th style="width: 50px;">#</th>
                                        <th>Student Name</th>
                                        <th>Reg No / Email</th>
                                        <th class="text-center" style="width: 320px;">Attendance Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($enrolledStudents as $idx => $student)
                                        <tr>
                                            <td>{{ $idx + 1 }}</td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <img src="{{ $student->profile_picture_url }}" class="rounded-circle" width="32" height="32" style="object-fit: cover;">
                                                    <span class="fw-bold text-dark">{{ $student->full_name }}</span>
                                                </div>
                                            </td>
                                            <td>
                                                <small class="text-muted">{{ $student->studentProfile?->registration_number ?? $student->email }}</small>
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-group btn-group-sm w-100" role="group">
                                                    <input type="radio" class="btn-check att-status" name="attendance[{{ $student->id }}]" id="pres_{{ $student->id }}" value="present" checked>
                                                    <label class="btn btn-outline-success" for="pres_{{ $student->id }}">Present</label>

                                                    <input type="radio" class="btn-check att-status" name="attendance[{{ $student->id }}]" id="late_{{ $student->id }}" value="late">
                                                    <label class="btn btn-outline-warning" for="late_{{ $student->id }}">Late</label>

                                                    <input type="radio" class="btn-check att-status" name="attendance[{{ $student->id }}]" id="abs_{{ $student->id }}" value="absent">
                                                    <label class="btn btn-outline-danger" for="abs_{{ $student->id }}">Absent</label>

                                                    <input type="radio" class="btn-check att-status" name="attendance[{{ $student->id }}]" id="exc_{{ $student->id }}" value="excused">
                                                    <label class="btn btn-outline-info" for="exc_{{ $student->id }}">Excused</label>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-warning text-center">
                            <i class="bi bi-exclamation-circle me-2"></i>No enrolled students found for this course.
                        </div>
                    @endif
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-bold px-4" {{ (!isset($enrolledStudents) || count($enrolledStudents) === 0) ? 'disabled' : '' }}>
                        <i class="bi bi-save me-1"></i>Save Attendance Records
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function markAll(status) {
    document.querySelectorAll('.att-status[value="' + status + '"]').forEach(function(radio) {
        radio.checked = true;
    });
}

document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('take') === '1') {
        const modal = new bootstrap.Modal(document.getElementById('recordAttendanceModal'));
        modal.show();
    }
});
</script>
@endsection
