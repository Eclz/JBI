@extends('layouts.app')

@section('title', 'HOD Operations & Result Moderation Hub')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Header Section -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge rounded-pill bg-primary bg-opacity-10 text-primary px-3 py-2 fw-semibold">
                    <i class="bi bi-shield-check me-1"></i> Departmental Operations & Quality Assurance
                </span>
                <span class="badge rounded-pill bg-light text-secondary border px-3 py-2 fw-normal">
                    {{ $department->name }} ({{ $department->code ?? 'HOD' }})
                </span>
            </div>
            <h1 class="h3 mb-1 text-dark fw-bold mt-2">
                <i class="bi bi-briefcase text-primary me-2"></i>Head of Department Operations Hub
            </h1>
            <p class="text-muted mb-0">
                Departmental governance, staff leave approvals, student program transfers, and <strong>Academic Result Moderation</strong>.
            </p>
        </div>

        @if(!empty($accessibleDepartments) && $accessibleDepartments->count() > 1)
            <div class="d-flex align-items-center gap-2">
                <form action="{{ route('faculty.hod.operations.index') }}" method="GET" class="d-flex align-items-center gap-2">
                    <label class="text-muted small fw-semibold text-nowrap"><i class="bi bi-buildings me-1"></i>Department:</label>
                    <select name="department_id" class="form-select form-select-sm" onchange="this.form.submit()">
                        @foreach($accessibleDepartments as $d)
                            <option value="{{ $d->id }}" {{ $department->id == $d->id ? 'selected' : '' }}>
                                {{ $d->name }} ({{ $d->code }})
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>
        @endif
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm alert-dismissible fade show d-flex align-items-center" role="alert">
            <i class="bi bi-check-circle-fill fs-5 me-2 text-success"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- High-Impact Operations KPI Cards Strip -->
    <div class="row g-3 mb-4">
        <!-- 1. Result Moderation Pending KPI -->
        <div class="col-xl-4 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white overflow-hidden position-relative">
                <div class="position-absolute top-0 start-0 w-100" style="height: 4px; background: linear-gradient(90deg, #10b981, #059669);"></div>
                <div class="card-body p-3 pt-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-uppercase fw-bold text-muted small" style="letter-spacing: 0.5px;">Result Moderation</span>
                        @if($pendingItemsCount > 0)
                            <span class="badge rounded-pill bg-danger bg-opacity-10 text-danger fw-bold px-2.5 py-1">
                                <i class="bi bi-exclamation-circle me-1"></i>{{ $pendingItemsCount }} Tasks Pending
                            </span>
                        @else
                            <span class="badge rounded-pill bg-success bg-opacity-10 text-success fw-bold px-2.5 py-1">
                                <i class="bi bi-check2-all me-1"></i>Up to Date
                            </span>
                        @endif
                    </div>
                    <div class="d-flex align-items-baseline gap-2 mb-1">
                        <h2 class="fw-bold text-dark mb-0">{{ $totalPendingGrades }}</h2>
                        <span class="text-muted small">candidate marks awaiting sign-off</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center text-muted small mt-3 pt-2 border-top">
                        <span><i class="bi bi-journal-check me-1 text-success"></i>{{ $moderatedItemsCount }} Moderated & Published</span>
                        <span class="badge bg-light text-secondary border">{{ $totalAssessmentsCount }} Total Course Items</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Staff Leave Approvals KPI -->
        <div class="col-xl-4 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white overflow-hidden position-relative">
                <div class="position-absolute top-0 start-0 w-100" style="height: 4px; background: linear-gradient(90deg, #2563eb, #3b82f6);"></div>
                <div class="card-body p-3 pt-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-uppercase fw-bold text-muted small" style="letter-spacing: 0.5px;">Staff Leave Approvals</span>
                        <span class="badge rounded-pill bg-primary bg-opacity-10 text-primary fw-bold px-2.5 py-1">
                            {{ $pendingLeaves->count() }} Pending
                        </span>
                    </div>
                    <div class="d-flex align-items-baseline gap-2 mb-1">
                        <h2 class="fw-bold text-dark mb-0">{{ $pendingLeaves->count() }}</h2>
                        <span class="text-muted small">academic staff leave requests</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center text-muted small mt-3 pt-2 border-top">
                        <span><i class="bi bi-people me-1 text-primary"></i>{{ $department->facultyMembers->count() }} Department Lecturers</span>
                        <a href="#staffLeaveSection" class="text-decoration-none text-primary fw-semibold small">View Requests &rarr;</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Program Transfers KPI -->
        <div class="col-xl-4 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white overflow-hidden position-relative">
                <div class="position-absolute top-0 start-0 w-100" style="height: 4px; background: linear-gradient(90deg, #d97706, #f59e0b);"></div>
                <div class="card-body p-3 pt-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-uppercase fw-bold text-muted small" style="letter-spacing: 0.5px;">Program Transfers</span>
                        <span class="badge rounded-pill bg-warning bg-opacity-10 text-dark fw-bold px-2.5 py-1">
                            {{ $pendingProgramChanges->count() }} Pending
                        </span>
                    </div>
                    <div class="d-flex align-items-baseline gap-2 mb-1">
                        <h2 class="fw-bold text-dark mb-0">{{ $pendingProgramChanges->count() }}</h2>
                        <span class="text-muted small">student transfer endorsements</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center text-muted small mt-3 pt-2 border-top">
                        <span><i class="bi bi-mortarboard me-1 text-warning"></i>{{ $department->programs->count() }} Department Programs</span>
                        <a href="#programTransferSection" class="text-decoration-none text-warning fw-semibold small">View Transfers &rarr;</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MAIN SECTION: Result Moderation Hub -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white border-bottom py-3 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
            <div>
                <h5 class="mb-0 fw-bold text-dark">
                    <i class="bi bi-clipboard2-check text-success me-2"></i>Departmental Course Result Moderation
                </h5>
                <small class="text-muted">Review, calibrate grade distributions, adjust candidate marks, and endorse official results for publishing to student transcripts.</small>
            </div>
            @if($pendingItemsCount > 0)
                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2 rounded-pill fw-semibold">
                    <i class="bi bi-clock-history me-1"></i>{{ $pendingItemsCount }} Assessments Awaiting Moderation
                </span>
            @endif
        </div>

        <!-- Filter & Search Toolbar -->
        <div class="card-body bg-light border-bottom p-3">
            <form action="{{ route('faculty.hod.operations.index') }}" method="GET" class="row g-2 align-items-center">
                @if(request('department_id'))
                    <input type="hidden" name="department_id" value="{{ request('department_id') }}">
                @endif

                <!-- Type Filters Pills -->
                <div class="col-12 col-md-5">
                    <div class="btn-group btn-group-sm w-100" role="group">
                        <a href="{{ route('faculty.hod.operations.index', array_merge(request()->except('type', 'page'), ['type' => 'all'])) }}"
                           class="btn {{ $filterType === 'all' ? 'btn-dark fw-bold' : 'btn-outline-secondary' }}">
                            All ({{ $totalAssessmentsCount }})
                        </a>
                        <a href="{{ route('faculty.hod.operations.index', array_merge(request()->except('type', 'page'), ['type' => 'assignment'])) }}"
                           class="btn {{ $filterType === 'assignment' ? 'btn-primary fw-bold text-white' : 'btn-outline-primary' }}">
                            <i class="bi bi-journal-text me-1"></i>Coursework
                        </a>
                        <a href="{{ route('faculty.hod.operations.index', array_merge(request()->except('type', 'page'), ['type' => 'exam'])) }}"
                           class="btn {{ $filterType === 'exam' ? 'btn-danger fw-bold text-white' : 'btn-outline-danger' }}">
                            <i class="bi bi-file-earmark-medical me-1"></i>Exams
                        </a>
                        <a href="{{ route('faculty.hod.operations.index', array_merge(request()->except('type', 'page'), ['type' => 'quiz'])) }}"
                           class="btn {{ $filterType === 'quiz' ? 'btn-warning fw-bold text-dark' : 'btn-outline-warning text-dark' }}">
                            <i class="bi bi-patch-question me-1"></i>Quizzes
                        </a>
                    </div>
                </div>

                <!-- Status Filter -->
                <div class="col-sm-6 col-md-3">
                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="all" {{ $filterStatus === 'all' ? 'selected' : '' }}>All Moderation Statuses</option>
                        <option value="pending" {{ $filterStatus === 'pending' ? 'selected' : '' }}>Pending HOD Moderation ({{ $pendingItemsCount }})</option>
                        <option value="moderated" {{ $filterStatus === 'moderated' ? 'selected' : '' }}>Moderated & Published ({{ $moderatedItemsCount }})</option>
                    </select>
                </div>

                <!-- Course Selector Filter -->
                <div class="col-sm-6 col-md-3">
                    <select name="course_id" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="all">All Department Courses</option>
                        @foreach($departmentCourses as $c)
                            <option value="{{ $c->id }}" {{ $filterCourse == $c->id ? 'selected' : '' }}>
                                {{ $c->code }} - {{ $c->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                @if(request()->hasAny(['type', 'status', 'course_id']))
                    <div class="col-auto">
                        <a href="{{ route('faculty.hod.operations.index', request()->only('department_id')) }}" class="btn btn-sm btn-outline-secondary" title="Reset Filters">
                            <i class="bi bi-x-circle"></i>
                        </a>
                    </div>
                @endif
            </form>
        </div>

        <!-- Moderation Items Table -->
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-uppercase small text-muted">
                        <tr>
                            <th class="ps-4 py-3">Course</th>
                            <th class="py-3">Assessment Title</th>
                            <th class="py-3">Type</th>
                            <th class="py-3">Course Lecturer</th>
                            <th class="py-3 text-center">Candidates Graded</th>
                            <th class="py-3 text-center">Class Average</th>
                            <th class="py-3 text-center">Moderation Status</th>
                            <th class="text-end pe-4 py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($moderationItems as $item)
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold text-dark font-monospace">{{ $item->course_code }}</div>
                                    <small class="text-muted d-inline-block text-truncate" style="max-width: 180px;">{{ $item->course_name }}</small>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $item->title }}</div>
                                    <small class="text-muted">
                                        Max Score: <strong>{{ number_format($item->max_points, 1) }} pts</strong>
                                        @if($item->date)
                                            &bull; {{ \Carbon\Carbon::parse($item->date)->format('M d, Y') }}
                                        @endif
                                    </small>
                                </td>
                                <td>
                                    <span class="{{ $item->type_badge }} rounded-pill px-2.5 py-1 small">
                                        <i class="bi {{ $item->type_icon }} me-1"></i>{{ $item->type_label }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center me-2 fw-bold" style="width: 28px; height: 28px; font-size: 0.75rem;">
                                            {{ substr($item->lecturer_name, 0, 1) }}
                                        </div>
                                        <span class="small fw-semibold text-dark">{{ $item->lecturer_name }}</span>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="fw-bold text-dark">{{ $item->graded_count }} / {{ $item->total_submissions }}</div>
                                    @if($item->unmoderated_count > 0)
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill py-0 px-2 small">
                                            {{ $item->unmoderated_count }} pending
                                        </span>
                                    @else
                                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill py-0 px-2 small">
                                            All published
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="fw-bold {{ $item->avg_percentage >= 70 ? 'text-success' : ($item->avg_percentage >= 50 ? 'text-warning' : 'text-danger') }}">
                                        {{ $item->avg_score }} / {{ $item->max_points }}
                                    </span>
                                    <div class="small text-muted">({{ $item->avg_percentage }}%)</div>
                                </td>
                                <td class="text-center">
                                    @if($item->is_moderated)
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1.5 rounded-pill fw-semibold">
                                            <i class="bi bi-check-circle-fill me-1"></i>Moderated & Published
                                        </span>
                                    @else
                                        <span class="badge bg-warning-subtle text-dark border border-warning-subtle px-3 py-1.5 rounded-pill fw-semibold">
                                            <i class="bi bi-hourglass-split me-1 text-warning"></i>Awaiting Moderation
                                        </span>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-flex justify-content-end gap-1">
                                        <a href="{{ route('faculty.hod.operations.results.show', ['type' => $item->type, 'id' => $item->id]) }}" 
                                           class="btn btn-sm btn-outline-primary fw-bold"
                                           title="Review student submissions and calibrate grades">
                                            <i class="bi bi-sliders me-1"></i>Moderate & Review
                                        </a>

                                        @if(!$item->is_moderated)
                                            <form action="{{ route('faculty.hod.operations.results.approve', ['type' => $item->type, 'id' => $item->id]) }}" method="POST" class="d-inline" onsubmit="return confirm('Endorse and publish all pending marks for {{ addslashes($item->title) }} to student portals?');">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success fw-bold" title="Quick Approve & Publish">
                                                    <i class="bi bi-check-lg me-1"></i>Approve
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="bi bi-clipboard2-check fs-1 d-block mb-2 text-success opacity-75"></i>
                                        <h6 class="fw-bold text-dark">No Course Results Awaiting Moderation</h6>
                                        <p class="small mb-0">All course results for {{ $department->name }} have either been moderated or have no graded submissions yet.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- LOWER ROW: Staff Leave Approvals & Program Transfers -->
    <div class="row g-4">
        <!-- Staff Leave Approvals -->
        <div class="col-lg-6" id="staffLeaveSection">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-dark">
                        <i class="bi bi-calendar2-check text-primary me-2"></i>Staff Leave Requests
                    </h5>
                    <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-2.5 py-1">
                        {{ $pendingLeaves->count() }} Pending
                    </span>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($pendingLeaves as $leave)
                            <div class="list-group-item p-3">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h6 class="fw-bold mb-1 text-dark">{{ $leave->user->name }}</h6>
                                        <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill">{{ ucfirst($leave->type) }}</span>
                                    </div>
                                    <span class="text-muted small">
                                        {{ $leave->start_date ? $leave->start_date->format('M d') : '-' }} &ndash; {{ $leave->end_date ? $leave->end_date->format('M d, Y') : '-' }}
                                    </span>
                                </div>
                                <p class="text-muted small mb-3 border-start border-3 border-primary ps-3 py-1 bg-light">{{ $leave->reason }}</p>
                                <div class="d-flex gap-2">
                                    <form action="{{ route('faculty.hod.operations.leave.approve', $leave) }}" method="POST" class="flex-grow-1" onsubmit="return confirm('Approve this staff leave request?');">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-success w-100 fw-bold">
                                            <i class="bi bi-check-lg me-1"></i>Approve Leave
                                        </button>
                                    </form>
                                    <form action="{{ route('faculty.hod.operations.leave.reject', $leave) }}" method="POST" class="flex-grow-1" onsubmit="return confirm('Reject this leave request?');">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-danger w-100 fw-bold">
                                            <i class="bi bi-x-lg me-1"></i>Reject
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-3 d-block mb-2 text-secondary opacity-50"></i>
                                <span class="small">No pending staff leave requests for {{ $department->name }}.</span>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Program Change Endorsements -->
        <div class="col-lg-6" id="programTransferSection">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-dark">
                        <i class="bi bi-arrow-left-right text-warning me-2"></i>Program Transfer Endorsements
                    </h5>
                    <span class="badge bg-warning bg-opacity-10 text-dark rounded-pill px-2.5 py-1">
                        {{ $pendingProgramChanges->count() }} Pending
                    </span>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($pendingProgramChanges as $change)
                            <div class="list-group-item p-3">
                                <div class="mb-2">
                                    <h6 class="fw-bold mb-1 text-dark">{{ $change->student->name }}</h6>
                                    <div class="d-flex align-items-center mt-1 small">
                                        <span class="text-muted text-decoration-line-through me-2">{{ $change->currentProgram->name ?? 'Current Program' }}</span>
                                        <i class="bi bi-arrow-right text-primary me-2"></i>
                                        <span class="fw-bold text-dark">{{ $change->requestedProgram->name }}</span>
                                    </div>
                                </div>
                                <p class="text-muted small mb-3 border-start border-3 border-warning ps-3 py-1 bg-light">
                                    <strong>Justification:</strong> {{ $change->reason }}
                                </p>
                                <form action="{{ route('faculty.hod.operations.program-change.endorse', $change) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-primary w-100 fw-bold">
                                        <i class="bi bi-check-circle me-1"></i>Endorse Transfer to Academic Registry
                                    </button>
                                </form>
                            </div>
                        @empty
                            <div class="text-center py-5 text-muted">
                                <i class="bi bi-shield-check fs-3 d-block mb-2 text-secondary opacity-50"></i>
                                <span class="small">No pending student program transfer requests.</span>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
