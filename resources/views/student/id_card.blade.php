@extends('layouts.app')

@section('title', 'Student ID Card')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 d-print-none">
        <div>
            <h1 class="h3 mb-1 fw-bold text-dark"><i class="bi bi-person-badge me-2"></i>My Digital ID Card</h1>
            <p class="text-muted mb-0">View and print your official JBI University Student Identification Card.</p>
        </div>
        <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn btn-primary fw-bold shadow-sm">
                <i class="bi bi-printer me-1"></i> Print ID Card
            </button>
            <a href="{{ route('student.dashboard') }}" class="btn btn-outline-secondary fw-bold shadow-sm">Back to Dashboard</a>
        </div>
    </div>

    <!-- ID Card Container -->
    <div class="d-flex justify-content-center mt-5">
        <div class="id-card-wrapper shadow-lg position-relative" style="width: 350px; border-radius: 15px; overflow: hidden; background: #fff; border: 1px solid #e0e0e0;">
            
            <!-- Header Section (University Branding) -->
            <div class="id-header text-center py-3" style="background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%); color: white;">
                <div class="d-flex align-items-center justify-content-center mb-1">
                    <i class="bi bi-bank2 fs-4 me-2"></i>
                    <h5 class="fw-bold mb-0 text-uppercase" style="letter-spacing: 1px;">JBI University</h5>
                </div>
                <p class="mb-0" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px;">Student Identification Card</p>
            </div>

            <!-- Profile Photo & QR Section -->
            <div class="text-center mt-4 mb-3 position-relative">
                <div class="profile-photo mx-auto p-1 bg-white shadow-sm" style="width: 120px; height: 120px; border-radius: 50%; border: 3px solid #0d6efd;">
                    @if($student->avatar)
                        <img src="{{ Storage::url($student->avatar) }}" alt="Student Photo" class="img-fluid rounded-circle" style="width: 100%; height: 100%; object-fit: cover;">
                    @else
                        <div class="d-flex align-items-center justify-content-center h-100 w-100 bg-light rounded-circle">
                            <span class="fs-1 fw-bold text-secondary">{{ substr($student->first_name, 0, 1) }}{{ substr($student->last_name, 0, 1) }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Student Details -->
            <div class="id-body px-4 pb-3 text-center">
                <h4 class="fw-bold text-dark mb-0">{{ strtoupper($student->name) }}</h4>
                <p class="text-primary fw-bold mb-3 font-monospace" style="font-size: 1.1rem; letter-spacing: 1px;">{{ $studentProfile->student_number }}</p>
                
                <div class="text-start mt-3 px-2">
                    <div class="row mb-1">
                        <div class="col-4 text-muted fw-bold" style="font-size: 0.7rem; text-transform: uppercase;">Program</div>
                        <div class="col-8 fw-bold text-dark" style="font-size: 0.85rem;">{{ $program }}</div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-4 text-muted fw-bold" style="font-size: 0.7rem; text-transform: uppercase;">Dept</div>
                        <div class="col-8 fw-bold text-dark" style="font-size: 0.85rem;">{{ $department }}</div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-4 text-muted fw-bold" style="font-size: 0.7rem; text-transform: uppercase;">Valid Till</div>
                        <div class="col-8 fw-bold text-dark" style="font-size: 0.85rem;">{{ $studentProfile->expected_graduation_date ? $studentProfile->expected_graduation_date->format('M d, Y') : 'N/A' }}</div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-4 text-muted fw-bold" style="font-size: 0.7rem; text-transform: uppercase;">Blood Grp</div>
                        <div class="col-8 fw-bold text-danger" style="font-size: 0.85rem;">{{ $bloodGroup }}</div>
                    </div>
                </div>
            </div>

            <!-- Footer / QR Code -->
            <div class="id-footer d-flex align-items-center justify-content-between px-4 py-3 border-top bg-light">
                <div style="width: 50px; height: 50px;">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data={{ urlencode(route('student.dashboard')) }}&margin=0" alt="QR Code" class="img-fluid mix-blend-multiply">
                </div>
                <div class="text-end" style="font-size: 0.65rem;">
                    <p class="mb-0 fw-bold text-dark text-uppercase">Authorized Signature</p>
                    <div class="mt-2 border-bottom border-dark d-inline-block" style="width: 80px;"></div>
                    <p class="mb-0 text-muted mt-1">Registrar</p>
                </div>
            </div>
            
            <!-- Bottom Accent -->
            <div style="height: 6px; background: #0d6efd; width: 100%;"></div>
        </div>
    </div>
    
    <!-- Information Alert -->
    <div class="d-flex justify-content-center mt-4 d-print-none">
        <div class="alert alert-info border-0 shadow-sm" style="max-width: 600px; border-radius: 10px;">
            <i class="bi bi-info-circle-fill me-2 text-primary"></i> 
            This digital ID card is valid for official campus use. When printing, ensure your printer settings are set to <strong>100% scale</strong> and <strong>Background Graphics are enabled</strong>.
        </div>
    </div>
</div>

<style>
    .mix-blend-multiply {
        mix-blend-mode: multiply;
    }
    
    @media print {
        body * {
            visibility: hidden;
        }
        .container-fluid {
            padding: 0 !important;
            margin: 0 !important;
        }
        .id-card-wrapper, .id-card-wrapper * {
            visibility: visible;
        }
        .id-card-wrapper {
            position: absolute !important;
            left: 50% !important;
            top: 20mm !important;
            transform: translateX(-50%) !important;
            box-shadow: none !important;
            border: 1px solid #ccc !important;
            /* Adjust properties to ensure background colors print perfectly */
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        .d-print-none {
            display: none !important;
        }
    }
</style>
@endsection
