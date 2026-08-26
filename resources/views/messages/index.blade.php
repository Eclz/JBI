@extends('layouts.app')

@section('title', 'Mailbox & Messaging')

@section('content')
<div class="container-fluid px-4 py-4">
    @if(Auth::user()->isStudent())
        @include('partials.student-header-bar')
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold text-dark text-uppercase mb-0">
                <i class="bi bi-envelope-paper text-primary me-2"></i>MAILBOX & MESSAGING
            </h5>
            <p class="text-muted small mb-0">Send messages to fellow students and faculty staff, and receive system alerts</p>
        </div>
        <div class="d-flex gap-2">
            @if(Auth::user()->isFaculty() || Auth::user()->isAdmin())
                <button class="btn btn-outline-primary fw-bold" data-bs-toggle="modal" data-bs-target="#composeGroupMessageModal">
                    <i class="bi bi-people me-2"></i>COMPOSE GROUP MESSAGE
                </button>
            @endif
            <button class="btn btn-primary fw-bold" data-bs-toggle="modal" data-bs-target="#composeMessageModal">
                <i class="bi bi-pencil-square me-2"></i>COMPOSE MESSAGE
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i><strong>Error:</strong> {{ $errors->first() }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <!-- Sidebar Navigation Tabs -->
        <div class="col-md-3 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-2">
                    <div class="nav flex-column nav-pills" role="tablist">
                        <a href="{{ route('messages.index', ['type' => 'all']) }}" class="nav-link text-start py-2.5 px-3 mb-1 fw-bold {{ $type === 'all' ? 'active bg-primary' : 'text-dark' }}">
                            <i class="bi bi-inbox me-2"></i>All Mailbox Messages
                        </a>
                        <a href="{{ route('messages.index', ['type' => 'personal']) }}" class="nav-link text-start py-2.5 px-3 mb-1 fw-bold {{ $type === 'personal' ? 'active bg-primary' : 'text-dark' }}">
                            <i class="bi bi-person-lines-fill me-2"></i>Direct Messages
                        </a>
                        <a href="{{ route('messages.index', ['type' => 'sent']) }}" class="nav-link text-start py-2.5 px-3 mb-1 fw-bold {{ $type === 'sent' ? 'active bg-primary' : 'text-dark' }}">
                            <i class="bi bi-send me-2"></i>Sent Messages
                        </a>
                        <a href="{{ route('messages.index', ['type' => 'alerts']) }}" class="nav-link text-start py-2.5 px-3 fw-bold {{ $type === 'alerts' ? 'active bg-primary' : 'text-dark' }}">
                            <i class="bi bi-bell-fill me-2"></i>Assessment & Exam Alerts
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mailbox Messages List -->
        <div class="col-md-9">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 border-bottom border-primary border-2 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0 text-primary"><i class="bi bi-envelope-open me-2"></i>INBOX MESSAGES</h6>
                    <span class="badge bg-primary px-3 py-1">{{ $unreadCount }} UNREAD</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 small">
                            <thead class="bg-light text-muted">
                                <tr>
                                    <th style="width: 220px;">{{ $type === 'sent' ? 'RECIPIENT' : 'SENDER' }}</th>
                                    <th>SUBJECT & PREVIEW</th>
                                    <th style="width: 130px;">CATEGORY</th>
                                    <th style="width: 140px;">DATE</th>
                                    <th style="width: 80px;" class="text-end">ACTION</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($messages as $msg)
                                    <tr class="{{ !$msg->is_read ? 'fw-bold bg-light' : '' }}">
                                        <td>
                                            @if($type === 'sent')
                                                <span class="text-dark">{{ $msg->receiver?->full_name ?? 'System / Broadcast' }}</span><br>
                                                <small class="text-muted">{{ $msg->receiver?->role ? ucfirst($msg->receiver->role) : 'System' }}</small>
                                            @else
                                                <span class="text-dark">{{ $msg->sender?->full_name ?? 'JBI University Academic System' }}</span><br>
                                                <small class="text-muted">{{ $msg->sender?->role ? ucfirst($msg->sender->role) : 'System Alert' }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('messages.show', $msg) }}" class="text-decoration-none text-dark d-block text-truncate" style="max-width: 380px;">
                                                <span class="fw-bold text-primary">{{ $msg->subject }}</span> — {{ Str::limit(strip_tags($msg->body), 60) }}
                                            </a>
                                        </td>
                                        <td>
                                            @if($msg->type === 'assignment_alert')
                                                <span class="badge bg-warning text-dark px-2 py-1"><i class="bi bi-journal-text me-1"></i>ASSIGNMENT</span>
                                            @elseif($msg->type === 'quiz_alert')
                                                <span class="badge bg-info px-2 py-1"><i class="bi bi-patch-question me-1"></i>QUIZ</span>
                                            @elseif($msg->type === 'exam_alert')
                                                <span class="badge bg-danger px-2 py-1"><i class="bi bi-pencil-square me-1"></i>EXAM ALERT</span>
                                            @else
                                                <span class="badge bg-secondary px-2 py-1"><i class="bi bi-chat-left-text me-1"></i>MESSAGE</span>
                                            @endif
                                        </td>
                                        <td>{{ $msg->created_at->format('M d, H:i') }}</td>
                                        <td class="text-end">
                                            <a href="{{ route('messages.show', $msg) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">
                                            <i class="bi bi-inbox fs-1 d-block mb-2 text-muted opacity-50"></i>
                                            No messages in your mailbox for this category.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Compose Message Modal -->
<div class="modal fade" id="composeMessageModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
            <form action="{{ route('messages.store') }}" method="POST">
                @csrf
                <div class="modal-header text-white py-3" style="background: linear-gradient(135deg, #1e3a8a 0%, #3b5bdb 100%);">
                    <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i>Compose Message</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-uppercase">Recipient <span class="text-danger">*</span></label>
                        <select name="receiver_id" class="form-select" required>
                            <option value="">-- Select Recipient --</option>
                            @foreach($recipients as $r)
                                <option value="{{ $r->id }}" {{ isset($replyTo) && (int)$replyTo === $r->id ? 'selected' : '' }}>{{ $r->first_name }} {{ $r->last_name }} ({{ ucfirst($r->role) }} - {{ $r->email }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-uppercase">Subject <span class="text-danger">*</span></label>
                        <input type="text" name="subject" class="form-control" placeholder="e.g. Question regarding SE301 Course Project" value="{{ $replySubject ?? '' }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-uppercase">Message Content <span class="text-danger">*</span></label>
                        <textarea name="body" class="form-control" rows="5" placeholder="Type your message here..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary fw-bold px-4">
                        <i class="bi bi-send me-1"></i>Send Message
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@if(Auth::user()->isFaculty() || Auth::user()->isAdmin())
<!-- Compose Group Message Modal -->
<div class="modal fade" id="composeGroupMessageModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
            <form action="{{ route('messages.storeGroup') }}" method="POST">
                @csrf
                <div class="modal-header text-white py-3" style="background: linear-gradient(135deg, #1e3a8a 0%, #10b981 100%);">
                    <h5 class="modal-title fw-bold"><i class="bi bi-people me-2"></i>Compose Group Message</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <label class="form-label fw-bold small text-uppercase">Target Course <span class="text-danger">*</span></label>
                            <select name="course_id" class="form-select" required>
                                <option value="">-- Select Course --</option>
                                @foreach($groupCourses as $course)
                                    <option value="{{ $course->id }}">{{ $course->course_code }} - {{ $course->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-uppercase">Year of Study (Optional)</label>
                            <select name="year" class="form-select">
                                <option value="">All Years</option>
                                <option value="1">Year 1</option>
                                <option value="2">Year 2</option>
                                <option value="3">Year 3</option>
                                <option value="4">Year 4</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-uppercase">Subject <span class="text-danger">*</span></label>
                        <input type="text" name="subject" class="form-control" placeholder="e.g. Announcement about Midterm Exam" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-uppercase">Message & Email Body <span class="text-danger">*</span></label>
                        <textarea name="body" class="form-control" rows="6" placeholder="Type your message to be sent to LMS mailbox and email..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success fw-bold px-4">
                        <i class="bi bi-send me-1"></i>Send Group Message
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@if(isset($replyTo))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var myModal = new bootstrap.Modal(document.getElementById('composeMessageModal'));
        myModal.show();
    });
</script>
@endif
@endsection
