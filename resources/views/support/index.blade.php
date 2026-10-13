@extends('layouts.app')

@section('title', 'Help & Support Desk')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h1 class="h3 mb-0 text-primary fw-bold">
                <i class="bi bi-headset me-2"></i>Help & Support Desk
            </h1>
            <p class="text-muted mb-0">Submit tickets, track inquiries, and access official university assistance</p>
        </div>
        <div>
            <a href="#newTicketCard" class="btn btn-primary fw-bold shadow-sm">
                <i class="bi bi-plus-circle me-1"></i>Open New Ticket
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Admin / Staff Queue (Visible only to Admin & SuperAdmin) -->
    @if($isAdminOrStaff && isset($adminTickets))
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h5 class="card-title mb-0 fw-bold text-dark">
                        <i class="bi bi-shield-check text-warning me-2"></i>Staff Ticket Dispatch Queue
                    </h5>
                    <small class="text-muted">Review and resolve incoming support requests across all university stakeholders</small>
                </div>
                <form method="GET" action="{{ route('support.index') }}" class="d-flex gap-2">
                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">All Statuses</option>
                        <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>Open</option>
                        <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Resolved</option>
                        <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Closed</option>
                    </select>
                    <select name="priority" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">All Priorities</option>
                        <option value="urgent" {{ request('priority') === 'urgent' ? 'selected' : '' }}>Urgent</option>
                        <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>High</option>
                        <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Low</option>
                    </select>
                </form>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Ticket #</th>
                                <th>Submitter</th>
                                <th>Subject & Category</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th>Submitted</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($adminTickets as $t)
                                <tr>
                                    <td>
                                        <span class="fw-bold font-monospace text-primary">{{ $t->ticket_number }}</span>
                                    </td>
                                    <td>
                                        <div class="fw-semibold">{{ $t->user->full_name ?? 'Unknown User' }}</div>
                                        <small class="text-muted text-uppercase">{{ $t->user->role ?? 'user' }}</small>
                                    </td>
                                    <td>
                                        <div class="fw-bold">{{ $t->subject }}</div>
                                        <span class="badge bg-light text-dark border">{{ ucfirst($t->category) }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $t->priority_badge }} text-uppercase">{{ $t->priority }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $t->status_badge }} text-uppercase">{{ str_replace('_', ' ', $t->status) }}</span>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $t->created_at->format('M d, Y H:i') }}</small>
                                    </td>
                                    <td class="text-end">
                                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#adminTicketModal{{ $t->id }}">
                                            <i class="bi bi-pencil-square me-1"></i>Manage
                                        </button>
                                    </td>
                                </tr>

                                <!-- Staff Manage Modal -->
                                <div class="modal fade" id="adminTicketModal{{ $t->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-lg">
                                        <div class="modal-content border-0 shadow">
                                            <form action="{{ route('support.update', $t) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-header bg-primary text-white py-3">
                                                    <h5 class="modal-title fw-bold">
                                                        <i class="bi bi-ticket-detailed me-2"></i>Ticket {{ $t->ticket_number }}: {{ $t->subject }}
                                                    </h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body p-4">
                                                    <div class="row g-3 mb-3">
                                                        <div class="col-md-6">
                                                            <small class="text-muted d-block text-uppercase">Submitter</small>
                                                            <strong>{{ $t->user->full_name ?? 'User' }} ({{ $t->user->email ?? 'N/A' }})</strong>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <small class="text-muted d-block text-uppercase">Category</small>
                                                            <strong>{{ ucfirst($t->category) }}</strong>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <small class="text-muted d-block text-uppercase">Priority</small>
                                                            <span class="badge bg-{{ $t->priority_badge }} text-uppercase">{{ $t->priority }}</span>
                                                        </div>
                                                    </div>

                                                    <div class="card bg-light border-0 p-3 mb-3">
                                                        <small class="text-muted fw-bold text-uppercase mb-1">Original User Inquiry:</small>
                                                        <p class="mb-0 text-dark" style="white-space: pre-wrap;">{{ $t->message }}</p>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">Update Status <span class="text-danger">*</span></label>
                                                        <select name="status" class="form-select" required>
                                                            <option value="open" {{ $t->status === 'open' ? 'selected' : '' }}>Open</option>
                                                            <option value="in_progress" {{ $t->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                                            <option value="resolved" {{ $t->status === 'resolved' ? 'selected' : '' }}>Resolved</option>
                                                            <option value="closed" {{ $t->status === 'closed' ? 'selected' : '' }}>Closed</option>
                                                        </select>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">Staff Resolution Notes / User Reply</label>
                                                        <textarea name="admin_notes" class="form-control" rows="4" placeholder="Provide notes on actions taken or reply directly to the user...">{{ old('admin_notes', $t->admin_notes) }}</textarea>
                                                        <small class="text-muted">These notes will be recorded on the ticket and notified to the submitter.</small>
                                                    </div>
                                                </div>
                                                <div class="modal-footer bg-light py-2">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                    <button type="submit" class="btn btn-primary fw-bold">
                                                        <i class="bi bi-save me-1"></i>Save & Update Ticket
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        <i class="bi bi-inbox fs-2 d-block mb-2"></i>No support tickets found in this queue.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($adminTickets->hasPages())
                    <div class="p-3 border-top">
                        {{ $adminTickets->links() }}
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- User's Submitted Tickets -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3 border-bottom">
            <h5 class="card-title mb-0 fw-bold text-dark">
                <i class="bi bi-ticket-perforated text-primary me-2"></i>My Support Tickets
            </h5>
            <small class="text-muted">Track the progress and administrative responses to your submitted tickets</small>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Ticket #</th>
                            <th>Subject</th>
                            <th>Category</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th>Date Opened</th>
                            <th class="text-end">Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($myTickets as $ticket)
                            <tr>
                                <td>
                                    <span class="fw-bold font-monospace text-primary">{{ $ticket->ticket_number }}</span>
                                </td>
                                <td>
                                    <span class="fw-semibold">{{ $ticket->subject }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">{{ ucfirst($ticket->category) }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $ticket->priority_badge }} text-uppercase">{{ $ticket->priority }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $ticket->status_badge }} text-uppercase">{{ str_replace('_', ' ', $ticket->status) }}</span>
                                </td>
                                <td>
                                    <small class="text-muted">{{ $ticket->created_at->format('M d, Y H:i') }}</small>
                                </td>
                                <td class="text-end">
                                    <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#userTicketModal{{ $ticket->id }}">
                                        <i class="bi bi-eye me-1"></i>View
                                    </button>
                                </td>
                            </tr>

                            <!-- User View Modal -->
                            <div class="modal fade" id="userTicketModal{{ $ticket->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content border-0 shadow">
                                        <div class="modal-header bg-light py-3 border-bottom">
                                            <h5 class="modal-title fw-bold text-dark">
                                                <i class="bi bi-receipt me-2"></i>Ticket {{ $ticket->ticket_number }}
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body p-4">
                                            <div class="mb-3">
                                                <h6 class="fw-bold text-primary mb-1">{{ $ticket->subject }}</h6>
                                                <div class="d-flex gap-2 align-items-center">
                                                    <span class="badge bg-{{ $ticket->status_badge }}">{{ strtoupper(str_replace('_', ' ', $ticket->status)) }}</span>
                                                    <span class="badge bg-{{ $ticket->priority_badge }}">{{ strtoupper($ticket->priority) }} PRIORITY</span>
                                                    <small class="text-muted">{{ $ticket->created_at->format('M d, Y \a\t h:i A') }}</small>
                                                </div>
                                            </div>

                                            <div class="card bg-light border-0 p-3 mb-3">
                                                <small class="text-muted fw-bold text-uppercase mb-1">Your Inquiry:</small>
                                                <p class="mb-0 text-dark" style="white-space: pre-wrap;">{{ $ticket->message }}</p>
                                            </div>

                                            @if($ticket->admin_notes)
                                                <div class="card border border-success border-opacity-25 bg-success bg-opacity-10 p-3">
                                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                                        <small class="text-success fw-bold text-uppercase">
                                                            <i class="bi bi-chat-left-dots-fill me-1"></i>Official Support Resolution / Response:
                                                        </small>
                                                        @if($ticket->resolved_at)
                                                            <small class="text-muted">{{ $ticket->resolved_at->format('M d, Y') }}</small>
                                                        @endif
                                                    </div>
                                                    <p class="mb-0 text-dark" style="white-space: pre-wrap;">{{ $ticket->admin_notes }}</p>
                                                </div>
                                            @else
                                                <div class="alert alert-info py-2 px-3 small mb-0">
                                                    <i class="bi bi-hourglass-split me-1"></i>Our support desk is currently evaluating your request. You will receive an update once reviewed.
                                                </div>
                                            @endif
                                        </div>
                                        <div class="modal-footer bg-light py-2">
                                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    <i class="bi bi-inbox fs-2 d-block mb-2"></i>You have not submitted any support tickets yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($myTickets->hasPages())
                <div class="p-3 border-top">
                    {{ $myTickets->links() }}
                </div>
            @endif
        </div>
    </div>

    <div class="row">
        <!-- Contact Form & FAQs -->
        <div class="col-lg-8 mb-4">
            <!-- Submit Form -->
            <div class="card border-0 shadow-sm mb-4" id="newTicketCard">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="card-title mb-0 fw-bold text-primary">
                        <i class="bi bi-pencil-square me-2"></i>Open a New Support Ticket
                    </h5>
                    <small class="text-muted">Fill out the details below and an official tracking ticket will be generated</small>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('support.store') }}" method="POST">
                        @csrf

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Inquiry Category <span class="text-danger">*</span></label>
                                <select name="category" class="form-select @error('category') is-invalid @enderror" required>
                                    <option value="general" selected>General Inquiry</option>
                                    <option value="technical">Technical & Portal Access</option>
                                    <option value="academic">Academic & Course Registration</option>
                                    <option value="finance">Tuition, Fees & Invoices</option>
                                    <option value="admissions">Admissions & Records</option>
                                </select>
                                @error('category')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Priority Level <span class="text-danger">*</span></label>
                                <select name="priority" class="form-select @error('priority') is-invalid @enderror" required>
                                    <option value="low">Low - Routine inquiry</option>
                                    <option value="medium" selected>Medium - Normal assistance</option>
                                    <option value="high">High - Urgent / Exam or Portal Issue</option>
                                    <option value="urgent">Urgent - Critical blocker</option>
                                </select>
                                @error('priority')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Subject <span class="text-danger">*</span></label>
                            <input type="text" name="subject" class="form-control @error('subject') is-invalid @enderror"
                                   placeholder="Brief summary of your inquiry..."
                                   value="{{ old('subject') }}" required>
                            @error('subject')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Detailed Message <span class="text-danger">*</span></label>
                            <textarea name="message" class="form-control @error('message') is-invalid @enderror"
                                      rows="5" placeholder="Please provide detailed information regarding your issue or inquiry..." required>{{ old('message') }}</textarea>
                            @error('message')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary fw-bold px-4 py-2 shadow-sm">
                            <i class="bi bi-send-check me-2"></i>Submit Support Ticket
                        </button>
                    </form>
                </div>
            </div>

            <!-- FAQ Accordion -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="card-title mb-0 fw-bold text-dark">
                        <i class="bi bi-patch-question text-info me-2"></i>Frequently Asked Questions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="accordion" id="faqAccordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                    How do I reset my password?
                                </button>
                            </h2>
                            <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-muted">
                                    You can reset your password from the login page by clicking "Forgot Password?" or by going to your Profile Settings and selecting "Change Password".
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                    How do I enroll in a course?
                                </button>
                            </h2>
                            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-muted">
                                    Navigate to your Courses or Academics page, view available courses for your active semester, and click the "Enroll" button. Some courses may require prerequisite verification or department approval.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                    Where can I view my grades and academic transcript?
                                </button>
                            </h2>
                            <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-muted">
                                    Go to your Student Dashboard and click on "Grades" or "Academic Records" to view your provisional grades, semester GPAs, and cumulative transcripts.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                                    How do I submit an assignment or take an online quiz?
                                </button>
                            </h2>
                            <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-muted">
                                    Access the specific course page from your dashboard, navigate to the "Assignments" or "Quizzes" tab, and use the upload/attempt form before the deadline expires.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Links & Direct Contacts -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="card-title mb-0 fw-bold text-dark">
                        <i class="bi bi-link-45deg text-primary me-2"></i>Quick Navigation
                    </h5>
                </div>
                <div class="list-group list-group-flush">
                    <a href="{{ route('help.index') }}" class="list-group-item list-group-item-action d-flex align-items-center py-3">
                        <i class="bi bi-journal-text fs-5 text-primary me-3"></i>
                        <div>
                            <div class="fw-semibold">Knowledge Base & FAQ</div>
                            <small class="text-muted">Browse full system help guides</small>
                        </div>
                    </a>
                    <a href="{{ route('forums.index') }}" class="list-group-item list-group-item-action d-flex align-items-center py-3">
                        <i class="bi bi-chat-dots fs-5 text-success me-3"></i>
                        <div>
                            <div class="fw-semibold">Community Forums</div>
                            <small class="text-muted">Discuss topics with students & staff</small>
                        </div>
                    </a>
                    <a href="{{ route('profile.show') }}" class="list-group-item list-group-item-action d-flex align-items-center py-3">
                        <i class="bi bi-person-gear fs-5 text-info me-3"></i>
                        <div>
                            <div class="fw-semibold">Profile Settings</div>
                            <small class="text-muted">Manage account credentials</small>
                        </div>
                    </a>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="card-title mb-0 fw-bold text-dark">
                        <i class="bi bi-info-circle text-primary me-2"></i>Direct University Support
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3 pb-3 border-bottom">
                        <small class="text-muted text-uppercase fw-bold d-block">Official Support Email</small>
                        <a href="mailto:info@jbiuniversity.com" class="text-decoration-none fw-semibold text-primary">
                            <i class="bi bi-envelope me-1"></i>info@jbiuniversity.com
                        </a>
                    </div>
                    <div class="mb-3 pb-3 border-bottom">
                        <small class="text-muted text-uppercase fw-bold d-block">WhatsApp Hotline</small>
                        <span class="fw-semibold text-dark">
                            <i class="bi bi-whatsapp text-success me-1"></i>+27 68 443 8415
                        </span>
                    </div>
                    <div>
                        <small class="text-muted text-uppercase fw-bold d-block">Support Desk Hours</small>
                        <span class="text-muted">
                            <i class="bi bi-clock me-1"></i>Monday &ndash; Friday: 9:00 AM &ndash; 5:00 PM (SAST)
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
