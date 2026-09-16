@extends('layouts.app')

@section('title', 'Staff Records')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1 fw-bold text-dark">Staff Records</h2>
            <p class="text-muted mb-0">Manage employee profiles, assignments, and responsibilities.</p>
        </div>
        <div>
            <a href="{{ route('human-resources.index') }}" class="btn btn-outline-secondary">Back to HR dashboard</a>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Role</th>
                            <th>Department</th>
                            <th>Employment status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($employees as $employee)
                            <tr>
                                <td>
                                    {{ $employee->full_name }}
                                    @if($employee->hrProfile)
                                        <div class="small text-muted">{{ $employee->hrProfile->employee_number }}</div>
                                    @endif
                                </td>
                                <td>{{ $employee->hrProfile?->job_title ?? $employee->role_name }}</td>
                                <td>{{ $employee->hrProfile?->department ?? '—' }}</td>
                                <td>
                                    @php
                                        $status = $employee->hrProfile?->status ?? ($employee->is_active ? 'Active' : 'Inactive');
                                        $badgeClass = $status === 'Active' ? 'success' : ($status === 'On Leave' ? 'info' : 'warning');
                                    @endphp
                                    <span class="badge text-bg-{{ $badgeClass }}">{{ $status }}</span>
                                </td>
                                <td class="text-end">
                                    @if(auth()->user()->hasPermission('hr_core', 'edit'))
                                        <a href="{{ route('human-resources.staff.edit', $employee) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                    @endif
                                    @if(auth()->user()->hasPermission('hr_core', 'delete'))
                                        <form method="POST" action="{{ route('human-resources.staff.destroy', $employee) }}" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this employee record?')">Delete</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted py-4">No employee records found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $employees->links() }}
        </div>
    </div>
</div>
@endsection
