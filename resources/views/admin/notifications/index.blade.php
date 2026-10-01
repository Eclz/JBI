@extends('layouts.app')

@section('title', 'Manage Notifications')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1 fw-bold text-dark">System Notifications</h2>
            <p class="text-muted mb-0">Broadcast alerts and messages to users across the system.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>Please fix the errors below.
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- New Notification Form -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-send me-2 text-primary"></i>Send Notification</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.notifications.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Target Audience <span class="text-danger">*</span></label>
                            <select name="target_type" id="targetType" class="form-select bg-light" required>
                                <option value="all">All Users in System</option>
                                <option value="role">Specific Role</option>
                                <option value="user">Specific User</option>
                            </select>
                        </div>
                        
                        <div class="mb-3 d-none" id="roleSelector">
                            <label class="form-label fw-semibold">Select Role <span class="text-danger">*</span></label>
                            <select name="target_role" class="form-select">
                                <option value="">-- Choose Role --</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="mb-3 d-none" id="userSelector">
                            <label class="form-label fw-semibold">Select User <span class="text-danger">*</span></label>
                            <select name="target_user" class="form-select">
                                <option value="">-- Choose User --</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Priority Level <span class="text-danger">*</span></label>
                            <div class="d-flex gap-2">
                                <input type="radio" class="btn-check" name="priority" id="pri_low" value="low" autocomplete="off">
                                <label class="btn btn-outline-secondary btn-sm flex-grow-1" for="pri_low">Low</label>
                                
                                <input type="radio" class="btn-check" name="priority" id="pri_normal" value="normal" autocomplete="off" checked>
                                <label class="btn btn-outline-primary btn-sm flex-grow-1" for="pri_normal">Normal</label>
                                
                                <input type="radio" class="btn-check" name="priority" id="pri_high" value="high" autocomplete="off">
                                <label class="btn btn-outline-warning btn-sm flex-grow-1" for="pri_high">High</label>
                                
                                <input type="radio" class="btn-check" name="priority" id="pri_urgent" value="urgent" autocomplete="off">
                                <label class="btn btn-outline-danger btn-sm flex-grow-1" for="pri_urgent">Urgent</label>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Notification Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" required placeholder="Short, clear subject">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Message Content <span class="text-danger">*</span></label>
                            <textarea name="message" class="form-control" rows="4" required placeholder="Detailed message..."></textarea>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Action URL (Optional)</label>
                            <input type="url" name="action_url" class="form-control" placeholder="https://...">
                            <div class="form-text">If provided, the notification will be clickable.</div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100 fw-bold py-2">
                            <i class="bi bi-broadcast me-2"></i> Broadcast Notification
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Notification History -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-clock-history me-2 text-primary"></i>Recent Dispatches</h5>
                    <span class="badge bg-light text-dark border">{{ $recentNotifications->total() }} Sent</span>
                </div>
                <div class="card-body p-0">
                    @if($recentNotifications->isEmpty())
                        <div class="p-5 text-center text-muted">
                            <i class="bi bi-inbox fs-1 mb-3 d-block opacity-50"></i>
                            <h6>No notifications sent yet.</h6>
                            <p class="small">Broadcasts you send will appear in this history log.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4">Recipient</th>
                                        <th>Subject & Message</th>
                                        <th>Priority</th>
                                        <th>Status</th>
                                        <th class="pe-4 text-end">Sent At</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentNotifications as $notification)
                                        <tr>
                                            <td class="ps-4">
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar bg-light text-primary rounded-circle me-2 d-flex align-items-center justify-content-center border" style="width: 32px; height: 32px;">
                                                        {{ strtoupper(substr($notification->user->name ?? '?', 0, 1)) }}
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold fs-7">{{ $notification->user->name ?? 'Deleted User' }}</div>
                                                        <div class="text-muted small" style="font-size: 0.75rem;">{{ $notification->user->email ?? 'N/A' }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td style="max-width: 250px;">
                                                <div class="fw-bold text-truncate">{{ $notification->title }}</div>
                                                <div class="text-muted small text-truncate">{{ $notification->message }}</div>
                                            </td>
                                            <td>
                                                @if($notification->priority === 'urgent')
                                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle">Urgent</span>
                                                @elseif($notification->priority === 'high')
                                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle">High</span>
                                                @elseif($notification->priority === 'low')
                                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">Low</span>
                                                @else
                                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle">Normal</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($notification->is_read)
                                                    <span class="text-success small fw-semibold"><i class="bi bi-check2-all me-1"></i>Read</span>
                                                @else
                                                    <span class="text-muted small"><i class="bi bi-check2 me-1"></i>Delivered</span>
                                                @endif
                                            </td>
                                            <td class="pe-4 text-end text-muted small">
                                                {{ $notification->created_at->diffForHumans() }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
                @if($recentNotifications->hasPages())
                    <div class="card-footer bg-white py-3 border-top">
                        {{ $recentNotifications->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const targetType = document.getElementById('targetType');
        const roleSelector = document.getElementById('roleSelector');
        const userSelector = document.getElementById('userSelector');
        
        targetType.addEventListener('change', function() {
            if (this.value === 'all') {
                roleSelector.classList.add('d-none');
                userSelector.classList.add('d-none');
                roleSelector.querySelector('select').required = false;
                userSelector.querySelector('select').required = false;
            } else if (this.value === 'role') {
                roleSelector.classList.remove('d-none');
                userSelector.classList.add('d-none');
                roleSelector.querySelector('select').required = true;
                userSelector.querySelector('select').required = false;
            } else if (this.value === 'user') {
                roleSelector.classList.add('d-none');
                userSelector.classList.remove('d-none');
                roleSelector.querySelector('select').required = false;
                userSelector.querySelector('select').required = true;
            }
        });
    });
</script>
@endpush
@endsection
