@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);">
                <div class="card-body p-4 text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-1" style="font-weight: 600;">Registrar Dashboard</h3>
                            <p class="mb-0" style="color: rgba(255, 255, 255, 0.85);">University-wide academic and student administration overview.</p>
                        </div>
                        <div class="text-end">
                            <i class="bi bi-bank2 display-4" style="color: rgba(255, 255, 255, 0.2);"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Metrics Row -->
    <div class="row g-4 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="mb-2 text-uppercase text-muted small fw-bold">Total Students</p>
                            <h3 class="mb-0 text-dark">{{ number_format($totalStudents) }}</h3>
                        </div>
                        <div class="p-3 bg-primary bg-opacity-10 rounded">
                            <i class="bi bi-people-fill fs-4 text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="mb-2 text-uppercase text-muted small fw-bold">Active Enrollments</p>
                            <h3 class="mb-0 text-dark">{{ number_format($activeEnrollments) }}</h3>
                        </div>
                        <div class="p-3 bg-success bg-opacity-10 rounded">
                            <i class="bi bi-book fs-4 text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="mb-2 text-uppercase text-muted small fw-bold">Total Faculties</p>
                            <h3 class="mb-0 text-dark">{{ $totalFaculties }}</h3>
                        </div>
                        <div class="p-3 bg-info bg-opacity-10 rounded">
                            <i class="bi bi-building fs-4 text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="mb-2 text-uppercase text-muted small fw-bold">Academic Programs</p>
                            <h3 class="mb-0 text-dark">{{ $totalPrograms }}</h3>
                        </div>
                        <div class="p-3 bg-warning bg-opacity-10 rounded">
                            <i class="bi bi-journal-bookmark-fill fs-4 text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Recent Students -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold">Recently Registered Students</h5>
                    <a href="{{ route('admin.students.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Admission No.</th>
                                    <th>Student Name</th>
                                    <th>Program</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentStudents as $student)
                                    <tr>
                                        <td>
                                            <span class="badge bg-secondary">{{ $student->admission_number }}</span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-sm me-3 bg-primary bg-opacity-10 text-primary rounded-circle d-flex justify-content-center align-items-center" style="width: 32px; height: 32px;">
                                                    {{ strtoupper(substr($student->first_name, 0, 1)) }}
                                                </div>
                                                <div>
                                                    <div class="fw-bold">{{ $student->first_name }} {{ $student->last_name }}</div>
                                                    <small class="text-muted">{{ $student->user->email ?? '' }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $student->program->name ?? 'N/A' }}</td>
                                        <td>
                                            @if($student->is_active)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-danger">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.students.show', $student->id) }}" class="btn btn-sm btn-light text-primary">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">No students found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Links -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">Registrar Quick Links</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <a href="{{ route('admin.approval-hub.index') }}" class="list-group-item list-group-item-action d-flex align-items-center py-3">
                            <div class="p-2 bg-primary bg-opacity-10 text-primary rounded me-3">
                                <i class="bi bi-check-circle-fill"></i>
                            </div>
                            <div>
                                <h6 class="mb-0">Central Approval Hub</h6>
                                <small class="text-muted">Approve university-wide requests</small>
                            </div>
                            <i class="bi bi-chevron-right ms-auto text-muted"></i>
                        </a>
                        <a href="{{ route('admin.programs.index') }}" class="list-group-item list-group-item-action d-flex align-items-center py-3">
                            <div class="p-2 bg-info bg-opacity-10 text-info rounded me-3">
                                <i class="bi bi-journal-album"></i>
                            </div>
                            <div>
                                <h6 class="mb-0">Manage Programs</h6>
                                <small class="text-muted">View and edit academic programs</small>
                            </div>
                            <i class="bi bi-chevron-right ms-auto text-muted"></i>
                        </a>
                        <a href="{{ route('admin.courses.index') }}" class="list-group-item list-group-item-action d-flex align-items-center py-3">
                            <div class="p-2 bg-success bg-opacity-10 text-success rounded me-3">
                                <i class="bi bi-book-half"></i>
                            </div>
                            <div>
                                <h6 class="mb-0">Course Catalog</h6>
                                <small class="text-muted">Manage university courses</small>
                            </div>
                            <i class="bi bi-chevron-right ms-auto text-muted"></i>
                        </a>
                        <a href="{{ route('admin.faculties.index') }}" class="list-group-item list-group-item-action d-flex align-items-center py-3">
                            <div class="p-2 bg-warning bg-opacity-10 text-warning rounded me-3">
                                <i class="bi bi-building"></i>
                            </div>
                            <div>
                                <h6 class="mb-0">Faculties & Departments</h6>
                                <small class="text-muted">Manage institutional structure</small>
                            </div>
                            <i class="bi bi-chevron-right ms-auto text-muted"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
