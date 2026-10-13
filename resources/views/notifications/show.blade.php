@extends('layouts.app')

@section('title', $notification->title ?? 'Notification')

@section('content')
<div class="mb-3">
    <a href="{{ route('notifications.index') }}" class="text-decoration-none small">
        <i class="bi bi-arrow-left me-1"></i>All notifications
    </a>
</div>

<div class="card">
    <div class="card-header">
        <h1 class="h5 mb-0">{{ $notification->title ?? 'Notification' }}</h1>
        <small class="text-muted">{{ optional($notification->created_at)->format('M d, Y g:i A') }}</small>
    </div>
    <div class="card-body" style="white-space: pre-line;">{{ $notification->message ?? $notification->body ?? '' }}</div>
</div>
@endsection
