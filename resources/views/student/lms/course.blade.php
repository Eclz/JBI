@extends('layouts.app')

@section('title', 'Course Learning Path')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">{{ $course->name }}</h2>
            <p class="text-muted mb-0">Learning Path • {{ $course->semester->name ?? 'No Semester' }}</p>
        </div>
        <div class="d-flex gap-2">
            @if($progress['total'] > 0 && $progress['percent'] >= 100)
                <a href="{{ route('student.lms.certificate', $course) }}" class="btn btn-success">View Certificate</a>
            @endif
            <a href="{{ route('student.lms.index') }}" class="btn btn-outline-secondary">Back to My Learning</a>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between mb-2">
                <span>Overall Progress</span>
                <strong>{{ $progress['percent'] }}%</strong>
            </div>
            <div class="progress" style="height: 12px;">
                <div class="progress-bar" style="width: {{ $progress['percent'] }}%"></div>
            </div>
            <div class="row mt-3 small text-muted">
                <div class="col-md-3">Materials: {{ $progress['materials'][0] }}/{{ $progress['materials'][1] }}</div>
                <div class="col-md-3">Assignments: {{ $progress['assignments'][0] }}/{{ $progress['assignments'][1] }}</div>
                <div class="col-md-3">Quizzes: {{ $progress['quizzes'][0] }}/{{ $progress['quizzes'][1] }}</div>
                <div class="col-md-3">Exams: {{ $progress['exams'][0] }}/{{ $progress['exams'][1] }}</div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white"><h5 class="mb-0">Module 1: Course Materials</h5></div>
        <div class="card-body border-bottom bg-light">
            <div class="row g-2">
                <div class="col-md-4">
                    <select id="materialTypeFilter" class="form-select form-select-sm">
                        <option value="">All types</option>
                        <option value="video">Video</option>
                        <option value="document">Document</option>
                        <option value="link">Link</option>
                        <option value="audio">Audio</option>
                        <option value="image">Image</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <select id="materialStatusFilter" class="form-select form-select-sm">
                        <option value="">All status</option>
                        <option value="read">Read/Watched</option>
                        <option value="watching">Watching</option>
                        <option value="unread">Unread</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <ul class="list-group list-group-flush" id="materialsList">
                @forelse($materials as $material)
                    @php
                        $isDone = in_array($material->id, $completed['materials'], true);
                        $progressRow = $materialProgress->get($material->id);
                        $isVideo = $material->type === 'video';
                        $hasRead = !is_null($progressRow?->read_at);
                        $videoDone = (bool) ($progressRow?->is_video_completed ?? false);
                        $watchedSeconds = (int) ($progressRow?->video_watched_seconds ?? 0);
                        $durationSeconds = (int) ($progressRow?->video_duration_seconds ?? 0);
                        $watchedPercent = $durationSeconds > 0 ? min(100, round(($watchedSeconds / $durationSeconds) * 100)) : 0;
                    @endphp
                    <li
                        class="list-group-item d-flex justify-content-between align-items-center material-item"
                        data-material-type="{{ $material->type }}"
                        data-material-status="{{ $isDone || $videoDone || $hasRead ? 'read' : ($isVideo && $watchedPercent > 0 ? 'watching' : 'unread') }}"
                    >
                        <div>
                            <div class="fw-semibold">{{ $material->title }}</div>
                            <div class="small text-muted">{{ ucfirst($material->type) }}</div>
                            @if($isVideo && $watchedPercent > 0 && !$videoDone)
                                <div class="small text-muted">Watched {{ $watchedPercent }}%</div>
                            @endif
                        </div>
                        <div class="d-flex gap-2 align-items-center">
                            <a href="{{ route('student.lms.material', [$course, $material]) }}" class="btn btn-sm btn-outline-primary">
                                {{ $isDone ? 'Review' : ($progressRow ? 'Continue' : 'Open') }}
                            </a>
                            @if($isDone || $videoDone || $hasRead)
                                <span class="badge bg-success align-self-center">{{ $isVideo ? 'Watched' : 'Read' }}</span>
                            @elseif($isVideo && $watchedPercent > 0)
                                <span class="badge bg-warning text-dark align-self-center">Watching</span>
                            @else
                                <span class="badge bg-secondary align-self-center">{{ $isVideo ? 'Not Watched' : 'Unread' }}</span>
                            @endif
                        </div>
                    </li>
                @empty
                    <li class="list-group-item text-muted">No materials published yet.</li>
                @endforelse
            </ul>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white"><h5 class="mb-0">Module 2: Assignments</h5></div>
        <div class="card-body p-0">
            <ul class="list-group list-group-flush">
                @forelse($assignments as $assignment)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fw-semibold">{{ $assignment->title }}</div>
                            <div class="small text-muted">Due: {{ $assignment->due_date?->format('M d, Y H:i') ?? 'N/A' }}</div>
                        </div>
                        <div class="d-flex gap-2 align-items-center">
                            <a href="{{ route('student.assignments.show', $assignment) }}" class="btn btn-sm btn-outline-primary">Open</a>
                            @if(in_array($assignment->id, $completed['assignments'], true))
                                <span class="badge bg-success">Completed</span>
                            @endif
                        </div>
                    </li>
                @empty
                    <li class="list-group-item text-muted">No assignments published yet.</li>
                @endforelse
            </ul>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white"><h5 class="mb-0">Module 3: Quizzes</h5></div>
        <div class="card-body p-0">
            <ul class="list-group list-group-flush">
                @forelse($quizzes as $quiz)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fw-semibold">{{ $quiz->title }}</div>
                            <div class="small text-muted">Duration: {{ $quiz->duration_minutes }} min</div>
                        </div>
                        <div class="d-flex gap-2 align-items-center">
                            <a href="{{ route('student.quizzes.show', $quiz) }}" class="btn btn-sm btn-outline-primary">Open</a>
                            @if(in_array($quiz->id, $completed['quizzes'], true))
                                <span class="badge bg-success">Completed</span>
                            @endif
                        </div>
                    </li>
                @empty
                    <li class="list-group-item text-muted">No quizzes published yet.</li>
                @endforelse
            </ul>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white"><h5 class="mb-0">Module 4: Exams</h5></div>
        <div class="card-body p-0">
            <ul class="list-group list-group-flush">
                @forelse($exams as $exam)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fw-semibold">{{ $exam->title }}</div>
                            <div class="small text-muted">Window: {{ $exam->start_time?->format('M d, Y H:i') }} - {{ $exam->end_time?->format('M d, Y H:i') }}</div>
                        </div>
                        <div class="d-flex gap-2 align-items-center">
                            <a href="{{ route('student.exams.show', $exam) }}" class="btn btn-sm btn-outline-primary">Open</a>
                            @if(in_array($exam->id, $completed['exams'], true))
                                <span class="badge bg-success">Completed</span>
                            @endif
                        </div>
                    </li>
                @empty
                    <li class="list-group-item text-muted">No exams published yet.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const typeFilter = document.getElementById('materialTypeFilter');
    const statusFilter = document.getElementById('materialStatusFilter');
    const items = Array.from(document.querySelectorAll('.material-item'));

    function applyFilters() {
        const typeValue = (typeFilter?.value || '').trim();
        const statusValue = (statusFilter?.value || '').trim();

        items.forEach((item) => {
            const matchesType = !typeValue || item.dataset.materialType === typeValue;
            const matchesStatus = !statusValue || item.dataset.materialStatus === statusValue;
            item.style.display = matchesType && matchesStatus ? '' : 'none';
        });
    }

    typeFilter?.addEventListener('change', applyFilters);
    statusFilter?.addEventListener('change', applyFilters);
});
</script>
@endpush
