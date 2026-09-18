@extends('layouts.app')

@section('title', 'Apply for Student Leadership: ' . $session->title)

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Breadcrumb -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item"><a href="{{ route('student.evoting.index') }}">E-Voting</a></li>
                <li class="breadcrumb-item active" aria-current="page">Apply for Candidacy</li>
            </ol>
        </nav>
        <a href="{{ route('student.evoting.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Back
        </a>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(isset($hasRetake) && $hasRetake)
        <div class="alert alert-warning border-start border-warning border-4 shadow-sm mb-4">
            <div class="d-flex align-items-start">
                <i class="bi bi-exclamation-triangle-fill fs-3 me-3 text-warning"></i>
                <div>
                    <h5 class="fw-bold mb-1">Academic Retake / Failed Course Warning</h5>
                    <p class="mb-0 small">
                        Our academic system has detected an active course retake, failing grade, or retake fee invoice on your profile. 
                        Under JBI Electoral Regulations, candidate applications from students with course retakes will be <strong>automatically vetted out and disqualified</strong> upon submission.
                    </p>
                </div>
            </div>
        </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                <div class="card-header bg-primary text-white p-4" style="background: linear-gradient(135deg, #1e3a8a, #3b82f6) !important;">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <span class="badge bg-warning text-dark fw-bold px-3 py-1 mb-2 text-uppercase">Candidacy Application</span>
                            <h2 class="h3 fw-bold mb-1">{{ $session->title }}</h2>
                            <p class="text-white-50 mb-0 small">Submit your application to contest for a student leadership portfolio. All applications undergo official vetting by the Electoral Commission.</p>
                        </div>
                        <span class="badge bg-light text-dark py-2 px-3 fw-bold border">
                            <i class="bi bi-calendar3 me-1"></i>Deadline: {{ $session->application_end_at ? $session->application_end_at->format('M d, Y H:i') : 'Open' }}
                        </span>
                    </div>
                </div>

                <div class="card-body p-4 p-md-5 bg-white">
                    <form action="{{ route('student.evoting.apply.store', $session) }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- Step 1: Position Selection -->
                        <div class="mb-4 pb-3 border-bottom">
                            <h5 class="fw-bold text-dark mb-3">
                                <span class="badge bg-primary rounded-circle me-2" style="width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center;">1</span>
                                Select Leadership Portfolio
                            </h5>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Choose Contested Position *</label>
                                <select name="voting_position_id" class="form-select form-select-lg" required>
                                    <option value="">Select an available position...</option>
                                    @foreach($eligiblePositions as $pos)
                                        @php
                                            $alreadyApplied = in_array($pos->id, $existingApplications);
                                        @endphp
                                        <option value="{{ $pos->id }}" {{ (old('voting_position_id') == $pos->id) ? 'selected' : '' }} {{ $alreadyApplied ? 'disabled' : '' }}>
                                            {{ $pos->title }} &mdash; 
                                            @if($pos->scope === 'university_wide')
                                                [University-Wide Portfolio]
                                            @else
                                                [Faculty of {{ $pos->faculty->name ?? 'Your Faculty' }}]
                                            @endif
                                            {{ $alreadyApplied ? ' (Already Applied)' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="form-text small">
                                    Only positions matching your registered faculty (<strong>{{ $studentProfile->department?->faculty?->name ?? 'General' }}</strong>) and university-wide portfolios are available.
                                </div>
                            </div>
                        </div>

                        <!-- Step 2: Campaign Identity & Manifesto -->
                        <div class="mb-4 pb-3 border-bottom">
                            <h5 class="fw-bold text-dark mb-3">
                                <span class="badge bg-primary rounded-circle me-2" style="width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center;">2</span>
                                Campaign Identity & Manifesto
                            </h5>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Campaign Slogan / Motto</label>
                                    <input type="text" name="slogan" class="form-control" placeholder="e.g. Inclusivity, Innovation & Service" value="{{ old('slogan') }}">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Party / Group Affiliation (Optional)</label>
                                    <input type="text" name="party_affiliation" class="form-control" placeholder="e.g. Progressive Student Movement / Independent" value="{{ old('party_affiliation') }}">
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label fw-semibold">Official Manifesto & Vision Statement *</label>
                                    <textarea name="manifesto" class="form-control" rows="5" placeholder="Detail your plans, key objectives, and why the student body should vote for you..." required>{{ old('manifesto') }}</textarea>
                                    <div class="form-text small">This manifesto will be reviewed by the Electoral Commission and presented to all student voters on the official ballot.</div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 3: Candidate Photo & Credentials -->
                        <div class="mb-4 pb-3 border-bottom">
                            <h5 class="fw-bold text-dark mb-3">
                                <span class="badge bg-primary rounded-circle me-2" style="width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center;">3</span>
                                Profile & Supporting Documents
                            </h5>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Campaign Profile Photo (High Quality)</label>
                                    <input type="file" name="photo" class="form-control" accept="image/jpeg,image/png,image/jpg">
                                    <div class="form-text small">Will appear on the official electronic voting ballot.</div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Supporting Documents (CV, Endorsements, Certificates)</label>
                                    <input type="file" name="documents[]" class="form-control" multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                    <div class="form-text small">Upload recommendation letters or certificates (PDF/Images).</div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Current Year of Study</label>
                                    <input type="number" name="year_of_study" class="form-control" value="{{ old('year_of_study', $studentProfile->year_of_study ?? 1) }}" min="1" max="7" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Cumulative GPA</label>
                                    <input type="number" step="0.01" name="cgpa" class="form-control" value="{{ old('cgpa') }}" min="0" max="5" placeholder="e.g. 3.75">
                                </div>
                            </div>
                        </div>

                        <!-- Step 4: Affirmation & Submission -->
                        <div class="mb-4">
                            <div class="form-check p-3 bg-light rounded-3 border">
                                <input class="form-check-input ms-0 me-2" type="checkbox" id="affirmationCheck" required>
                                <label class="form-check-label small text-muted" for="affirmationCheck">
                                    I hereby confirm that all submitted details and credentials are true and accurate. I understand that my candidacy is subject to official vetting by the Electoral Commission and agreement with university election guidelines.
                                </label>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('student.evoting.index') }}" class="btn btn-outline-secondary px-4">Cancel</a>
                            <button type="submit" class="btn btn-primary fw-bold px-5">
                                <i class="bi bi-send-fill me-1"></i>Submit Application for Vetting
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
