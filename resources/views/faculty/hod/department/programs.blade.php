@extends('layouts.app')

@section('title', 'Department Programmes - ' . $department->name)

@section('content')
<div class="container-fluid">
    <div class="mb-4 d-flex align-items-center justify-content-between">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-graduation-cap text-info mr-2"></i>Programmes
            </h1>
            <p class="text-muted mb-0">Academic programmes offered by {{ $department->name }}</p>
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
                            <th>Programme Code</th>
                            <th>Name</th>
                            <th>Level</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($programs as $program)
                        <tr>
                            <td><span class="badge badge-light px-2 py-1 border text-dark">{{ $program->code ?? 'N/A' }}</span></td>
                            <td class="font-weight-bold">{{ $program->name }}</td>
                            <td>{{ $program->level->name ?? 'N/A' }}</td>
                            <td>
                                @if($program->is_active ?? true)
                                    <span class="badge badge-success px-2 py-1">Active</span>
                                @else
                                    <span class="badge badge-secondary px-2 py-1">Inactive</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">
                                <i class="fas fa-award fa-3x mb-3 text-gray-300"></i>
                                <h5>No programmes found</h5>
                                <p>There are no programmes currently listed under this department.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4">
                {{ $programs->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
