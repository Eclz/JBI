@extends('layouts.app')

@section('title', $topic->title)

@section('content')
<div class="container-fluid px-4 py-4">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('forums.index') }}">Forums</a></li>
            <li class="breadcrumb-item"><a href="{{ route('forums.show', $topic->forum) }}">{{ $topic->forum->name }}</a></li>
            <li class="breadcrumb-item active">{{ $topic->title }}</li>
        </ol>
    </nav>

    <!-- Topic Header -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <h2 class="h4 mb-0">
                    @if($topic->is_pinned)
                        <i class="bi bi-pin-angle-fill text-warning me-2"></i>
                    @endif
                    {{ $topic->title }}
                </h2>
            </div>

            <div class="d-flex align-items-center">
                <div class="me-3">
                    <div class="avatar bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <span>{{ substr($topic->author->first_name, 0, 1) }}{{ substr($topic->author->last_name, 0, 1) }}</span>
                    </div>
                </div>
                <div>
                    <strong>{{ $topic->author->first_name }} {{ $topic->author->last_name }}</strong>
                    <div class="small text-muted">Posted {{ $topic->created_at->diffForHumans() }}</div>
                </div>
            </div>

            <hr>

            <div class="topic-content">
                {!! nl2br(e($topic->content)) !!}
            </div>
        </div>
    </div>

    <!-- Replies -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="bi bi-chat-left-text me-2"></i>Replies ({{ $replies->total() }})
            </h5>
        </div>
        <div class="card-body">
            @forelse($replies as $reply)
                <div class="d-flex mb-4 {{ !$loop->last ? 'pb-4 border-bottom' : '' }}">
                    <div class="me-3">
                        <div class="avatar bg-secondary text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            <span>{{ substr($reply->author->first_name, 0, 1) }}{{ substr($reply->author->last_name, 0, 1) }}</span>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <strong>{{ $reply->author->first_name }} {{ $reply->author->last_name }}</strong>
                                <span class="badge bg-info ms-2">{{ ucfirst($reply->author->role) }}</span>
                            </div>
                            <small class="text-muted">{{ $reply->created_at->diffForHumans() }}</small>
                        </div>
                        <div>{!! nl2br(e($reply->content)) !!}</div>
                    </div>
                </div>
            @empty
                <p class="text-muted text-center mb-0">No replies yet. Be the first to reply!</p>
            @endforelse

            @if($replies->hasPages())
                <div class="mt-4">
                    {{ $replies->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Reply Form -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Post a Reply</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('forums.topics.replies.store', [$topic->forum, $topic]) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <textarea name="content" class="form-control" rows="5" placeholder="Write your reply..." required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-send me-2"></i>Post Reply
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
