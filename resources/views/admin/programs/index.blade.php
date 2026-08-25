@extends('layouts.app')

@section('title', 'Programs')

@push('styles')
<style>
    .level-chip { display: inline-flex; align-items: center; color: #172033 !important; font-weight: 700; border: 1px solid transparent; white-space: nowrap; }
    .level-chip-CERT { background: #e8f0ff !important; border-color: #8db1ff; color: #123b8f !important; }
    .level-chip-ADVDIP { background: #e2f7fb !important; border-color: #7ccbd8; color: #155b66 !important; }
    .level-chip-DIP { background: #e7f7ed !important; border-color: #82cea0; color: #176038 !important; }
    .level-chip-BACH { background: #fff4cf !important; border-color: #e4c45c; color: #684f00 !important; }
    .level-chip-MASTER { background: #fde9ed !important; border-color: #e497a5; color: #84243a !important; }
    .level-chip-PHD { background: #e9eaf0 !important; border-color: #9da1af; color: #242733 !important; }
    .level-chip-GENERAL { background: #eef0f3 !important; border-color: #b8bec8; color: #394150 !important; }
</style>
@endpush

@section('content')
@php
    $levelStyles = [
        'CERT' => ['primary', 'bi-award'], 'ADVDIP' => ['info', 'bi-patch-check'],
        'DIP' => ['success', 'bi-journal-check'], 'BACH' => ['warning', 'bi-mortarboard'],
        'MASTER' => ['danger', 'bi-mortarboard-fill'], 'PHD' => ['dark', 'bi-stars'],
    ];
@endphp
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Programs</h1>
            <p class="text-muted">Programs under departments with levels</p>
        </div>
        <a href="{{ route('admin.programs.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Add Program
        </a>
    </div>

    <div class="row g-2 mb-4">
        @foreach($levels as $level)
            @php([$colour, $icon] = $levelStyles[$level->code] ?? ['secondary', 'bi-book'])
            <div class="col-6 col-md-4 col-xl-2">
                <a href="{{ route('admin.programs.index', ['level' => $level->id]) }}" class="text-decoration-none">
                    <div class="card h-100 border-start border-4 border-{{ $colour }} {{ request('level') == $level->id ? 'shadow-sm bg-light' : '' }}">
                        <div class="card-body py-3 px-3">
                            <i class="bi {{ $icon }} text-{{ $colour }} me-1"></i>
                            <span class="small fw-semibold text-dark">{{ $level->name }}</span>
                            <div class="fs-5 fw-bold text-{{ $colour }}">{{ $level->programs_count }}</div>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.programs.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Search by name or code">
                </div>
                <div class="col-md-3">
                    <label for="department" class="form-label">Department</label>
                    <select class="form-select" id="department" name="department">
                        <option value="">All Departments</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}" {{ request('department') == $department->id ? 'selected' : '' }}>{{ $department->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="level" class="form-label">Level</label>
                    <select class="form-select" id="level" name="level">
                        <option value="">All Levels</option>
                        @foreach($levels as $level)
                            <option value="{{ $level->id }}" {{ request('level') == $level->id ? 'selected' : '' }}>{{ $level->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">All</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-12 d-flex gap-2">
                    <button type="submit" class="btn btn-outline-primary">Filter</button>
                    <a href="{{ route('admin.programs.index') }}" class="btn btn-outline-secondary">Clear</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            @if($programs->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Name</th>
                                <th>Department</th>
                                <th>Qualification Level</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($programs as $program)
                                @php([$levelColour, $levelIcon] = $levelStyles[$program->level->code ?? ''] ?? ['secondary', 'bi-book'])
                                <tr class="border-start border-3 border-{{ $levelColour }}">
                                    <td><code class="text-dark">{{ $program->code }}</code></td>
                                    <td><strong>{{ $program->name }}</strong></td>
                                    <td>{{ $program->department->name ?? '—' }}</td>
                                    <td><span class="badge level-chip level-chip-{{ $program->level->code ?? 'GENERAL' }}"><i class="bi {{ $levelIcon }} me-1"></i>{{ $program->level->name ?? 'Unassigned' }}</span></td>
                                    <td>
                                        <span class="badge bg-{{ $program->is_active ? 'success' : 'secondary' }}">
                                            {{ $program->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('admin.programs.show', $program) }}" class="btn btn-sm btn-outline-info">View</a>
                                        <a href="{{ route('admin.programs.edit', $program) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                        <form action="{{ route('admin.programs.destroy', $program) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this program?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-journal-bookmark display-1 text-muted"></i>
                    <h5 class="mt-3">No programs found</h5>
                    <p class="text-muted">Create programs under departments to continue.</p>
                </div>
            @endif
        </div>
        @if($programs->hasPages())
            <div class="card-footer">
                {{ $programs->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
