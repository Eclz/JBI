@extends('layouts.app')

@section('title', $forum->name)

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-2">
                    <li class="breadcrumb-item"><a href="{{ route('forums.index') }}">Forums</a></li>
                    <li class="breadcrumb-item active">{{ $forum->name }}</li>
                </ol>
            </nav>
            <h1 class="h3 mb-0 text-primary">{{ $forum->name }}</h1>
            <p class="text-muted mb-0">{{ $forum->description }}</p>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newTopicModal">
            <i class="bi bi-plus-circle me-2"></i>New Topic
        </button>
    </div>

    <!-- Search and Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-10">
                    <input type="text" name="search" class="form-control" placeholder="Search topics..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search me-2"></i>Search
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Topics List -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Topics</h5>
        </div>
        <div class="card-body p-0">
            @if($topics->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Topic</th>
                                <th class="text-center" style="width: 120px;">Replies</th>
                                <th class="text-center" style="width: 150px;">Last Activity</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($topics as $topic)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-start">
                                            @if($topic->is_pinned)
                                                <i class="bi bi-pin-angle-fill text-warning me-2 mt-1"></i>
                                            @endif
                                            <div>
                                                <h6 class="mb-1">
                                                    <a href="{{ route('forums.topics.show', $topic) }}" class="text-decoration-none">
                                                        {{ $topic->title }}
                                                    </a>
                                                </h6>
                                                <div class="small text-muted">
                                                    Started by {{ $topic->author->first_name }} {{ $topic->author->last_name }} •
                                                    {{ $topic->created_at->diffForHumans() }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-primary">{{ $topic->replies->count() }}</span>
                                    </td>
                                    <td class="text-center small text-muted">
                                        {{ $topic->updated_at->diffForHumans() }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-chat-dots text-muted" style="font-size: 3rem;"></i>
                    <p class="text-muted mt-3">No topics yet. Be the first to start a discussion!</p>
                </div>
            @endif
        </div>
        @if($topics->hasPages())
            <div class="card-footer">
                {{ $topics->links() }}
            </div>
        @endif
    </div>

    <!-- New Topic Modal -->
    <div class="modal fade" id="newTopicModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="{{ route('forums.topics.store', $forum) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Create New Topic</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Content <span class="text-danger">*</span></label>
                            <textarea name="content" class="form-control" rows="8" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check me-2"></i>Create Topic
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
