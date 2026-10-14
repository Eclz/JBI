@extends('layouts.app')

@section('title', $announcement->title)

@section('content')
@php
    $priorityColors = ['low' => 'secondary', 'normal' => 'info', 'high' => 'warning', 'urgent' => 'danger'];
@endphp

<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <a href="{{ route('announcements.index') }}" class="text-decoration-none small">
            <i class="bi bi-arrow-left me-1"></i>All announcements
        </a>
        <h1 class="h3 mt-2 mb-1">{{ $announcement->title }}</h1>
        <div class="text-muted small">
            <span class="badge bg-{{ $priorityColors[$announcement->priority] ?? 'secondary' }} me-1">{{ ucfirst($announcement->priority) }}</span>
            <span class="badge bg-light text-dark me-2">{{ ucfirst($announcement->type) }}</span>
            <i class="bi bi-person me-1"></i>{{ optional($announcement->creator)->first_name }} {{ optional($announcement->creator)->last_name }}
            <i class="bi bi-calendar ms-3 me-1"></i>{{ ($announcement->published_at ?? $announcement->created_at)->format('M d, Y g:i A') }}
            @if($announcement->expires_at)
                <i class="bi bi-hourglass-split ms-3 me-1"></i>Expires {{ $announcement->expires_at->format('M d, Y') }}
            @endif
        </div>
    </div>

    @can('update', $announcement)
        <div class="d-flex gap-2">
            <a href="{{ route('announcements.edit', $announcement) }}" class="btn btn-outline-primary">
                <i class="bi bi-pencil me-1"></i>Edit
            </a>
            <form method="POST" action="{{ route('announcements.destroy', $announcement) }}"
                  onsubmit="return confirm('Delete this announcement? This cannot be undone.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger"><i class="bi bi-trash me-1"></i>Delete</button>
            </form>
        </div>
    @endcan
</div>

@unless($announcement->is_published)
    <div class="alert alert-warning">This announcement is a draft and is not visible to its audience yet.</div>
@endunless

<div class="card">
    <div class="card-body">
        <div style="white-space: pre-line;">{{ $announcement->content }}</div>
    </div>
    <div class="card-footer text-muted small">
        <span class="me-3"><i class="bi bi-people me-1"></i>{{ $announcement->target_roles ? implode(', ', array_map('ucfirst', $announcement->target_roles)) : 'Everyone' }}</span>
        @if($announcement->course)
            <span class="me-3"><i class="bi bi-book me-1"></i>{{ $announcement->course->code }} - {{ $announcement->course->name }}</span>
        @endif
        @if($announcement->department)
            <span><i class="bi bi-building me-1"></i>{{ $announcement->department->name }}</span>
        @endif
    </div>
</div>
@endsection
