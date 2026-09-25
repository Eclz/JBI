@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h4 mb-1">Shift Manager</h2>
            <p class="text-muted mb-0">Define shift patterns and manage employee work schedules.</p>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#createShiftModal">
                <i class="bi bi-clock me-1"></i> New Shift Pattern
            </button>
            <button type="button" class="btn btn-primary fw-bold" data-bs-toggle="modal" data-bs-target="#assignShiftModal">
                <i class="bi bi-person-lines-fill me-1"></i> Assign Shift
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Statistics -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Total Shift Patterns</h6>
                            <h3 class="mb-0 fw-bold">{{ $stats['total_shifts'] }}</h3>
                        </div>
                        <div class="p-3 bg-primary bg-opacity-10 rounded text-white fs-4">
                            <i class="bi bi-clock-history"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Total Assignments</h6>
                            <h3 class="mb-0 fw-bold">{{ $stats['total_assignments'] }}</h3>
                        </div>
                        <div class="p-3 bg-info bg-opacity-10 rounded text-info fs-4">
                            <i class="bi bi-people"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Active Staff on Shifts</h6>
                            <h3 class="mb-0 fw-bold">{{ $stats['active_assignments'] }}</h3>
                        </div>
                        <div class="p-3 bg-success bg-opacity-10 rounded text-success fs-4">
                            <i class="bi bi-check-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Shift Patterns -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white pt-4 pb-3 border-bottom">
                    <h5 class="mb-0 fw-bold">Shift Patterns</h5>
                </div>
                <div class="card-body p-0">
                    @if($shifts->isEmpty())
                        <div class="p-4 text-center text-muted">
                            <p class="mb-2">No shift patterns defined.</p>
                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#createShiftModal">Create First Shift</button>
                        </div>
                    @else
                        <div class="list-group list-group-flush">
                            @foreach($shifts as $shift)
                                <div class="list-group-item p-3 border-bottom">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <h6 class="mb-0 fw-bold">{{ $shift->name }}</h6>
                                        <span class="badge bg-light text-dark border">{{ $shift->assignments_count }} Staff</span>
                                    </div>
                                    <div class="d-flex gap-2 text-muted small">
                                        <span><i class="bi bi-clock me-1"></i> {{ \Carbon\Carbon::parse($shift->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($shift->end_time)->format('H:i') }}</span>
                                        <span>•</span>
                                        <span><i class="bi bi-stopwatch me-1"></i> {{ $shift->grace_period_minutes }}m grace</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Shift Assignments -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white pt-4 pb-3 border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold">Employee Schedules</h5>
                </div>
                <div class="card-body p-0">
                    @if($assignments->isEmpty())
                        <div class="p-5 text-center text-muted flex-grow-1">
                            <i class="bi bi-calendar2-week fs-1 mb-3 d-block opacity-50"></i>
                            <h6>No employees assigned to shifts.</h6>
                            <p class="small">Assign a shift to an employee to see their schedule here.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="py-3 px-4 border-0">Employee</th>
                                        <th class="py-3 px-4 border-0">Shift Pattern</th>
                                        <th class="py-3 px-4 border-0">Schedule Time</th>
                                        <th class="py-3 px-4 border-0">Duration</th>
                                        <th class="py-3 px-4 border-0">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($assignments as $assignment)
                                        <tr>
                                            <td class="px-4">
                                                <div class="d-flex align-items-center">
                                                    <img src="{{ $assignment->user->profile_picture_url ?? 'https://ui-avatars.com/api/?name='.urlencode($assignment->user->name).'&background=random' }}" alt="{{ $assignment->user->name }}" class="rounded-circle me-3" style="width: 35px; height: 35px; object-fit: cover;">
                                                    <div>
                                                        <h6 class="mb-0">{{ $assignment->user->name }}</h6>
                                                        <small class="text-muted">{{ $assignment->user->hrProfile->department ?? 'General' }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-4 fw-semibold text-primary">
                                                {{ $assignment->shift->name }}
                                            </td>
                                            <td class="px-4 text-muted small">
                                                {{ \Carbon\Carbon::parse($assignment->shift->start_time)->format('H:i') }} to {{ \Carbon\Carbon::parse($assignment->shift->end_time)->format('H:i') }}
                                            </td>
                                            <td class="px-4 text-muted small">
                                                <div>{{ $assignment->start_date->format('d M Y') }}</div>
                                                @if($assignment->end_date)
                                                    <div>to {{ $assignment->end_date->format('d M Y') }}</div>
                                                @else
                                                    <div class="fst-italic opacity-75">Ongoing</div>
                                                @endif
                                            </td>
                                            <td class="px-4">
                                                @if(!$assignment->end_date || $assignment->end_date >= now()->toDateString())
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-secondary">Expired</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create Shift Modal -->
<div class="modal fade" id="createShiftModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('human-resources.shifts.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Create Shift Pattern</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Shift Name</label>
                        <input type="text" name="name" class="form-control" required placeholder="e.g. Morning Shift (08:00 - 16:00)">
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label">Start Time</label>
                            <input type="time" name="start_time" class="form-control" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label">End Time</label>
                            <input type="time" name="end_time" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Grace Period (Minutes)</label>
                        <input type="number" name="grace_period_minutes" class="form-control" required value="15" min="0">
                        <div class="form-text">Allowable late arrival time before flagging as late.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Shift</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Assign Shift Modal -->
<div class="modal fade" id="assignShiftModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('human-resources.shifts.assign') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Assign Employee Shift</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Employee</label>
                        <select name="user_id" class="form-select" required>
                            <option value="">-- Select Employee --</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->hrProfile->department ?? 'General' }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Shift Pattern</label>
                        <select name="hr_shift_id" class="form-select" required>
                            <option value="">-- Select Shift --</option>
                            @foreach($shifts as $shift)
                                <option value="{{ $shift->id }}">{{ $shift->name }} ({{ \Carbon\Carbon::parse($shift->start_time)->format('H:i') }}-{{ \Carbon\Carbon::parse($shift->end_time)->format('H:i') }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label">Start Date</label>
                            <input type="date" name="start_date" class="form-control" required value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-6">
                            <label class="form-label">End Date (Optional)</label>
                            <input type="date" name="end_date" class="form-control">
                            <div class="form-text small">Leave blank if ongoing.</div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Any specific instructions..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Assign Shift</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
