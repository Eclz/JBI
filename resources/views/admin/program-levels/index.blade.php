@extends('layouts.app')

@section('title', 'Program Levels')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Program Levels</h1>
            <p class="text-muted">Manage qualification levels (Diploma, Degree, Masters, etc.)</p>
        </div>
        <a href="{{ route('admin.program-levels.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Add Level
        </a>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.program-levels.index') }}" class="row g-3">
                <div class="col-md-6">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Search by name or code">
                </div>
                <div class="col-md-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">All</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-outline-primary w-100">Filter</button>
                    <a href="{{ route('admin.program-levels.index') }}" class="btn btn-outline-secondary w-100">Clear</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            @if($programLevels->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Code</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($programLevels as $level)
                                <tr>
                                    <td>{{ $level->name }}</td>
                                    <td>{{ $level->code ?? '—' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $level->is_active ? 'success' : 'secondary' }}">
                                            {{ $level->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('admin.program-levels.edit', $level) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                        <form action="{{ route('admin.program-levels.destroy', $level) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this program level?');">
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
                    <i class="bi bi-layers display-1 text-muted"></i>
                    <h5 class="mt-3">No program levels</h5>
                    <p class="text-muted">Create levels like Diploma, Degree, or Masters.</p>
                </div>
            @endif
        </div>
        @if($programLevels->hasPages())
            <div class="card-footer">
                {{ $programLevels->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
