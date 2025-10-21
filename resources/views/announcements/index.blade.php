@extends('layouts.app')

@section('title', 'Announcements')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0 text-primary">
            <i class="bi bi-megaphone me-2"></i>Announcements
        </h1>
        <p class="text-muted mb-0">Stay updated with important information</p>
    </div>
    @if(Auth::user()->role === 'admin' || Auth::user()->role === 'faculty')
        <a href="{{ route('announcements.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-2"></i>Create Announcement
        </a>
    @endif
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('announcements.index') }}" class="row g-3">
            <div class="col-md-4">
                <label for="priority" class="form-label">Priority</label>
                <select class="form-select" id="priority" name="priority">
                    <option value="">All Priorities</option>
                    <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Low</option>
                    <option value="normal" {{ request('priority') == 'normal' ? 'selected' : '' }}>Normal</option>
                    <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>High</option>
                    <option value="urgent" {{ request('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                </select>
            </div>
            <div class="col-md-4">
                <label for="course" class="form-label">Course</label>
                <select class="form-select" id="course" name="course">
                    <option value="">All Courses</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" 
                                {{ request('course') == $course->id ? 'selected' : '' }}>
                            {{ $course->code }} - {{ $course->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label for="target" class="form-label">Target</label>
                <select class="form-select" id="target" name="target">
                    <option value="">All</option>
                    <option value="all" {{ request('target') == 'all' ? 'selected' : '' }}>Everyone</option>
                    <option value="student" {{ request('target') == 'student' ? 'selected' : '' }}>Students</option>
                    <option value="faculty" {{ request('target') == 'faculty' ? 'selected' : '' }}>Faculty</option>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-outline-primary me-2">
                    <i class="bi bi-search"></i> Filter
                </button>
                <a href="{{ route('announcements.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-x-lg"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Announcements List -->
<div class="row">
    @forelse($announcements as $announcement)
        @php
            $priorityColors = [
                'low' => 'secondary',
                'normal' => 'info',
                'high' => 'warning',
                'urgent' => 'danger'
            ];
            $priorityIcons = [
                'low' => 'bi-info-circle',
                'normal' => 'bi-bell',
                'high' => 'bi-exclamation-triangle',
                'urgent' => 'bi-exclamation-triangle-fill'
            ];
        @endphp
        
        <div class="col-12 mb-3">
            <div class="card {{ $announcement->priority === 'urgent' ? 'border-danger' : ($announcement->priority === 'high' ? 'border-warning' : '') }}">
                <div class="card-header d-flex justify-content-between align-items-start">
                    <div class="d-flex align-items-start">
                        <i class="bi {{ $priorityIcons[$announcement->priority] }} text-{{ $priorityColors[$announcement->priority] }} me-2 mt-1"></i>
                        <div>
                            <h5 class="card-title mb-1">
                                <a href="{{ route('announcements.show', $announcement) }}" class="text-decoration-none">
                                    {{ $announcement->title }}
                                </a>
                            </h5>
                            <div class="d-flex align-items-center gap-3">
                                <small class="text-muted">
                                    <i class="bi bi-person me-1"></i>
                                    {{ $announcement->author->first_name }} {{ $announcement->author->last_name }}
                                </small>
                                <small class="text-muted">
                                    <i class="bi bi-calendar me-1"></i>
                                    {{ $announcement->created_at->format('M d, Y g:i A') }}
                                </small>
                                @if($announcement->course)
                                    <small class="text-muted">
                                        <i class="bi bi-book me-1"></i>
                                        {{ $announcement->course->code }}
                                    </small>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-{{ $priorityColors[$announcement->priority] }}">
                            {{ ucfirst($announcement->priority) }}
                        </span>
                        <span class="badge bg-light text-dark">
                            {{ ucfirst($announcement->target_role) }}
                        </span>
                        @if(Auth::user()->id === $announcement->author_id)
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-three-dots"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="{{ route('announcements.edit', $announcement) }}">
                                        <i class="bi bi-pencil me-2"></i>Edit
                                    </a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item text-danger" href="#" onclick="confirmDelete({{ $announcement->id }})">
                                        <i class="bi bi-trash me-2"></i>Delete
                                    </a></li>
                                </ul>
                            </div>
                        @endif
                    </div>
                </div>
                
                <div class="card-body">
                    <p class="card-text">{{ Str::limit($announcement->content, 200) }}</p>
                    
                    @if($announcement->attachment_path)
                        <div class="mt-3">
                            <a href="{{ Storage::url($announcement->attachment_path) }}" 
                               class="btn btn-sm btn-outline-primary" target="_blank">
                                <i class="bi bi-paperclip me-2"></i>View Attachment
                            </a>
                        </div>
                    @endif
                    
                    <div class="mt-3">
                        <a href="{{ route('announcements.show', $announcement) }}" class="btn btn-primary">
                            Read More <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="text-center py-5">
                <i class="bi bi-megaphone display-1 text-muted"></i>
                <h4 class="mt-3">No announcements found</h4>
                <p class="text-muted">No announcements match your current filters.</p>
                @if(Auth::user()->role === 'admin' || Auth::user()->role === 'faculty')
                    <a href="{{ route('announcements.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-lg me-2"></i>Create First Announcement
                    </a>
                @endif
            </div>
        </div>
    @endforelse
</div>

<!-- Pagination -->
@if($announcements->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $announcements->links() }}
    </div>
@endif

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this announcement? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function confirmDelete(announcementId) {
    const form = document.getElementById('deleteForm');
    form.action = `/announcements/${announcementId}`;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>
@endpush

@push('styles')
<style>
.card.border-danger {
    border-color: #dc3545 !important;
    border-width: 2px;
}

.card.border-warning {
    border-color: #ffc107 !important;
    border-width: 2px;
}
</style>
@endpush
