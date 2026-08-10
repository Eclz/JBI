@extends('layouts.app')

@section('title', 'Official Letter of Admission - JBI University')

@section('content')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<div class="container-fluid px-4 py-4">
    <!-- Top Action Bar (Hidden when printing/generating PDF) -->
    <div class="d-flex justify-content-between align-items-center mb-4 no-print" style="max-width: 900px; margin: 0 auto;">
        <div>
            <h5 class="fw-bold text-dark text-uppercase mb-0">
                <i class="bi bi-file-earmark-pdf text-primary me-2"></i>OFFICIAL LETTER OF ADMISSION
            </h5>
            <p class="text-muted small mb-0">Johnson Bible Institute (JBI) University Official Admission Record</p>
        </div>
        <div class="d-flex gap-2">
            <button id="download-pdf-btn" onclick="downloadAdmissionLetterPDF()" class="btn btn-primary fw-bold px-4 py-2 shadow-sm" style="border-radius: 8px;">
                <i class="bi bi-download me-2"></i>DOWNLOAD PDF LETTER
            </button>
            <button onclick="window.print()" class="btn btn-outline-secondary px-3 py-2" style="border-radius: 8px;">
                <i class="bi bi-printer me-1"></i>Print Letter
            </button>
            <a href="{{ route('student.dashboard') }}" class="btn btn-outline-dark px-3 py-2" style="border-radius: 8px;">
                <i class="bi bi-arrow-left me-1"></i>Back to Portal
            </a>
        </div>
    </div>

    <!-- Official Printable Letterhead Card -->
    <div id="admission-letter-card" class="card border-0 shadow-lg mx-auto p-4 p-md-5 bg-white position-relative overflow-hidden" 
         style="max-width: 880px; font-family: 'Times New Roman', Times, serif; color: #1a1a1a; background: #ffffff; border: 12px double #0f2942 !important; border-radius: 4px;">
        
        <!-- Subtle Background Watermark Seal -->
        <div class="position-absolute top-50 start-50 translate-middle pointer-events-none text-center" style="opacity: 0.05; z-index: 0; width: 480px;">
            <img src="{{ asset('images/jbi-logo.webp') }}" alt="Watermark Seal" class="w-100 img-fluid mx-auto d-block">
        </div>

        <!-- Gold Accent Top Bar -->
        <div style="height: 6px; background: linear-gradient(90deg, #0f2942 0%, #c59b27 50%, #0f2942 100%); margin: -3rem -3rem 2rem -3rem;" class="no-pdf-margin"></div>

        <!-- University Header & Letterhead Logo -->
        <div class="text-center pb-3 mb-4 border-bottom border-2 border-dark position-relative" style="z-index: 1;">
            <div class="mb-2 d-flex justify-content-center align-items-center">
                <img src="{{ asset('images/jbi-logo.webp') }}" alt="JBI University Emblem" 
                     style="max-height: 125px; width: auto; object-fit: contain;" 
                     class="mx-auto d-block">
            </div>
            <h1 class="fw-bold text-uppercase mb-0 text-center" style="color: #0f2942; letter-spacing: 2px; font-size: 2.1rem; font-family: 'Georgia', serif;">
                JOHNSON BIBLE INSTITUTE UNIVERSITY
            </h1>
            <div class="text-uppercase fw-bold text-center mb-2" style="color: #c59b27; letter-spacing: 1.5px; font-size: 0.85rem;">
                OFFICE OF THE ACADEMIC REGISTRAR & ADMISSIONS
            </div>
            <div class="small text-muted text-center" style="font-size: 0.85rem; line-height: 1.4; font-family: Arial, sans-serif;">
                91 Progress Road, Lindhaven, Roodepoort, South Africa<br>
                Telephone: +27 67 965 3866 | Email: admissions@johnsonbibleinstitute.com | Website: www.johnsonbibleinstitute.com
            </div>
        </div>

        <!-- Reference Bar & Date -->
        <div class="row mb-4 pb-2 border-bottom text-uppercase small font-monospace" style="font-size: 0.85rem; z-index: 1;">
            <div class="col-7">
                <strong>REF NO:</strong> <span class="text-primary fw-bold">JBI/ADM/{{ date('Y') }}/{{ str_pad($application->id ?? 101, 5, '0', STR_PAD_LEFT) }}</span><br>
                <strong>REGISTRATION NO:</strong> <span class="fw-bold">{{ $student->student_id ?? 'JBI-'.date('Y').'-001' }}</span>
            </div>
            <div class="col-5 text-end">
                <strong>DATE OF ISSUE:</strong> {{ now()->format('F j, Y') }}<br>
                <strong>STATUS:</strong> <span class="badge bg-success px-2 py-1">OFFICIALLY VERIFIED</span>
            </div>
        </div>

        <!-- Recipient Information Address Box -->
        <div class="mb-4 p-3 bg-light rounded border" style="font-family: Arial, sans-serif; font-size: 0.9rem; z-index: 1;">
            <div class="row">
                <div class="col-md-7">
                    <span class="text-muted text-uppercase fw-bold small d-block mb-1" style="color: #0f2942 !important;">ADMITTED APPLICANT DETAILS</span>
                    <h5 class="fw-bold text-dark mb-1" style="font-family: 'Georgia', serif;">{{ strtoupper($student->full_name) }}</h5>
                    <p class="mb-0 text-secondary">
                        Email Address: <strong>{{ $student->email }}</strong><br>
                        Contact Telephone: <strong>{{ $student->phone ?? 'Registered Student' }}</strong>
                    </p>
                </div>
                <div class="col-md-5 text-md-end mt-2 mt-md-0 border-start border-md-start-0">
                    <span class="text-muted text-uppercase fw-bold small d-block mb-1">ENTRY ACADEMIC PERIOD</span>
                    <strong>ACADEMIC YEAR:</strong> {{ date('Y') }}/{{ date('Y') + 1 }}<br>
                    <strong>ENTRY LEVEL:</strong> Year 1, Semester I
                </div>
            </div>
        </div>

        <!-- Title Banner -->
        <div class="text-center my-4 py-2" style="background: #0f2942; color: #ffffff; border-left: 5px solid #c59b27; z-index: 1;">
            <h3 class="fw-bold text-uppercase mb-0" style="letter-spacing: 2px; font-size: 1.35rem; font-family: 'Georgia', serif;">
                OFFICIAL PROVISIONAL LETTER OF ADMISSION
            </h3>
        </div>

        <!-- Main Letter Content -->
        <div class="lh-lg mb-4" style="font-size: 1.05rem; text-align: justify; z-index: 1;">
            <p class="mb-3">Dear <strong>{{ $student->first_name }}</strong>,</p>

            <p class="mb-3">
                On behalf of the Senate and Academic Council of <strong>Johnson Bible Institute (JBI) University</strong>, it is our distinct privilege to inform you that following the review of your academic qualifications and application credentials, you have been officially granted admission to pursue studies at JBI University.
            </p>

            <!-- Admission Particulars Summary Box -->
            <div class="my-4 p-4 border rounded" style="background-color: #f8fafc; border-left: 5px solid #0f2942 !important; font-family: Arial, sans-serif;">
                <h6 class="fw-bold text-uppercase mb-3 text-primary border-bottom pb-2" style="color: #0f2942 !important; font-size: 0.95rem;">
                    OFFICIAL PROGRAMME & REGISTRATION PARTICULARS
                </h6>
                <div class="row g-2 small">
                    <div class="col-sm-4 text-muted font-weight-bold">OFFICIAL PROGRAMME:</div>
                    <div class="col-sm-8 fw-bold text-dark fs-6" style="color: #0f2942 !important;">
                        {{ $application->programRecord->name ?? $application->program ?? 'Bachelor Degree Programme' }}
                    </div>

                    <div class="col-sm-4 text-muted">ACADEMIC DEPARTMENT:</div>
                    <div class="col-sm-8 fw-bold">{{ $student->studentProfile?->department?->name ?? 'School of Academic Studies' }}</div>

                    <div class="col-sm-4 text-muted">MODE OF STUDY:</div>
                    <div class="col-sm-8 fw-bold">Full-Time / Campus & Portal Learning</div>

                    <div class="col-sm-4 text-muted">STUDENT MATRIC NUMBER:</div>
                    <div class="col-sm-8 fw-bold text-success font-monospace">{{ $student->student_id ?? 'JBI-'.date('Y').'-001' }}</div>
                </div>
            </div>

            <p class="mb-3">
                This offer of admission is subject to compliance with the following mandatory conditions:
            </p>

            <ol class="ps-3 mb-4 small" style="font-family: Arial, sans-serif; line-height: 1.6;">
                <li class="mb-2"><strong>Registration & Fee Settlement:</strong> Complete your formal course registration and tuition fee arrangements as stipulated in the university fee structure schedule.</li>
                <li class="mb-2"><strong>Document Verification:</strong> Present original academic certificates, transcripts, and identification documents for physical or digital verification upon request.</li>
                <li class="mb-2"><strong>University Regulations:</strong> Strict adherence to the statutes, regulations, and student code of conduct of JBI University at all times.</li>
            </ol>

            <p class="mb-4">
                We warmly congratulate you on achieving this significant academic milestone and welcome you to the JBI University community.
            </p>
        </div>

        <!-- Official Signatures & Digital Seal Row -->
        <div class="row pt-4 mt-3 border-top align-items-center" style="z-index: 1; font-family: Arial, sans-serif;">
            <div class="col-6">
                <div class="p-3 text-center border border-2 border-primary rounded bg-light" style="max-width: 260px;">
                    <div class="text-uppercase fw-bold text-primary small mb-1" style="letter-spacing: 0.5px;">VERIFIED DIGITAL SEAL</div>
                    <i class="bi bi-shield-check fs-2 text-primary d-block mb-1"></i>
                    <span class="small text-muted d-block font-monospace">JBI-SECURE-STAMP-{{ date('Y') }}</span>
                    <small class="text-success fw-bold" style="font-size: 0.75rem;">OFFICE OF THE REGISTRAR</small>
                </div>
            </div>
            <div class="col-6 text-end">
                <div class="mb-2">
                    <span class="fst-italic fs-4 fw-bold text-primary font-monospace" style="letter-spacing: 2px; font-family: 'Georgia', serif;">Prof. D. K. Miller</span>
                </div>
                <div class="border-top border-dark d-inline-block pt-1 text-end" style="min-width: 200px;">
                    <h6 class="fw-bold text-dark mb-0" style="font-size: 0.95rem;">Academic Registrar</h6>
                    <small class="text-muted d-block">Directorate of Academic Affairs</small>
                    <small class="text-muted d-block">Johnson Bible Institute University</small>
                </div>
            </div>
        </div>

        <!-- Footer Notice -->
        <div class="mt-4 pt-3 border-top text-center text-muted small" style="font-size: 0.75rem; font-family: Arial, sans-serif; z-index: 1;">
            This is an official computer-generated Letter of Admission issued by JBI University. Authenticity can be verified via the Student Portal.
        </div>
    </div>
</div>

<script>
function downloadAdmissionLetterPDF() {
    const downloadBtn = document.getElementById('download-pdf-btn');
    const originalText = downloadBtn.innerHTML;
    downloadBtn.disabled = true;
    downloadBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Generating PDF...';

    const element = document.getElementById('admission-letter-card');
    
    if (typeof html2pdf !== 'undefined') {
        const opt = {
            margin:       [0.2, 0.2, 0.2, 0.2],
            filename:     'JBI_Official_Admission_Letter_{{ Str::slug($student->full_name) }}.pdf',
            image:        { type: 'jpeg', quality: 0.98 },
            html2canvas:  { scale: 2, useCORS: true, logging: false },
            jsPDF:        { unit: 'in', format: 'a4', orientation: 'portrait' }
        };

        html2pdf().set(opt).from(element).save().then(() => {
            downloadBtn.disabled = false;
            downloadBtn.innerHTML = originalText;
        }).catch(err => {
            console.error('PDF Generation Error:', err);
            downloadBtn.disabled = false;
            downloadBtn.innerHTML = originalText;
            window.print();
        });
    } else {
        downloadBtn.disabled = false;
        downloadBtn.innerHTML = originalText;
        window.print();
    }
}
</script>

<style>
@media print {
    .no-print, header, nav, .sidebar-wrapper, .navbar, footer {
        display: none !important;
    }
    body {
        background-color: white !important;
        padding: 0 !important;
        margin: 0 !important;
    }
    #admission-letter-card {
        box-shadow: none !important;
        border: 8px double #0f2942 !important;
        width: 100% !important;
        max-width: 100% !important;
        margin: 0 !important;
    }
}
</style>
@endsection
