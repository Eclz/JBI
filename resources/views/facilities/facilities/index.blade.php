@extends('layouts.app')

@section('title', 'Campus Facilities')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1 fw-bold text-dark">Campus Facilities</h2>
            <p class="text-muted mb-0">Manage facility names and the rooms associated with each facility.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('facilities.rooms.index') }}" class="btn btn-outline-secondary">Rooms</a>
            @if(auth()->user()->hasPermission('facilities', 'create'))
                <a href="{{ route('facilities.buildings.create') }}" class="btn btn-primary">Add facility</a>
            @endif
        </div>
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
        <div class="card-body table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Facility</th>
                        <th>Type</th>
                        <th>Location</th>
                        <th>Rooms</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($facilities as $facility)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $facility->name }}</div>
                                @if($facility->description)
                                    <small class="text-muted">{{ \Illuminate\Support\Str::limit($facility->description, 100) }}</small>
                                @endif
                            </td>
                            <td><span class="badge text-bg-{{ $facility->type === 'hall' ? 'primary' : 'secondary' }}">{{ $facility->type === 'hall' ? 'Hall of Residence' : 'Facility' }}</span></td>
                            <td>{{ $facility->location ?: '—' }}</td>
                            <td>{{ $facility->rooms_count + $facility->hostel_rooms_count }}</td>
                            <td><span class="badge text-bg-{{ $facility->is_active ? 'success' : 'secondary' }}">{{ $facility->is_active ? 'Active' : 'Inactive' }}</span></td>
                            <td class="text-end">
                                @if($facility->type === 'hall' && auth()->user()->hasPermission('halls_of_residence', 'edit'))
                                    <a href="{{ route('admin.hostel.edit', $facility) }}" class="btn btn-sm btn-outline-primary">Edit hall</a>
                                @elseif(auth()->user()->hasPermission('facilities', 'edit'))
                                    <a href="{{ route('facilities.buildings.edit', $facility) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">No facilities have been added yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
