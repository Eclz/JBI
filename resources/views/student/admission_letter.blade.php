@extends('layouts.app')

@section('title', 'Official Letter of Admission - JBI University')

@section('content')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<div class="container-fluid px-4 py-3">
    <!-- Top Action Bar (Hidden when printing/generating PDF) -->
    <div class="d-flex justify-content-between align-items-center mb-3 no-print" style="max-width: 800px; margin: 0 auto;">
        <div>
            <h5 class="fw-bold text-dark text-uppercase mb-0">
                <i class="bi bi-file-earmark-pdf text-primary me-2"></i>OFFICIAL LETTER OF ADMISSION
            </h5>
            <p class="text-muted small mb-0">Johnson Bible Institute (JBI) University Official Admission Record</p>
        </div>
        <div class="d-flex gap-2">
            <button id="download-pdf-btn" onclick="downloadAdmissionLetterPDF()" class="btn btn-primary fw-bold px-3 py-1.5 shadow-sm" style="border-radius: 6px;">
                <i class="bi bi-download me-1.5"></i>DOWNLOAD PDF (1 PAGE)
            </button>
            <button onclick="window.print()" class="btn btn-outline-secondary px-3 py-1.5" style="border-radius: 6px;">
                <i class="bi bi-printer me-1"></i>Print Letter
            </button>
            <a href="{{ route('student.dashboard') }}" class="btn btn-outline-dark px-3 py-1.5" style="border-radius: 6px;">
                <i class="bi bi-arrow-left me-1"></i>Back to Portal
            </a>
        </div>
    </div>

    <!-- Official Printable Single-Page A4 Letterhead Card -->
    <div id="admission-letter-card" class="card border-0 shadow-lg mx-auto bg-white position-relative overflow-hidden" 
         style="width: 794px; max-width: 100%; height: 1060px; padding: 2rem 2.5rem; font-family: 'Times New Roman', Times, serif; color: #111111; background: #ffffff; border: 8px double #0f2942 !important; border-radius: 2px; box-sizing: border-box;">
        
        <!-- Subtle Background Watermark Seal -->
        <div class="position-absolute top-50 start-50 translate-middle pointer-events-none text-center" style="opacity: 0.05; z-index: 0; width: 400px;">
            <img src="{{ asset('images/jbi-logo.webp') }}" alt="Watermark Seal" class="w-100 img-fluid mx-auto d-block">
        </div>

        <!-- University Header & Letterhead Logo -->
        <div class="text-center pb-2 mb-3 border-bottom border-2 border-dark position-relative" style="z-index: 1;">
            <div class="mb-1 d-flex justify-content-center align-items-center">
                <img src="{{ asset('images/jbi-logo.webp') }}" alt="JBI University Emblem" 
                     style="height: 75px; width: auto; object-fit: contain;" 
                     class="mx-auto d-block">
            </div>
            <h2 class="fw-bold text-uppercase mb-0 text-center" style="color: #0f2942; letter-spacing: 1.5px; font-size: 1.6rem; font-family: 'Georgia', serif;">
                JOHNSON BIBLE INSTITUTE UNIVERSITY
            </h2>
            <div class="text-uppercase fw-bold text-center mb-1" style="color: #c59b27; letter-spacing: 1px; font-size: 0.75rem;">
                OFFICE OF THE ACADEMIC REGISTRAR & ADMISSIONS
            </div>
            <div class="text-muted text-center" style="font-size: 0.75rem; line-height: 1.3; font-family: Arial, sans-serif;">
                91 Progress Road, Lindhaven, Roodepoort, South Africa | Tel: +27 67 965 3866<br>
                Email: admissions@johnsonbibleinstitute.com | Website: www.johnsonbibleinstitute.com
            </div>
        </div>

        <!-- Reference Bar & Date -->
        <div class="row mb-3 pb-1 border-bottom text-uppercase font-monospace" style="font-size: 0.75rem; z-index: 1;">
            <div class="col-7">
                <strong>REF NO:</strong> <span class="text-primary fw-bold">JBI/ADM/{{ date('Y') }}/{{ str_pad($application->id ?? 101, 5, '0', STR_PAD_LEFT) }}</span><br>
                <strong>REGISTRATION NO:</strong> <span class="fw-bold">{{ $student->student_id ?? 'JBI-'.date('Y').'-001' }}</span>
            </div>
            <div class="col-5 text-end">
                <strong>DATE OF ISSUE:</strong> {{ now()->format('F j, Y') }}<br>
                <strong>STATUS:</strong> <span class="badge bg-success px-2 py-0.5" style="font-size: 0.7rem;">OFFICIALLY VERIFIED</span>
            </div>
        </div>

        <!-- Recipient Information Address Box -->
        <div class="mb-3 p-2.5 bg-light rounded border" style="font-family: Arial, sans-serif; font-size: 0.8rem; z-index: 1;">
            <div class="row">
                <div class="col-7">
                    <span class="text-muted text-uppercase fw-bold d-block mb-0.5" style="color: #0f2942 !important; font-size: 0.7rem;">ADMITTED APPLICANT DETAILS</span>
                    <h6 class="fw-bold text-dark mb-0.5" style="font-family: 'Georgia', serif; font-size: 0.95rem;">{{ strtoupper($student->full_name) }}</h6>
                    <span class="text-secondary d-block">Email: <strong>{{ $student->email }}</strong> | Phone: <strong>{{ $student->phone ?? 'Registered Student' }}</strong></span>
                </div>
                <div class="col-5 text-end border-start">
                    <span class="text-muted text-uppercase fw-bold d-block mb-0.5" style="font-size: 0.7rem;">ENTRY ACADEMIC PERIOD</span>
                    <span><strong>ACADEMIC YEAR:</strong> {{ date('Y') }}/{{ date('Y') + 1 }}</span><br>
                    <span><strong>ENTRY LEVEL:</strong> Year 1, Semester I</span>
                </div>
            </div>
        </div>

        <!-- Title Banner -->
        <div class="text-center my-2 py-1.5" style="background: #0f2942; color: #ffffff; border-left: 4px solid #c59b27; z-index: 1;">
            <h4 class="fw-bold text-uppercase mb-0" style="letter-spacing: 1.5px; font-size: 1.1rem; font-family: 'Georgia', serif;">
                OFFICIAL PROVISIONAL LETTER OF ADMISSION
            </h4>
        </div>

        <!-- Main Letter Content -->
        <div class="mb-2" style="font-size: 0.92rem; line-height: 1.45; text-align: justify; z-index: 1;">
            <p class="mb-2">Dear <strong>{{ $student->first_name }}</strong>,</p>

            <p class="mb-2">
                On behalf of the Senate and Academic Council of <strong>Johnson Bible Institute (JBI) University</strong>, we are pleased to inform you that following the evaluation of your credentials, you have been officially granted admission to pursue studies at JBI University.
            </p>

            <!-- Admission Particulars Summary Box -->
            <div class="my-2 p-2.5 border rounded" style="background-color: #f8fafc; border-left: 4px solid #0f2942 !important; font-family: Arial, sans-serif; font-size: 0.8rem;">
                <div class="row g-1">
                    <div class="col-4 text-muted fw-bold">OFFICIAL PROGRAMME:</div>
                    <div class="col-8 fw-bold text-dark" style="color: #0f2942 !important; font-size: 0.85rem;">
                        {{ $application->programRecord->name ?? $application->program ?? 'Bachelor Degree Programme' }}
                    </div>

                    <div class="col-4 text-muted fw-bold">ACADEMIC DEPARTMENT:</div>
                    <div class="col-8 fw-bold">{{ $student->studentProfile?->department?->name ?? 'School of Academic Studies' }}</div>

                    <div class="col-4 text-muted fw-bold">MODE OF STUDY:</div>
                    <div class="col-8 fw-bold">Full-Time / Campus & Portal Learning</div>

                    <div class="col-4 text-muted fw-bold">STUDENT MATRIC NUMBER:</div>
                    <div class="col-8 fw-bold text-success font-monospace">{{ $student->student_id ?? 'JBI-'.date('Y').'-001' }}</div>
                </div>
            </div>

            <p class="mb-1.5">
                This offer of admission is subject to compliance with the following mandatory conditions:
            </p>

            <ol class="ps-3 mb-2" style="font-family: Arial, sans-serif; font-size: 0.78rem; line-height: 1.35;">
                <li class="mb-1"><strong>Registration & Fee Settlement:</strong> Complete your formal course registration and tuition fee arrangements as stipulated in the university fee schedule.</li>
                <li class="mb-1"><strong>Document Verification:</strong> Present original academic certificates and identification documents for verification upon request.</li>
                <li class="mb-1"><strong>University Regulations:</strong> Strict adherence to the statutes, regulations, and student code of conduct of JBI University at all times.</li>
            </ol>

            <p class="mb-2">
                We warmly congratulate you on achieving this academic milestone and welcome you to JBI University.
            </p>
        </div>

        <!-- Official Signatures & Digital Seal Row -->
        <div class="row pt-2 mt-auto border-top align-items-center" style="z-index: 1; font-family: Arial, sans-serif;">
            <div class="col-6">
                <div class="p-2 text-center border border-2 border-primary rounded bg-light" style="max-width: 220px;">
                    <div class="text-uppercase fw-bold text-primary mb-0.5" style="letter-spacing: 0.5px; font-size: 0.65rem;">VERIFIED DIGITAL SEAL</div>
                    <i class="bi bi-shield-check fs-4 text-primary d-block mb-0.5"></i>
                    <span class="text-muted d-block font-monospace" style="font-size: 0.65rem;">JBI-SECURE-STAMP-{{ date('Y') }}</span>
                    <small class="text-success fw-bold" style="font-size: 0.65rem;">OFFICE OF THE REGISTRAR</small>
                </div>
            </div>
            <div class="col-6 text-end">
                <div class="mb-1">
                    <span class="fst-italic fs-5 fw-bold text-primary font-monospace" style="letter-spacing: 1.5px; font-family: 'Georgia', serif;">Prof. D. K. Miller</span>
                </div>
                <div class="border-top border-dark d-inline-block pt-0.5 text-end" style="min-width: 180px;">
                    <h6 class="fw-bold text-dark mb-0" style="font-size: 0.85rem;">Academic Registrar</h6>
                    <small class="text-muted d-block" style="font-size: 0.7rem;">Directorate of Academic Affairs</small>
                    <small class="text-muted d-block" style="font-size: 0.7rem;">Johnson Bible Institute University</small>
                </div>
            </div>
        </div>

        <!-- Footer Notice -->
        <div class="mt-2 pt-1.5 border-top text-center text-muted" style="font-size: 0.68rem; font-family: Arial, sans-serif; z-index: 1;">
            This is an official computer-generated Letter of Admission issued by JBI University. Authenticity can be verified via the Student Portal.
        </div>
    </div>
</div>

<script>
function downloadAdmissionLetterPDF() {
    const downloadBtn = document.getElementById('download-pdf-btn');
    const originalText = downloadBtn.innerHTML;
    downloadBtn.disabled = true;
    downloadBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Generating Single A4 Page PDF...';

    const element = document.getElementById('admission-letter-card');
    
    if (typeof html2pdf !== 'undefined') {
        const opt = {
            margin:       [0, 0, 0, 0],
            filename:     'JBI_Official_Admission_Letter_{{ Str::slug($student->full_name) }}.pdf',
            image:        { type: 'jpeg', quality: 0.98 },
            html2canvas:  { scale: 2, useCORS: true, scrollY: 0, logging: false },
            jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' },
            pagebreak:    { mode: 'avoid-all' }
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
    @page {
        size: A4 portrait;
        margin: 0mm;
    }
    body * {
        visibility: hidden !important;
    }
    #admission-letter-card, #admission-letter-card * {
        visibility: visible !important;
    }
    #admission-letter-card {
        position: absolute !important;
        left: 0 !important;
        top: 0 !important;
        width: 100% !important;
        max-width: 100% !important;
        height: 100vh !important;
        max-height: 100vh !important;
        margin: 0 !important;
        padding: 1.5rem 2rem !important;
        box-shadow: none !important;
        border: 6px double #0f2942 !important;
        page-break-after: avoid !important;
        page-break-inside: avoid !important;
        page-break-before: avoid !important;
    }
    .no-print, header, nav, .sidebar-wrapper, .navbar, footer, .layout-navbar, .layout-menu {
        display: none !important;
    }
}
</style>
@endsection
