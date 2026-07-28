@extends('layouts.app')

@section('title', 'Forums')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-primary">
                <i class="bi bi-chat-dots me-2"></i>Discussion Forums
            </h1>
            <p class="text-muted mb-0">Connect and discuss with the community</p>
        </div>
    </div>

    <!-- Recent Topics -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="bi bi-clock-history me-2"></i>Recent Topics
            </h5>
        </div>
        <div class="card-body">
            @if($recentTopics->count() > 0)
                <div class="list-group list-group-flush">
                    @foreach($recentTopics as $topic)
                        <a href="{{ route('forums.topics.show', $topic) }}" class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">
                                        @if($topic->is_pinned)
                                            <i class="bi bi-pin-angle-fill text-warning me-1"></i>
                                        @endif
                                        {{ $topic->title }}
                                    </h6>
                                    <p class="mb-1 text-muted small">
                                        in <span class="badge bg-info">{{ $topic->forum->name }}</span>
                                        by {{ $topic->author->first_name }} {{ $topic->author->last_name }}
                                    </p>
                                </div>
                                <div class="text-end ms-3">
                                    <span class="badge bg-primary">{{ $topic->replies->count() }} replies</span>
                                    <div class="small text-muted mt-1">{{ $topic->updated_at->diffForHumans() }}</div>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <p class="text-muted text-center mb-0">No recent topics</p>
            @endif
        </div>
    </div>

    <!-- Forums List -->
    <div class="row">
        @forelse($forums as $forum)
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">
                            <a href="{{ route('forums.show', $forum) }}" class="text-decoration-none">
                                {{ $forum->name }}
                            </a>
                        </h5>
                        <p class="card-text text-muted">{{ $forum->description }}</p>

                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div>
                                <span class="badge bg-primary me-2">{{ $forum->topics->count() }} topics</span>
                                @if($forum->moderated)
                                    <span class="badge bg-info">Moderated</span>
                                @endif
                            </div>
                            <a href="{{ route('forums.show', $forum) }}" class="btn btn-sm btn-outline-primary">
                                View Forum <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>

                        @if($forum->topics->count() > 0)
                            <hr>
                            <div class="small text-muted">
                                <strong>Latest:</strong> {{ $forum->topics->first()->title }}
                                <div class="text-truncate">by {{ $forum->topics->first()->author->first_name }} • {{ $forum->topics->first()->created_at->diffForHumans() }}</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>No forums available at the moment.
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection
