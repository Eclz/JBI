@extends('layouts.app')

@section('title', 'Edit Loan')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1 fw-bold text-dark">Edit Loan</h2>
            <p class="text-muted mb-0">Update loan details for {{ $loan->user->name ?? 'Unknown' }}.</p>
        </div>
        <a href="{{ route('library.loans.index') }}" class="btn btn-outline-secondary">Back to loans</a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            
            <form method="POST" action="{{ route('library.loans.update', $loan) }}">
                @csrf
                @method('PUT')
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Library Item</label>
                        <input type="text" class="form-control bg-light" value="{{ $loan->libraryItem->title ?? 'Unknown' }}" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">User</label>
                        <input type="text" class="form-control bg-light" value="{{ $loan->user->name ?? 'Unknown' }}" readonly>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Borrowed Date</label>
                        <input type="date" name="borrowed_at" class="form-control" required value="{{ old('borrowed_at', $loan->borrowed_at->format('Y-m-d')) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Due Date</label>
                        <input type="date" name="due_at" class="form-control" required value="{{ old('due_at', $loan->due_at->format('Y-m-d')) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Returned Date</label>
                        <input type="date" name="returned_at" class="form-control" value="{{ old('returned_at', $loan->returned_at ? $loan->returned_at->format('Y-m-d') : '') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select" required>
                            <option value="borrowed" {{ old('status', $loan->status) == 'borrowed' ? 'selected' : '' }}>Borrowed</option>
                            <option value="overdue" {{ old('status', $loan->status) == 'overdue' ? 'selected' : '' }}>Overdue</option>
                            <option value="returned" {{ old('status', $loan->status) == 'returned' ? 'selected' : '' }}>Returned</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Fines</label>
                        <input type="number" step="0.01" min="0" name="fines" class="form-control" required value="{{ old('fines', $loan->fines) }}">
                    </div>
                </div>
                <button class="btn btn-primary">Update Loan</button>
            </form>
        </div>
    </div>
</div>
@endsection
