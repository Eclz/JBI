@extends('layouts.app')

@section('title', 'Access Denied')

@section('content')
<div class="container-fluid px-4 py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body p-5">
                    <div class="mb-4">
                        <i class="bi bi-shield-lock text-danger" style="font-size: 5rem;"></i>
                    </div>
                    <h2 class="h3 fw-bold mb-3 text-dark">Access Denied (403)</h2>
                    <p class="lead text-muted mb-4">
                        {{ $exception->getMessage() ?: 'You do not have permission to access this resource.' }}
                    </p>
                    <div class="d-flex justify-content-center gap-3 mt-4">
                        <button onclick="window.history.back()" class="btn btn-outline-secondary px-4">
                            <i class="bi bi-arrow-left me-2"></i>Go Back
                        </button>
                        <a href="{{ url('/') }}" class="btn btn-primary px-4">
                            <i class="bi bi-house-door me-2"></i>Return Home
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
