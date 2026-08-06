@if(Auth::check() && Auth::user()->isStudent())
@php
    $student = Auth::user();
    $sp = $student->studentProfile;
    $currYr = $sp?->academic_year ?? '2026/2027';
    $currSem = $sp?->current_semester ? 'SEMESTER ' . ($sp->current_semester == 1 ? 'I' : 'II') : 'SEMESTER I';
    $acadStatus = $sp?->status === 'active' ? 'NORMAL PROGRESS' : strtoupper($sp?->status ?? 'NORMAL PROGRESS');
    
    // Calculate ACTUAL Balance dynamically from Fee Records
    $actualBalance = \App\Models\FeeRecord::where('user_id', $student->id)->sum('balance_amount');
    $paidAmount = \App\Models\FeeRecord::where('user_id', $student->id)->sum('paid_amount');
@endphp
<div class="card mb-4 border-0 shadow-sm rounded-3 overflow-hidden bg-white">
    <!-- Academic Details & Balance Row (Programme row obsolete & replaced in top navbar) -->
    <div class="px-3 py-3 d-flex flex-wrap align-items-center justify-content-between gap-3">
        <!-- Academic Info (Year, Academic Status, Semester) -->
        <div class="d-flex align-items-center flex-wrap gap-3 small">
            <div>
                <span class="text-muted text-uppercase fw-semibold">CURRENT YR.:</span>
                <span class="badge bg-primary ms-1 px-2.5 py-1.5 fw-bold">{{ $currYr }}</span>
            </div>
            <div>
                <span class="text-muted text-uppercase fw-semibold">ACADEMIC STATUS:</span>
                <span class="badge bg-warning text-dark ms-1 px-2.5 py-1.5 fw-bold text-uppercase">{{ $acadStatus }}</span>
            </div>
            <div>
                <span class="text-muted text-uppercase fw-semibold">CURRENT SEM.:</span>
                <span class="badge bg-primary ms-1 px-2.5 py-1.5 fw-bold">{{ $currSem }}</span>
            </div>
        </div>

        <!-- Calculated Fees Balance & Paid Amount -->
        <div class="d-flex align-items-center flex-wrap gap-3 small">
            <div>
                <span class="text-muted fw-semibold text-uppercase">TOTAL FEES BAL DUE:</span>
                <span class="fw-bold text-danger ms-1 fs-6">UGX {{ number_format($actualBalance, 2) }}</span>
            </div>
            <div>
                <span class="badge bg-success px-2.5 py-1.5 text-white">
                    <i class="bi bi-check-circle me-1"></i>PAID: UGX {{ number_format($paidAmount, 2) }}
                </span>
            </div>
        </div>
    </div>

    <!-- Quick Navigation Buttons Bar -->
    <div class="px-3 py-2.5 bg-light border-top d-flex flex-wrap align-items-center justify-content-between gap-2">
        <div class="btn-group flex-wrap shadow-sm rounded" role="group" style="gap: 2px;">
            <a href="{{ route('profile.show') }}" class="btn btn-sm px-3 py-2 {{ request()->routeIs('profile.*') ? 'btn-primary active text-white fw-bold shadow-sm' : 'btn-outline-primary text-dark fw-semibold bg-white' }}">
                <i class="bi bi-person me-1.5"></i>VIEW BIO DATA
            </a>
            <a href="{{ route('student.grades.index') }}" class="btn btn-sm px-3 py-2 {{ request()->routeIs('student.grades.*') ? 'btn-primary active text-white fw-bold shadow-sm' : 'btn-outline-primary text-dark fw-semibold bg-white' }}">
                <i class="bi bi-award me-1.5"></i>VIEW RESULTS
            </a>
            <a href="{{ route('student.fees.index') }}" class="btn btn-sm px-3 py-2 {{ request()->routeIs('student.fees.index') ? 'btn-primary active text-white fw-bold shadow-sm' : 'btn-outline-primary text-dark fw-semibold bg-white' }}">
                <i class="bi bi-receipt me-1.5"></i>VIEW INVOICES
            </a>
            <a href="{{ route('student.fees.ledger') }}" class="btn btn-sm px-3 py-2 {{ request()->routeIs('student.fees.ledger') ? 'btn-primary active text-white fw-bold shadow-sm' : 'btn-outline-primary text-dark fw-semibold bg-white' }}">
                <i class="bi bi-file-earmark-text me-1.5"></i>MY TRANSACTIONS
            </a>
        </div>
        <a href="{{ route('student.fees.index') }}" class="btn btn-sm btn-primary fw-bold px-3 py-2 shadow-sm">
            <i class="bi bi-qr-code me-1.5"></i>Generate PRN
        </a>
    </div>
</div>
@endif
