@extends('layouts.app')

@section('title', 'Faculty Evaluations')

@section('content')
<div class="container-fluid px-4 py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h3 mb-0 text-gray-800">Faculty Evaluations</h2>
            <p class="text-muted mb-0">Evaluate and review faculty performance.</p>
        </div>
        <a href="{{ route('faculty.dean.evaluations.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Draft New Evaluation
        </a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Faculty Member</th>
                            <th>Academic Year</th>
                            <th>Score</th>
                            <th>Status</th>
                            <th>Created Date</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($evaluations as $evaluation)
                            <tr>
                                <td class="ps-4 fw-medium">{{ $evaluation->faculty->name ?? 'N/A' }}</td>
                                <td>{{ $evaluation->academic_year }}</td>
                                <td>{{ $evaluation->performance_score }}/100</td>
                                <td>
                                    <span class="badge bg-{{ $evaluation->status === 'Approved' ? 'success' : ($evaluation->status === 'Rejected' ? 'danger' : ($evaluation->status === 'Pending Approval' ? 'warning text-dark' : 'secondary')) }}">
                                        {{ $evaluation->status }}
                                    </span>
                                </td>
                                <td>{{ $evaluation->created_at->format('M d, Y') }}</td>
                                <td class="text-end pe-4">
                                    <div class="btn-group">
                                        <a href="{{ route('faculty.dean.evaluations.show', $evaluation) }}" class="btn btn-sm btn-outline-primary" title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        @if($evaluation->status === 'Draft')
                                            <a href="{{ route('faculty.dean.evaluations.edit', $evaluation) }}" class="btn btn-sm btn-outline-secondary" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form action="{{ route('faculty.dean.evaluations.submit', $evaluation) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-success" title="Submit for Approval" onclick="return confirm('Submit this evaluation for approval?')">
                                                    <i class="bi bi-send"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="bi bi-folder2-open display-4 d-block mb-3"></i>
                                    <p class="mb-0">No faculty evaluations found.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($evaluations->hasPages())
            <div class="card-footer bg-white border-0 py-3">
                {{ $evaluations->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
