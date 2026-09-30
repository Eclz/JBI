@extends('layouts.app')

@section('title', 'Estates & Facilities')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1 fw-bold text-dark">Estates & Facilities</h2>
            <p class="text-muted mb-0">Rooms, bookings, maintenance, and campus asset oversight.</p>
        </div>
        <a href="{{ route('facilities.rooms.index') }}" class="btn btn-primary">Manage facilities</a>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted small fw-semibold text-uppercase">Rooms</span>
                        <i class="bi bi-door-open fs-4 text-primary"></i>
                    </div>
                    <h3 class="fw-bold mb-0">{{ $rooms }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted small fw-semibold text-uppercase">Bookings</span>
                        <i class="bi bi-calendar-event fs-4 text-success"></i>
                    </div>
                    <h3 class="fw-bold mb-0">{{ $bookings }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted small fw-semibold text-uppercase">Maintenance</span>
                        <i class="bi bi-wrench fs-4 text-warning"></i>
                    </div>
                    <h3 class="fw-bold mb-0">{{ $maintenance }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h5 class="fw-bold mb-3">Upcoming room requests</h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Request</th>
                            <th>Location</th>
                            <th>Date / Time</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($upcomingBookings ?? [] as $booking)
                        <tr>
                            <td>{{ $booking->title }}</td>
                            <td>{{ $booking->facilityRoom->name ?? 'N/A' }}</td>
                            <td>{{ $booking->start_time->format('M d, Y h:i A') }}</td>
                            <td>
                                @php
                                    $badge = 'secondary';
                                    if($booking->status == 'Approved') $badge = 'primary';
                                    if($booking->status == 'Assigned') $badge = 'info';
                                    if($booking->status == 'In Progress') $badge = 'warning';
                                    if($booking->status == 'Completed') $badge = 'success';
                                    if($booking->status == 'Cancelled') $badge = 'danger';
                                @endphp
                                <span class="badge text-bg-{{ $badge }}">{{ $booking->status }}</span>
                            </td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-outline-secondary me-1" data-bs-toggle="modal" data-bs-target="#showBookingModal{{ $booking->id }}">
                                    <i class="bi bi-eye"></i> Show
                                </button>
                                @if($canManage)
                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editBookingModal{{ $booking->id }}">
                                    <i class="bi bi-pencil"></i> Edit
                                </button>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">No upcoming requests.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modals for Upcoming Bookings -->
@foreach($upcomingBookings ?? [] as $booking)
    @php
        $badge = 'secondary';
        if($booking->status == 'Approved') $badge = 'primary';
        if($booking->status == 'Assigned') $badge = 'info';
        if($booking->status == 'In Progress') $badge = 'warning';
        if($booking->status == 'Completed') $badge = 'success';
        if($booking->status == 'Cancelled') $badge = 'danger';
    @endphp
    <!-- Show Modal -->
    <div class="modal fade" id="showBookingModal{{ $booking->id }}" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">Request Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-start">
                    <p><strong>Title:</strong> {{ $booking->title }}</p>
                    <p><strong>Requester:</strong> {{ $booking->user->name ?? 'N/A' }}</p>
                    <p><strong>Location:</strong> {{ $booking->facilityRoom->name ?? 'N/A' }}</p>
                    <p><strong>Type:</strong> {{ $booking->booking_type }}</p>
                    <p><strong>Purpose:</strong> {{ $booking->purpose }}</p>
                    <p><strong>Start:</strong> {{ $booking->start_time->format('M d, Y h:i A') }}</p>
                    <p><strong>End:</strong> {{ $booking->end_time->format('M d, Y h:i A') }}</p>
                    <p><strong>Status:</strong> <span class="badge text-bg-{{ $badge }}">{{ $booking->status }}</span></p>
                    <p><strong>Notes:</strong> {{ $booking->notes }}</p>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    @if($canManage)
    <!-- Edit Modal -->
    <div class="modal fade" id="editBookingModal{{ $booking->id }}" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content text-start">
                <form action="{{ route('facilities.bookings.update', $booking) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold">Edit Request</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label text-muted small fw-bold">STATUS</label>
                            <select name="status" class="form-select" required>
                                @foreach(['Pending', 'Approved', 'Assigned', 'In Progress', 'Completed', 'Cancelled'] as $status)
                                    <option value="{{ $status }}" {{ $booking->status == $status ? 'selected' : '' }}>{{ $status }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted small fw-bold">START TIME</label>
                            <input type="datetime-local" name="start_time" class="form-control" value="{{ $booking->start_time->format('Y-m-d\TH:i') }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted small fw-bold">END TIME</label>
                            <input type="datetime-local" name="end_time" class="form-control" value="{{ $booking->end_time->format('Y-m-d\TH:i') }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted small fw-bold">NOTES (Optional)</label>
                            <textarea name="notes" class="form-control" rows="3">{{ $booking->notes }}</textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
@endforeach
@endsection
