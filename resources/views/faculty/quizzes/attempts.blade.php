@extends('layouts.app')

@section('title', 'Quiz Attempts')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="mb-4">
        <a href="{{ route('faculty.quizzes.show', $quiz) }}" class="text-decoration-none small">
            <i class="bi bi-arrow-left me-1"></i>Back to quiz
        </a>
        <h1 class="h3 mt-2 mb-0">Attempts: {{ $quiz->title }}</h1>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Student</th>
                        <th>Started</th>
                        <th>Submitted</th>
                        <th>Score</th>
                        <th>%</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attempts as $attempt)
                        <tr>
                            <td>{{ optional($attempt->student)->full_name ?? 'Unknown' }}</td>
                            <td>{{ optional($attempt->started_at)->format('M d, Y g:i A') ?? '—' }}</td>
                            <td>{{ optional($attempt->submitted_at)->format('M d, Y g:i A') ?? '—' }}</td>
                            <td>{{ $attempt->score ?? '—' }}</td>
                            <td>{{ $attempt->percentage !== null ? number_format($attempt->percentage, 1) . '%' : '—' }}</td>
                            <td><span class="badge bg-secondary">{{ ucfirst(str_replace('_', ' ', $attempt->status)) }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">No attempts yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($attempts->hasPages())
            <div class="card-footer">{{ $attempts->links() }}</div>
        @endif
    </div>
</div>
@endsection
