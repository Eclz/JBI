@extends('layouts.app')

@section('title', 'My Borrowing')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1 fw-bold text-dark">My Borrowing</h2>
            <p class="text-muted mb-0">Track your current loans, renewals, and due dates.</p>
        </div>
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
                            <th>Title</th>
                            <th>Issued</th>
                            <th>Due date</th>
                            <th>Status</th>
                            <th>Fines</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($loans as $loan)
                        <tr>
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
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">You have no borrowing history.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
