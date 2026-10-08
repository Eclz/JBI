@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h3 class="mb-1 fw-bold text-dark">
                <i class="bi bi-building-gear me-2"></i>Halls of Residence Management
            </h3>
            <p class="text-muted mb-0">Manage university halls, rooms, and student allocations.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('facilities.buildings.index') }}" class="btn btn-outline-primary">All Facilities</a>
            @if(auth()->user()->hasPermission('halls_of_residence', 'create'))
                <button type="button" class="btn btn-primary shadow-sm fw-bold" data-bs-toggle="modal" data-bs-target="#createHostelModal">
                    <i class="bi bi-plus-lg me-1"></i> Add New Hall
                </button>
            @endif
        </div>
    </div>

    <!-- Alerts -->
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

    <div class="row g-4">
        <!-- ===================== HALLS SECTION ===================== -->
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h5 class="mb-0 fw-bold text-dark">University Halls</h5>

                    <!-- View Switcher -->
                    <div class="btn-group" role="group" aria-label="View switcher">
                        <button type="button" class="btn btn-sm btn-outline-secondary active" id="btnCardView" title="Card View">
                            <i class="bi bi-grid-3x3-gap"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="btnTableView" title="Table View">
                            <i class="bi bi-list-ul"></i>
                        </button>
                    </div>
                </div>

                <div class="card-body">
                    <!-- ===== CARD VIEW ===== -->
                    <div id="hallsCardView" class="row g-4">
                        @forelse($hostels as $hostel)
                            <div class="col-md-6 col-lg-4">
                                <div class="card h-100 border bg-light shadow-sm">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h5 class="fw-bold mb-0 text-dark">{{ $hostel->name }}</h5>
                                            <span class="badge {{ $hostel->is_active ? 'bg-success' : 'bg-danger' }} rounded-pill">
                                                {{ $hostel->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </div>
                                        <div class="mb-3">
                                            <span class="badge bg-primary text-uppercase me-1">Hall · {{ $hostel->hall_type }}</span>
                                            <small class="text-muted"><i class="bi bi-geo-alt me-1"></i>{{ $hostel->location }}</small>
                                        </div>
                                        <p class="small text-muted mb-0">{{ Str::limit($hostel->description, 100) }}</p>

                                        <div class="d-flex justify-content-between text-muted small fw-bold mt-4 pt-3 border-top">
                                            <span><i class="bi bi-door-closed me-1"></i>{{ $hostel->rooms_count }} Rooms</span>
                                            <span><i class="bi bi-people me-1"></i>Capacity: {{ $hostel->capacity }}</span>
                                        </div>
                                    </div>
                                    <div class="card-footer bg-white border-0 py-3 text-center">
                                        @if(auth()->user()->hasPermission('halls_of_residence', 'edit'))
                                            <a href="{{ route('admin.hostel.edit', $hostel) }}" class="btn btn-sm btn-outline-secondary w-100 fw-bold mb-2">
                                                <i class="bi bi-pencil me-1"></i> Edit Hall
                                            </a>
                                        @endif
                                        @if(auth()->user()->hasPermission('halls_of_residence', 'create'))
                                        <button type="button"
                                                class="btn btn-sm btn-outline-primary w-100 fw-bold btn-add-room"
                                                data-bs-toggle="modal"
                                                data-bs-target="#addRoomModal"
                                                data-hostel-id="{{ $hostel->id }}"
                                                data-hostel-name="{{ $hostel->name }}"
                                                data-action="{{ route('admin.hostel.storeRoom', $hostel) }}">
                                            <i class="bi bi-plus-lg me-1"></i> Add Room
                                        </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="text-center py-5 text-muted">
                                    <i class="bi bi-building-x fs-1 mb-3 d-block"></i>
                                    <h5>No Halls Found</h5>
                                    <p>Get started by adding a new hall to the system.</p>
                                </div>
                            </div>
                        @endforelse
                    </div>

                    <!-- ===== TABLE VIEW ===== -->
                    <div id="hallsTableView" class="d-none">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-3">Hall Name</th>
                                        <th>Type</th>
                                        <th>Location</th>
                                        <th>Rooms</th>
                                        <th>Capacity</th>
                                        <th>Status</th>
                                        <th class="text-end pe-3">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($hostels as $hostel)
                                        <tr>
                                            <td class="ps-3">
                                                <div class="fw-bold">{{ $hostel->name }}</div>
                                                @if($hostel->description)
                                                    <small class="text-muted">{{ Str::limit($hostel->description, 60) }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-primary text-uppercase">Hall · {{ $hostel->hall_type }}</span>
                                            </td>
                                            <td>
                                                <i class="bi bi-geo-alt me-1 text-muted"></i>{{ $hostel->location ?: '—' }}
                                            </td>
                                            <td>{{ $hostel->rooms_count }}</td>
                                            <td>{{ $hostel->capacity }}</td>
                                            <td>
                                                <span class="badge {{ $hostel->is_active ? 'bg-success' : 'bg-danger' }} rounded-pill">
                                                    {{ $hostel->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </td>
                                            <td class="text-end pe-3">
                                                @if(auth()->user()->hasPermission('halls_of_residence', 'edit'))
                                                    <a href="{{ route('admin.hostel.edit', $hostel) }}" class="btn btn-sm btn-outline-secondary fw-bold">Edit Hall</a>
                                                @endif
                                                @if(auth()->user()->hasPermission('halls_of_residence', 'create'))
                                                <button type="button"
                                                        class="btn btn-sm btn-outline-primary fw-bold btn-add-room"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#addRoomModal"
                                                        data-hostel-id="{{ $hostel->id }}"
                                                        data-hostel-name="{{ $hostel->name }}"
                                                        data-action="{{ route('admin.hostel.storeRoom', $hostel) }}">
                                                    <i class="bi bi-plus-lg me-1"></i> Add Room
                                                </button>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-5 text-muted">
                                                <i class="bi bi-building-x fs-1 mb-3 d-block"></i>
                                                No Halls Found. Get started by adding a new hall.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ===================== STUDENT REQUESTS ===================== -->
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-bold text-dark">Student Room Requests</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">Student</th>
                                    <th>Hall & Room</th>
                                    <th>Semester</th>
                                    <th>Request Date</th>
                                    <th>Status</th>
                                    <th class="text-end pe-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($allocations as $allocation)
                                    <tr>
                                        <td class="ps-4">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold me-3"
                                                     style="width: 40px; height: 40px;">
                                                    {{ substr($allocation->user->first_name ?? 'U', 0, 1) }}
                                                </div>
                                                <div>
                                                    <h6 class="mb-0 fw-bold">{{ $allocation->user->first_name }} {{ $allocation->user->last_name }}</h6>
                                                    <small class="text-muted">{{ $allocation->user->email }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="fw-bold">{{ $allocation->room->hostel->name }}</div>
                                            <small class="text-muted">Room: {{ $allocation->room->room_number }}</small>
                                        </td>
                                        <td>{{ $allocation->semester->name ?? 'N/A' }}</td>
                                        <td>{{ $allocation->allocation_date->format('M d, Y') }}</td>
                                        <td>
                                            @if($allocation->status === 'approved')
                                                <span class="badge bg-success rounded-pill px-2">Approved</span>
                                            @elseif($allocation->status === 'pending')
                                                <span class="badge bg-warning text-dark rounded-pill px-2">Pending</span>
                                            @elseif($allocation->status === 'rejected')
                                                <span class="badge bg-danger rounded-pill px-2">Rejected</span>
                                            @else
                                                <span class="badge bg-secondary rounded-pill px-2">{{ ucfirst($allocation->status) }}</span>
                                            @endif
                                        </td>
                                        <td class="text-end pe-4">
                                            @if($allocation->status === 'pending' && auth()->user()->hasPermission('halls_of_residence', 'approve'))
                                                <form action="{{ route('admin.hostel.approveAllocation', $allocation) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success fw-bold me-1">Approve</button>
                                                </form>
                                                <form action="{{ route('admin.hostel.rejectAllocation', $allocation) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-danger fw-bold">Reject</button>
                                                </form>
                                            @else
                                                <span class="text-muted small">Processed</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">No room requests found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($allocations->hasPages())
                    <div class="card-footer bg-white border-top">
                        {{ $allocations->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- ===================== SINGLE REUSABLE ADD ROOM MODAL ===================== --}}
<div class="modal fade" id="addRoomModal" tabindex="-1" aria-labelledby="addRoomModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="addRoomForm" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="addRoomModalLabel">Add Room</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Room Number/Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="room_number" required placeholder="e.g. A101" autocomplete="off">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Capacity <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="capacity" required min="1" value="2">
                        <div class="form-text">Number of students this room can hold.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Fee Per Semester ($) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="fee_per_semester" required min="0" step="0.01" placeholder="e.g. 500.00">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-bold">Save Room</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Create Hostel Modal (unchanged) --}}
<div class="modal fade" id="createHostelModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('admin.hostel.storeHostel') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Add New Hostel</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Hall Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="name" required placeholder="e.g. Mandela Hall">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Hall Type <span class="text-danger">*</span></label>
                            <select class="form-select" name="type" required>
                                <option value="male">Male Only</option>
                                <option value="female">Female Only</option>
                                <option value="mixed">Mixed</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Total Capacity <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="capacity" required min="1" placeholder="e.g. 500">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Location</label>
                            <input type="text" class="form-control" name="location" placeholder="e.g. North Campus">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">Description</label>
                            <textarea class="form-control" name="description" rows="3" placeholder="Brief details about the hall amenities..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary fw-bold" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-bold px-4">Create Hall</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // ========== Single Add Room Modal Handler ==========
    const addRoomModal = document.getElementById('addRoomModal');
    const addRoomForm  = document.getElementById('addRoomForm');
    const modalTitle   = document.getElementById('addRoomModalLabel');

    addRoomModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        if (!button) return;

        const hostelName = button.getAttribute('data-hostel-name');
        const action     = button.getAttribute('data-action');

        modalTitle.textContent = `Add Room to ${hostelName}`;
        addRoomForm.action     = action;

        // Reset form fields for cleanliness
        addRoomForm.reset();
        addRoomForm.querySelector('[name="capacity"]').value = 2;
    });

    // Optional: clear form when modal is fully hidden
    addRoomModal.addEventListener('hidden.bs.modal', function () {
        addRoomForm.reset();
        addRoomForm.querySelector('[name="capacity"]').value = 2;
    });

    // ========== View Switcher (Card / Table) ==========
    const cardView   = document.getElementById('hallsCardView');
    const tableView  = document.getElementById('hallsTableView');
    const btnCard    = document.getElementById('btnCardView');
    const btnTable   = document.getElementById('btnTableView');

    function setView(mode) {
        if (mode === 'table') {
            cardView.classList.add('d-none');
            tableView.classList.remove('d-none');
            btnCard.classList.remove('active');
            btnTable.classList.add('active');
            localStorage.setItem('hallsViewMode', 'table');
        } else {
            tableView.classList.add('d-none');
            cardView.classList.remove('d-none');
            btnTable.classList.remove('active');
            btnCard.classList.add('active');
            localStorage.setItem('hallsViewMode', 'card');
        }
    }

    btnCard.addEventListener('click', () => setView('card'));
    btnTable.addEventListener('click', () => setView('table'));

    // Restore last preference
    const saved = localStorage.getItem('hallsViewMode') || 'card';
    setView(saved);
});
</script>
@endpush
