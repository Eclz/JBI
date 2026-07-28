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
                        <div class="list-group-item list-group-item-action {{ $notification->read_at ? '' : 'bg-light' }}">
                            <div class="d-flex w-100 justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-start">
                                        <div class="notification-icon me-3">
                                            @if(isset($notification->data['type']))
                                                @switch($notification->data['type'])
                                                    @case('assignment')
                                                        <i class="bi bi-file-earmark-text text-primary" style="font-size: 1.5rem;"></i>
                                                        @break
                                                    @case('grade')
                                                        <i class="bi bi-star text-warning" style="font-size: 1.5rem;"></i>
                                                        @break
                                                    @case('message')
                                                        <i class="bi bi-envelope text-info" style="font-size: 1.5rem;"></i>
                                                        @break
                                                    @default
                                                        <i class="bi bi-info-circle text-secondary" style="font-size: 1.5rem;"></i>
                                                @endswitch
                                            @else
                                                <i class="bi bi-bell text-primary" style="font-size: 1.5rem;"></i>
                                            @endif
                                        </div>
                                        <div>
                                            <h6 class="mb-1">
                                                {{ $notification->data['title'] ?? 'Notification' }}
                                                @if(!$notification->read_at)
                                                    <span class="badge bg-primary ms-2">New</span>
                                                @endif
                                            </h6>
                                            <p class="mb-1">{{ $notification->data['message'] ?? 'You have a new notification' }}</p>
                                            <small class="text-muted">
                                                <i class="bi bi-clock me-1"></i>{{ $notification->created_at->diffForHumans() }}
                                            </small>
                                        </div>
                                    </div>
                                </div>
                                <div class="ms-3">
                                    @if(!$notification->read_at)
                                        <button class="btn btn-sm btn-outline-primary" onclick="markAsRead('{{ $notification->id }}')">
                                            <i class="bi bi-check"></i>
                                        </button>
                                    @endif
                                    <form action="{{ route('notifications.destroy', $notification) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this notification?')">
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
            <div class="card-footer">
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
