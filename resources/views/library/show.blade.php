@extends('layouts.app')

@section('title', 'Catalogue Item Details')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1 fw-bold text-dark">Catalogue Item Details</h2>
            <p class="text-muted mb-0">View information about {{ $item->title }}.</p>
        </div>
        <div>
            <a href="{{ route('library.catalogue.index') }}" class="btn btn-outline-secondary">Back to Catalogue</a>
            @if(auth()->user()->hasPermission('library_catalogue', 'edit'))
                <a href="{{ route('library.catalogue.edit', $item) }}" class="btn btn-primary">Edit Item</a>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="bi bi-book text-secondary" style="font-size: 5rem;"></i>
                    </div>
                    <h4>{{ $item->title }}</h4>
                    <p class="text-muted mb-1">{{ $item->author ?: 'Unknown Author' }}</p>
                    <p class="text-muted mb-3">{{ $item->category }}</p>
                    
                    <span class="badge text-bg-{{ $item->available_copies > 0 ? 'success' : 'warning' }} px-3 py-2 fs-6">
                        {{ $item->available_copies > 0 ? 'Available' : 'On Loan' }}
                    </span>
                </div>
            </div>
        </div>

        <div class="col-md-8 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-bottom">
                    <h5 class="mb-0 fw-bold">Item Information</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted fw-semibold">Title</div>
                        <div class="col-sm-8">{{ $item->title }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted fw-semibold">Author</div>
                        <div class="col-sm-8">{{ $item->author ?: '—' }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted fw-semibold">ISBN</div>
                        <div class="col-sm-8">{{ $item->isbn ?: '—' }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted fw-semibold">Category</div>
                        <div class="col-sm-8">{{ $item->category }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted fw-semibold">Total Copies</div>
                        <div class="col-sm-8">{{ $item->total_copies }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted fw-semibold">Available Copies</div>
                        <div class="col-sm-8">{{ $item->available_copies }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted fw-semibold">Status</div>
                        <div class="col-sm-8">
                            @if($item->is_active ?? true)
                                <span class="badge bg-success">Active in Catalogue</span>
                            @else
                                <span class="badge bg-danger">Archived / Inactive</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
