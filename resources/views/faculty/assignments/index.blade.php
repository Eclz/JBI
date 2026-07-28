@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1" style="color: #1e293b; font-weight: 600;">Assignment Management</h1>
            <p class="text-muted mb-0">Create and manage assignments for your courses</p>
        </div>
        <a href="{{ route('faculty.assignments.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i>Create New Assignment
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead style="background-color: #f8fafc;">
                        <tr>
                            <th style="color: #64748b; font-weight: 600; font-size: 0.75rem; text-transform: uppercase; padding: 1rem;">Assignment Title</th>
                            <th style="color: #64748b; font-weight: 600; font-size: 0.75rem; text-transform: uppercase; padding: 1rem;">Course</th>
                            <th style="color: #64748b; font-weight: 600; font-size: 0.75rem; text-transform: uppercase; padding: 1rem;">Due Date</th>
                            <th style="color: #64748b; font-weight: 600; font-size: 0.75rem; text-transform: uppercase; padding: 1rem;">Max Score</th>
                            <th style="color: #64748b; font-weight: 600; font-size: 0.75rem; text-transform: uppercase; padding: 1rem;">Submissions</th>
                            <th style="color: #64748b; font-weight: 600; font-size: 0.75rem; text-transform: uppercase; padding: 1rem;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($assignments as $assignment)
                        <tr>
                            <td class="align-middle" style="padding: 1rem;">
                                <div class="fw-semibold" style="color: #1e293b;">{{ $assignment->title }}</div>
                                <small class="text-muted">{{ Str::limit($assignment->description, 50) }}</small>
                            </td>
                            <td class="align-middle" style="padding: 1rem;">
                                <div style="color: #1e293b;">{{ $assignment->course->code }}</div>
                                <small class="text-muted">{{ $assignment->course->name }}</small>
                            </td>
                            <td class="align-middle" style="padding: 1rem;">
                                <div style="color: #1e293b;">{{ \Carbon\Carbon::parse($assignment->due_date)->format('M d, Y') }}</div>
                                <small class="text-muted">{{ \Carbon\Carbon::parse($assignment->due_date)->format('h:i A') }}</small>
                            </td>
                            <td class="align-middle" style="padding: 1rem;">
                                <span style="color: #1e293b; font-weight: 600;">{{ $assignment->max_score }}</span>
                            </td>
                            <td class="align-middle" style="padding: 1rem;">
                                <span class="badge bg-primary">{{ $assignment->submissions->count() }} submissions</span>
                            </td>
                            <td class="align-middle" style="padding: 1rem;">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('faculty.assignments.show', $assignment) }}" class="btn btn-sm btn-outline-primary" title="View">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('faculty.assignments.edit', $assignment) }}" class="btn btn-sm btn-outline-secondary" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('faculty.assignments.destroy', $assignment) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this assignment?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="bi bi-clipboard-x" style="font-size: 3rem; color: #cbd5e1;"></i>
                                <p class="text-muted mt-3">No assignments found. Create your first assignment to get started.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-4">
        {{ $assignments->links() }}
    </div>
</div>
@endsection
