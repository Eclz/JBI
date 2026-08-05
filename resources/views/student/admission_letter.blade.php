@extends('layouts.app')

@section('title', 'Official Letter of Admission')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
        <div>
            <h5 class="fw-bold text-dark text-uppercase mb-0">
                <i class="bi bi-file-earmark-pdf text-primary me-2"></i>OFFICIAL LETTER OF ADMISSION
            </h5>
            <p class="text-muted small mb-0">Johnson Bible Institute (JBI) University Official Admission Record</p>
        </div>
        <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn btn-primary fw-bold">
                <i class="bi bi-printer me-2"></i>PRINT / DOWNLOAD PDF
            </button>
            <a href="{{ route('student.dashboard') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Back to Dashboard
            </a>
        </div>
    </div>

    <!-- Official Printable Letterhead -->
    <div class="card border-0 shadow-sm mx-auto p-4 p-md-5 bg-white" style="max-width: 850px; font-family: 'Georgia', serif; color: #222; border-top: 6px solid #1e3a8a !important;">
        <!-- Header & Logo -->
        <div class="text-center pb-4 mb-4 border-bottom border-2 border-primary">
            <img src="{{ asset('images/jbi.png') }}" alt="JBI Logo" style="max-height: 80px;" class="mb-2">
            <h2 class="fw-bold text-uppercase mb-1" style="color: #1e3a8a; letter-spacing: 1px;">JOHNSON BIBLE INSTITUTE UNIVERSITY</h2>
            <p class="fst-italic text-muted small mb-1">"Excellence in Education, Service to Humanity & Faith"</p>
            <div class="small text-muted">
                91 Progress Road, Lindhaven, Roodepoort, South Africa<br>
                Tel: +27 67 965 3866 | Email: info@johnsonbibleinstitute.com | Website: www.johnsonbibleinstitute.com
            </div>
        </div>

        <!-- Reference & Date -->
        <div class="d-flex justify-content-between mb-4 small fw-bold">
            <div>
                <strong>REF NO:</strong> JBI/ADM/{{ date('Y') }}/{{ str_pad($application->id ?? 101, 4, '0', STR_PAD_LEFT) }}<br>
                <strong>REG NO:</strong> {{ $student->student_id ?? 'JBI-'.date('Y').'-001' }}
            </div>
            <div class="text-end">
                <strong>DATE:</strong> {{ now()->format('F j, Y') }}
            </div>
        </div>

        <!-- Recipient Information -->
        <div class="mb-4">
            <h5 class="fw-bold mb-1">{{ $student->full_name }}</h5>
            <p class="mb-0 text-muted small">
                Email: {{ $student->email }}<br>
                Phone: {{ $student->phone ?? 'N/A' }}
            </p>
        </div>

        <!-- Title -->
        <div class="text-center my-4 py-2 bg-light border-start border-4 border-primary rounded">
            <h4 class="fw-bold text-uppercase text-primary mb-0" style="letter-spacing: 1.5px;">OFFICIAL LETTER OF ADMISSION</h4>
        </div>

        <!-- Body Content -->
        <div class="lh-lg mb-4 fs-6">
            <p>Dear <strong>{{ $student->first_name }}</strong>,</p>

            <p>
                We take immense pleasure in informing you that upon evaluation of your academic qualifications and application credentials, you have been officially granted admission to <strong>JBI University</strong> for the Academic Year <strong>{{ date('Y') }}/{{ date('Y') + 1 }}</strong>.
            </p>

            <div class="p-3 my-3 bg-light rounded border">
                <table class="table table-borderless table-sm mb-0">
                    <tr>
                        <th style="width: 200px;">PROGRAMME ADMITTED:</th>
                        <td class="fw-bold text-primary">{{ $application->programRecord->name ?? $application->program ?? 'Bachelor of Science' }}</td>
                    </tr>
                    <tr>
                        <th>YEAR OF STUDY:</th>
                        <td>Year 1, Semester 1</td>
                    </tr>
                    <tr>
                        <th>STUDENT REG NO:</th>
                        <td class="fw-bold">{{ $student->student_id ?? 'JBI-'.date('Y').'-001' }}</td>
                    </tr>
                    <tr>
                        <th>ADMISSION STATUS:</th>
                        <td><span class="badge bg-success">FULLY ADMITTED & VERIFIED</span></td>
                    </tr>
                </table>
            </div>

            <p>
                As a fully admitted student, you are required to complete your semester course registration via the online Student Portal and adhere strictly to the university's academic calendar and code of conduct.
            </p>

            <p>
                We congratulate you on your acceptance and look forward to partnering with you on your academic journey.
            </p>
        </div>

        <!-- Signatures & Stamp -->
        <div class="row pt-4 mt-4 border-top align-items-center">
            <div class="col-6">
                <div class="p-3 text-center border border-dashed rounded text-primary fw-bold small">
                    <i class="bi bi-patch-check-fill fs-3 d-block mb-1 text-primary"></i>
                    OFFICIAL ACADEMIC SEAL<br>
                    JBI UNIVERSITY ADMISSIONS
                </div>
            </div>
            <div class="col-6 text-end">
                <p class="mb-1 fw-bold">Prof. Academic Registrar</p>
                <p class="text-muted small mb-0">Office of the Academic Registrar<br>JBI University</p>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .no-print, header, nav, .sidebar-wrapper, .navbar {
        display: none !important;
    }
    body {
        background-color: white !important;
        padding: 0 !important;
    }
    .card {
        box-shadow: none !important;
        border: none !important;
        width: 100% !important;
        max-width: 100% !important;
    }
}
</style>
@endsection
