@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="bi bi-file-earmark-person me-2"></i>
                            Application Details - {{ $application->first_name }} {{ $application->last_name }}
                        </h4>
                        <span class="badge
                            @if($application->status === 'pending') bg-warning
                            @elseif($application->status === 'approved') bg-info
                            @elseif($application->status === 'rejected') bg-danger
                            @elseif($application->status === 'admitted') bg-success
                            @else bg-secondary @endif fs-6">
                            {{ ucfirst($application->status) }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Left Column -->
                        <div class="col-md-8">
                            <!-- Personal Information -->
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0"><i class="bi bi-person me-2"></i>Personal Information</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>Full Name:</strong> {{ $application->first_name }} {{ $application->last_name }}</p>
                                            <p><strong>Email:</strong> {{ $application->email }}</p>
                                            <p><strong>Phone:</strong> {{ $application->phone }}</p>
                                            <p><strong>Date of Birth:</strong> {{ $application->date_of_birth ? $application->date_of_birth->format('M d, Y') : 'Not provided' }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Gender:</strong> {{ ucfirst($application->gender ?? 'Not specified') }}</p>
                                            <p><strong>Address:</strong><br>{{ $application->address }}</p>
                                            <p><strong>Type:</strong> <span class="badge bg-primary">{{ ucfirst($application->type) }}</span></p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Applied Courses / Programme Choices & Live Slots Tracker -->
                            <div class="card mb-4 border-0 shadow-sm overflow-hidden" style="border-radius: 10px;">
                                <div class="card-header bg-white border-bottom border-primary border-2 py-3 d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0 text-primary fw-bold">
                                        <i class="bi bi-journal-bookmark-fill me-2"></i>Applicant's Course & Programme Choices
                                    </h5>
                                    <span class="badge bg-primary px-3 py-2" style="border-radius: 6px;">
                                        {{ isset($programChoices) ? count($programChoices) : 1 }} Choice(s) Submitted
                                    </span>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Preference Rank</th>
                                                    <th>Code</th>
                                                    <th>Programme Name</th>
                                                    <th>Faculty / Department</th>
                                                    <th>Available Slots Tracker</th>
                                                    <th class="text-end">Assign Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if(isset($programChoices) && count($programChoices) > 0)
                                                    @foreach($programChoices as $rankIndex => $choiceProg)
                                                        @php
                                                            // FIX: compare by ID only — mixing ID and name comparisons is fragile
                                                            // (case/whitespace mismatches, or coincidental name collisions).
                                                            $isAssigned = $application->program_id == $choiceProg->id;
                                                            $rankLabel = match($rankIndex) {
                                                                0 => '1st Choice (Primary)',
                                                                1 => '2nd Choice',
                                                                2 => '3rd Choice',
                                                                3 => '4th Choice',
                                                                4 => '5th Choice',
                                                                5 => '6th Choice',
                                                                default => ($rankIndex + 1) . 'th Choice'
                                                            };
                                                        @endphp
                                                        <tr class="{{ $isAssigned ? 'table-success bg-opacity-10' : '' }}">
                                                            <td>
                                                                <span class="badge {{ $rankIndex === 0 ? 'bg-primary' : 'bg-secondary' }}">
                                                                    {{ $rankLabel }}
                                                                </span>
                                                            </td>
                                                            <td><code class="fw-bold bg-light px-2 py-1 rounded text-dark">{{ $choiceProg->code }}</code></td>
                                                            <td>
                                                                <strong class="text-dark">{{ $choiceProg->name }}</strong>
                                                                <div class="small text-muted">{{ $choiceProg->level->name ?? 'N/A' }}</div>
                                                            </td>
                                                            <td>{{ $choiceProg->department->name ?? 'N/A' }}</td>
                                                            <td>
                                                                <div class="d-flex align-items-center">
                                                                    <div class="me-2">
                                                                        <span class="badge {{ $choiceProg->is_full ? 'bg-danger' : 'bg-success' }} px-2 py-1">
                                                                            <i class="bi bi-people-fill me-1"></i>
                                                                            {{ $choiceProg->available_slots }} / {{ $choiceProg->capacity }} Slots Left
                                                                        </span>
                                                                    </div>
                                                                    <small class="text-muted">({{ $choiceProg->enrolled_count }} enrolled)</small>
                                                                </div>
                                                            </td>
                                                            <td class="text-end">
                                                                @if($isAssigned)
                                                                    <span class="badge bg-success px-3 py-1 fs-6">
                                                                        <i class="bi bi-check-circle-fill me-1"></i>ASSIGNED TO APPLICANT
                                                                    </span>
                                                                @else
                                                                    {{-- FIX: no raw string interpolated into onclick/confirm() anymore.
                                                                         Programme name is passed via data-attribute and read safely in JS,
                                                                         so an apostrophe in the name can no longer break out of the
                                                                         inline JS string and kill the handler. --}}
                                                                    <form action="{{ route('admin.applications.update-program', $application->id) }}" method="POST" class="d-inline">
                                                                        @csrf
                                                                        <input type="hidden" name="program_id" value="{{ $choiceProg->id }}">
                                                                        <button type="submit"
                                                                                class="btn btn-sm btn-outline-primary fw-bold assign-btn"
                                                                                data-program-name="{{ $choiceProg->name }}">
                                                                            <i class="bi bi-person-check me-1"></i>Select & Assign
                                                                        </button>
                                                                    </form>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @else
                                                    <tr>
                                                        <td colspan="6" class="text-center text-muted py-3">
                                                            Primary Programme: <strong>{{ $application->programRecord->name ?? $application->program ?? 'Not specified' }}</strong>
                                                        </td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- Admin Change Course Selector Form -->
                                    <div class="p-3 bg-light border-top">
                                        <form action="{{ route('admin.applications.update-program', $application->id) }}" method="POST">
                                            @csrf
                                            <div class="row align-items-center g-2">
                                                <div class="col-md-7">
                                                    <label class="form-label small fw-bold mb-1 text-dark">Assign Different University Programme for Applicant:</label>
                                                    <select name="program_id" class="form-select form-select-sm" required>
                                                        <option value="">Select University Programme...</option>
                                                        @foreach($allPrograms as $prog)
                                                            <option value="{{ $prog->id }}" {{ $application->program_id == $prog->id ? 'selected' : '' }}>
                                                                {{ $prog->name }} ({{ $prog->code }}) &mdash; {{ $prog->available_slots }} slots available
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-5 d-flex align-items-end gap-2 mt-md-4">
                                                    <button type="submit" class="btn btn-sm btn-primary fw-bold px-3" style="border-radius: 6px;">
                                                        <i class="bi bi-save me-1"></i>Update Assigned Programme
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Academic Information -->
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0"><i class="bi bi-mortarboard me-2"></i>Academic Information</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>Assigned Program:</strong> <span class="badge bg-primary fs-6">{{ $application->programRecord->name ?? $application->program ?? 'Not specified' }}</span></p>
                                            <p><strong>Program Level:</strong> {{ $application->programRecord->level->name ?? 'Not specified' }}</p>
                                            <p><strong>Department:</strong> {{ $application->programRecord->department->name ?? 'Not specified' }}</p>
                                            <p><strong>Previous Institution:</strong> {{ $application->previous_school }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            @if($application->admission_number)
                                            <p><strong>Admission Number:</strong> <span class="badge bg-success">{{ $application->admission_number }}</span></p>
                                            @endif
                                            @if($application->student_number)
                                            <p><strong>Student Number:</strong> <span class="badge bg-info">{{ $application->student_number }}</span></p>
                                            @endif
                                            <p><strong>Qualifications:</strong><br>
                                                <small class="text-muted">{{ $application->previous_qualification ?? 'Not provided' }}</small>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Guardian Information (for students) -->
                            @if($application->type === 'student')
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0"><i class="bi bi-people me-2"></i>Guardian Information</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>Guardian Name:</strong> {{ $application->guardian_name }}</p>
                                            <p><strong>Guardian Phone:</strong> {{ $application->guardian_phone }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Guardian Email:</strong> {{ $application->guardian_email ?? 'Not provided' }}</p>
                                            <p><strong>Relationship:</strong> {{ $application->guardian_relationship ?? 'Not specified' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif

                            <!-- Documents -->
                            @if($application->documents && is_array($application->documents) && count($application->documents) > 0)
                            <div class="card mb-3 border-0 shadow-sm">
                                <div class="card-header bg-white border-bottom border-primary border-2 py-3">
                                    <h5 class="mb-0 text-primary fw-bold"><i class="bi bi-file-earmark-text me-2"></i>Submitted Applicant Documents ({{ count($application->documents) }})</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        @foreach($application->documents as $index => $document)
                                            @php
                                                // FIX: guard against $document being an array/object (e.g. metadata)
                                                // instead of a plain path string — pathinfo() on a non-string
                                                // throws a TypeError and 500s the whole page.
                                                $docPath = is_array($document) ? ($document['path'] ?? null) : (is_string($document) ? $document : null);
                                                $fileUrl = $docPath ? asset('storage/' . $docPath) : null;
                                                $ext = $docPath ? strtolower(pathinfo($docPath, PATHINFO_EXTENSION)) : '';
                                                $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                                $isPdf = $ext === 'pdf';
                                            @endphp
                                            @if($docPath)
                                            <div class="col-md-6 col-lg-4">
                                                <div class="card h-100 border p-3 text-center bg-light">
                                                    <div class="mb-2 fs-1 text-primary">
                                                        @if($isImage)
                                                            <i class="bi bi-file-earmark-image"></i>
                                                        @elseif($isPdf)
                                                            <i class="bi bi-file-earmark-pdf text-danger"></i>
                                                        @else
                                                            <i class="bi bi-file-earmark-word text-info"></i>
                                                        @endif
                                                    </div>
                                                    <h6 class="fw-bold mb-1 text-truncate">Document {{ $index + 1 }}</h6>
                                                    <small class="text-muted d-block mb-3 text-uppercase">{{ $ext ?: 'FILE' }} Document</small>

                                                    <div class="d-flex justify-content-center gap-2">
                                                        <button type="button" class="btn btn-sm btn-primary preview-doc-btn"
                                                                data-url="{{ $fileUrl }}"
                                                                data-title="Document {{ $index + 1 }}"
                                                                data-ext="{{ $ext }}">
                                                            <i class="bi bi-eye me-1"></i>Preview
                                                        </button>
                                                        <a href="{{ $fileUrl }}" target="_blank" download class="btn btn-sm btn-outline-secondary">
                                                            <i class="bi bi-download"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            @endif

                            <style>
                                /* FIX: replaced Bootstrap's JS-driven modal (bootstrap.Modal) with a plain
                                   fixed-position overlay for the document preview. Bootstrap's modal engine
                                   runs scrollbar-width measurement and dialog re-adjustment logic
                                   (_adjustDialog) on resize/focus events, which — combined with the earlier
                                   !important transition/transform overrides — caused the dialog to repeatedly
                                   fight between its default top position and modal-dialog-centered's flex
                                   centering, producing the top-then-middle flicker whenever focus or layout
                                   changed near the modal (e.g. mouse movement over the iframe/image). A
                                   custom overlay has no such lifecycle, so there's nothing left to flicker. */
                                #adminDocPreviewBackdrop {
                                    position: fixed;
                                    inset: 0;
                                    background: rgba(0, 0, 0, 0.5);
                                    z-index: 1055;
                                    display: none;
                                }
                                #adminDocPreviewBackdrop.show { display: block; }

                                #adminDocPreviewModal {
                                    position: fixed;
                                    inset: 0;
                                    z-index: 1060;
                                    display: none;
                                    align-items: center;
                                    justify-content: center;
                                    padding: 1.75rem 1rem;
                                }
                                #adminDocPreviewModal.show { display: flex; }

                                #adminDocPreviewModal .modal-dialog {
                                    width: 100%;
                                    max-width: 1100px;
                                    margin: 0;
                                }
                            </style>

                            <!-- Document Preview Overlay (custom — no Bootstrap Modal JS, no reflow/flicker) -->
                            <div id="adminDocPreviewBackdrop"></div>
                            <div id="adminDocPreviewModal" tabindex="-1" aria-hidden="true" role="dialog" aria-modal="true">
                                <div class="modal-dialog modal-xl">
                                    <div class="modal-content border-0 shadow-lg overflow-hidden" style="border-radius: 10px;">
                                        <div class="modal-header text-white py-3 px-4" style="background: linear-gradient(135deg, #0f2942 0%, #1e3a8a 100%);">
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-file-earmark-pdf fs-4 me-2 text-warning"></i>
                                                <div>
                                                    <h5 class="modal-title fw-bold mb-0 text-white" id="adminDocPreviewTitle">Document Preview</h5>
                                                    <small class="text-white-50" id="adminDocPreviewSubtitle">Official Applicant Document</small>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center gap-2">
                                                <a id="adminDocOpenNewTabBtn" href="#" target="_blank" class="btn btn-sm btn-outline-light px-3 py-1.5" style="border-radius: 6px;">
                                                    <i class="bi bi-box-arrow-up-right me-1"></i>Open Full Screen
                                                </a>
                                                <a id="adminDocDownloadHeaderBtn" href="#" download class="btn btn-sm btn-warning fw-bold px-3 py-1.5 text-dark" style="border-radius: 6px;">
                                                    <i class="bi bi-download me-1"></i>Download
                                                </a>
                                                <button type="button" class="btn-close btn-close-white ms-2" id="adminDocPreviewCloseBtn" aria-label="Close"></button>
                                            </div>
                                        </div>
                                        <div class="modal-body p-0 bg-light position-relative" style="height: 75vh; min-height: 580px; overflow: hidden; background-color: #f8fafc !important;">
                                            <div id="adminDocPreviewContainer" class="w-100 h-100 position-relative" style="overflow: hidden; background: #f8fafc;">
                                                <div class="position-absolute top-50 start-50 text-primary fw-bold">Loading document preview...</div>
                                            </div>
                                        </div>
                                        <div class="modal-footer bg-white py-2.5 px-4 d-flex justify-content-between align-items-center">
                                            <small class="text-muted"><i class="bi bi-shield-check text-success me-1"></i>Verified Applicant Document Record</small>
                                            <button type="button" class="btn btn-secondary px-4 fw-bold" id="adminDocPreviewCloseBtn2" style="border-radius: 6px;">Close Preview</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <script>
                                // FIX: custom overlay show/hide — no bootstrap.Modal instance, no internal
                                // resize/focus-driven dialog re-adjustment, so nothing recalculates position
                                // on hover/focus and the top-then-middle flicker cannot occur.
                                function resetAdminDocPreviewContainer() {
                                    const container = document.getElementById('adminDocPreviewContainer');
                                    if (container) {
                                        container.innerHTML = '<div class="position-absolute top-50 start-50 text-primary fw-bold">Loading document preview...</div>';
                                    }
                                }

                                function closeAdminDocPreview() {
                                    const modalEl = document.getElementById('adminDocPreviewModal');
                                    const backdropEl = document.getElementById('adminDocPreviewBackdrop');
                                    if (!modalEl || !modalEl.classList.contains('show')) {
                                        return;
                                    }
                                    modalEl.classList.remove('show');
                                    backdropEl.classList.remove('show');
                                    document.body.style.overflow = '';
                                    resetAdminDocPreviewContainer();
                                }

                                function openAdminDocPreview(url, title, ext) {
                                    // FIX: guard against a missing/blank URL (e.g. a malformed document entry)
                                    // instead of handing an empty src to <img>/<iframe>.
                                    if (!url) {
                                        return;
                                    }

                                    document.getElementById('adminDocPreviewTitle').innerText = title || 'Document Preview';
                                    document.getElementById('adminDocPreviewSubtitle').innerText = 'Format: ' + (ext || 'Document').toUpperCase();

                                    document.getElementById('adminDocOpenNewTabBtn').href = url;
                                    document.getElementById('adminDocDownloadHeaderBtn').href = url;

                                    const container = document.getElementById('adminDocPreviewContainer');
                                    const cleanExt = (ext || '').toLowerCase().trim();

                                    if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(cleanExt)) {
                                        const img = document.createElement('img');
                                        img.src = url;
                                        img.className = 'img-fluid rounded shadow';
                                        img.style.maxHeight = '100%';
                                        img.style.maxWidth = '100%';
                                        img.style.objectFit = 'contain';

                                        const wrap = document.createElement('div');
                                        wrap.className = 'w-100 h-100 d-flex align-items-center justify-content-center p-3';
                                        wrap.style.background = '#0f172a';
                                        wrap.appendChild(img);

                                        container.innerHTML = '';
                                        container.appendChild(wrap);
                                    } else if (cleanExt === 'pdf') {
                                        const iframe = document.createElement('iframe');
                                        iframe.src = url + '#toolbar=1&navpanes=0&scrollbar=1';
                                        iframe.className = 'w-100 h-100';
                                        iframe.style.minHeight = '580px';
                                        iframe.style.border = 'none';
                                        iframe.style.display = 'block';
                                        iframe.style.width = '100%';
                                        iframe.style.height = '100%';
                                        iframe.style.background = '#ffffff';

                                        container.innerHTML = '';
                                        container.appendChild(iframe);
                                    } else {
                                        container.innerHTML = '<div class="w-100 h-100 d-flex flex-column align-items-center justify-content-center p-5 text-center" style="background: #f8fafc;"><i class="bi bi-file-earmark-word fs-1 text-primary d-block mb-3"></i><h5 class="text-dark fw-bold mb-2">Preview Not Available Inline</h5><p class="text-muted fs-6 mb-4">Direct inline preview is not supported for <strong>' + cleanExt.toUpperCase() + '</strong> files.</p><div class="d-flex gap-2"><a href="' + url + '" target="_blank" class="btn btn-outline-primary px-4 py-2 fw-bold" style="border-radius: 6px;"><i class="bi bi-box-arrow-up-right me-2"></i>Open File</a><a href="' + url + '" download class="btn btn-primary px-4 py-2 fw-bold" style="border-radius: 6px;"><i class="bi bi-download me-2"></i>Download File</a></div></div>';
                                    }

                                    const modalEl = document.getElementById('adminDocPreviewModal');
                                    const backdropEl = document.getElementById('adminDocPreviewBackdrop');

                                    // FIX: guard against double-open (double-click) — no-op if already shown,
                                    // instead of re-triggering any show logic.
                                    if (modalEl.classList.contains('show')) {
                                        return;
                                    }
                                    modalEl.classList.add('show');
                                    backdropEl.classList.add('show');
                                    document.body.style.overflow = 'hidden';
                                }

                                document.addEventListener('DOMContentLoaded', function() {
                                    // FIX: "portal" the modal + backdrop to be direct children of <body>.
                                    // Root cause of the centering/flicker bug: position:fixed is anchored
                                    // to the viewport ONLY if no ancestor has a transform/filter/
                                    // perspective/will-change applied. This theme's sidebar applies a
                                    // transform to a wrapping container on hover (the "sidebar-mini
                                    // hover-expand" effect), which was silently turning our fixed modal
                                    // into something anchored to that wrapper instead of the viewport —
                                    // so it jumped position every time the sidebar hover state toggled.
                                    // Moving these two elements to <body> removes them from that
                                    // container entirely, so their fixed positioning is always relative
                                    // to the real viewport regardless of sidebar hover state.
                                    var previewModalEl = document.getElementById('adminDocPreviewModal');
                                    var previewBackdropEl = document.getElementById('adminDocPreviewBackdrop');
                                    if (previewModalEl && previewModalEl.parentElement !== document.body) {
                                        document.body.appendChild(previewBackdropEl);
                                        document.body.appendChild(previewModalEl);
                                    }

                                    // FIX: delegated, injection-safe handlers for "Select & Assign" and
                                    // "Preview" buttons — replaces string-interpolated onclick="confirm(...)"
                                    // calls that broke when a name/title contained an apostrophe or quote.
                                    document.querySelectorAll('.assign-btn').forEach(function (btn) {
                                        btn.addEventListener('click', function (e) {
                                            const name = this.dataset.programName || 'this programme';
                                            if (!confirm('Assign ' + name + ' to this applicant?')) {
                                                e.preventDefault();
                                            }
                                        });
                                    });

                                    document.querySelectorAll('.preview-doc-btn').forEach(function (btn) {
                                        btn.addEventListener('click', function () {
                                            openAdminDocPreview(this.dataset.url, this.dataset.title, this.dataset.ext);
                                        });
                                    });

                                    // Close handlers: header X button, footer Close button, backdrop click,
                                    // and Escape key. None of these touch layout/position, so no flicker path.
                                    const closeBtn1 = document.getElementById('adminDocPreviewCloseBtn');
                                    const closeBtn2 = document.getElementById('adminDocPreviewCloseBtn2');
                                    const backdropEl = document.getElementById('adminDocPreviewBackdrop');
                                    if (closeBtn1) closeBtn1.addEventListener('click', closeAdminDocPreview);
                                    if (closeBtn2) closeBtn2.addEventListener('click', closeAdminDocPreview);
                                    if (backdropEl) backdropEl.addEventListener('click', closeAdminDocPreview);

                                    document.addEventListener('keydown', function (e) {
                                        if (e.key === 'Escape') {
                                            closeAdminDocPreview();
                                        }
                                    });
                                });
                            </script>

                            <!-- Payment Information -->
                            @if($application->payment_proof || $application->payment_status || $application->status === 'admitted')
                            <div class="card mb-3 border-0 shadow-sm">
                                <div class="card-header bg-white border-bottom border-primary border-2 py-3">
                                    <h5 class="mb-0 text-primary fw-bold"><i class="bi bi-credit-card me-2"></i>Admission Fee Payment Information</h5>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                                        <div>
                                            <strong>Payment Status:</strong>
                                            @if($application->payment_status === 'verified' || $application->payment_verified_at)
                                                <span class="badge bg-success ms-1"><i class="bi bi-check-circle me-1"></i>Payment Verified</span>
                                            @elseif($application->payment_proof || $application->payment_status === 'uploaded')
                                                <span class="badge bg-info ms-1"><i class="bi bi-file-earmark-check me-1"></i>Proof Uploaded - Awaiting Confirmation</span>
                                            @else
                                                <span class="badge bg-warning text-dark ms-1"><i class="bi bi-clock me-1"></i>Payment Pending</span>
                                            @endif
                                        </div>
                                        @if($application->payment_ref)
                                            <div class="small text-muted"><strong>Ref:</strong> <code>{{ $application->payment_ref }}</code></div>
                                        @endif
                                    </div>

                                    @if($application->payment_proof)
                                        @php
                                            $proofUrl = asset('storage/' . $application->payment_proof);
                                            $proofExt = strtolower(pathinfo($application->payment_proof, PATHINFO_EXTENSION));
                                        @endphp
                                        <div class="d-flex align-items-center gap-2 mb-2">
                                            <button type="button" class="btn btn-primary btn-sm preview-doc-btn"
                                                    data-url="{{ $proofUrl }}"
                                                    data-title="Admission Fee Payment Proof"
                                                    data-ext="{{ $proofExt }}">
                                                <i class="bi bi-eye me-1"></i>Preview Payment Receipt
                                            </button>
                                            <a href="{{ $proofUrl }}" target="_blank" class="btn btn-outline-secondary btn-sm">
                                                <i class="bi bi-download me-1"></i>Download
                                            </a>
                                        </div>
                                        @if($application->payment_verified_at)
                                            <div class="small text-success mt-2">
                                                <i class="bi bi-check2-all me-1"></i>Verified on {{ $application->payment_verified_at->format('M d, Y H:i') }}
                                                @if($application->verifier)
                                                    by {{ $application->verifier->name }}
                                                @endif
                                            </div>
                                        @endif
                                    @else
                                        <p class="text-muted small mb-0">Payment receipt has not been submitted by applicant yet.</p>
                                    @endif
                                </div>
                            </div>
                            @endif

                            <!-- Notes -->
                            @if($application->review_notes)
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0"><i class="bi bi-sticky me-2"></i>Reviewer Notes</h5>
                                </div>
                                <div class="card-body">
                                    <p class="mb-0">{{ $application->review_notes }}</p>
                                </div>
                            </div>
                            @endif
                        </div>

                        <!-- Right Column - Actions -->
                        <div class="col-md-4">
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Application Details</h5>
                                </div>
                                <div class="card-body">
                                    <p><strong>Application ID:</strong> {{ $application->id }}</p>
                                    <p><strong>Submitted:</strong> {{ $application->created_at->format('M d, Y H:i') }}</p>
                                    <p><strong>Last Updated:</strong> {{ $application->updated_at->format('M d, Y H:i') }}</p>
                                    @if($application->reviewed_at)
                                    <p><strong>Reviewed:</strong> {{ $application->reviewed_at->format('M d, Y H:i') }}</p>
                                    @endif
                                </div>
                            </div>

                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0"><i class="bi bi-check2-square me-2"></i>Approval Readiness</h5>
                                </div>
                                <div class="card-body">
                                    @php
                                        $readiness = $approvalReadiness ?? ['score' => 0, 'ready' => false, 'checks' => []];
                                        $readinessColor = $readiness['ready'] ? 'success' : ($readiness['score'] >= 60 ? 'warning' : 'danger');
                                    @endphp
                                    <div class="mb-2">
                                        <span class="badge bg-{{ $readinessColor }}">{{ $readiness['score'] }}%</span>
                                    </div>
                                    <ul class="list-group list-group-flush">
                                        @foreach(($readiness['weighted_checks'] ?? []) as $check)
                                            <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                                <span>{{ $check['label'] }}</span>
                                                @if($check['passed'])
                                                    <span class="badge bg-success">OK</span>
                                                @else
                                                    <span class="badge bg-danger">Missing ({{ $check['weight'] }}%)</span>
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>

                            <!-- Actions -->
                            @if(!in_array($application->status, ['admitted', 'rejected']))
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0"><i class="bi bi-gear me-2"></i>Admission Review & Actions</h5>
                                </div>
                                <div class="card-body">
                                    {{-- 1. Verify Payment Form (if proof is uploaded but not yet verified) --}}
                                    @if($application->payment_proof && !$application->payment_verified_at)
                                    <form action="{{ route('admin.applications.verify-payment', $application->id) }}" method="POST" class="mb-3 pb-3 border-bottom">
                                        @csrf
                                        <label class="form-label fw-bold text-primary small">Step 1: Confirm Payment</label>
                                        <button type="submit" class="btn btn-info text-white w-100 confirm-submit" data-confirm="Confirm and verify applicant payment proof?">
                                            <i class="bi bi-check2-square me-1"></i>Verify & Confirm Payment
                                        </button>
                                    </form>
                                    @endif

                                    {{-- 2. Approve & Admit Student Form --}}
                                    <form action="{{ route('admin.applications.approve', $application->id) }}" method="POST" class="mb-3">
                                        @csrf
                                        <div class="mb-2">
                                            <label for="approval_notes" class="form-label fw-bold small text-success">
                                                {{ $application->payment_verified_at ? 'Final Step: Approve & Admit Student' : 'Approve & Admit Student' }}
                                            </label>
                                            <textarea name="notes" id="approval_notes" class="form-control form-control-sm" rows="2" placeholder="Approval notes (optional)"></textarea>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" name="force_approve" id="force_approve" value="1">
                                            <label class="form-check-label small text-muted" for="force_approve">
                                                Force approval if checklist is incomplete
                                            </label>
                                        </div>
                                        <button type="submit" class="btn btn-success w-100 confirm-submit" data-confirm="Approve and officially admit this student? Official Student Number will be issued.">
                                            <i class="bi bi-person-check-fill me-1"></i>Approve & Admit Student
                                        </button>
                                        <div class="form-text small mt-1">
                                            Issues official Student Number, activates portal account, and sends Admission Letter.
                                        </div>
                                    </form>

                                    {{-- 3. Reject Application Form --}}
                                    <form action="{{ route('admin.applications.reject', $application->id) }}" method="POST">
                                        @csrf
                                        <div class="mb-2">
                                            <label for="rejection_notes" class="form-label fw-bold small text-danger">Reject Application <span class="text-danger">*</span></label>
                                            <textarea name="notes" id="rejection_notes" class="form-control form-control-sm" rows="2" placeholder="Provide rejection reason..." required></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-outline-danger w-100 confirm-submit" data-confirm="Reject this application?">
                                            <i class="bi bi-x-circle me-1"></i>Reject Application
                                        </button>
                                    </form>
                                </div>
                            </div>
                            @else
                            <div class="card mb-3">
                                <div class="card-body text-center">
                                    @if($application->status === 'admitted')
                                        <span class="badge bg-success fs-6 py-2 px-3 mb-2 d-inline-block">
                                            <i class="bi bi-check-circle me-1"></i>Officially Admitted
                                        </span>
                                        <p class="small text-muted mb-0">Student Number: <strong>{{ $application->student_number }}</strong></p>
                                    @elseif($application->status === 'rejected')
                                        <span class="badge bg-danger fs-6 py-2 px-3 mb-2 d-inline-block">
                                            <i class="bi bi-x-circle me-1"></i>Application Rejected
                                        </span>
                                    @endif
                                </div>
                            </div>
                            @endif

                            <div class="card">
                                <div class="card-body">
                                    <a href="{{ route('admin.applications.index') }}" class="btn btn-outline-secondary w-100">
                                        <i class="bi bi-arrow-left me-1"></i>Back to Applications
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // FIX: these confirm() calls had no interpolated variables so they were not exploitable,
    // but moved to delegated listeners for consistency with the fixes above and so future
    // edits (e.g. adding the applicant's name to the message) stay injection-safe.
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.confirm-submit').forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                if (!confirm(this.dataset.confirm || 'Are you sure?')) {
                    e.preventDefault();
                }
            });
        });
    });
</script>
@endsection