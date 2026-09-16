@extends('layouts.app')

@section('title', 'Human Resources')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1 fw-bold text-dark">Human Resources</h2>
            <p class="text-muted mb-0">Staff records, leave tracking, and employee lifecycle administration.</p>
        </div>
        <a href="{{ route('human-resources.staff.index') }}" class="btn btn-primary">View staff records</a>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted small fw-semibold text-uppercase">Active staff</span>
                        <i class="bi bi-people fs-4 text-primary"></i>
                    </div>
                    <h3 class="fw-bold mb-0">{{ $staffCount }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted small fw-semibold text-uppercase">Leave requests</span>
                        <i class="bi bi-calendar2-range fs-4 text-success"></i>
                    </div>
                    <h3 class="fw-bold mb-0">{{ $leaveRequests }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted small fw-semibold text-uppercase">Onboarding</span>
                        <i class="bi bi-clipboard-check fs-4 text-warning"></i>
                    </div>
                    <h3 class="fw-bold mb-0">0</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h5 class="fw-bold mb-3">Staff summary</h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Role</th>
                            <th>Department</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Sarah Bassey</td>
                            <td>Lecturer</td>
                            <td>Computing</td>
                            <td><span class="badge text-bg-success">Active</span></td>
                        </tr>
                        <tr>
                            <td>Daniel Akpan</td>
                            <td>HR Specialist</td>
                            <td>People & Culture</td>
                            <td><span class="badge text-bg-primary">On duty</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
