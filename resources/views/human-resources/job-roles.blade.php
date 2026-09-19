@extends('layouts.app')

@section('title', 'Job Roles')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1 fw-bold text-dark">Job Roles & Titles</h2>
            <p class="text-muted mb-0">Manage organizational job titles, descriptions, and salary bands.</p>
        </div>
        <button class="btn btn-primary"><i class="bi bi-plus-circle me-2"></i>New Job Role</button>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Job Title</th>
                            <th>Department</th>
                            <th>Salary Band</th>
                            <th>Headcount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="fw-semibold">Senior Lecturer</td>
                            <td>Academic Affairs</td>
                            <td>Band D</td>
                            <td>12</td>
                            <td><span class="badge bg-success">Active</span></td>
                        </tr>
                        <tr>
                            <td class="fw-semibold">Librarian</td>
                            <td>Library Services</td>
                            <td>Band C</td>
                            <td>4</td>
                            <td><span class="badge bg-success">Active</span></td>
                        </tr>
                        <tr>
                            <td class="fw-semibold">HR Officer</td>
                            <td>Human Resources</td>
                            <td>Band B</td>
                            <td>2</td>
                            <td><span class="badge bg-success">Active</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
