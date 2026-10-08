@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-1 fw-bold text-dark"><i class="bi bi-briefcase me-2"></i>HOD Operations Hub</h3>
            <p class="text-muted mb-0">Manage departmental approvals, moderate grades, and oversee operations for {{ $department->name }}.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 bg-primary text-white" style="border-radius: 12px;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="bg-white bg-opacity-25 rounded p-2">
                            <i class="bi bi-person-lines-fill fs-4"></i>
                        </div>
                        <span class="badge bg-white text-primary rounded-pill px-3">{{ $pendingLeaves->count() }} Pending</span>
                    </div>
                    <h5 class="fw-bold mb-1">Leave Approvals</h5>
                    <p class="mb-0 small text-white text-opacity-75">Pending staff leave requests</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 bg-warning text-dark" style="border-radius: 12px;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="bg-dark bg-opacity-10 rounded p-2">
                            <i class="bi bi-arrow-left-right fs-4"></i>
                        </div>
                        <span class="badge bg-dark text-white rounded-pill px-3">{{ $pendingProgramChanges->count() }} Pending</span>
                    </div>
                    <h5 class="fw-bold mb-1">Program Transfers</h5>
                    <p class="mb-0 small text-dark text-opacity-75">Student program change requests</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 bg-success text-white" style="border-radius: 12px;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="bg-white bg-opacity-25 rounded p-2">
                            <i class="bi bi-clipboard-check fs-4"></i>
                        </div>
                        <span class="badge bg-white text-success rounded-pill px-3">{{ $assignmentsToModerate->count() }} Tasks</span>
                    </div>
                    <h5 class="fw-bold mb-1">Result Moderation</h5>
                    <p class="mb-0 small text-white text-opacity-75">{{ $pendingGradesCount }} grades pending HOD approval</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Result Moderation -->
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold" style="color: #212529;"><i class="bi bi-clipboard-data text-success me-2"></i>Academic Result Moderation</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">Course</th>
                                    <th>Assessment / Assignment</th>
                                    <th>Lecturer</th>
                                    <th>Grades Pending</th>
                                    <th class="text-end pe-4">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($assignmentsToModerate as $assignment)
                                    <tr>
                                        <td class="ps-4 fw-bold">{{ $assignment->course->code }}</td>
                                        <td>{{ $assignment->title }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 30px; height: 30px; font-size: 0.8rem;">
                                                    {{ substr($assignment->course->instructor->first_name ?? 'L', 0, 1) }}
                                                </div>
                                                <span>{{ $assignment->course->instructor->name ?? 'Unknown' }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-warning text-dark rounded-pill px-3">{{ $assignment->grades->count() }} unmoderated</span>
                                        </td>
                                        <td class="text-end pe-4">
                                            <form action="{{ route('faculty.hod.operations.moderate-grades', $assignment) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success fw-bold px-3" onclick="return confirm('Approve and publish all pending grades for this assessment?')">
                                                    Approve Results
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">
                                            <i class="bi bi-check-circle fs-3 d-block mb-2 text-success"></i>
                                            All grades have been moderated.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <!-- Staff Leave Approvals -->
            <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold" style="color: #212529;"><i class="bi bi-calendar2-event text-primary me-2"></i>Staff Leave Requests</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($pendingLeaves as $leave)
                            <div class="list-group-item p-4">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h6 class="fw-bold mb-1">{{ $leave->user->name }}</h6>
                                        <span class="badge bg-info text-dark rounded-pill">{{ ucfirst($leave->type) }}</span>
                                    </div>
                                    <span class="text-muted small">{{ $leave->start_date->format('M d') }} - {{ $leave->end_date->format('M d, Y') }}</span>
                                </div>
                                <p class="text-muted small mb-3 border-start border-3 border-primary ps-3 py-1 bg-light">{{ $leave->reason }}</p>
                                <div class="d-flex gap-2">
                                    <form action="{{ route('faculty.hod.operations.leave.approve', $leave) }}" method="POST" class="flex-grow-1">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-success w-100 fw-bold">Approve</button>
                                    </form>
                                    <form action="{{ route('faculty.hod.operations.leave.reject', $leave) }}" method="POST" class="flex-grow-1">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-danger w-100 fw-bold">Reject</button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                                No pending leave requests.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <!-- Program Change Endorsements -->
            <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold" style="color: #212529;"><i class="bi bi-arrow-left-right text-warning me-2"></i>Program Transfers</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($pendingProgramChanges as $change)
                            <div class="list-group-item p-4">
                                <div class="mb-3">
                                    <h6 class="fw-bold mb-1">{{ $change->student->name }}</h6>
                                    <div class="d-flex align-items-center mt-2 small">
                                        <span class="text-muted text-decoration-line-through me-2">{{ $change->currentProgram->name ?? 'Unknown' }}</span>
                                        <i class="bi bi-arrow-right text-primary me-2"></i>
                                        <span class="fw-bold text-dark">{{ $change->requestedProgram->name }}</span>
                                    </div>
                                </div>
                                <p class="text-muted small mb-3 border-start border-3 border-warning ps-3 py-1 bg-light"><strong>Reason:</strong> {{ $change->reason }}</p>
                                <form action="{{ route('faculty.hod.operations.program-change.endorse', $change) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-primary w-100 fw-bold">Endorse Transfer to Registrar</button>
                                </form>
                            </div>
                        @empty
                            <div class="text-center py-5 text-muted">
                                <i class="bi bi-shield-check fs-3 d-block mb-2"></i>
                                No program transfer requests.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
