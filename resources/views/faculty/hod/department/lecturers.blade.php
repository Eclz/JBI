@extends('layouts.app')

@section('title', 'Department Lecturers - ' . $department->name)

@section('content')
<div class="container-fluid">
    <div class="mb-4 d-flex align-items-center justify-content-between">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-chalkboard-teacher text-success mr-2"></i>Faculty Members
            </h1>
            <p class="text-muted mb-0">Lecturers assigned to {{ $department->name }}</p>
        </div>
        <a href="{{ route('faculty.hod.department.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left mr-1"></i> Back to Dashboard
        </a>
    </div>

    <div class="card shadow border-0">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th>Staff ID</th>
                            <th>Name</th>
                            <th>Designation</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($lecturers as $lecturer)
                        <tr>
                            <td><span class="badge badge-light px-2 py-1 border text-dark">{{ $lecturer->employee_id ?? 'N/A' }}</span></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm bg-success text-white rounded-circle mr-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                        {{ substr($lecturer->user->first_name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="font-weight-bold">{{ $lecturer->user->first_name }} {{ $lecturer->user->last_name }}</div>
                                        <div class="small text-muted">{{ $lecturer->user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $lecturer->designation ?? 'Lecturer' }}</td>
                            <td>
                                @if($lecturer->status === 'active' || $lecturer->employment_status === 'active')
                                    <span class="badge badge-success px-2 py-1">Active</span>
                                @else
                                    <span class="badge badge-secondary px-2 py-1">{{ ucfirst($lecturer->employment_status ?? 'Inactive') }}</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">
                                <i class="fas fa-user-tie fa-3x mb-3 text-gray-300"></i>
                                <h5>No lecturers found</h5>
                                <p>There are no faculty members assigned to this department.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4">
                {{ $lecturers->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
