@extends('layouts.app')

@section('title', 'Learning Material')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">{{ $material->title }}</h2>
            <p class="text-muted mb-0">{{ $course->name }} • {{ ucfirst($material->type) }}</p>
        </div>
        <a href="{{ route('student.lms.show', $course) }}" class="btn btn-outline-secondary">Back to Learning Path</a>
    </div>

    @php
        $isVideo = $material->type === 'video';
        $isRead = !is_null($progress->read_at);
        $isVideoCompleted = (bool) $progress->is_video_completed;
        $initialStatus = $isVideo
            ? ($isVideoCompleted ? 'Watched' : 'Watching in progress')
            : ($isRead ? 'Read' : 'Not read yet');
        $watchedSeconds = (int) ($progress->video_watched_seconds ?? 0);
        $durationSeconds = (int) ($progress->video_duration_seconds ?? 0);
        $watchedPercent = $durationSeconds > 0 ? min(100, round(($watchedSeconds / $durationSeconds) * 100)) : 0;
    @endphp

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            @if($isVideo && $material->file_url)
                <video id="lmsVideoPlayer" class="w-100 rounded" controls preload="metadata" style="max-height: 560px;" src="{{ $material->file_url }}"></video>
            @elseif($material->type === 'link' && $material->external_url)
                <div class="mb-3">
                    <a href="{{ $material->external_url }}" target="_blank" class="btn btn-primary">Open External Resource</a>
                </div>
                <iframe src="{{ $material->external_url }}" class="w-100 border rounded" style="height: 70vh;"></iframe>
            @elseif($material->file_url)
                <iframe src="{{ $material->file_url }}" class="w-100 border rounded" style="height: 75vh;"></iframe>
            @else
                <div class="alert alert-warning mb-0">Material file/link is unavailable.</div>
            @endif
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <div class="fw-semibold">Learning Tracker</div>
                <div class="small text-muted" id="trackingStatusText">{{ $initialStatus }}</div>
                @if($isVideo)
                    <div class="small text-muted mt-1">
                        Watched: <span id="watchedPercentText">{{ $watchedPercent }}</span>%
                    </div>
                @endif
            </div>
            <div class="d-flex gap-2">
                @if($isVideo)
                    <button id="markAsWatchedBtn" class="btn btn-success" type="button">Mark as Watched</button>
                @else
                    <button id="markAsReadBtn" class="btn btn-success" type="button">{{ $isRead ? 'Mark as Read Again' : 'Mark as Read' }}</button>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const trackUrl = @json(route('student.lms.material.track', [$course, $material]));
    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    const markAsReadBtn = document.getElementById('markAsReadBtn');
    const markAsWatchedBtn = document.getElementById('markAsWatchedBtn');
    const statusText = document.getElementById('trackingStatusText');
    const watchedPercentText = document.getElementById('watchedPercentText');
    const video = document.getElementById('lmsVideoPlayer');
    let lastSentSecond = -1;
    let requestInFlight = false;

    function updateUiState(payload) {
        if (payload.event === 'read') {
            statusText.textContent = 'Read';
        } else if (payload.event === 'video_complete') {
            statusText.textContent = 'Watched';
            if (watchedPercentText) watchedPercentText.textContent = '100';
        } else if (payload.event === 'video_progress') {
            statusText.textContent = 'Watching in progress';
            const duration = Number(payload.duration_seconds || 0);
            const watched = Number(payload.watched_seconds || 0);
            if (duration > 0 && watchedPercentText) {
                watchedPercentText.textContent = String(Math.min(100, Math.round((watched / duration) * 100)));
            }
        }
    }

    async function sendTracking(payload, skipIfBusy = false) {
        if (skipIfBusy && requestInFlight) return;
        requestInFlight = true;
        try {
            await fetch(trackUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                    'Accept': 'application/json',
                },
                body: JSON.stringify(payload),
            });
            updateUiState(payload);
        } catch (e) {
            // Keep silent to avoid disrupting learning flow.
        } finally {
            requestInFlight = false;
        }
    }

    markAsReadBtn?.addEventListener('click', function () {
        sendTracking({ event: 'read' });
    });

    markAsWatchedBtn?.addEventListener('click', function () {
        if (!video) {
            return;
        }

        const duration = Math.floor(video.duration || 0);
        const watched = Math.max(Math.floor(video.currentTime || 0), duration);
        sendTracking({
            event: 'video_complete',
            watched_seconds: watched,
            duration_seconds: duration,
            position_seconds: watched,
        });
    });

    if (video) {
        video.addEventListener('loadedmetadata', function () {
            const resumeAt = @json((int) ($progress->last_video_position_seconds ?? 0));
            if (resumeAt > 0 && resumeAt < video.duration) {
                video.currentTime = resumeAt;
            }
        });

        video.addEventListener('timeupdate', function () {
            const second = Math.floor(video.currentTime || 0);
            if (second > 0 && second % 10 === 0 && second !== lastSentSecond) {
                lastSentSecond = second;
                sendTracking({
                    event: 'video_progress',
                    watched_seconds: second,
                    duration_seconds: Math.floor(video.duration || 0),
                    position_seconds: second,
                }, true);
            }
        });

        video.addEventListener('ended', function () {
            sendTracking({
                event: 'video_complete',
                watched_seconds: Math.floor(video.duration || 0),
                duration_seconds: Math.floor(video.duration || 0),
                position_seconds: Math.floor(video.duration || 0),
            });
        });
    }
});
</script>
@endpush
