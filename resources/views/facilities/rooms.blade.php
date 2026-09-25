@extends('layouts.app')

@section('title', 'Rooms & Assets')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1 fw-bold text-dark">Rooms & Assets</h2>
            <p class="text-muted mb-0">Campus facilities inventory and room allocations.</p>
        </div>
        <div>
            <a href="{{ route('facilities.index') }}" class="btn btn-outline-secondary">Back to facilities</a>
            @if(auth()->user()->hasPermission('facilities_rooms', 'create'))
                <a href="{{ route('facilities.rooms.create') }}" class="btn btn-primary">Add room</a>
            @endif
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Room</th>
                            <th>Block</th>
                            <th>Capacity</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rooms as $room)
                            <tr>
                                <td>{{ $room->name }}</td>
                                <td>{{ $room->building ?: '—' }}</td>
                                <td>{{ $room->capacity }}</td>
                                <td><span class="badge text-bg-{{ $room->status === 'Available' ? 'success' : 'warning' }}">{{ $room->status }}</span></td>
                                <td class="text-end">
                                    @if(auth()->user()->hasPermission('facilities_rooms', 'edit'))
                                        <a href="{{ route('facilities.rooms.edit', $room) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                    @endif
                                    @if(auth()->user()->hasPermission('facilities_rooms', 'delete'))
                                        <form method="POST" action="{{ route('facilities.rooms.destroy', $room) }}" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this facility room?')">Delete</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted py-4">No rooms have been registered.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
