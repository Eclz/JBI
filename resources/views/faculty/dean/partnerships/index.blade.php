@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-2" style="color: #1e293b; font-weight: 600;">External Relations</h1>
            <p class="text-muted">Manage industry partnerships, alumni connections, and donor relationships.</p>
        </div>
        <div>
            <a href="{{ route('faculty.dean.partnerships.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i> New Partnership
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
                            <th class="ps-4">Organization</th>
                            <th>Type</th>
                            <th>Funding Expected (GHS)</th>
                            <th>Status</th>
                            <th>Date Created</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($partnerships as $partnership)
                        <tr>
                            <td class="ps-4 fw-medium text-dark">{{ $partnership->organization_name }}</td>
                            <td>
                                <span class="badge bg-light text-dark border border-secondary border-opacity-25">{{ $partnership->partnership_type }}</span>
                            </td>
                            <td class="font-monospace">
                                @if($partnership->funding_amount > 0)
                                    ₵{{ number_format($partnership->funding_amount, 2) }}
                                @else
                                    <span class="text-muted fst-italic">Non-Financial</span>
                                @endif
                            </td>
                            <td>
                                @if($partnership->status === 'Pending')
                                    <span class="badge bg-warning text-dark rounded-pill">Pending</span>
                                @elseif($partnership->status === 'Active')
                                    <span class="badge bg-success rounded-pill">Active</span>
                                @elseif($partnership->status === 'Concluded')
                                    <span class="badge bg-secondary rounded-pill">Concluded</span>
                                @endif
                            </td>
                            <td>{{ $partnership->created_at->format('M d, Y') }}</td>
                            <td class="text-end pe-4">
                                <a href="{{ route('faculty.dean.partnerships.show', $partnership) }}" class="btn btn-sm btn-outline-info">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('faculty.dean.partnerships.edit', $partnership) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('faculty.dean.partnerships.destroy', $partnership) }}" method="POST" class="d-inline">
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
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-globe fs-1 d-block mb-3"></i>
                                <p class="mb-0">No external partnerships established.</p>
                                <a href="{{ route('faculty.dean.partnerships.create') }}" class="btn btn-sm btn-primary mt-3">Log First Partnership</a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($partnerships->hasPages())
        <div class="card-footer bg-white border-top">
            {{ $partnerships->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
