@extends('layouts.app')

@section('title', 'Faculty Details - ' . $faculty->name)

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-primary fw-bold"><i class="bi bi-buildings me-2"></i>{{ $faculty->name }}</h1>
            <p class="text-muted mb-0">Faculty Code: <span class="badge bg-secondary font-monospace">{{ $faculty->code }}</span></p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.faculties.edit', $faculty) }}" class="btn btn-primary fw-bold">
                <i class="bi bi-pencil me-1"></i>Edit Faculty
            </a>
            <a href="{{ route('admin.faculties.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Back to Faculties
            </a>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <!-- Faculty Overview Info Card -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="card-title mb-0 fw-bold text-dark"><i class="bi bi-info-circle me-2 text-primary"></i>Faculty Information</h5>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <small class="text-uppercase fw-bold text-muted d-block">Dean of Faculty</small>
                        @if($faculty->dean)
                            <div class="fw-bold text-dark fs-6">{{ $faculty->dean->full_name }}</div>
                            <small class="text-muted">{{ $faculty->dean->email }}</small>
                        @else
                            <span class="text-muted italic">Not Assigned</span>
                        @endif
                    </div>
                    <hr>
                    <div class="mb-3">
                        <small class="text-uppercase fw-bold text-muted d-block">Building & Location</small>
                        <span class="fw-semibold text-dark">{{ $faculty->location ?? 'Main Campus' }}</span>
                    </div>
                    <hr>
                    <div class="mb-3">
                        <small class="text-uppercase fw-bold text-muted d-block">Contact Info</small>
                        <div><i class="bi bi-envelope me-2"></i>{{ $faculty->email ?? 'N/A' }}</div>
                        <div><i class="bi bi-telephone me-2"></i>{{ $faculty->phone ?? 'N/A' }}</div>
                    </div>
                    <hr>
                    <div>
                        <small class="text-uppercase fw-bold text-muted d-block">Description</small>
                        <p class="text-muted mb-0 small">{{ $faculty->description ?? 'No description provided.' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Linked Academic Departments Table -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 fw-bold text-dark"><i class="bi bi-diagram-3 me-2 text-primary"></i>Departments Under {{ $faculty->name }}</h5>
                    <a href="{{ route('admin.departments.create') }}" class="btn btn-sm btn-outline-primary fw-bold"><i class="bi bi-plus-lg me-1"></i>Add Department</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Code</th>
                                    <th>Department Name</th>
                                    <th>Head of Department (HOD)</th>
                                    <th class="text-end pe-3">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($faculty->departments as $dept)
                                    <tr>
                                        <td class="ps-3"><span class="badge bg-secondary font-monospace">{{ $dept->code }}</span></td>
                                        <td class="fw-bold text-dark">{{ $dept->name }}</td>
                                        <td>
                                            @if($dept->headOfDepartment)
                                                <div class="fw-bold text-primary">{{ $dept->headOfDepartment->full_name }}</div>
                                                <small class="text-muted">{{ $dept->headOfDepartment->email }}</small>
                                            @else
                                                <span class="text-muted small">Not Assigned</span>
                                            @endif
                                        </td>
                                        <td class="text-end pe-3">
                                            <a href="{{ route('admin.departments.show', $dept) }}" class="btn btn-sm btn-outline-info"><i class="bi bi-eye"></i> View</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">No academic departments linked to this faculty yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
