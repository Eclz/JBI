@extends('layouts.app')

@section('title', 'Bookings & Maintenance')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1 fw-bold text-dark">Bookings & Maintenance</h2>
            <p class="text-muted mb-0">Room bookings, service requests, and maintenance follow-up.</p>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#requestBookingModal"><i class="bi bi-plus-circle me-2"></i>Request booking</button>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Location</th>
                            <th>Date / Time</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bookings as $booking)
                        <tr>
                            <td>{{ $booking->title }}</td>
                            <td>{{ $booking->facilityRoom->name ?? 'N/A' }}</td>
                            <td>
                                {{ $booking->start_time->format('M d, Y h:i A') }} <br>
                                <small class="text-muted">to {{ $booking->end_time->format('M d, Y h:i A') }}</small>
                            </td>
                            <td>{{ $booking->booking_type }}</td>
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
                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editBookingModal{{ $booking->id }}">
                                    <i class="bi bi-pencil"></i> Edit
                                </button>
                            </td>
                        </tr>

                        <!-- Show Modal -->
                        <div class="modal fade" id="showBookingModal{{ $booking->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header border-0 pb-0">
                                        <h5 class="modal-title fw-bold">Booking Details</h5>
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

                        <!-- Edit Modal -->
                        <div class="modal fade" id="editBookingModal{{ $booking->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content text-start">
                                    <form action="{{ route('facilities.bookings.update', $booking) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-header border-0 pb-0">
                                            <h5 class="modal-title fw-bold">Edit Booking</h5>
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
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No bookings found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $bookings->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Register Booking Modal -->
<div class="modal fade" id="requestBookingModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('facilities.bookings.store') }}" method="POST">
                @csrf
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">Register Booking / Maintenance</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label text-muted small fw-bold">TITLE</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted small fw-bold">TYPE</label>
                            <select name="booking_type" class="form-select" required>
                                <option value="Room Booking">Room Booking</option>
                                <option value="Maintenance">Maintenance</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label text-muted small fw-bold">LOCATION (ROOM)</label>
                            <select name="facility_room_id" class="form-select" required>
                                <option value="">Select a room...</option>
                                @foreach($rooms as $room)
                                    <option value="{{ $room->id }}">{{ $room->name }} (Capacity: {{ $room->capacity }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold">START TIME</label>
                            <input type="datetime-local" name="start_time" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold">END TIME</label>
                            <input type="datetime-local" name="end_time" class="form-control" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label text-muted small fw-bold">PURPOSE</label>
                            <textarea name="purpose" class="form-control" rows="3" required></textarea>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label text-muted small fw-bold">NOTES (Optional)</label>
                            <textarea name="notes" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Submit Request</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
