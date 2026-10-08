@extends('layouts.app')

@section('title', 'Academic Calendar')

@section('content')
<div class="container-fluid px-4 py-4">
    @if(Auth::user()->isStudent())
        @include('partials.student-header-bar')
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold text-dark text-uppercase mb-0">
                <i class="bi bi-calendar-week text-primary me-2"></i>ACADEMIC CALENDAR & KEY EVENTS
            </h5>
            <p class="text-muted small mb-0">Academic Year {{ $currentYear?->name ?? '2026/2027' }} Schedule & Exam Dates</p>
        </div>
    </div>

    <div class="row g-4">
        <!-- Upcoming Examinations Card -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3 border-bottom border-danger border-2 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0 text-danger"><i class="bi bi-pencil-square me-2"></i>UPCOMING EXAMINATIONS</h6>
                    <span class="badge bg-danger px-3 py-1">{{ $upcomingExams->count() }} SCHEDULED</span>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush small">
                        @forelse($upcomingExams as $exam)
                            <div class="list-group-item p-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-bold text-primary">{{ $exam->course?->code }} - {{ $exam->title }}</span>
                                    <span class="badge bg-light text-dark border"><i class="bi bi-clock me-1"></i>{{ $exam->duration_minutes ?? 120 }} Mins</span>
                                </div>
                                <div class="text-muted small">
                                    <i class="bi bi-calendar-event me-1"></i>{{ $exam->start_time ? $exam->start_time->format('M d, Y h:i A') : 'TBA' }}
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4 text-muted">No upcoming exams scheduled.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Upcoming Course Assignments Card -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3 border-bottom border-primary border-2 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0 text-primary"><i class="bi bi-journal-text me-2"></i>COURSEWORK & ASSIGNMENTS</h6>
                    <span class="badge bg-primary px-3 py-1">{{ $upcomingAssignments->count() }} DUE</span>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush small">
                        @forelse($upcomingAssignments as $assign)
                            <div class="list-group-item p-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-bold text-dark">{{ $assign->course?->code }} - {{ $assign->title }}</span>
                                    <span class="badge bg-primary bg-opacity-10 text-white border border-primary">{{ $assign->max_score ?? 100 }} Marks</span>
                                </div>
                                <div class="text-muted small">
                                    <i class="bi bi-hourglass-split me-1 text-danger"></i>Due Date: {{ $assign->due_date ? $assign->due_date->format('M d, Y h:i A') : 'TBA' }}
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4 text-muted">No upcoming assignment deadlines.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    <div class="row g-4 mt-1">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 border-bottom border-2">
                    <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-calendar3 me-2"></i>MASTER CALENDAR</h6>
                </div>
                <div class="card-body">
                    <div id="calendar"></div>
                </div>
            </div>
        </div>
    </div>
    </div>
</div>

<!-- Event Details Modal -->
<div class="modal fade" id="eventDetailsModal" tabindex="-1" aria-labelledby="eventDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header text-white" id="eventModalHeader">
                <h5 class="modal-title fw-bold" id="eventDetailsModalLabel"><i class="bi bi-calendar-event me-2"></i>Event Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <h4 id="eventModalTitle" class="fw-bold text-dark mb-3"></h4>
                <div class="mb-3 d-flex align-items-center text-muted small">
                    <i class="bi bi-tag-fill me-2 text-primary"></i>
                    <span id="eventModalType" class="fw-semibold"></span>
                </div>
                <div class="mb-3 d-flex align-items-center text-muted small">
                    <i class="bi bi-clock-fill me-2 text-danger"></i>
                    <span id="eventModalTime"></span>
                </div>
                <div class="p-3 bg-light rounded-3 small text-secondary" id="eventModalDescription">
                    <!-- Description goes here -->
                </div>
            </div>
            <div class="modal-footer border-top-0">
                <button type="button" class="btn btn-secondary btn-sm px-4" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<!-- FullCalendar CSS -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet" />
<style>
    /* Premium tweaks for FullCalendar */
    .fc-theme-standard .fc-scrollgrid {
        border-color: #dee2e6;
        border-radius: 0.5rem;
        overflow: hidden;
    }
    .fc-header-toolbar {
        margin-bottom: 1.5rem !important;
    }
    .fc-toolbar-title {
        font-weight: 700;
        font-size: 1.25rem !important;
    }
    .fc-event {
        cursor: pointer;
        border-radius: 4px;
        padding: 2px 4px;
        font-size: 0.75rem;
        box-shadow: 0 1px 2px rgba(0,0,0,0.1);
        border: none !important;
    }
    .fc-daygrid-day.fc-day-today {
        background-color: rgba(13, 110, 253, 0.05) !important;
    }
</style>
@endpush

@push('scripts')
<!-- FullCalendar JS -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        var eventsData = @json($events);

        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,listMonth'
            },
            themeSystem: 'bootstrap5',
            events: eventsData,
            eventTimeFormat: {
                hour: '2-digit',
                minute: '2-digit',
                meridiem: 'short'
            },
            height: 'auto',
            navLinks: true, // can click day/week names to navigate views
            dayMaxEvents: true, // allow "more" link when too many events
            eventClick: function(info) {
                // Populate Modal with Event Data
                var title = info.event.title;
                var start = info.event.start ? info.event.start.toLocaleString() : '';
                var end = info.event.end ? ' - ' + info.event.end.toLocaleString() : '';
                var type = info.event.extendedProps.type || 'Event';
                var description = info.event.extendedProps.description || 'No additional details provided.';
                var bgColor = info.event.backgroundColor || '#0d6efd';

                document.getElementById('eventModalTitle').innerText = title;
                document.getElementById('eventModalType').innerText = type;
                document.getElementById('eventModalTime').innerText = info.event.allDay ? 'All Day Event' : start + end;
                document.getElementById('eventModalDescription').innerHTML = description;
                document.getElementById('eventModalHeader').style.backgroundColor = bgColor;

                // Show Modal
                var eventModal = new bootstrap.Modal(document.getElementById('eventDetailsModal'));
                eventModal.show();
                
                if(info.event.url) {
                    info.jsEvent.preventDefault();
                }
            }
        });

        calendar.render();
    });
</script>
@endpush
