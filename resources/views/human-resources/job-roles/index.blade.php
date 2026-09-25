@extends('layouts.app')

@section('title', 'Job Roles & Banding')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1 fw-bold text-dark">Job Roles & Salary Banding</h2>
            <p class="text-muted mb-0">Manage organizational positions, applicable departments, and payroll salary bands.</p>
        </div>
        @if($canCreate ?? false)
            <a href="{{ route('human-resources.job-roles.create') }}" class="btn btn-primary shadow-sm">
                <i class="bi bi-plus-circle me-2"></i>Create New Role
            </a>
        @endif
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
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
                            <th class="border-0 px-4 py-3">System Link</th>
                            <th class="border-0 px-4 py-3">Applicable Departments</th>
                            <th class="border-0 px-4 py-3">Salary Band</th>
                            <th class="border-0 px-4 py-3">Status</th>
                            <th class="border-0 px-4 py-3 text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($jobRoles as $role)
                            <tr>
                                <td class="px-4 py-3">
                                    <div class="fw-bold text-dark">{{ $role->title }}</div>
                                    @if($role->description)
                                        <div class="small text-muted text-truncate" style="max-width: 250px;">{{ $role->description }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if($role->role)
                                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2 py-1">
                                            <i class="bi bi-shield-lock me-1"></i>{{ $role->role->name }}
                                        </span>
                                    @else
                                        <span class="badge bg-light text-muted border px-2 py-1">
                                            Custom Role
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @php
                                        $depts = is_array($role->departments) ? $role->departments : [];
                                    @endphp
                                    @if(empty($depts))
                                        <span class="badge bg-light text-secondary border">
                                            <i class="bi bi-globe me-1"></i>Institution-wide (All)
                                        </span>
                                    @elseif(count($depts) <= 2)
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach($depts as $d)
                                                <span class="badge bg-light text-dark border">{{ $d }}</span>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25" 
                                              title="{{ implode(', ', $depts) }}" data-bs-toggle="tooltip">
                                            <i class="bi bi-building me-1"></i>{{ $depts[0] }}, {{ $depts[1] }} +{{ count($depts) - 2 }} more
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if($role->salary_band_min || $role->salary_band_max)
                                        <span class="fw-semibold text-dark">
                                            ${{ number_format($role->salary_band_min, 0) }} - ${{ number_format($role->salary_band_max, 0) }}
                                        </span>
                                    @else
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary">Not Configured</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <span class="badge bg-{{ $role->is_active ? 'success' : 'danger' }} bg-opacity-10 text-{{ $role->is_active ? 'success' : 'danger' }} px-3 py-1 rounded-pill">
                                        {{ $role->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-end">
                                    <a href="{{ route('human-resources.job-roles.edit', $role) }}" class="btn btn-sm btn-outline-primary me-1" title="Edit Role & Salary Bands">
                                        <i class="bi bi-pencil me-1"></i>Edit
                                    </a>
                                    @if(auth()->user()->isSuperAdmin() || auth()->user()->hasPermission('roles', 'delete') || auth()->user()->hasPermission('hr_core', 'delete'))
                                        <form action="{{ route('human-resources.job-roles.destroy', $role) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this job role?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Role">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="bi bi-diagram-3 display-4 d-block mb-3 text-secondary"></i>
                                    No job roles found. Create one or sync system roles to get started.
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
