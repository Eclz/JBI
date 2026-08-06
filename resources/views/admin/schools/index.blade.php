@extends('layouts.app')

@section('title', 'Academic Schools Management')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-primary fw-bold">
                <i class="bi bi-buildings me-2"></i>Academic Schools Management
            </h1>
            <p class="text-muted mb-0">Manage university schools, assigned deans, and academic departments</p>
        </div>
        <a href="{{ route('admin.schools.create') }}" class="btn btn-primary fw-bold px-3 py-2 shadow-sm">
            <i class="bi bi-plus-lg me-1"></i>Add New School
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Search & Filter Card -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <form action="{{ route('admin.schools.index') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-md-6">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Search by school name, code or description..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-sm btn-secondary w-100 fw-bold">
                        <i class="bi bi-filter me-1"></i>Filter Results
                    </button>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('admin.schools.index') }}" class="btn btn-sm btn-outline-secondary w-100">
                        <i class="bi bi-arrow-counterclockwise me-1"></i>Reset Filters
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Schools Grid Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th class="ps-3">Code</th>
                            <th>School Name</th>
                            <th>Dean / Head</th>
                            <th>Departments</th>
                            <th>Location</th>
                            <th>Status</th>
                            <th class="text-end pe-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($schools as $school)
                            <tr>
                                <td class="ps-3">
                                    <span class="badge bg-primary fs-6 font-monospace">{{ $school->code }}</span>
                                </td>
                                <td>
                                    <a href="{{ route('admin.schools.show', $school->id) }}" class="fw-bold text-dark text-decoration-none">
                                        {{ $school->name }}
                                    </a>
                                    <div class="small text-muted text-truncate" style="max-width: 280px;">
                                        {{ $school->description }}
                                    </div>
                                </td>
                                <td>
                                    @if($school->dean)
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px; font-size: 0.85rem;">
                                                {{ substr($school->dean->first_name, 0, 1) }}{{ substr($school->dean->last_name, 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="fw-semibold text-dark small">{{ $school->dean->first_name }} {{ $school->dean->last_name }}</div>
                                                <div class="text-muted small" style="font-size: 0.75rem;">Dean</div>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-muted small italic">Not Assigned</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-info text-dark px-2.5 py-1.5 fw-bold">
                                        <i class="bi bi-diagram-3 me-1"></i>{{ $school->departments_count }} Departments
                                    </span>
                                </td>
                                <td class="small text-muted">
                                    <i class="bi bi-geo-alt me-1 text-primary"></i>{{ $school->location ?? 'Main Campus' }}
                                </td>
                                <td>
                                    @if($school->is_active)
                                        <span class="badge bg-success px-2 py-1"><i class="bi bi-check-circle me-1"></i>Active</span>
                                    @else
                                        <span class="badge bg-secondary px-2 py-1">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-end pe-3">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.schools.show', $school->id) }}" class="btn btn-outline-info" title="View Details">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.schools.edit', $school->id) }}" class="btn btn-outline-primary" title="Edit School">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('admin.schools.destroy', $school->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this school?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" title="Delete School">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="bi bi-buildings display-6 d-block mb-2 text-secondary"></i>
                                    No Academic Schools found. Click <strong>Add New School</strong> to create one.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($schools->hasPages())
            <div class="card-footer bg-white border-top p-3">
                {{ $schools->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
