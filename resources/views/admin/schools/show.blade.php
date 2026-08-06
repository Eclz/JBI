@extends('layouts.app')

@section('title', $school->name . ' Details')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-primary fs-5 px-3 py-1 font-monospace">{{ $school->code }}</span>
                <h1 class="h3 mb-0 text-primary fw-bold">{{ $school->name }}</h1>
            </div>
            <p class="text-muted mb-0">Overview of school departments, assigned dean, and location details</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.schools.edit', $school->id) }}" class="btn btn-primary fw-bold">
                <i class="bi bi-pencil me-1"></i>Edit School
            </a>
            <a href="{{ route('admin.schools.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Back to Schools
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- School Metadata Card -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="card-title mb-0 fw-bold"><i class="bi bi-info-circle me-2"></i>School Summary</h5>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <small class="text-muted text-uppercase fw-semibold d-block">Description</small>
                        <p class="mb-0 fw-medium text-dark">{{ $school->description ?? 'No detailed mission description available.' }}</p>
                    </div>

                    <hr class="my-3">

                    <div class="mb-3">
                        <small class="text-muted text-uppercase fw-semibold d-block">Dean / School Leadership</small>
                        @if($school->dean)
                            <div class="d-flex align-items-center mt-2">
                                <div class="avatar-md bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 42px; height: 42px; font-weight: bold;">
                                    {{ substr($school->dean->first_name, 0, 1) }}{{ substr($school->dean->last_name, 0, 1) }}
                                </div>
                                <div>
                                    <div class="fw-bold text-dark">{{ $school->dean->first_name }} {{ $school->dean->last_name }}</div>
                                    <small class="text-muted d-block">{{ $school->dean->email }}</small>
                                </div>
                            </div>
                        @else
                            <span class="badge bg-warning text-dark mt-1">No Dean Assigned</span>
                        @endif
                    </div>

                    <hr class="my-3">

                    <div class="mb-2">
                        <small class="text-muted text-uppercase fw-semibold d-block mb-1">Contact Info & Location</small>
                        <div class="small mb-1"><i class="bi bi-geo-alt text-primary me-2"></i><strong>Location:</strong> {{ $school->location ?? 'Main Campus' }}</div>
                        <div class="small mb-1"><i class="bi bi-telephone text-primary me-2"></i><strong>Phone:</strong> {{ $school->phone ?? 'Not provided' }}</div>
                        <div class="small"><i class="bi bi-envelope text-primary me-2"></i><strong>Email:</strong> {{ $school->email ?? 'Not provided' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Assigned Departments under this School -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 fw-bold text-primary">
                        <i class="bi bi-diagram-3 me-2"></i>Departments under {{ $school->name }}
                    </h5>
                    <span class="badge bg-primary px-3 py-1.5 fw-bold fs-6">{{ $school->departments->count() }} Departments</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Code</th>
                                    <th>Department Name</th>
                                    <th>Head of Department</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($school->departments as $dept)
                                    <tr>
                                        <td class="ps-3"><span class="badge bg-secondary font-monospace">{{ $dept->code }}</span></td>
                                        <td class="fw-bold text-dark">{{ $dept->name }}</td>
                                        <td class="small">
                                            @if($dept->headOfDepartment)
                                                {{ $dept->headOfDepartment->first_name }} {{ $dept->headOfDepartment->last_name }}
                                            @else
                                                <span class="text-muted italic">Unassigned</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($dept->is_active)
                                                <span class="badge bg-success px-2 py-1">Active</span>
                                            @else
                                                <span class="badge bg-secondary px-2 py-1">Inactive</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">
                                            <i class="bi bi-diagram-3 display-6 text-secondary d-block mb-2"></i>
                                            No departments currently assigned under this school.
                                        </td>
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
