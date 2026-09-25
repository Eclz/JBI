@extends('layouts.app')

@section('title', 'Issue New Loan')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1 fw-bold text-dark">Issue New Loan</h2>
            <p class="text-muted mb-0">Record a new book loan for a user.</p>
        </div>
        @php $isLibrarianOrAdmin = !auth()->user()->isStudent(); @endphp
        @if($isLibrarianOrAdmin)
            <a href="{{ route('library.loans.index') }}" class="btn btn-outline-secondary">Back to loans</a>
        @else
            <a href="{{ route('library.catalogue.index') }}" class="btn btn-outline-secondary">Back to catalogue</a>
        @endif
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            
            <form method="POST" action="{{ route('library.loans.store') }}">
                @csrf
                <div class="row g-3 mb-4">
                    <div class="col-md-{{ $isLibrarianOrAdmin ? '6' : '12' }}">
                        <label class="form-label">Library Item</label>
                        <select name="library_item_id" class="form-select" required>
                            <option value="">Select item...</option>
                            @foreach($items as $item)
                                <option value="{{ $item->id }}" {{ (old('library_item_id') == $item->id || request('item_id') == $item->id) ? 'selected' : '' }}>
                                    {{ $item->title }} (Avail: {{ $item->available_copies }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @if($isLibrarianOrAdmin)
                    <div class="col-md-6">
                        <label class="form-label">User</label>
                        <select name="user_id" class="form-select select2" data-placeholder="Select user..." required>
                            <option value="">Select user...</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }} ({{ $user->email }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Borrowed Date</label>
                        <input type="date" name="borrowed_at" class="form-control" required value="{{ old('borrowed_at', now()->format('Y-m-d')) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Due Date</label>
                        <input type="date" name="due_at" class="form-control" required value="{{ old('due_at', now()->addDays(14)->format('Y-m-d')) }}">
                    </div>
                    @endif
                </div>
                <button class="btn btn-primary">Issue Loan</button>
            </form>
        </div>
    </div>
</div>
@endsection
