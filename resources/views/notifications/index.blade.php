@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-primary">
                <i class="bi bi-bell me-2"></i>Notifications
            </h1>
            <p class="text-muted mb-0">Stay updated with your activity</p>
        </div>
        @if(auth()->user()->unreadNotifications->count() > 0)
            <button class="btn btn-outline-primary" onclick="markAllAsRead()">
                <i class="bi bi-check-all me-2"></i>Mark All as Read
            </button>
        @endif
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body p-0">
            @if($notifications->count() > 0)
                <div class="list-group list-group-flush">
                    @foreach($notifications as $notification)
                        @php
                            $isUnread = !$notification->is_read && !$notification->read_at;
                            $title = $notification->title ?? ($notification->data['title'] ?? 'Notification');
                            $message = $notification->message ?? ($notification->data['message'] ?? '');
                            $type = $notification->type ?? ($notification->data['type'] ?? 'general');
                            $actionUrl = $notification->action_url ?? ($notification->data['action_url'] ?? null);
                        @endphp
                        <div class="list-group-item list-group-item-action {{ $isUnread ? 'bg-light' : '' }} p-3">
                            <div class="d-flex w-100 justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-start">
                                        <div class="notification-icon me-3 flex-shrink-0">
                                            @if($notification->priority === 'urgent')
                                                <div class="rounded-circle bg-danger text-white d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                                                    <i class="bi bi-exclamation-octagon fs-5"></i>
                                                </div>
                                            @elseif($notification->priority === 'high' || $type === 'application')
                                                <div class="rounded-circle bg-warning text-white d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                                                    <i class="bi bi-bell-fill fs-5"></i>
                                                </div>
                                            @elseif($type === 'payment')
                                                <div class="rounded-circle bg-info text-white d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                                                    <i class="bi bi-credit-card-fill fs-5"></i>
                                                </div>
                                            @elseif($type === 'grade_posted' || $type === 'grade')
                                                <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                                                    <i class="bi bi-trophy-fill fs-5"></i>
                                                </div>
                                            @elseif($type === 'assignment_due' || $type === 'assignment')
                                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                                                    <i class="bi bi-file-earmark-text-fill fs-5"></i>
                                                </div>
                                            @elseif($type === 'announcement')
                                                <div class="rounded-circle bg-dark text-white d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                                                    <i class="bi bi-megaphone-fill fs-5"></i>
                                                </div>
                                            @else
                                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                                                    <i class="bi bi-bell-fill fs-5"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <h6 class="mb-1 fw-bold text-dark">
                                                @if($actionUrl)
                                                    <a href="{{ $actionUrl }}" class="text-decoration-none text-dark hover-primary">
                                                        {{ $title }}
                                                    </a>
                                                @else
                                                    {{ $title }}
                                                @endif
                                                @if($isUnread)
                                                    <span class="badge bg-primary ms-2 rounded-pill" style="font-size: 0.65rem;">New</span>
                                                @endif
                                            </h6>
                                            <p class="mb-1 text-secondary" style="font-size: 0.9rem;">{{ $message }}</p>
                                            <div class="d-flex align-items-center gap-3">
                                                <small class="text-muted">
                                                    <i class="bi bi-clock me-1"></i>{{ $notification->created_at->diffForHumans() }}
                                                </small>
                                                @if($actionUrl)
                                                    <a href="{{ $actionUrl }}" class="small text-primary text-decoration-none fw-medium">
                                                        View details <i class="bi bi-arrow-right"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="ms-3 d-flex align-items-center gap-1">
                                    @if($isUnread)
                                        <button class="btn btn-sm btn-outline-primary" onclick="markAsRead('{{ $notification->id }}')" title="Mark as read">
                                            <i class="bi bi-check2"></i>
                                        </button>
                                    @endif
                                    <form action="{{ route('notifications.destroy', $notification) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this notification?')" title="Delete notification">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-bell-slash text-muted" style="font-size: 3rem;"></i>
                    <p class="text-muted mt-3">No notifications</p>
                </div>
            @endif
        </div>
        @if($notifications->hasPages())
            <div class="card-footer bg-white border-top">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
function markAsRead(notificationId) {
    fetch(`/notifications/${notificationId}/mark-read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}

function markAllAsRead() {
    fetch('/notifications/mark-all-read', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}
</script>
@endpush
