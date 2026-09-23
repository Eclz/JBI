@extends('layouts.app')

@section('title', 'Job Roles & Banding')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1 fw-bold text-dark">Job Roles & Banding</h2>
            <p class="text-muted mb-0">Manage job positions and their associated salary bands.</p>
        </div>
        <a href="{{ route('human-resources.job-roles.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i>Create New Role
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="border-0 px-4 py-3">Role Title</th>
                            <th class="border-0 px-4 py-3">Department</th>
                            <th class="border-0 px-4 py-3">Salary Band</th>
                            <th class="border-0 px-4 py-3">Status</th>
                            <th class="border-0 px-4 py-3 text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($jobRoles as $role)
                            <tr>
                                <td class="px-4 py-3 fw-semibold text-dark">{{ $role->title }}</td>
                                <td class="px-4 py-3">{{ $role->department ?? 'N/A' }}</td>
                                <td class="px-4 py-3">
                                    @if($role->salary_band_min || $role->salary_band_max)
                                        ${{ number_format($role->salary_band_min, 2) }} - ${{ number_format($role->salary_band_max, 2) }}
                                    @else
                                        <span class="text-muted">Not Set</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <span class="badge bg-{{ $role->is_active ? 'success' : 'danger' }} bg-opacity-10 text-{{ $role->is_active ? 'success' : 'danger' }} px-3 py-2 rounded-pill">
                                        {{ $role->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-end">
                                    <a href="{{ route('human-resources.job-roles.edit', $role) }}" class="btn btn-sm btn-outline-secondary me-2">Edit</a>
                                    <form action="{{ route('human-resources.job-roles.destroy', $role) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this job role?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="bi bi-tag display-4 d-block mb-3"></i>
                                    No job roles found. Create one to get started.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($jobRoles->hasPages())
            <div class="card-footer bg-white border-0 pt-4">
                {{ $jobRoles->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
