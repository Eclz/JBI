@extends('layouts.app')

@section('title', 'Official Program Fee Structure')

@section('content')
<div class="container-fluid px-4 py-4">
    @include('partials.student-header-bar')

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-primary fw-bold">
                <i class="bi bi-file-earmark-spreadsheet me-2"></i>Official Degree Fee Structure
            </h1>
            <p class="text-muted mb-0">Detailed breakdown of tuition, functional fees, exam fees & mandatory university charges</p>
        </div>
        <a href="{{ route('student.fees.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Back to Fee Invoices
        </a>
    </div>

    <!-- Active Program Banner -->
    <div class="card border-0 shadow-sm rounded-3 mb-4 bg-primary text-white">
        <div class="card-body p-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div>
                <small class="text-uppercase fw-bold text-white-50 d-block mb-1" style="letter-spacing: 0.5px;">ENROLLED DEGREE PROGRAMME</small>
                <h4 class="fw-bold mb-1">{{ $sp->program ?? 'Bachelor of Science in Software Engineering' }}</h4>
                <div class="d-flex align-items-center gap-3 small text-white-50">
                    <span><i class="bi bi-calendar2-range me-1"></i>Academic Year: <strong>{{ $sp->academic_year ?? '2026/2027' }}</strong></span>
                    <span><i class="bi bi-building me-1"></i>Department: <strong>{{ $sp->department->name ?? 'School of Computing & IT' }}</strong></span>
                </div>
            </div>
            <div class="text-end">
                <span class="badge bg-warning text-dark px-3 py-2 fs-6 fw-bold">Approved Fee Schedule</span>
            </div>
        </div>
    </div>

    <!-- Mandatory Fee Items Table -->
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-header bg-white border-bottom py-3">
            <h5 class="card-title mb-0 fw-bold text-dark"><i class="bi bi-card-checklist me-2 text-primary"></i>Mandatory Semester & Annual Fees</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">Fee Type</th>
                            <th>Item Name & Description</th>
                            <th>Frequency</th>
                            <th>Mandatory</th>
                            <th class="text-end pe-3">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($feeStructures as $fee)
                            <tr>
                                <td class="ps-3">
                                    <span class="badge bg-primary text-uppercase px-2.5 py-1.5 fw-bold">{{ $fee->type }}</span>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $fee->name }}</div>
                                    <small class="text-muted">{{ $fee->description }}</small>
                                </td>
                                <td class="small text-uppercase fw-semibold text-secondary">{{ str_replace('_', ' ', $fee->frequency) }}</td>
                                <td>
                                    @if($fee->is_mandatory)
                                        <span class="badge bg-success px-2 py-1"><i class="bi bi-check-circle me-1"></i>Mandatory</span>
                                    @else
                                        <span class="badge bg-secondary px-2 py-1">Optional</span>
                                    @endif
                                </td>
                                <td class="text-end pe-3 fw-bold text-dark fs-6">{{ $currencyCode }} {{ number_format($fee->amount, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">No fee structure items configured.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Official Fee Policy Note -->
    <div class="alert alert-info border-0 shadow-sm rounded-3 d-flex align-items-center gap-3">
        <i class="bi bi-info-circle-fill fs-3 text-info"></i>
        <div>
            <h6 class="fw-bold mb-1">University Tuition & Fee Regulations</h6>
            <p class="mb-0 small">All students are expected to pay 100% tuition or register under an approved installment plan before sit-in examinations. PRN payment references can be generated directly from your invoices view.</p>
        </div>
    </div>
</div>
@endsection
