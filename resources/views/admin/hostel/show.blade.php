@extends('layouts.app')

@section('title', $hostel->name . ' - Rooms & Occupancy')

@section('content')
<div class="container-fluid py-4">
    <!-- Breadcrumb & Navigation Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('admin.hostel.index') }}" class="text-decoration-none">Halls of Residence</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $hostel->name }}</li>
                </ol>
            </nav>
            <h3 class="mb-1 fw-bold text-dark d-flex align-items-center gap-2">
                <i class="bi bi-building text-primary"></i>
                <span>{{ $hostel->name }}</span>
                <span class="badge {{ $hostel->is_active ? 'bg-success' : 'bg-danger' }} fs-6 rounded-pill">
                    {{ $hostel->is_active ? 'Active' : 'Inactive' }}
                </span>
            </h3>
            <p class="text-muted mb-0">
                <span class="badge bg-primary text-uppercase me-2">Hall · {{ $hostel->hall_type }}</span>
                @if($hostel->location)
                    <span class="me-3"><i class="bi bi-geo-alt me-1 text-muted"></i>{{ $hostel->location }}</span>
                @endif
                @if($hostel->description)
                    <span><i class="bi bi-info-circle me-1 text-muted"></i>{{ $hostel->description }}</span>
                @endif
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.hostel.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back to Halls
            </a>
            @if(auth()->user()->hasPermission('halls_of_residence', 'create'))
                <button type="button" class="btn btn-primary shadow-sm fw-bold" data-bs-toggle="modal" data-bs-target="#addRoomModal">
                    <i class="bi bi-plus-lg me-1"></i> Add Room to Hall
                </button>
            @endif
        </div>
    </div>

    <!-- Feedback Alerts -->
    @if(session('success'))
        <div class="alert alert-success bg-success text-white border-0 shadow-sm alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger bg-danger text-white border-0 shadow-sm alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger bg-danger text-white border-0 shadow-sm alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Hall Statistics Cards -->
    @php
        $occupancyRate = $totalCapacity > 0 ? round(($totalOccupancy / $totalCapacity) * 100, 1) : 0;
        $fullyOccupiedCount = $rooms->filter(fn($r) => $r->occupancy >= $r->capacity && $r->capacity > 0)->count();
        $availableRoomsCount = $rooms->filter(fn($r) => $r->occupancy < $r->capacity)->count();
    @endphp
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="p-3 bg-primary bg-opacity-10 text-primary rounded-3 me-3">
                        <i class="bi bi-door-closed fs-3"></i>
                    </div>
                    <div>
                        <div class="text-muted small text-uppercase fw-bold">Total Rooms</div>
                        <h4 class="mb-0 fw-bold">{{ $rooms->count() }}</h4>
                        <small class="text-muted">{{ $availableRoomsCount }} with vacancies</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="p-3 bg-info bg-opacity-10 text-info rounded-3 me-3">
                        <i class="bi bi-people fs-3"></i>
                    </div>
                    <div>
                        <div class="text-muted small text-uppercase fw-bold">Total Capacity</div>
                        <h4 class="mb-0 fw-bold">{{ $totalCapacity }}</h4>
                        <small class="text-muted">Maximum student beds</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="p-3 bg-warning bg-opacity-10 text-warning rounded-3 me-3">
                        <i class="bi bi-person-check fs-3"></i>
                    </div>
                    <div>
                        <div class="text-muted small text-uppercase fw-bold">Current Occupancy</div>
                        <h4 class="mb-0 fw-bold">{{ $totalOccupancy }}</h4>
                        <small class="text-muted">{{ $availableSpots }} beds available</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="p-3 bg-success bg-opacity-10 text-success rounded-3 me-3">
                        <i class="bi bi-pie-chart fs-3"></i>
                    </div>
                    <div>
                        <div class="text-muted small text-uppercase fw-bold">Occupancy Rate</div>
                        <h4 class="mb-0 fw-bold">{{ $occupancyRate }}%</h4>
                        <small class="text-muted">{{ $fullyOccupiedCount }} fully occupied</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content: Rooms List & Quick Add Room Provision -->
    <div class="row g-4">
        <!-- Rooms Table / List -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h5 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                        <i class="bi bi-grid text-primary"></i>
                        <span>Rooms in {{ $hostel->name }}</span>
                    </h5>
                    <span class="badge bg-light text-dark border">{{ $rooms->count() }} Registered Room(s)</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-3">Room</th>
                                    <th>Capacity</th>
                                    <th>Occupancy & Rate</th>
                                    <th>Status</th>
                                    <th>Fee / Semester</th>
                                    <th>Current Occupants</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($rooms as $room)
                                    @php
                                        $isFull = $room->occupancy >= $room->capacity && $room->capacity > 0;
                                        $percent = $room->capacity > 0 ? min(100, round(($room->occupancy / $room->capacity) * 100)) : 0;
                                        $progressBarColor = $isFull ? 'bg-danger' : ($percent > 50 ? 'bg-warning' : 'bg-success');
                                    @endphp
                                    <tr>
                                        <td class="ps-3">
                                            <div class="fw-bold text-dark fs-6">{{ $room->room_number }}</div>
                                            <small class="text-muted">ID: #{{ $room->id }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border font-monospace">
                                                <i class="bi bi-person me-1"></i>{{ $room->capacity }} Beds
                                            </span>
                                        </td>
                                        <td style="min-width: 170px;">
                                            <div class="d-flex justify-content-between small fw-bold mb-1">
                                                <span>{{ $room->occupancy }} / {{ $room->capacity }} Occupied</span>
                                                <span>{{ $percent }}%</span>
                                            </div>
                                            <div class="progress" style="height: 6px;">
                                                <div class="progress-bar {{ $progressBarColor }}" role="progressbar" style="width: {{ $percent }}%;" aria-valuenow="{{ $percent }}" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                        </td>
                                        <td>
                                            @if($isFull)
                                                <span class="badge bg-danger rounded-pill px-2">
                                                    <i class="bi bi-x-circle me-1"></i>Fully Occupied
                                                </span>
                                            @elseif($room->occupancy == 0)
                                                <span class="badge bg-info text-dark rounded-pill px-2">
                                                    <i class="bi bi-check-circle me-1"></i>Vacant
                                                </span>
                                            @else
                                                <span class="badge bg-success rounded-pill px-2">
                                                    <i class="bi bi-door-open me-1"></i>{{ $room->capacity - $room->occupancy }} Available
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($room->fee_per_semester > 0)
                                                <span class="fw-bold text-success">
                                                    {{ $currencyCode }} {{ number_format($room->fee_per_semester, 2) }}
                                                </span>
                                            @else
                                                <span class="badge bg-secondary">Free / Unpaid</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($room->allocations->count() > 0)
                                                <div class="d-flex flex-wrap gap-1">
                                                    @foreach($room->allocations as $alloc)
                                                        @if($alloc->user)
                                                            <span class="badge bg-light text-dark border" title="{{ $alloc->user->email }}">
                                                                <i class="bi bi-person-fill text-primary me-1"></i>{{ $alloc->user->name }}
                                                            </span>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            @else
                                                <small class="text-muted fst-italic">No active residents</small>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5 text-muted">
                                            <i class="bi bi-door-closed fs-1 mb-3 d-block text-secondary"></i>
                                            <h5>No Rooms Registered In This Hall</h5>
                                            <p class="mb-3">Use the form on the right or the button above to add the first room.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Inline Provision: Add Room to this Hall -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="card-title mb-0 fw-bold d-flex align-items-center">
                        <i class="bi bi-plus-circle me-2"></i> Add Room to {{ $hostel->name }}
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('admin.hostel.storeRoom', $hostel) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-bold">Room Number / Identifier <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="room_number" required placeholder="e.g. Room 101, Block B-04" autocomplete="off">
                            <small class="text-muted">Unique room code or door number in this hall.</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Bed Capacity <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-people"></i></span>
                                <input type="number" class="form-control" name="capacity" required min="1" value="2">
                            </div>
                            <small class="text-muted">Maximum number of students permitted in this room.</small>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Fee Per Semester ({{ $currencyCode }}) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text fw-bold">{{ $currencyCode }}</span>
                                <input type="number" class="form-control" name="fee_per_semester" required min="0" step="0.01" placeholder="e.g. 500.00" value="0.00">
                            </div>
                            <small class="text-muted">Set &gt; 0 for paid rooms. On approval, students receive an invoice and financial transaction.</small>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 fw-bold py-2 shadow-sm">
                            <i class="bi bi-plus-lg me-1"></i> Add Room to Hall
                        </button>
                    </form>
                </div>
            </div>

            <!-- Hall Information Summary Card -->
            <div class="card border-0 shadow-sm rounded-3 mt-4 bg-light">
                <div class="card-body p-3">
                    <h6 class="fw-bold text-dark mb-2"><i class="bi bi-info-circle me-1 text-primary"></i> Hall Details</h6>
                    <ul class="list-unstyled mb-0 small">
                        <li class="py-1 d-flex justify-content-between border-bottom">
                            <span class="text-muted">Hall Type:</span>
                            <span class="fw-bold text-capitalize">{{ $hostel->hall_type }}</span>
                        </li>
                        <li class="py-1 d-flex justify-content-between border-bottom">
                            <span class="text-muted">Campus Location:</span>
                            <span class="fw-bold">{{ $hostel->location ?: 'Main Campus' }}</span>
                        </li>
                        <li class="py-1 d-flex justify-content-between border-bottom">
                            <span class="text-muted">Designated Capacity:</span>
                            <span class="fw-bold">{{ $hostel->capacity }} students</span>
                        </li>
                        <li class="py-1 d-flex justify-content-between">
                            <span class="text-muted">Actual Rooms Configured:</span>
                            <span class="fw-bold">{{ $rooms->count() }} rooms ({{ $totalCapacity }} beds)</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Add Room Modal (also accessible from top header) --}}
<div class="modal fade" id="addRoomModal" tabindex="-1" aria-labelledby="addRoomModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('admin.hostel.storeRoom', $hostel) }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold" id="addRoomModalLabel">
                        <i class="bi bi-door-closed me-1"></i> Add Room to {{ $hostel->name }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Room Number/Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="room_number" required placeholder="e.g. Room A101" autocomplete="off">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Bed Capacity <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="capacity" required min="1" value="2">
                        <div class="form-text">Number of students this room can accommodate.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Fee Per Semester ({{ $currencyCode }}) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="fee_per_semester" required min="0" step="0.01" placeholder="e.g. 500.00" value="0.00">
                        <div class="form-text">Paid rooms trigger automatic invoice and finance transaction upon approval.</div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-bold px-4">Save Room</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
