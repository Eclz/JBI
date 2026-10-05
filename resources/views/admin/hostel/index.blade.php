@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-1 fw-bold text-dark"><i class="bi bi-building-gear me-2"></i>Hostel Management</h3>
            <p class="text-muted mb-0">Manage university hostels, rooms, and student allocations.</p>
        </div>
        <button type="button" class="btn btn-primary shadow-sm fw-bold" data-bs-toggle="modal" data-bs-target="#createHostelModal">
            <i class="bi bi-plus-lg me-1"></i> Add New Hostel
        </button>
    </div>

    <!-- Error/Success Messages -->
    @if(session('success'))
        <div class="alert alert-success bg-success text-white border-0 shadow-sm alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger bg-danger text-white border-0 shadow-sm alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger bg-danger text-white border-0 shadow-sm alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- Hostels List -->
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-bold" style="color: #212529;">University Hostels</h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
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
                                            <span class="badge bg-primary text-uppercase me-1">{{ $hostel->type }}</span>
                                            <small class="text-muted"><i class="bi bi-geo-alt me-1"></i>{{ $hostel->location }}</small>
                                        </div>
                                        <p class="small text-muted">{{ Str::limit($hostel->description, 100) }}</p>
                                        
                                        <div class="d-flex justify-content-between text-muted small fw-bold mt-4 pt-3 border-top">
                                            <span><i class="bi bi-door-closed me-1"></i>{{ $hostel->rooms_count }} Rooms</span>
                                            <span><i class="bi bi-people me-1"></i>Capacity: {{ $hostel->capacity }}</span>
                                        </div>
                                    </div>
                                    <div class="card-footer bg-white border-0 py-3 text-center">
                                        <button type="button" class="btn btn-sm btn-outline-primary w-100 fw-bold" data-bs-toggle="modal" data-bs-target="#addRoomModal-{{ $hostel->id }}">
                                            <i class="bi bi-plus-lg me-1"></i> Add Room
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Add Room Modal for this hostel -->
                            <div class="modal fade" id="addRoomModal-{{ $hostel->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <form action="{{ route('admin.hostel.storeRoom', $hostel) }}" method="POST">
                                        @csrf
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Add Room to {{ $hostel->name }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold">Room Number/Name <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" name="room_number" required placeholder="e.g. A101">
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
                        @empty
                            <div class="col-12">
                                <div class="text-center py-5 text-muted">
                                    <i class="bi bi-building-x fs-1 mb-3 d-block"></i>
                                    <h5>No Hostels Found</h5>
                                    <p>Get started by adding a new hostel to the system.</p>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Room Allocations Pending Approval -->
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-bold" style="color: #212529;">Student Room Requests</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">Student</th>
                                    <th>Hostel & Room</th>
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
                                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold me-3" style="width: 40px; height: 40px;">
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
                                            @if($allocation->status === 'pending')
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

<!-- Create Hostel Modal -->
<div class="modal fade" id="createHostelModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('admin.hostel.storeHostel') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Add New Hostel</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Hostel Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="name" required placeholder="e.g. Mandela Hall">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Hostel Type <span class="text-danger">*</span></label>
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
                            <textarea class="form-control" name="description" rows="3" placeholder="Brief details about the hostel amenities..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary fw-bold" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-bold px-4">Create Hostel</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
