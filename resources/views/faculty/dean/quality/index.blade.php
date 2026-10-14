@extends('layouts.app')

@section('title', 'Academic Quality Reviews')

@section('content')
<div class="container-fluid px-4 py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h3 mb-0 text-gray-800">Academic Quality Reviews</h2>
            <p class="text-muted mb-0">Manage and monitor the quality of academic programs.</p>
        </div>
        <a href="{{ route('faculty.dean.quality.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Draft New Review
        </a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Title</th>
                            <th>Program</th>
                            <th>Status</th>
                            <th>Created Date</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reviews as $review)
                            <tr>
                                <td class="ps-4 fw-medium">{{ $review->title }}</td>
                                <td>{{ $review->program->name ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge bg-{{ $review->status === 'Approved' ? 'success' : ($review->status === 'Rejected' ? 'danger' : ($review->status === 'Pending Approval' ? 'warning text-dark' : 'secondary')) }}">
                                        {{ $review->status }}
                                    </span>
                                </td>
                                <td>{{ $review->created_at->format('M d, Y') }}</td>
                                <td class="text-end pe-4">
                                    <div class="btn-group">
                                        <a href="{{ route('faculty.dean.quality.show', $review) }}" class="btn btn-sm btn-outline-primary" title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        @if($review->status === 'Draft')
                                            <a href="{{ route('faculty.dean.quality.edit', $review) }}" class="btn btn-sm btn-outline-secondary" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form action="{{ route('faculty.dean.quality.submit', $review) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-success" title="Submit for Approval" onclick="return confirm('Submit this review for approval?')">
                                                    <i class="bi bi-send"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="bi bi-folder2-open display-4 d-block mb-3"></i>
                                    <p class="mb-0">No quality reviews found.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($reviews->hasPages())
            <div class="card-footer bg-white border-0 py-3">
                {{ $reviews->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
