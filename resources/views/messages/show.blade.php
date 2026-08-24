@extends('layouts.app')

@section('title', 'View Message')

@section('content')
<div class="container-fluid px-4 py-4">
    @if(Auth::user()->isStudent())
        @include('partials.student-header-bar')
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold text-dark text-uppercase mb-0">
                <i class="bi bi-envelope-open text-primary me-2"></i>VIEW MESSAGE
            </h5>
            <p class="text-muted small mb-0">{{ $message->subject }}</p>
        </div>
        <a href="{{ route('messages.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>Back to Mailbox
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 border-bottom border-primary border-2 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="fw-bold text-primary mb-1">{{ $message->subject }}</h5>
                        <small class="text-muted">
                            From: <strong>{{ $message->sender?->full_name ?? 'System Notification' }}</strong> 
                            ({{ $message->sender?->email ?? 'info@jbiuniversity.com' }})
                            • {{ $message->created_at->format('M d, Y h:i A') }}
                        </small>
                    </div>
                    <span class="badge bg-primary px-3 py-2 text-uppercase">{{ $message->type }}</span>
                </div>
                <div class="card-body p-4 fs-6 text-dark" style="line-height: 1.7;">
                    {!! nl2br(e($message->body)) !!}

                    @if($message->related_link)
                        <div class="mt-4 pt-3 border-top">
                            <a href="{{ $message->related_link }}" class="btn btn-primary fw-bold">
                                <i class="bi bi-box-arrow-up-right me-1"></i>View Related Content
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
