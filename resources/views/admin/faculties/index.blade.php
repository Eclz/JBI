@extends('layouts.app')

@section('title', 'Faculties Management')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-primary fw-bold">
                <i class="bi bi-buildings me-2"></i>Faculties Management
            </h1>
            <p class="text-muted mb-0">Create, manage, and oversee university faculties and their academic departments</p>
        </div>
        <a href="{{ route('admin.faculties.create') }}" class="btn btn-primary fw-bold">
            <i class="bi bi-plus-lg me-1"></i>Create New Faculty
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th class="ps-3">Code</th>
                            <th>Faculty Name</th>
                            <th>Dean of Faculty</th>
                            <th>Location / Contacts</th>
                            <th>Linked Departments</th>
                            <th>Status</th>
                            <th class="text-end pe-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($faculties as $faculty)
                            <tr>
                                <td class="ps-3"><span class="badge bg-secondary font-monospace fs-6">{{ $faculty->code }}</span></td>
                                <td>
                                    <div class="fw-bold text-dark fs-6">{{ $faculty->name }}</div>
                                    <small class="text-muted">{{ Str::limit($faculty->description, 60) }}</small>
                                </td>
                                <td>
                                    @if($faculty->dean)
                                        <div class="fw-bold text-primary"><i class="bi bi-person-badge me-1"></i>{{ $faculty->dean->full_name }}</div>
                                        <small class="text-muted">{{ $faculty->dean->email }}</small>
                                    @else
                                        <span class="text-muted italic">Not Assigned</span>
                                    @endif
                                </td>
                                <td class="small">
                                    <div><i class="bi bi-geo-alt me-1"></i>{{ $faculty->location ?? 'Main Campus' }}</div>
                                    <small class="text-muted">{{ $faculty->email }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-info text-dark px-2.5 py-1.5 fw-bold fs-6">
                                        <i class="bi bi-diagram-3 me-1"></i>{{ $faculty->departments_count }} Departments
                                    </span>
                                </td>
                                <td>
                                    @if($faculty->is_active)
                                        <span class="badge bg-success px-2.5 py-1.5">Active</span>
                                    @else
                                        <span class="badge bg-secondary px-2.5 py-1.5">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-end pe-3">
                                    <a href="{{ route('admin.faculties.show', $faculty) }}" class="btn btn-sm btn-outline-info me-1" title="View Details">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.faculties.edit', $faculty) }}" class="btn btn-sm btn-outline-primary me-1" title="Edit Faculty">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.faculties.destroy', $faculty) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this faculty?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Faculty">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">No faculties created yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($faculties->hasPages())
            <div class="card-footer bg-white border-top p-3">{{ $faculties->links() }}</div>
        @endif
    </div>
</div>
@endsection
