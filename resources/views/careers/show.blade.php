@extends('layouts.guest')

@section('title', $vacancy->position_title)

@section('content')
<div class="container py-5">
    <a href="{{ route('careers.index') }}" class="btn btn-outline-secondary mb-4">
        <i class="bi bi-arrow-left me-1"></i> All vacancies
    </a>

    <div class="row g-4">
        <div class="col-lg-5 order-lg-2">
            <div class="card border-0 shadow-sm sticky-top" style="top: 1rem;">
                <div class="card-body p-4">
                    <h2 class="h4 fw-bold mb-1">Apply for this position</h2>
                    <p class="text-muted small mb-4">No account is needed. Your CV and documents are stored privately.</p>

                    <form method="POST" action="{{ route('careers.apply', $vacancy) }}" enctype="multipart/form-data">
                        @csrf
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <label for="first_name" class="form-label">First name</label>
                                <input id="first_name" name="first_name" class="form-control" value="{{ old('first_name') }}" maxlength="100" required>
                                @error('first_name')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-sm-6">
                                <label for="last_name" class="form-label">Last name</label>
                                <input id="last_name" name="last_name" class="form-control" value="{{ old('last_name') }}" maxlength="100" required>
                                @error('last_name')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label for="email" class="form-label">Email address</label>
                                <input id="email" type="email" name="email" class="form-control" value="{{ old('email') }}" maxlength="255" required>
                                @error('email')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label for="phone" class="form-label">Phone number</label>
                                <input id="phone" name="phone" class="form-control" value="{{ old('phone') }}" maxlength="50" required>
                                @error('phone')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label for="cv" class="form-label">CV / Resume (PDF or Word, up to 10 MB)</label>
                                <input id="cv" type="file" name="cv" class="form-control" accept=".pdf,.doc,.docx" required>
                                @error('cv')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label for="documents" class="form-label">Supporting documents (optional, up to 8 files)</label>
                                <input id="documents" type="file" name="documents[]" class="form-control" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" multiple>
                                <div class="form-text">PDF, Word, or image files; up to 10 MB each.</div>
                                @error('documents')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                                @error('documents.*')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label for="cover_letter" class="form-label">Cover letter</label>
                                <textarea id="cover_letter" name="cover_letter" class="form-control" rows="4" maxlength="10000">{{ old('cover_letter') }}</textarea>
                                @error('cover_letter')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <div class="form-check">
                                    <input id="consent" type="checkbox" name="consent" value="1" class="form-check-input" @checked(old('consent')) required>
                                    <label for="consent" class="form-check-label small">I consent to JBI University processing my application information for recruitment.</label>
                                </div>
                                @error('consent')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary w-100">Submit application</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-7 order-lg-1">
            <div class="mb-4">
                <span class="badge text-bg-light border mb-3">{{ $vacancy->employment_type }}</span>
                <h1 class="display-6 fw-bold">{{ $vacancy->position_title }}</h1>
                <p class="text-muted mb-1">{{ $vacancy->departmentRecord?->name ?? $vacancy->department ?? 'JBI University' }}</p>
                <p class="text-muted"><i class="bi bi-geo-alt me-1"></i>{{ $vacancy->location ?: 'JBI University' }}</p>
            </div>
            <h2 class="h5 fw-bold">About the role</h2>
            <div class="mb-4">{!! nl2br(e($vacancy->job_description ?: 'Please contact the recruitment team for more information.')) !!}</div>
            @if($vacancy->requirements)
                <h2 class="h5 fw-bold">Requirements</h2>
                <div class="mb-4">{!! nl2br(e($vacancy->requirements)) !!}</div>
            @endif
            @if($vacancy->closing_date)
                <p class="text-muted"><strong>Application deadline:</strong> {{ $vacancy->closing_date->format('F j, Y') }}</p>
            @endif
        </div>
    </div>
</div>
@endsection
