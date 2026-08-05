@extends('layouts.app')

@section('title', 'Semester Enrollment & Registration')

@section('content')
<div class="container-fluid px-4 py-4">
    @include('partials.student-header-bar')

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold text-dark text-uppercase mb-0">
                <i class="bi bi-person-plus-fill text-primary me-2"></i>SEMESTER ENROLLMENT & REGISTRATION
            </h5>
            <p class="text-muted small mb-0">Select academic year and semester to complete your semester enrollment</p>
        </div>
        <a href="{{ route('student.my-programme') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>Back to My Programme
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 border-bottom border-primary border-2">
                    <h6 class="fw-bold mb-0 text-primary">ENROLLMENT FORM</h6>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('student.enrollment.store') }}" method="POST">
                        @csrf
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase">Academic Year <span class="text-danger">*</span></label>
                                <select name="academic_year_id" class="form-select" required>
                                    @foreach($academicYears as $ay)
                                        <option value="{{ $ay->id }}">{{ $ay->name ?? $ay->year }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase">Year of Study <span class="text-danger">*</span></label>
                                <select name="year_of_study" class="form-select" required>
                                    @for($y = 1; $y <= 4; $y++)
                                        <option value="{{ $y }}" {{ ($studentProfile?->year_of_study ?? 1) == $y ? 'selected' : '' }}>Year {{ $y }}</option>
                                    @endfor
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase">Semester <span class="text-danger">*</span></label>
                                <select name="current_semester" class="form-select" required>
                                    <option value="1" {{ ($studentProfile?->current_semester ?? 1) == 1 ? 'selected' : '' }}>Semester I</option>
                                    <option value="2" {{ ($studentProfile?->current_semester ?? 1) == 2 ? 'selected' : '' }}>Semester II</option>
                                </select>
                            </div>

                            <div class="col-12 bg-light p-3 rounded">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="enrollCheck" required>
                                    <label class="form-check-label small" for="enrollCheck">
                                        I confirm that I am enrolling for the selected academic year and semester, and agree to university guidelines.
                                    </label>
                                </div>
                            </div>

                            <div class="col-12 text-end">
                                <button type="submit" class="btn btn-primary px-4 fw-bold">
                                    <i class="bi bi-check-circle me-1"></i>SUBMIT ENROLLMENT
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
