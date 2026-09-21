@extends('layouts.app')

@section('title', 'Loans & Returns')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1 fw-bold text-dark">Loans & Returns</h2>
            <p class="text-muted mb-0">Issue, renew, return, and manage circulation records.</p>
        </div>
        <a href="{{ route('library.loans.create') }}" class="btn btn-primary"><i class="bi bi-plus-circle me-2"></i>New loan</a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Member</th>
                            <th>Title</th>
                            <th>Issued</th>
                            <th>Due date</th>
                            <th>Status</th>
                            <th>Fines</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($loans as $loan)
                        <tr>
                            <td>{{ $loan->user->name ?? 'Unknown' }}</td>
                            <td>{{ $loan->libraryItem->title ?? 'Unknown' }}</td>
                            <td>{{ $loan->borrowed_at->format('Y-m-d') }}</td>
                            <td>{{ $loan->due_at->format('Y-m-d') }}</td>
                            <td>
                                @if($loan->status === 'returned')
                                    <span class="badge bg-success">Returned</span>
                                @elseif($loan->due_at < now() && $loan->status !== 'returned')
                                    <span class="badge bg-danger">Overdue</span>
                                @else
                                    <span class="badge bg-primary">Borrowed</span>
                                @endif
                            </td>
                            <td>{{ number_format($loan->fines, 2) }}</td>
                            <td>
                                <a href="{{ route('library.loans.show', $loan) }}" class="btn btn-sm btn-outline-info">Show</a>
                                <a href="{{ route('library.loans.edit', $loan) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                                @if($loan->status !== 'returned')
                                    <form method="POST" action="{{ route('library.loans.renew', $loan) }}" class="d-inline">
                                        @csrf
                                        @method('PUT')
                                        <button class="btn btn-sm btn-outline-primary">Renew</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">No loans found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $loans->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
