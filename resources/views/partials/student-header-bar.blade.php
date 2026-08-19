@if(Auth::check() && Auth::user()->isStudent())
@php
    $student = Auth::user();
    $sp = $student->studentProfile;
    $app = \App\Models\Application::where('email', $student->email)->orderBy('created_at', 'desc')->first();
    $isAdmitted = ($sp && $sp->status === 'active') || ($app && in_array($app->status, ['admitted', 'approved']));

    $currYr = $sp?->academic_year ?? '2026/2027';
    $currSem = $sp?->current_semester ? 'SEMESTER ' . ($sp->current_semester == 1 ? 'I' : 'II') : 'SEMESTER I';
    $acadStatus = $sp?->status === 'active' ? 'NORMAL PROGRESS' : ($isAdmitted ? strtoupper($sp?->status ?? 'ACTIVE') : 'UNADMITTED APPLICANT');
    
    if ($isAdmitted) {
        // Ensure student has official fee invoices generated (prevents 0 balance with no records)
        \App\Services\FeeInvoiceService::ensureStudentInvoiced($student);

        // Calculate ACTUAL Balance dynamically from Fee Records
        $actualBalance = \App\Models\FeeRecord::where('user_id', $student->id)->sum('balance_amount');
        $paidAmount = \App\Models\FeeRecord::where('user_id', $student->id)->sum('paid_amount');
    }
@endphp
<div class="card mb-4 border-0 shadow-sm rounded-3 overflow-hidden bg-white">
    <!-- Academic Details & Balance Row -->
    <div class="px-3 py-3 d-flex flex-wrap align-items-center justify-content-between gap-3">
        <!-- Academic Info (Year, Academic Status, Semester) -->
        <div class="d-flex align-items-center flex-wrap gap-3 small">
            @if($isAdmitted)
                <div>
                    <span class="text-muted text-uppercase fw-semibold">CURRENT YR.:</span>
                    <span class="badge bg-primary ms-1 px-2.5 py-1.5 fw-bold">{{ $currYr }}</span>
                </div>
            @endif
            <div>
                <span class="text-muted text-uppercase fw-semibold">ACADEMIC STATUS:</span>
                <span class="badge {{ $isAdmitted ? 'bg-warning text-dark' : 'bg-info text-dark' }} ms-1 px-2.5 py-1.5 fw-bold text-uppercase">{{ $acadStatus }}</span>
            </div>
            @if($isAdmitted)
                <div>
                    <span class="text-muted text-uppercase fw-semibold">CURRENT SEM.:</span>
                    <span class="badge bg-primary ms-1 px-2.5 py-1.5 fw-bold">{{ $currSem }}</span>
                </div>
            @endif
        </div>

        @if($isAdmitted)
            <!-- Calculated Fees Balance & Paid Amount -->
            <div class="d-flex align-items-center flex-wrap gap-3 small">
                <div>
                    <span class="text-muted fw-semibold text-uppercase">TOTAL FEES BAL DUE:</span>
                    <span class="fw-bold text-danger ms-1 fs-6">{{ $currencyCode }} {{ number_format($actualBalance, 2) }}</span>
                </div>
                <div>
                    <span class="badge bg-success px-2.5 py-1.5 text-white">
                        <i class="bi bi-check-circle me-1"></i>PAID: {{ $currencyCode }} {{ number_format($paidAmount, 2) }}
                    </span>
                </div>
            </div>
        @else
            <div>
                <span class="badge bg-secondary px-3 py-2 text-white">
                    <i class="bi bi-clock-history me-1"></i>Admission Application Status: {{ strtoupper($app?->status ?? 'PENDING') }}
                </span>
            </div>
        @endif
    </div>

    <!-- Quick Navigation Buttons Bar -->
    <div class="px-3 py-2.5 bg-light border-top d-flex flex-wrap align-items-center justify-content-between gap-2">
        <div class="btn-group flex-wrap shadow-sm rounded" role="group" style="gap: 2px;">
            <a href="{{ route('profile.show') }}" class="btn btn-sm px-3 py-2 {{ request()->routeIs('profile.*') ? 'btn-primary active text-white fw-bold shadow-sm' : 'btn-outline-primary text-dark fw-semibold bg-white' }}">
                <i class="bi bi-person me-1.5"></i>VIEW BIO DATA
            </a>
            @if($isAdmitted)
                <a href="{{ route('student.grades.index') }}" class="btn btn-sm px-3 py-2 {{ request()->routeIs('student.grades.*') ? 'btn-primary active text-white fw-bold shadow-sm' : 'btn-outline-primary text-dark fw-semibold bg-white' }}">
                    <i class="bi bi-award me-1.5"></i>VIEW RESULTS
                </a>
                <a href="{{ route('student.fees.structure') }}" class="btn btn-sm px-3 py-2 {{ request()->routeIs('student.fees.structure') ? 'btn-primary active text-white fw-bold shadow-sm' : 'btn-outline-primary text-dark fw-semibold bg-white' }}">
                    <i class="bi bi-file-earmark-spreadsheet me-1.5"></i>FEE STRUCTURE
                </a>
                <a href="{{ route('student.fees.index') }}" class="btn btn-sm px-3 py-2 {{ request()->routeIs('student.fees.index') ? 'btn-primary active text-white fw-bold shadow-sm' : 'btn-outline-primary text-dark fw-semibold bg-white' }}">
                    <i class="bi bi-receipt me-1.5"></i>VIEW INVOICES
                </a>
                <a href="{{ route('student.fees.ledger') }}" class="btn btn-sm px-3 py-2 {{ request()->routeIs('student.fees.ledger') ? 'btn-primary active text-white fw-bold shadow-sm' : 'btn-outline-primary text-dark fw-semibold bg-white' }}">
                    <i class="bi bi-file-earmark-text me-1.5"></i>MY TRANSACTIONS
                </a>
            @endif
        </div>
        @if($isAdmitted)
            <button type="button" class="btn btn-sm btn-primary fw-bold px-3 py-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#generatePrnHeaderModal">
                <i class="bi bi-qr-code me-1.5"></i>Generate PRN
            </button>
        @endif
    </div>
</div>

<!-- Modal: Generate PRN -->
<div class="modal fade" id="generatePrnHeaderModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form action="{{ route('student.fees.prn.generate') }}" method="POST">
                @csrf
                <div class="modal-header bg-primary text-white py-3">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-qr-code-scan me-2"></i>Generate Payment Reference Number (PRN)
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    @php
                        $unpaidFeesList = \App\Models\FeeRecord::where('user_id', Auth::id())->where('balance_amount', '>', 0)->get();
                    @endphp

                    @if($unpaidFeesList->count() > 0)
                        <div class="alert alert-info small py-2 px-3 mb-3">
                            <i class="bi bi-clock-history me-1"></i>Generated PRNs are time-bound and valid for <strong>30 days</strong>. Pay in full or partial.
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-uppercase">Select Fee Item / Invoice <span class="text-danger">*</span></label>
                            <select name="fee_record_id" class="form-select" id="headerPrnFeeSelect" required onchange="updateHeaderPrnAmount(this)">
                                @foreach($unpaidFeesList as $fee)
                                    <option value="{{ $fee->id }}" data-balance="{{ $fee->balance_amount }}">
                                        {{ $fee->payment_notes ?? $fee->feeStructure?->name ?? 'Tuition Fee' }} - Outstanding Bal: {{ $currencyCode }} {{ number_format($fee->balance_amount, 2) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-uppercase">Payment Type <span class="text-danger">*</span></label>
                            <div class="d-flex gap-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="payment_type" id="headerPayFull" value="full" checked onclick="toggleHeaderCustomAmount(false)">
                                    <label class="form-check-label fw-semibold" for="headerPayFull">Full Payment (No Balance)</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="payment_type" id="headerPayPartial" value="partial" onclick="toggleHeaderCustomAmount(true)">
                                    <label class="form-check-label fw-semibold" for="headerPayPartial">Partial Payment</label>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3 d-none" id="headerCustomAmountBox">
                            <label class="form-label fw-bold small text-uppercase">Enter Partial Amount ({{ $currencyCode }}) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="1" name="custom_amount" id="headerCustomAmountInput" class="form-control" placeholder="e.g. 200.00">
                        </div>
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-check-circle-fill text-success fs-1 d-block mb-2"></i>
                            <h6 class="fw-bold text-dark">No Outstanding Fee Balances</h6>
                            <p class="small mb-0">All your fee invoices have been fully cleared!</p>
                        </div>
                    @endif
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    @if($unpaidFeesList->count() > 0)
                        <button type="submit" class="btn btn-primary fw-bold">
                            <i class="bi bi-qr-code me-1"></i>GENERATE PRN (30-DAY VALIDITY)
                        </button>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function toggleHeaderCustomAmount(show) {
    const box = document.getElementById('headerCustomAmountBox');
    const input = document.getElementById('headerCustomAmountInput');
    if (show) {
        box.classList.remove('d-none');
        input.required = true;
    } else {
        box.classList.add('d-none');
        input.required = false;
    }
}
function updateHeaderPrnAmount(select) {
    const opt = select.options[select.selectedIndex];
    const bal = opt.getAttribute('data-balance');
    const input = document.getElementById('headerCustomAmountInput');
    if (input) input.max = bal;
}
</script>
@endif
