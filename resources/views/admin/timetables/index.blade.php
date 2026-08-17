@extends('layouts.app')

@section('title', 'Timetable Management')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-primary"><i class="bi bi-calendar3 me-2"></i>Timetable Management</h1>
            <p class="text-muted mb-0">Set and manage course lecture schedules, tests, and exam timetables</p>
        </div>
        <a href="{{ route('admin.timetables.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-2"></i>Add Timetable Entry
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.timetables.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Programme</label>
                    <select name="program_id" class="form-select">
                        <option value="">All Programmes</option>
                        @foreach($programs as $p)
                            <option value="{{ $p->id }}" {{ request('program_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Type</label>
                    <select name="type" class="form-select">
                        <option value="">All Types</option>
                        <option value="teaching" {{ request('type') == 'teaching' ? 'selected' : '' }}>Teaching Timetable</option>
                        <option value="tests" {{ request('type') == 'tests' ? 'selected' : '' }}>Tests Timetable</option>
                        <option value="exams" {{ request('type') == 'exams' ? 'selected' : '' }}>Exams Timetable</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Year of Study</label>
                    <select name="year_of_study" class="form-select">
                        <option value="">All Years</option>
                        @for($i = 1; $i <= 4; $i++)
                            <option value="{{ $i }}" {{ request('year_of_study') == $i ? 'selected' : '' }}>Year {{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-secondary w-100 me-2"><i class="bi bi-filter me-1"></i>Filter</button>
                    <a href="{{ route('admin.timetables.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-counterclockwise"></i></a>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>Course</th>
                            <th>Programme</th>
                            <th>Year / Sem</th>
                            <th>Type</th>
                            <th>Day & Time</th>
                            <th>Room / Venue</th>
                            <th>Lecturer</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($timetables as $slot)
                            <tr>
                                <td>
                                    <span class="fw-bold">{{ $slot->course?->code }}</span><br>
                                    <small class="text-muted">{{ $slot->course?->title }}</small>
                                </td>
                                <td><span class="badge bg-light text-dark border">{{ $slot->program?->code ?? 'All Programs' }}</span></td>
                                <td>Year {{ $slot->year_of_study }} Sem {{ $slot->semester_number }}</td>
                                <td>
                                    <span class="badge bg-{{ $slot->type === 'teaching' ? 'info' : ($slot->type === 'tests' ? 'warning' : 'danger') }} text-uppercase">
                                        {{ $slot->type }}
                                    </span>
                                </td>
                                <td>
                                    <span class="fw-semibold">{{ $slot->day_of_week }}</span><br>
                                    <small class="text-muted"><i class="bi bi-clock me-1"></i>{{ $slot->start_time }} - {{ $slot->end_time }}</small>
                                </td>
                                <td><span class="badge bg-secondary bg-opacity-10 text-secondary border px-2 py-1"><i class="bi bi-geo-alt me-1"></i>{{ $slot->room_venue }}</span></td>
                                <td>{{ $slot->faculty?->full_name ?? 'Unassigned' }}</td>
                                <td class="text-end">
                                    <form action="{{ route('admin.timetables.destroy', $slot) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this timetable entry?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">No timetable entries found. Click "Add Timetable Entry" above to set schedules.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($timetables->hasPages())
                <div class="px-4 py-3 border-top">
                    {{ $timetables->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
