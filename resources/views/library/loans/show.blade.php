@extends('layouts.app')

@section('title', 'Loan Details')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1 fw-bold text-dark">Loan Details</h2>
            <p class="text-muted mb-0">View information about this loan.</p>
        </div>
        <div>
            <a href="{{ route('library.loans.index') }}" class="btn btn-outline-secondary">Back to loans</a>
            <a href="{{ route('library.loans.edit', $loan) }}" class="btn btn-primary">Edit Loan</a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-5 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-bottom">
                    <h5 class="mb-0 fw-bold">Library Item</h5>
                </div>
                <div class="card-body text-center">
                    <div class="mb-3">
                        @if($loan->libraryItem->cover_image ?? false)
                            <img src="{{ Storage::url($loan->libraryItem->cover_image) }}" alt="Cover Image" class="img-fluid rounded shadow-sm" style="max-height: 150px;">
                        @else
                            <i class="bi bi-book text-secondary" style="font-size: 4rem;"></i>
                        @endif
                    </div>
                    <h4>{{ $loan->libraryItem->title ?? 'Unknown Item' }}</h4>
                    <p class="text-muted mb-1">{{ $loan->libraryItem->author ?? 'Unknown Author' }}</p>
                    <p class="text-muted mb-3">ISBN: {{ $loan->libraryItem->isbn ?? '—' }}</p>
                </div>
            </div>
        </div>

        <div class="col-md-7 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold">Loan Information</h5>
                    @if($loan->status === 'returned')
                        <span class="badge bg-success">Returned</span>
                    @elseif($loan->due_at < now() && $loan->status !== 'returned')
                        <span class="badge bg-danger">Overdue</span>
                    @else
                        <span class="badge bg-primary">Borrowed</span>
                    @endif
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted fw-semibold">Borrower</div>
                        <div class="col-sm-8">{{ $loan->user->name ?? 'Unknown' }} ({{ $loan->user->email ?? '' }})</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted fw-semibold">Borrowed Date</div>
                        <div class="col-sm-8">{{ $loan->borrowed_at->format('M d, Y') }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted fw-semibold">Due Date</div>
                        <div class="col-sm-8">{{ $loan->due_at->format('M d, Y') }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted fw-semibold">Returned Date</div>
                        <div class="col-sm-8">{{ $loan->returned_at ? $loan->returned_at->format('M d, Y') : '—' }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted fw-semibold">Renewals</div>
                        <div class="col-sm-8">{{ $loan->renewals }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted fw-semibold">Fines Due</div>
                        <div class="col-sm-8 text-danger fw-bold">{{ number_format($loan->fines, 2) }}</div>
                    </div>
                    
                    @if($loan->status !== 'returned')
                    <hr>
                    <div class="d-flex gap-2">
                        <form method="POST" action="{{ route('library.loans.return', $loan) }}">
                            @csrf
                            @method('PUT')
                            <button class="btn btn-success"><i class="bi bi-box-arrow-in-down left me-1"></i> Return Item</button>
                        </form>
                        <form method="POST" action="{{ route('library.loans.renew', $loan) }}">
                            @csrf
                            @method('PUT')
                            <button class="btn btn-outline-primary"><i class="bi bi-arrow-repeat me-1"></i> Renew Loan</button>
                        </form>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
