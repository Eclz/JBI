@extends('layouts.app')

@section('title', 'Official Examination Clearance Permit - JBI University')

@section('content')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<div class="container-fluid px-3 py-3">
    <!-- Top Action Bar (Hidden when printing or exporting PDF) -->
    <div class="d-flex justify-content-between align-items-center mb-3 no-print" style="max-width: 820px; margin: 0 auto;">
        <div>
            <h5 class="fw-bold text-dark text-uppercase mb-0">
                <i class="bi bi-shield-check text-success me-2"></i>EXAMINATION CLEARANCE PERMIT
            </h5>
            <p class="text-muted small mb-0">Official Candidate Examination Pass &bull; Tuition Status: Cleared (0.00 Balance)</p>
        </div>
        <div class="d-flex gap-2">
            <button id="download-pdf-btn" onclick="downloadExamPermitPDF()" class="btn btn-primary fw-bold px-3 py-1.5 shadow-sm" style="border-radius: 6px;">
                <i class="bi bi-download me-1.5"></i>Download PDF
            </button>
            <button onclick="window.print()" class="btn btn-outline-secondary px-3 py-1.5" style="border-radius: 6px;">
                <i class="bi bi-printer me-1"></i>Print Permit
            </button>
            <a href="{{ route('student.exams.index') }}" class="btn btn-outline-dark px-3 py-1.5" style="border-radius: 6px;">
                <i class="bi bi-arrow-left me-1"></i>Back to Exams
            </a>
        </div>
    </div>

    <!-- Official Printable Single-Page A4 Examination Permit -->
    <div id="exam-permit-card" class="card border-0 shadow-lg mx-auto bg-white position-relative overflow-hidden" 
         style="width: 800px; max-width: 100%; min-height: 1080px; padding: 2rem 2.5rem; font-family: 'Times New Roman', Times, serif; color: #111111; background: #ffffff; border: 8px double #0f2942 !important; border-radius: 2px; box-sizing: border-box;">
        
        <!-- Background Watermark Seal -->
        <div class="position-absolute top-50 start-50 translate-middle pointer-events-none text-center" style="opacity: 0.04; z-index: 0; width: 450px;">
            <img src="{{ asset('images/jbi-blue.webp') }}" alt="Watermark Seal" class="w-100 img-fluid mx-auto d-block">
        </div>

        <!-- University Header & Crest -->
        <div class="text-center pb-2 mb-2 border-bottom border-2 border-dark position-relative" style="z-index: 1;">
            <div class="mb-1 d-flex justify-content-center align-items-center">
                <img src="{{ asset('images/jbi-blue.webp') }}" alt="JBI University Emblem"
                     style="height: 70px; width: auto; object-fit: contain;" 
                     class="mx-auto d-block">
            </div>
            <h2 class="fw-bold text-uppercase mb-0 text-center" style="color: #0f2942; letter-spacing: 1.5px; font-size: 1.6rem; font-family: 'Georgia', serif;">
                JBI UNIVERSITY
            </h2>
            <div class="text-uppercase fw-bold text-center mb-0.5" style="color: #c59b27; letter-spacing: 1px; font-size: 0.8rem;">
                DIRECTORATE OF ACADEMIC AFFAIRS & EXAMINATIONS
            </div>
            <div class="text-muted text-center" style="font-size: 0.72rem; line-height: 1.3; font-family: Arial, sans-serif;">
                Official Candidate Examination Pass &bull; South Africa &bull; Email: exams@jbiuniversity.com
            </div>
        </div>

        <!-- Title Banner -->
        <div class="text-center my-2 py-1.5 position-relative" style="background: #0f2942; color: #ffffff; border-left: 5px solid #c59b27; z-index: 1;">
            <h4 class="fw-bold text-uppercase mb-0" style="letter-spacing: 1.5px; font-size: 1.05rem; font-family: 'Georgia', serif;">
                OFFICIAL EXAMINATION CLEARANCE PERMIT / CANDIDATE PASS
            </h4>
        </div>

        <!-- Reference & Clearance Verification Bar -->
        <div class="row g-2 mb-3 pb-2 border-bottom text-uppercase font-monospace position-relative" style="font-size: 0.75rem; z-index: 1;">
            <div class="col-6">
                <strong>PERMIT NO:</strong> <span class="text-primary fw-bold">{{ $permitNumber }}</span><br>
                <strong>REGISTRATION NO:</strong> <span class="fw-bold">{{ $student->student_id ?? 'STU-'.date('Y').'-001' }}</span>
            </div>
            <div class="col-6 text-end">
                <strong>DATE OF ISSUE:</strong> {{ now()->format('F j, Y') }}<br>
                <strong>FINANCIAL STATUS:</strong> 
                <span class="badge bg-success px-2 py-0.5 text-white fw-bold" style="font-size: 0.7rem;">
                    <i class="bi bi-check-circle-fill me-1"></i>TUITION CLEARED (0.00 BALANCE)
                </span>
            </div>
        </div>

        <!-- Candidate Information & Photo Card -->
        <div class="mb-3 p-3 bg-light rounded border position-relative" style="font-family: Arial, sans-serif; font-size: 0.82rem; z-index: 1;">
            <div class="row align-items-center">
                <div class="col-8">
                    <span class="text-muted text-uppercase fw-bold d-block mb-1" style="color: #0f2942 !important; font-size: 0.7rem;">CANDIDATE PROFILE</span>
                    <h5 class="fw-bold text-dark mb-1" style="font-family: 'Georgia', serif; font-size: 1.05rem;">
                        {{ strtoupper($student->full_name) }}
                    </h5>
                    <div class="row g-1 text-secondary">
                        <div class="col-6">
                            <span><strong>Admission No:</strong> {{ $studentProfile->admission_number ?? ($student->student_id ?? 'JBI-'.date('Y')) }}</span>
                        </div>
                        <div class="col-6">
                            <span><strong>Department:</strong> {{ $studentProfile->department->name ?? ($studentProfile->programRecord->department->name ?? 'Academic Affairs') }}</span>
                        </div>
                        <div class="col-12 mt-1">
                            <span><strong>Enrolled Programme:</strong> <strong class="text-dark">{{ $studentProfile->programRecord->name ?? ($studentProfile->program ?? 'Degree Programme') }}</strong></span>
                        </div>
                        <div class="col-6 mt-1">
                            <span><strong>Year of Study:</strong> Year {{ $studentProfile->year_of_study ?? 1 }}</span>
                        </div>
                        <div class="col-6 mt-1">
                            <span><strong>Semester:</strong> Semester {{ $studentProfile->current_semester ?? 1 }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-4 text-center border-start ps-3">
                    @if($student->profile_picture)
                        <img src="{{ $student->profile_picture_url }}" alt="Candidate Photo" 
                             style="width: 95px; height: 110px; object-fit: cover; border: 2px solid #0f2942; border-radius: 4px;" class="shadow-sm">
                    @else
                        <div class="d-inline-flex flex-column align-items-center justify-content-center bg-white border border-2 border-dark rounded p-2 shadow-sm" style="width: 95px; height: 110px;">
                            <i class="bi bi-person-fill fs-1 text-secondary mb-0"></i>
                            <span style="font-size: 0.6rem;" class="fw-bold text-muted text-uppercase">Photo ID</span>
                        </div>
                    @endif
                    <div class="small fw-bold text-success mt-1" style="font-size: 0.65rem;">
                        <i class="bi bi-shield-fill-check me-1"></i>VERIFIED CANDIDATE
                    </div>
                </div>
            </div>
        </div>

        <!-- Registered Examination Courses Section -->
        <div class="mb-3 position-relative" style="z-index: 1;">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <h6 class="fw-bold text-uppercase mb-0" style="color: #0f2942; font-size: 0.85rem; font-family: 'Georgia', serif;">
                    APPROVED REGISTERED COURSES FOR EXAMINATION
                </h6>
                <span class="badge bg-secondary" style="font-family: Arial, sans-serif; font-size: 0.7rem;">
                    {{ $enrolledCourses->count() }} Course(s) Cleared
                </span>
            </div>

            <div class="table-responsive border border-dark rounded-1">
                <table class="table table-bordered table-sm mb-0 align-middle" style="font-family: Arial, sans-serif; font-size: 0.78rem;">
                    <thead style="background: #0f2942; color: #ffffff;">
                        <tr>
                            <th class="text-center" style="width: 35px;">#</th>
                            <th style="width: 105px;">Course Code</th>
                            <th>Course Title</th>
                            <th class="text-center" style="width: 55px;">Credits</th>
                            <th class="text-center" style="width: 95px;">Status</th>
                            <th class="text-center" style="width: 140px;">Invigilator Sign</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($enrolledCourses as $index => $enrollment)
                            @php
                                $course = $enrollment->course;
                            @endphp
                            <tr>
                                <td class="text-center fw-bold">{{ $index + 1 }}</td>
                                <td><code class="text-dark fw-bold">{{ $course->code ?? 'CRS-'.($index+1) }}</code></td>
                                <td class="fw-semibold text-dark">{{ $course->name ?? $course->title ?? 'Course Title' }}</td>
                                <td class="text-center">{{ $course->credits ?? $course->credit_units ?? 3 }}</td>
                                <td class="text-center">
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success px-1.5 py-0.5" style="font-size: 0.68rem;">
                                        CLEARED
                                    </span>
                                </td>
                                <td class="text-center" style="height: 32px; border-bottom: 1px dotted #999;">
                                    <span class="text-muted" style="font-size: 0.65rem;">&nbsp;</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-3 text-muted">
                                    No registered course units found. All active semester courses cleared for sitting.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Examination Instructions to Candidate -->
        <div class="mb-3 p-2.5 bg-white border border-secondary rounded position-relative" style="font-family: Arial, sans-serif; font-size: 0.73rem; line-height: 1.35; z-index: 1;">
            <strong class="d-block text-dark text-uppercase mb-1" style="color: #0f2942 !important;">
                <i class="bi bi-info-circle-fill text-primary me-1"></i>IMPORTANT RULES & INSTRUCTIONS FOR CANDIDATES:
            </strong>
            <ol class="mb-0 ps-3">
                <li>This official Examination Permit must be presented alongside a valid JBI University Student Identity Card before entry into any examination room or assessment interface.</li>
                <li>The candidate is qualified and authorized to sit <strong>strictly for the course units listed on this clearance permit</strong>.</li>
                <li>Possession of unauthorized materials, books, programmable calculators, smart watches, or mobile phones inside the examination venue constitutes severe academic malpractice.</li>
                <li>Candidates must be seated at least 15 minutes prior to the commencement of each paper. Impersonation will result in immediate disqualification and criminal prosecution.</li>
            </ol>
        </div>

        <!-- Verification Signatures & University Seals -->
        <div class="row pt-2 mt-auto border-top border-dark align-items-end position-relative" style="z-index: 1;">
            <div class="col-4 text-center">
                <div class="mb-1" style="height: 40px; display: flex; align-items: flex-end; justify-content: center;">
                    <span style="font-family: 'Brush Script MT', cursive; font-size: 1.6rem; color: #0f2942;">Dr. K. M. Ndlovu</span>
                </div>
                <div class="border-top border-dark pt-1">
                    <strong class="d-block" style="font-size: 0.75rem;">DR. K. M. NDLOVU</strong>
                    <span class="text-muted" style="font-size: 0.68rem; font-family: Arial, sans-serif;">Academic Registrar & Examinations</span>
                </div>
            </div>

            <div class="col-4 text-center">
                <div class="d-inline-flex flex-column align-items-center justify-content-center p-1 rounded-circle border border-2 border-danger text-danger shadow-sm mx-auto" 
                     style="width: 82px; height: 82px; background: rgba(220, 53, 69, 0.03); transform: rotate(-5deg);">
                    <i class="bi bi-patch-check-fill fs-4 text-danger mb-0"></i>
                    <span class="fw-bold" style="font-size: 0.55rem; letter-spacing: 0.5px;">JBI AUDIT</span>
                    <span class="fw-bold text-uppercase" style="font-size: 0.5rem;">CLEARED</span>
                </div>
                <div class="text-muted mt-1" style="font-size: 0.62rem; font-family: Arial, sans-serif;">
                    OFFICIAL SECURITY EMBOSSMENT
                </div>
            </div>

            <div class="col-4 text-center">
                <div class="mb-1" style="height: 40px; display: flex; align-items: flex-end; justify-content: center;">
                    <span style="font-family: 'Brush Script MT', cursive; font-size: 1.6rem; color: #198754;">A. S. Mokoena</span>
                </div>
                <div class="border-top border-dark pt-1">
                    <strong class="d-block" style="font-size: 0.75rem;">MR. A. S. MOKOENA</strong>
                    <span class="text-muted" style="font-size: 0.68rem; font-family: Arial, sans-serif;">University Bursar / Finance Directorate</span>
                </div>
            </div>
        </div>

        <!-- Security Verification Footer -->
        <div class="text-center mt-2 pt-1 border-top position-relative" style="font-family: Arial, sans-serif; font-size: 0.65rem; color: #666666; z-index: 1;">
            Permit Security Digest: <code>{{ strtoupper(sha1($permitNumber . $student->id . date('Y-m-d'))) }}</code> &bull; Verified Online via JBI Student Academic Portal
        </div>

    </div>
</div>

<script>
function downloadExamPermitPDF() {
    const btn = document.getElementById('download-pdf-btn');
    const originalText = btn.innerHTML;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Generating...';
    btn.disabled = true;

    const element = document.getElementById('exam-permit-card');
    const opt = {
        margin: [5, 5, 5, 5],
        filename: 'JBI_Examination_Permit_{{ $student->student_id ?? $student->id }}.pdf',
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2, useCORS: true, logging: false },
        jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
    };

    html2pdf().set(opt).from(element).save().then(() => {
        btn.innerHTML = originalText;
        btn.disabled = false;
    }).catch(err => {
        console.error(err);
        btn.innerHTML = originalText;
        btn.disabled = false;
        window.print();
    });
}
</script>

<style>
@media print {
    body * {
        visibility: hidden;
    }
    .no-print, nav, header, footer, .sidebar, .navbar, .btn {
        display: none !important;
    }
    #exam-permit-card, #exam-permit-card * {
        visibility: visible;
    }
    #exam-permit-card {
        position: absolute;
        left: 0;
        top: 0;
        width: 100% !important;
        max-width: 100% !important;
        margin: 0 !important;
        padding: 1.5rem !important;
        box-shadow: none !important;
        border: 4px double #0f2942 !important;
    }
}
</style>
@endsection
