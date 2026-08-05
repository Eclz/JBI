@if(Auth::check() && Auth::user()->isStudent())
@php
    $student = Auth::user();
    $sp = $student->studentProfile;
    $progName = $sp?->program ?? 'BACHELOR OF SCIENCE IN SOFTWARE ENGINEERING (BSSE)';
    $currYr = $sp?->academic_year ?? '2026/2027';
    $currSem = $sp?->current_semester ? 'SEMESTER ' . ($sp->current_semester == 1 ? 'I' : 'II') : 'SEMESTER I';
    $acadStatus = $sp?->status === 'active' ? 'NORMAL PROGRESS' : strtoupper($sp?->status ?? 'NORMAL PROGRESS');
    $feesBal = number_format(11033938);
@endphp
<div class="card mb-3 border-0 shadow-sm rounded-3 overflow-hidden bg-white">
    <div class="px-3 py-2 border-bottom bg-light d-flex flex-wrap align-items-center justify-content-between gap-2">
        <div class="d-flex align-items-center flex-wrap gap-2">
            <span class="badge bg-secondary text-uppercase px-2 py-1">PROGRAMME</span>
            <span class="fw-bold text-dark small text-uppercase">{{ $progName }}</span>
            <span class="badge bg-primary px-2 py-1 text-uppercase">ACTIVE</span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="small text-muted fw-semibold">ACADEMIC STATUS:</span>
            <span class="badge bg-warning text-dark px-2 py-1 fw-bold text-uppercase">{{ $acadStatus }}</span>
        </div>
    </div>
    
    <div class="px-3 py-2 d-flex flex-wrap align-items-center justify-content-between gap-2">
        <div class="d-flex align-items-center flex-wrap gap-3 small">
            <div>
                <span class="text-muted text-uppercase fw-semibold">CURRENT YR.</span>
                <span class="badge bg-primary ms-1 px-2 py-1">{{ $currYr }}</span>
            </div>
            <div>
                <span class="text-muted text-uppercase fw-semibold">CURRENT SEM.</span>
                <span class="badge bg-primary ms-1 px-2 py-1">{{ $currSem }}</span>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2 small">
            <div>
                <span class="text-muted fw-semibold text-uppercase">TOTAL FEES BAL DUE:</span>
                <span class="fw-bold text-primary ms-1">UGX {{ $feesBal }}/=</span>
            </div>
            <div>
                <span class="badge bg-primary px-2 py-1 text-white ms-2">
                    BALANCE ON ACCOUNT: 0/-
                </span>
            </div>
        </div>
    </div>

    <!-- Quick Navigation Buttons Bar -->
    <div class="px-3 py-2 bg-light border-top d-flex flex-wrap align-items-center justify-content-between gap-2">
        <div class="btn-group btn-group-sm flex-wrap" role="group">
            <a href="{{ route('profile.show') }}" class="btn btn-outline-secondary {{ request()->routeIs('profile.show') ? 'active bg-secondary text-white' : '' }}">
                <i class="bi bi-person me-1"></i>VIEW BIO DATA
            </a>
            <a href="{{ route('student.grades.index') }}" class="btn btn-outline-secondary {{ request()->routeIs('student.grades.*') ? 'active bg-secondary text-white' : '' }}">
                <i class="bi bi-award me-1"></i>VIEW RESULTS
            </a>
            <a href="{{ route('student.fees.index') }}" class="btn btn-outline-secondary {{ request()->routeIs('student.fees.index') ? 'active bg-secondary text-white' : '' }}">
                <i class="bi bi-receipt me-1"></i>VIEW INVOICES
            </a>
            <a href="{{ route('student.fees.ledger') }}" class="btn btn-outline-secondary {{ request()->routeIs('student.fees.ledger') ? 'active bg-secondary text-white' : '' }}">
                <i class="bi bi-file-earmark-text me-1"></i>VIEW FEES STRUCTURE
            </a>
        </div>
        <a href="{{ route('student.fees.index') }}" class="btn btn-sm btn-primary fw-bold">
            <i class="bi bi-qr-code me-1"></i>Generate PRN
        </a>
    </div>
</div>
@endif
