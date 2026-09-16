@extends('layouts.app')

@section('title', $section)

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1 fw-bold text-dark">{{ $section }}</h2>
            <p class="text-muted mb-0">This HR workspace is permission-gated and ready for its operational records.</p>
        </div>
        <a href="{{ route('human-resources.index') }}" class="btn btn-outline-secondary">Back to HR dashboard</a>
    </div>
    <div class="card border-0 shadow-sm">
        <div class="card-body py-5 text-center">
            <i class="bi bi-clipboard-data fs-1 text-primary"></i>
            <h5 class="mt-3">No records have been added yet</h5>
            <p class="text-muted mb-0">Use the HR staff and leave workflows to begin populating this workspace.</p>
        </div>
    </div>
</div>
@endsection
