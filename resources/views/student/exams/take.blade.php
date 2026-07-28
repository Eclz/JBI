@extends('layouts.app')

@section('title', 'Take Exam - ' . $exam->title)

@section('content')
<div class="container-fluid py-4">
    <!-- PERMANENT TIMER CARD - Always Visible at Top -->
    <div class="card shadow-lg mb-4" style="position: sticky; top: 60px; z-index: 1040; border-left: 5px solid #ffc107;">
        <div class="card-body p-3">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-clock fa-2x text-warning me-3"></i>
                        <div>
                            <h5 class="mb-0">{{ $exam->title }}</h5>
                            <small class="text-muted">{{ $exam->course->name }}</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 text-end">
                    <div class="d-flex align-items-center justify-content-end">
                        <div class="me-3">
                            <small class="text-muted d-block">Time Remaining</small>
                            <h3 class="mb-0" id="timer">{{ gmdate('H:i:s', $attempt->time_remaining_seconds ?? ($exam->duration_minutes * 60)) }}</h3>
                        </div>
                        <div style="width: 80px;">
                            <div class="progress" style="height: 60px; width: 60px; border-radius: 50%; position: relative;">
                                <svg width="60" height="60" style="transform: rotate(-90deg);">
                                    <circle cx="30" cy="30" r="25" stroke="#e9ecef" stroke-width="8" fill="none"></circle>
                                    <circle id="timerCircle" cx="30" cy="30" r="25" stroke="#ffc107" stroke-width="8" fill="none"
                                            stroke-dasharray="157" stroke-dashoffset="0" stroke-linecap="round"></circle>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="progress mt-2" style="height: 4px;">
                <div id="timerProgress" class="progress-bar bg-warning" style="width: 100%"></div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Exam Questions & Answers</h4>
                </div>
                <div class="card-body">
                    <!-- Exam Instructions -->
                    <div class="mb-4">
                        <h5>Instructions:</h5>
                        <div class="bg-light p-3 rounded">
                            {!! nl2br(e($exam->instructions)) !!}
                        </div>
                    </div>

                    @if($exam->description)
                    <div class="mb-4">
                        <h5>Description:</h5>
                        <div class="bg-light p-3 rounded">
                            {!! nl2br(e($exam->description)) !!}
                        </div>
                    </div>
                    @endif

                    <!-- Exam Paper Download -->
                    @if($exam->exam_paper_url)
                    <div class="mb-4">
                        <a href="{{ route('student.exams.download-paper', $exam) }}" class="btn btn-primary" target="_blank">
                            <i class="fas fa-download me-2"></i>Download Exam Paper
                        </a>
                    </div>
                    @endif

                    <!-- Answer Booklet Download (offline/hybrid only) -->
                    @if(in_array($exam->exam_mode ?? 'online', ['offline', 'hybrid'], true) && $exam->answer_booklet_url)
                    <div class="mb-4">
                        <a href="{{ route('student.exams.download-booklet', $exam) }}" class="btn btn-secondary" target="_blank">
                            <i class="fas fa-download me-2"></i>Download Answer Booklet
                        </a>
                    </div>
                    @endif

                    <!-- Submission Form -->
                    <form action="{{ route('student.exams.submit', $exam) }}" method="POST" enctype="multipart/form-data" id="examForm">
                        @csrf

                        <!-- Online Answers (if applicable) -->
                        <div class="mb-4">
                            <label for="answers" class="form-label fw-bold">Your Answers:</label>
                            <div class="border rounded">
                                <!-- Enhanced toolbar with table and image functionality -->
                                <div class="d-flex flex-wrap gap-2 p-2 border-bottom bg-light" style="position: sticky; top: 150px; z-index: 100;">
                                    <!-- Text Formatting -->
                                    <button type="button" class="btn btn-sm btn-outline-secondary" data-command="bold" title="Bold">
                                        <i class="fa-solid fa-bold"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" data-command="italic" title="Italic">
                                        <i class="fa-solid fa-italic"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" data-command="underline" title="Underline">
                                        <i class="fa-solid fa-underline"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" data-command="strikeThrough" title="Strikethrough">
                                        <i class="fa-solid fa-strikethrough"></i>
                                    </button>

                                    <div class="vr"></div>

                                    <!-- Alignment -->
                                    <button type="button" class="btn btn-sm btn-outline-secondary" data-command="justifyLeft" title="Align Left">
                                        <i class="fa-solid fa-align-left"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" data-command="justifyCenter" title="Align Center">
                                        <i class="fa-solid fa-align-center"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" data-command="justifyRight" title="Align Right">
                                        <i class="fa-solid fa-align-right"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" data-command="justifyFull" title="Justify">
                                        <i class="fa-solid fa-align-justify"></i>
                                    </button>

                                    <div class="vr"></div>

                                    <!-- Lists -->
                                    <button type="button" class="btn btn-sm btn-outline-secondary" data-command="insertUnorderedList" title="Bullet List">
                                        <i class="fa-solid fa-list-ul"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" data-command="insertOrderedList" title="Numbered List">
                                        <i class="fa-solid fa-list-ol"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" data-command="indent" title="Indent">
                                        <i class="fa-solid fa-indent"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" data-command="outdent" title="Outdent">
                                        <i class="fa-solid fa-outdent"></i>
                                    </button>

                                    <div class="vr"></div>

                                    <!-- Font Size -->
                                    <select class="form-select form-select-sm" style="width: auto;" data-command="fontSize" title="Font Size">
                                        <option value="">Size</option>
                                        <option value="1">Small</option>
                                        <option value="3">Normal</option>
                                        <option value="5">Large</option>
                                        <option value="7">Huge</option>
                                    </select>

                                    <!-- Text Color -->
                                    <input type="color" class="form-control form-control-sm" style="width: 50px; height: 31px;" data-command="foreColor" title="Text Color">

                                    <!-- Background Color -->
                                    <input type="color" class="form-control form-control-sm" style="width: 50px; height: 31px;" data-command="hiliteColor" title="Highlight">

                                    <div class="vr"></div>

                                    <!-- Added table and image buttons -->
                                    <!-- Table -->
                                    <button type="button" class="btn btn-sm btn-outline-secondary" id="insertTableBtn" title="Insert Table">
                                        <i class="fa-solid fa-table"></i>
                                    </button>

                                    <!-- Image -->
                                    <button type="button" class="btn btn-sm btn-outline-secondary" id="insertImageBtn" title="Insert Image">
                                        <i class="fa-solid fa-image"></i>
                                    </button>
                                    <input type="file" id="imageInput" accept="image/*" style="display: none;">

                                    <div class="vr"></div>

                                    <!-- Insert -->
                                    <button type="button" class="btn btn-sm btn-outline-secondary" data-command="createLink" title="Insert Link">
                                        <i class="fa-solid fa-link"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" data-command="insertHorizontalRule" title="Insert Line">
                                        <i class="fa-solid fa-minus"></i>
                                    </button>

                                    <div class="vr"></div>

                                    <!-- Undo/Redo -->
                                    <button type="button" class="btn btn-sm btn-outline-secondary" data-command="undo" title="Undo">
                                        <i class="fa-solid fa-arrow-rotate-left"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" data-command="redo" title="Redo">
                                        <i class="fa-solid fa-arrow-rotate-right"></i>
                                    </button>

                                    <div class="vr"></div>

                                    <!-- Clear -->
                                    <button type="button" class="btn btn-sm btn-outline-danger" data-command="removeFormat" title="Clear Formatting">
                                        <i class="fa-solid fa-eraser"></i>
                                    </button>
                                </div>
                                <div
                                    id="answers_editor"
                                    class="p-3"
                                    style="min-height: 400px; max-height: 800px; overflow-y: auto;"
                                    contenteditable="true"
                                    aria-label="Exam answers editor"></div>
                            </div>
                            <textarea
                                class="form-control @error('answers') is-invalid @enderror d-none"
                                id="answers"
                                name="answers"
                                rows="10">{{ old('answers', $attempt->answers) }}</textarea>
                            @error('answers')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <small class="text-muted">Use the toolbar to format text, insert tables, and add images to your answers.</small>
                                <small class="text-muted" id="lastSaved">Last saved: just now</small>
                            </div>
                        </div>

                        <!-- File Upload (offline/hybrid only) -->
                        @if(in_array($exam->exam_mode ?? 'online', ['offline', 'hybrid'], true))
                        <div class="mb-4">
                            <label for="submission_file" class="form-label fw-bold">Upload Answer File (Optional):</label>
                            <input
                                type="file"
                                class="form-control @error('submission_file') is-invalid @enderror"
                                id="submission_file"
                                name="submission_file"
                                accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                            @error('submission_file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Accepted formats: PDF, DOC, DOCX, JPG, PNG (Max: 10MB)</small>

                            @if($attempt->submission_file_url)
                            <div class="mt-2">
                                <small class="text-success">
                                    <i class="fas fa-check-circle me-1"></i>File already uploaded
                                </small>
                            </div>
                            @endif
                        </div>
                        @endif

                        <!-- Submit Button -->
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success btn-lg" id="submitBtn">
                                <i class="fas fa-paper-plane me-2"></i>Submit Exam
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-lg" onclick="saveDraft()">
                                <i class="fas fa-save me-2"></i>Save Draft
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Exam Details</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <strong>Course:</strong><br>
                            {{ $exam->course->name }}
                        </li>
                        <li class="mb-2">
                            <strong>Total Marks:</strong><br>
                            {{ $exam->total_marks }}
                        </li>
                        <li class="mb-2">
                            <strong>Duration:</strong><br>
                            {{ $exam->duration_minutes }} minutes
                        </li>
                        <li class="mb-2">
                            <strong>Status:</strong><br>
                            <span class="badge bg-warning">In Progress</span>
                        </li>
                        <li class="mb-0">
                            <strong>Started At:</strong><br>
                            {{ $attempt->started_at->format('M d, Y h:i A') }}
                        </li>
                    </ul>
                </div>
            </div>

            <div class="card shadow-sm border-warning">
                <div class="card-body">
                    <h6 class="text-warning"><i class="fas fa-exclamation-triangle me-2"></i>Important Notes:</h6>
                    <ul class="small mb-0">
                        <li>Make sure to submit before time runs out</li>
                        <li>Save your work regularly using the "Save Draft" button</li>
                        <li>Once submitted, you cannot make changes</li>
                        <li>If you face any issues, contact your instructor immediately</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Added Table Modal -->
<div class="modal fade" id="tableModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Insert Table</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="tableRows" class="form-label">Rows:</label>
                    <input type="number" class="form-control" id="tableRows" value="3" min="1" max="20">
                </div>
                <div class="mb-3">
                    <label for="tableCols" class="form-label">Columns:</label>
                    <input type="number" class="form-control" id="tableCols" value="3" min="1" max="10">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="insertTableConfirm">Insert Table</button>
            </div>
        </div>
    </div>
</div>

<script>
    // Timer countdown
    const startedAt = {{ $attempt->started_at ? $attempt->started_at->timestamp : 'null' }};
    const durationSeconds = {{ $exam->duration_minutes * 60 }};
    const serverNow = {{ now()->timestamp }};
    const fallbackRemaining = {{ $attempt->time_remaining_seconds ?? ($exam->duration_minutes * 60) }};
    const endTimeEpoch = startedAt
        ? startedAt + durationSeconds
        : Math.floor(Date.now() / 1000) + fallbackRemaining;
    let timeRemaining = Math.max(endTimeEpoch - serverNow, 0);
    const timerElement = document.getElementById('timer');
    const timerCircle = document.getElementById('timerCircle');
    const submitBtn = document.getElementById('submitBtn');
    const examForm = document.getElementById('examForm');
    const editor = document.getElementById('answers_editor');
    const answersField = document.getElementById('answers');
    const lastSavedLabel = document.getElementById('lastSaved');
    const timerProgress = document.getElementById('timerProgress');
    const autosaveUrl = "{{ route('student.exams.autosave', $exam) }}";
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    let hasSubmitted = false;
    let lastAutosavePayload = '';
    let autosaveInFlight = false;
    const totalDuration = Math.max(durationSeconds, 1);
    const circumference = 2 * Math.PI * 25; // radius is 25

    function formatClock(value) {
        const hours = Math.floor(value / 3600);
        const minutes = Math.floor((value % 3600) / 60);
        const seconds = value % 60;
        return (
            String(hours).padStart(2, '0') + ':' +
            String(minutes).padStart(2, '0') + ':' +
            String(seconds).padStart(2, '0')
        );
    }

    function setLastSaved(timestamp) {
        const time = timestamp || new Date();
        const label = time.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        lastSavedLabel.textContent = 'Last saved: ' + label;
    }

    function updateTimer() {
        timeRemaining = Math.max(endTimeEpoch - Math.floor(Date.now() / 1000), 0);
        timerElement.textContent = formatClock(timeRemaining);

        // Update progress bar
        const progressPercent = Math.max(Math.min((timeRemaining / totalDuration) * 100, 100), 0);
        timerProgress.style.width = progressPercent + '%';

        // Update circular progress
        const offset = circumference - (progressPercent / 100) * circumference;
        timerCircle.style.strokeDashoffset = offset;

        // Change colors based on time remaining
        if (timeRemaining <= 300) { // Last 5 minutes - RED
            timerElement.style.color = '#dc3545';
            timerCircle.setAttribute('stroke', '#dc3545');
            timerProgress.classList.remove('bg-warning');
            timerProgress.classList.add('bg-danger');
        } else if (timeRemaining <= 600) { // Last 10 minutes - YELLOW
            timerElement.style.color = '#ffc107';
            timerCircle.setAttribute('stroke', '#ffc107');
            timerProgress.classList.remove('bg-danger');
            timerProgress.classList.add('bg-warning');
        } else { // Default - ORANGE/YELLOW border, no specific color for timer text
            timerElement.style.color = ''; // Reset to default text color
            timerCircle.setAttribute('stroke', '#ffc107'); // Default border color
            timerProgress.classList.remove('bg-danger');
            timerProgress.classList.add('bg-warning');
        }

        if (timeRemaining <= 0) {
            if (!hasSubmitted) {
                autosaveToServer('timeup').finally(function() {
                    hasSubmitted = true;
                    syncEditor();
                    alert('Time is up! Your exam will be automatically submitted.');
                    examForm.submit();
                });
            }
        }
    }

    // Update timer every second - NEVER stop updating
    updateTimer();
    setInterval(updateTimer, 1000);

    // Auto-save draft every 2 minutes
    setInterval(saveDraft, 120000);

    function saveDraft() {
        syncEditor();
        const answers = answersField.value;
        localStorage.setItem('exam_draft_{{ $exam->id }}', answers);
        setLastSaved(new Date());
        autosaveToServer('interval');
    }

    window.addEventListener('DOMContentLoaded', function() {
        const draft = localStorage.getItem('exam_draft_{{ $exam->id }}');
        const initial = answersField.value;
        const content = draft || initial || '';
        editor.innerHTML = content;
        syncEditor();
        setLastSaved(new Date());
    });

    function syncEditor() {
        answersField.value = editor.innerHTML;
    }

    editor.addEventListener('input', syncEditor);

    const insertTableBtn = document.getElementById('insertTableBtn');
    const tableModal = new bootstrap.Modal(document.getElementById('tableModal'));
    const insertTableConfirm = document.getElementById('insertTableConfirm');

    insertTableBtn.addEventListener('click', function() {
        tableModal.show();
    });

    insertTableConfirm.addEventListener('click', function() {
        const rows = parseInt(document.getElementById('tableRows').value);
        const cols = parseInt(document.getElementById('tableCols').value);

        let tableHTML = '<table border="1" cellpadding="8" cellspacing="0" style="border-collapse: collapse; width: 100%; margin: 10px 0;">';
        for (let i = 0; i < rows; i++) {
            tableHTML += '<tr>';
            for (let j = 0; j < cols; j++) {
                tableHTML += '<td style="border: 1px solid #ddd; padding: 8px;">&nbsp;</td>';
            }
            tableHTML += '</tr>';
        }
        tableHTML += '</table>';

        document.execCommand('insertHTML', false, tableHTML);
        editor.focus();
        syncEditor();
        tableModal.hide();
    });

    const insertImageBtn = document.getElementById('insertImageBtn');
    const imageInput = document.getElementById('imageInput');

    insertImageBtn.addEventListener('click', function() {
        imageInput.click();
    });

    imageInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file && file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(event) {
                const img = '<img src="' + event.target.result + '" style="max-width: 100%; height: auto; margin: 10px 0;" />';
                document.execCommand('insertHTML', false, img);
                editor.focus();
                syncEditor();
            };
            reader.readAsDataURL(file);
        }
        imageInput.value = ''; // Reset input
    });

    // Existing toolbar button handlers
    document.querySelectorAll('[data-command]').forEach(function(button) {
        button.addEventListener('click', function(e) {
            const command = this.getAttribute('data-command');

            if (command === 'createLink') {
                const url = prompt('Enter URL:');
                if (url) {
                    document.execCommand(command, false, url);
                }
                editor.focus();
                return;
            }

            document.execCommand(command, false, null);
            editor.focus();
        });

        // Handle select dropdowns for font size, etc.
        if (button.tagName === 'SELECT') {
            button.addEventListener('change', function() {
                const command = this.getAttribute('data-command');
                const value = this.value;
                if (value) {
                    document.execCommand(command, false, value);
                    editor.focus();
                    this.value = ''; // Reset select after use
                }
            });
        }

        // Handle color inputs
        if (button.type === 'color') {
            button.addEventListener('input', function() { // Use 'input' for real-time updates
                const command = this.getAttribute('data-command');
                const value = this.value;
                document.execCommand(command, false, value);
                editor.focus();
            });
        }
    });

    function autosaveToServer(trigger) {
        if (autosaveInFlight) {
            return Promise.resolve();
        }

        syncEditor();
        const payload = {
            answers: answersField.value,
            time_remaining_seconds: Math.max(timeRemaining, 0),
        };
        const serialized = JSON.stringify(payload);
        if (serialized === lastAutosavePayload && trigger !== 'timeup') { // Don't skip autosave if time is up
            return Promise.resolve();
        }

        lastAutosavePayload = serialized;
        autosaveInFlight = true;

        return fetch(autosaveUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: serialized,
        }).then(function(response) {
            if (!response.ok) {
                console.error('Autosave failed:', response.statusText);
            } else {
                setLastSaved(new Date());
            }
        }).catch(function(error) {
            console.error('Autosave network error:', error);
        }).finally(function() {
            autosaveInFlight = false;
        });
    }

    // Use sendBeacon for critical data on page unload
    function autosaveBeacon() {
        if (!navigator.sendBeacon || hasSubmitted) {
            return;
        }
        syncEditor();
        const payload = JSON.stringify({
            answers: answersField.value,
            time_remaining_seconds: Math.max(timeRemaining, 0),
        });
        const blob = new Blob([payload], { type: 'application/json' });
        navigator.sendBeacon(autosaveUrl, blob);
    }

    // Clear draft on submit
    examForm.addEventListener('submit', function() {
        syncEditor();
        hasSubmitted = true;
        localStorage.removeItem('exam_draft_{{ $exam->id }}');
        // No need to call autosave here as the form is submitting
    });

    // Warn before leaving page to prevent data loss
    window.addEventListener('beforeunload', function(e) {
        // Only prompt if the exam hasn't been submitted yet
        if (!hasSubmitted) {
            saveDraft(); // Attempt to save before leaving
            autosaveBeacon(); // Try to send data using sendBeacon
            e.preventDefault(); // Standard way to show prompt
            e.returnValue = ''; // Required for some browsers to show the prompt
            return 'Are you sure you want to leave? Your exam progress may be lost.'; // Message to display
        }
    });
</script>

@endsection
