@extends('layouts.app')

@section('title', 'Payment Reference Slip - PRN')

@section('content')
<div class="container-fluid px-4 py-4">
    @if(Auth::check() && Auth::user()->isStudent())
        @include('partials.student-header-bar')
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-primary fw-bold">
                <i class="bi bi-qr-code-scan me-2"></i>Official Payment Reference Slip (PRN)
            </h1>
            <p class="text-muted mb-0">Use this PRN to make bank transfer, online, or counter fee payments</p>
        </div>
        <a href="{{ route('student.fees.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Back to Invoices & Fees
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-lg rounded-3 overflow-hidden" id="prn-slip-card">
                <!-- Slip Header -->
                <div class="card-header bg-primary text-white p-4 text-center">
                    <img src="/logo.png" alt="University Logo" style="height: 55px; width: auto;" class="mb-2 mx-auto d-block filter-invert" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=JBI+University&background=ffffff&color=0d6efd&size=100';">
                    <h4 class="fw-bold mb-1 text-uppercase">JBI UNIVERSITY ACADEMIC PORTAL</h4>
                    <p class="mb-0 small text-white-50 text-uppercase fw-semibold" style="letter-spacing: 1px;">OFFICIAL PAYMENT REFERENCE SLIP</p>
                </div>

                <div class="card-body p-4 p-md-5">
                    <!-- Status Banner -->
                    <div class="alert alert-{{ $prn->status === 'paid' ? 'success' : ($prn->is_expired ? 'danger' : 'warning') }} d-flex align-items-center justify-content-between p-3 mb-4 rounded-3">
                        <div class="d-flex align-items-center">
                            @if($prn->status === 'paid')
                                <i class="bi bi-check-circle-fill fs-2 me-3 text-success"></i>
                                <div>
                                    <h6 class="fw-bold mb-0 text-success text-uppercase">PAYMENT COMPLETED</h6>
                                    <span class="small">Paid on {{ $prn->paid_at ? $prn->paid_at->format('M d, Y h:i A') : 'N/A' }}</span>
                                </div>
                            @elseif($prn->is_expired)
                                <i class="bi bi-x-circle-fill fs-2 me-3 text-danger"></i>
                                <div>
                                    <h6 class="fw-bold mb-0 text-danger text-uppercase">PRN EXPIRED (30-DAY LIMIT EXCEEDED)</h6>
                                    <span class="small text-muted">Expired on {{ $prn->expires_at ? $prn->expires_at->format('M d, Y h:i A') : '30-day limit' }}. Please generate a new PRN.</span>
                                </div>
                            @else
                                <i class="bi bi-clock-history fs-2 me-3 text-warning"></i>
                                <div>
                                    <h6 class="fw-bold mb-0 text-warning text-uppercase">PENDING PAYMENT (TIME BOUND: 30 DAYS)</h6>
                                    <span class="small text-muted">Generated: {{ $prn->generated_at->format('M d, Y') }} | Valid Until: <strong>{{ $prn->expires_at ? $prn->expires_at->format('M d, Y h:i A') : '30 Days' }}</strong></span>
                                </div>
                            @endif
                        </div>
                        <span class="badge bg-{{ $prn->status === 'paid' ? 'success' : ($prn->is_expired ? 'danger' : 'warning text-dark') }} fs-6 px-3 py-2 text-uppercase">
                            {{ $prn->status }}
                        </span>
                    </div>

                    <!-- PRN Big Display Box -->
                    <div class="bg-light p-4 rounded-3 text-center border mb-4 border-2 border-primary">
                        <small class="text-muted fw-bold text-uppercase d-block mb-1">PAYMENT REFERENCE NUMBER (PRN)</small>
                        <h2 class="fw-extrabold text-primary mb-2 tracking-wide font-monospace" style="letter-spacing: 2px;">{{ $prn->prn_number }}</h2>
                        <div class="d-flex justify-content-center gap-2">
                            <button type="button" class="btn btn-sm btn-outline-primary fw-bold" onclick="navigator.clipboard.writeText('{{ $prn->prn_number }}'); alert('PRN Copied to Clipboard!');">
                                <i class="bi bi-copy me-1"></i>Copy PRN
                            </button>
                        </div>
                    </div>

                    <!-- Payment Details Table -->
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered align-middle">
                            <tbody class="small">
                                <tr>
                                    <th class="bg-light w-35 text-muted">STUDENT NAME</th>
                                    <td class="fw-bold text-dark">{{ Auth::user()->full_name }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light text-muted">STUDENT REG / ID</th>
                                    <td class="fw-bold text-dark">{{ Auth::user()->studentProfile?->registration_number ?? 'REG-'.Auth::id() }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light text-muted">FEE ITEM / INVOICE</th>
                                    <td class="fw-bold text-primary">{{ $prn->fee_item_name }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light text-muted">PAYMENT TYPE</th>
                                    <td><span class="badge bg-info text-dark text-uppercase px-2.5 py-1 fw-bold">{{ $prn->payment_type }} PAYMENT</span></td>
                                </tr>
                                <tr>
                                    <th class="bg-light text-muted fs-6">PAYABLE AMOUNT</th>
                                    <td class="fw-bold text-success fs-5">{{ $currencyCode }} {{ number_format($prn->amount, 2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    @if(!$prn->is_expired && $prn->status === 'pending')
                        <!-- Payment Processing Box -->
                        <div class="card border-primary mb-4 bg-light">
                            <div class="card-body p-4">
                                <h6 class="fw-bold text-primary mb-3"><i class="bi bi-credit-card-2-front me-2"></i>MAKE PAYMENT NOW WITH THIS PRN</h6>
                                <form action="{{ route('student.fees.prn.pay', $prn) }}" method="POST">
                                    @csrf
                                    <div class="row g-3 align-items-center">
                                        <div class="col-md-7">
                                            <label class="form-label small fw-bold text-uppercase">Select Payment Gateway / Method</label>
                                            <select name="payment_method" class="form-select" required>
                                                @foreach($paymentMethods as $key => $label)
                                                    <option value="{{ $key }}">{{ $label }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-5 text-end pt-md-4">
                                            <button type="submit" class="btn btn-success btn-lg w-100 fw-bold shadow-sm">
                                                <i class="bi bi-shield-check me-2"></i>PAY {{ $currencyCode }} {{ number_format($prn->amount, 2) }} NOW
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @elseif($prn->is_expired)
                        <!-- Expired Action Prompt -->
                        <div class="text-center p-3 border rounded bg-light mb-4">
                            <p class="text-danger fw-bold mb-2"><i class="bi bi-exclamation-triangle me-1"></i>This PRN expired after 30 days of non-payment.</p>
                            <a href="{{ route('student.fees.index') }}" class="btn btn-primary fw-bold px-4">
                                <i class="bi bi-plus-circle me-1"></i>Generate New PRN
                            </a>
                        </div>
                    @endif

                    <!-- Instructions Footer -->
                    <div class="border-top pt-3 text-muted small">
                        <h6 class="fw-bold text-dark mb-1">Payment Instructions:</h6>
                        <ul class="ps-3 mb-0">
                            <li>Present this Payment Reference Number (PRN) at any authorized bank branch or mobile money agent.</li>
                            <li>Payments made via online portals update your fee ledger balance immediately.</li>
                            <li>Each PRN is time-bound and valid for exactly <strong>30 days</strong> from generation date.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
