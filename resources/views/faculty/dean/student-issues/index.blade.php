@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-2" style="color: #1e293b; font-weight: 600;">Student Issues</h1>
            <p class="text-muted">Manage academic, disciplinary, and welfare issues reported by or about students.</p>
        </div>
        <div>
            <a href="{{ route('faculty.dean.student-issues.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i> Report Issue
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Student</th>
                            <th>Issue Type</th>
                            <th>Status</th>
                            <th>Date Reported</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($issues as $issue)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <img src="{{ $issue->student->profile_picture_url ?? 'https://ui-avatars.com/api/?name=User&color=1e3a8a&background=e0e7ff' }}" alt="" class="rounded-circle me-3" width="40" height="40">
                                    <div>
                                        <div class="fw-medium text-dark">{{ $issue->student->full_name ?? 'N/A' }}</div>
                                        <div class="text-muted small">{{ $issue->student->email ?? '' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border border-secondary border-opacity-25">{{ $issue->issue_type }}</span>
                            </td>
                            <td>
                                @if($issue->status === 'Open')
                                    <span class="badge bg-danger rounded-pill">Open</span>
                                @elseif($issue->status === 'In Progress')
                                    <span class="badge bg-warning text-dark rounded-pill">In Progress</span>
                                @elseif($issue->status === 'Resolved')
                                    <span class="badge bg-success rounded-pill">Resolved</span>
                                @elseif($issue->status === 'Escalated')
                                    <span class="badge bg-dark rounded-pill">Escalated</span>
                                @endif
                            </td>
                            <td>{{ $issue->created_at->format('M d, Y') }}</td>
                            <td class="text-end pe-4">
                                <a href="{{ route('faculty.dean.student-issues.show', $issue) }}" class="btn btn-sm btn-outline-info">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('faculty.dean.student-issues.edit', $issue) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('faculty.dean.student-issues.destroy', $issue) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this record?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="bi bi-life-preserver fs-1 d-block mb-3"></i>
                                <p class="mb-0">No student issues reported.</p>
                                <a href="{{ route('faculty.dean.student-issues.create') }}" class="btn btn-sm btn-primary mt-3">Report First Issue</a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($issues->hasPages())
        <div class="card-footer bg-white border-top">
            {{ $issues->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
