"""
Part 2: Master & Subsystem Use Cases, Sequence Diagrams & Detailed Message Traces, and Data Flow Architecture (DFD Levels 0, 1, 2)
"""

import docx
from docx.shared import Inches, Pt, RGBColor
from doc_builder_part1 import (
    COLOR_NAVY, COLOR_BLUE, COLOR_SLATE, COLOR_BODY, COLOR_MUTED, COLOR_AMBER, COLOR_GREEN,
    add_heading_1, add_heading_2, add_heading_3, add_heading_4,
    add_paragraph, add_bullet, add_callout, add_image_with_caption, add_styled_table
)

def build_part_2(doc):
    print("Building Part 2: Use Cases, Sequence Traces, and Data Flow Architecture...")

    # ==========================================
    # SECTION 4: SYSTEM USE CASE SPECIFICATIONS
    # ==========================================
    add_heading_1(doc, "4. Master & Subsystem Use Case Specifications")
    add_paragraph(doc, "The JBI University Management System encompasses a wide array of operational use cases spanning the academic, administrative, financial, and democratic governance spheres of the university. The system boundaries are strictly defined to provide autonomous service delivery to students, faculty, and administrators while maintaining immutable audit trails.")

    add_heading_2(doc, "4.1. Master Use Case Model")
    add_paragraph(doc, "The master use case diagram depicts the unified functional landscape across all 11 recognized university actors. Each actor interacts with authorized functional clusters governed by the multi-role RBAC security kernel.")

    add_image_with_caption(doc, "diagram_use_case_master.png", "Master University System Use Case Diagram (Covering 11 University Actors)")

    add_heading_2(doc, "4.2. Detailed Tabular Use Case Specifications")
    add_paragraph(doc, "Below are formal, exhaustive specifications for the eight primary architectural use cases underpinning university operations. Each specification details preconditions, triggers, main execution flows, exception handlings, and database postconditions.")

    # UC-01
    add_heading_3(doc, "UC-01: Online Application Submission & Credential Verification")
    uc01_headers = ["Attribute", "Specification Details"]
    uc01_data = [
        ["Use Case ID & Name", "UC-01: Online Application Submission & Automated Credential Verification"],
        ["Primary Actor", "Prospective Student Applicant"],
        ["Secondary Actors", "Registrar / Admissions Officer, Finance Officer"],
        ["Trigger", "Applicant clicks 'Submit Application' on the public admissions web portal."],
        ["Preconditions", "1. Active academic year and intake semester configured in academic_years and semesters tables.\n2. Applicant has registered a verified email address and basic biodata."],
        ["Main Success Scenario", "1. Applicant logs into Admissions Portal and selects an academic program.\n2. System validates that the application deadline has not elapsed.\n3. Applicant enters previous academic qualifications (O-Level / A-Level / Diploma grades) and uploads scanned certified certificates.\n4. System computes candidate eligibility index against program minimum entry criteria.\n5. Applicant pays non-refundable application fee via PRN gateway.\n6. System updates application_status to 'Under Review' and notifies Admissions Directorate.\n7. Admissions Officer audits uploaded credentials against national verification standards.\n8. Admissions Officer issues approval; system marks application as 'Admitted' and generates admission letter PDF."],
        ["Extensions & Exceptions", "4a. Qualifications fail minimum criteria: System displays eligibility warning and suggests alternative available diploma/certificate programs.\n5a. Payment gateway timeout: Transaction marked as 'Pending Reconciliation'; webhooks poll gateway until confirmation is received.\n7a. Forged or unreadable documents: Officer sets status to 'Clarification Requested' and triggers automated SMS/Email notification to applicant."],
        ["Postconditions", "1. Record created in applications table with status = 'Admitted'.\n2. Immutable audit trail entry logged in audit_logs.\n3. Admission Letter PDF archived in secure storage disk."],
        ["Security & Permissions", "Public applicant token with restricted scope; Admissions Officer requires 'admissions.review' permission."]
    ]
    add_styled_table(doc, uc01_headers, uc01_data, [Inches(1.8), Inches(4.7)])

    # UC-02
    add_heading_3(doc, "UC-02: Student Matriculation & Dynamic Account Activation")
    uc02_headers = ["Attribute", "Specification Details"]
    uc02_data = [
        ["Use Case ID & Name", "UC-02: Student Matriculation & Dynamic Account Activation"],
        ["Primary Actor", "Registrar / Admissions Officer"],
        ["Secondary Actors", "Admitted Student, Bursar"],
        ["Trigger", "Admitted applicant accepts admission offer and meets initial tuition commitment."],
        ["Preconditions", "1. Application status is 'Admitted'.\n2. Minimum tuition commitment percentage (e.g. 50%) is verified in fee_records."],
        ["Main Success Scenario", "1. Registrar reviews candidate in the Matriculation Queue.\n2. System triggers the Matriculation Engine within a database transaction.\n3. System allocates a unique institutional registration number (e.g., ADM-2026-0042) using an atomic sequential lock.\n4. System provisions a student record in users table with role 'student' and creates corresponding entry in student_profiles.\n5. System attaches default program curriculum and creates initial semester ledger.\n6. System generates institutional email credentials and dispatches activation link via SMS/Email.\n7. Student logs in, completes password reset, and activates multi-factor authentication."],
        ["Extensions & Exceptions", "2a. Tuition commitment unmet: System disallows matriculation until Bursary marks clearance flag.\n3a. Concurrency collision on registration number: Sequence generator locks counter row with 'SELECT ... FOR UPDATE' to ensure deterministic zero-gap allocation."],
        ["Postconditions", "1. User account provisioned in users table with role_id for Student.\n2. Student profile established in student_profiles.\n3. Status in applications transitioned to 'Matriculated'."],
        ["Security & Permissions", "Requires 'admissions.matriculate' permission granted to Registrar and SuperAdmin."]
    ]
    add_styled_table(doc, uc02_headers, uc02_data, [Inches(1.8), Inches(4.7)])

    # UC-03
    add_heading_3(doc, "UC-03: Semester Course Registration & Prerequisite Validation")
    uc03_headers = ["Attribute", "Specification Details"]
    uc03_data = [
        ["Use Case ID & Name", "UC-03: Semester Course Registration & Prerequisite Validation"],
        ["Primary Actor", "Student Body"],
        ["Secondary Actors", "Head of Department (HOD), Academic Advisor"],
        ["Trigger", "Student initiates course registration for an active academic semester."],
        ["Preconditions", "1. Semester registration window is open.\n2. Student has cleared tuition balance threshold (e.g., 60% paid).\n3. Student is not on academic suspension or expulsion."],
        ["Main Success Scenario", "1. Student accesses Student Portal -> Course Registration.\n2. System displays eligible core and elective courses based on student's program and level.\n3. Student selects desired courses and clicks 'Validate & Register'.\n4. For each selected course, system checks course_prerequisites table against student's historical passing grades.\n5. System validates total credit units against maximum allowable workload (e.g., 24 Credit Units).\n6. System writes pending records into course_enrollments table.\n7. HOD reviews and bulk-approves course registration batches.\n8. System updates enrollment status to 'Enrolled' and populates student LMS course list."],
        ["Extensions & Exceptions", "4a. Prerequisite not passed: System flags failed prerequisite course and blocks registration.\n5a. Credit overload exceeded: Student must submit an Academic Overload Petition to the Dean before approval.\n7a. Class capacity full: Student placed on waitlist or offered alternate elective."],
        ["Postconditions", "1. Records created in course_enrollments with status = 'Enrolled'.\n2. Student enrolled in corresponding LMS module sections."],
        ["Security & Permissions", "Student authorized only for own student_id; HOD requires 'department_courses.moderate' permission."]
    ]
    add_styled_table(doc, uc03_headers, uc03_data, [Inches(1.8), Inches(4.7)])

    # UC-04
    add_heading_3(doc, "UC-04: Continuous Assessment (CA) Submission & Daily QR Attendance Generation")
    uc04_headers = ["Attribute", "Specification Details"]
    uc04_data = [
        ["Use Case ID & Name", "UC-04: Continuous Assessment Submission & Daily QR Attendance"],
        ["Primary Actor", "Academic Lecturer"],
        ["Secondary Actors", "Student Body, HOD"],
        ["Trigger", "Lecturer conducts a lecture or grades continuous coursework / midterm assignments."],
        ["Preconditions", "1. Course is assigned to lecturer in course_lecturer table.\n2. Current date falls within the semester instructional calendar."],
        ["Main Success Scenario", "1. Lecturer opens Lecturer Portal -> Course Dashboard -> Attendance.\n2. Lecturer clicks 'Generate Session QR Code'.\n3. System produces a cryptographically signed, time-expiring (60-second) QR code containing session_id and HMAC salt.\n4. Attending students scan the projected QR code using their student portal app.\n5. System verifies student geo-location/IP, checks enrollment, and logs timestamped attendance record.\n6. Lecturer administers quizzes or enters continuous assessment marks (out of 40% weighting).\n7. System computes aggregate continuous assessment scores and flags students with <75% attendance for examination barring."],
        ["Extensions & Exceptions", "3a. QR code expired: Re-generation occurs dynamically on projector screen without reloading session.\n5a. Proxy attendance attempt (duplicate device UUID): System detects multiple scans from identical device fingerprint and rejects second scan.\n6a. Grade exceeds maximum mark: Real-time form validation rejects marks > 40."],
        ["Postconditions", "1. Timestamped attendance rows committed to attendance_logs.\n2. Continuous assessment scores stored in course_assessments and draft grades table."],
        ["Security & Permissions", "Requires 'courses.teach' permission and ownership check (auth()->id() == course.lecturer_id)."]
    ]
    add_styled_table(doc, uc04_headers, uc04_data, [Inches(1.8), Inches(4.7)])

    # UC-05
    add_heading_3(doc, "UC-05: Examination Grading, HOD Moderation & Dean Executive Endorsement")
    uc05_headers = ["Attribute", "Specification Details"]
    uc05_data = [
        ["Use Case ID & Name", "UC-05: Examination Grading, HOD Moderation & Dean Executive Endorsement"],
        ["Primary Actor", "Academic Lecturer"],
        ["Secondary Actors", "Head of Department (HOD), Dean of Faculty, Senate Board"],
        ["Trigger", "End-of-semester examination scripts graded by course lecturer."],
        ["Preconditions", "1. Final examination period completed.\n2. Enrolled students possess verified examination cards."],
        ["Main Success Scenario", "1. Lecturer inputs final exam marks (out of 60%) into the Grading Portal.\n2. System computes composite grade (CA 40% + Exam 60%), maps grade points (0.0 to 5.0), and assigns letter grade (A-F).\n3. Lecturer reviews the departmental grade sheet and clicks 'Submit to HOD for Moderation'.\n4. HOD receives notification, reviews distribution curves, checks for grade inflation/anomalies, and signs off moderation.\n5. Dean of Faculty inspects faculty-wide consolidated results, reviews grade distributions, and applies official Dean Endorsement.\n6. Dean submits endorsed results to Senate Examination Board.\n7. Upon Senate approval, system publishes results to student portal, updates student CGPAs, and locks grades against modification."],
        ["Extensions & Exceptions", "4a. HOD detects grading anomaly: HOD returns grade sheet to lecturer with mandatory revision comments; status reverts to 'Draft'.\n5a. Dean requests re-moderation: Results remanded to departmental committee for sample remarking.\n7a. Student files grade appeal: Trigger for formal remarking workflow governed by Senate regulations."],
        ["Postconditions", "1. Final grades saved in grades table with status = 'Published'.\n2. student_profiles.cgpa and cumulative_credits recomputed atomically.\n3. Immutable audit snapshot stored in exam_audit_trails."],
        ["Security & Permissions", "3-Tier Role Validation: Lecturer -> HOD ('department_grades.moderate') -> Dean ('dean_administration.endorse')."]
    ]
    add_styled_table(doc, uc05_headers, uc05_data, [Inches(1.8), Inches(4.7)])

    # UC-06
    add_heading_3(doc, "UC-06: Student Union E-Voting with Automated Academic Retake Disqualification")
    uc06_headers = ["Attribute", "Specification Details"]
    uc06_data = [
        ["Use Case ID & Name", "UC-06: Student Union E-Voting with Automated Retake Vetting Engine"],
        ["Primary Actor", "Student Body (Candidate / Voter)"],
        ["Secondary Actors", "Electoral Commission Trustee, Dean of Students, Registrar"],
        ["Trigger", "Student submits nomination for student union leadership OR casts vote during polling."],
        ["Preconditions", "1. Voting session is active with valid start_time and end_time.\n2. Student is fully registered in current semester with zero financial holds."],
        ["Main Success Scenario", "1. Student candidate submits nomination for an executive post (e.g., Guild President, Guild Speaker).\n2. System invokes Academic Vetting Service: queries grades and course_enrollments tables for failing grades (status = 'retake' or score < 50).\n3. If active retakes exist, system automatically rejects nomination with a detailed statutory citation.\n4. If cleared, Electoral Commission approves candidate manifesto.\n5. On election day, registered student enters secure E-Voting booth.\n6. System verifies voter has not previously voted in this session (voter_registry check).\n7. Student selects candidates and submits secret ballot.\n8. System records anonymized cryptographic ballot in votes table, marks voter as 'Voted', and updates real-time tally."],
        ["Extensions & Exceptions", "2a. Retake identified: System outputs 'DISQUALIFIED: Under Section 14(b) of University Guild Constitution, candidates with active academic retakes are ineligible'.\n6a. Duplicate vote attempt: System detects prior vote timestamp and aborts transaction with HTTP 403 Forbidden.\n8a. Network disconnection during submission: Transaction rolled back; student prompted to resubmit."],
        ["Postconditions", "1. Vote recorded with SHA-256 hash in votes table.\n2. Voter status updated to 'Voted' in voter_participations table.\n3. Zero linkage maintained between voter identity and candidate selection (secret ballot)."],
        ["Security & Permissions", "Strict token-based voting booth; Electoral Trustee requires 'elections.manage' clearance."]
    ]
    add_styled_table(doc, uc06_headers, uc06_data, [Inches(1.8), Inches(4.7)])

    # UC-07
    add_heading_3(doc, "UC-07: PRN Payment Invoice Generation & Real-Time Bank Settlement")
    uc07_headers = ["Attribute", "Specification Details"]
    uc07_data = [
        ["Use Case ID & Name", "UC-07: PRN Payment Invoice Generation & Real-Time Bank Settlement"],
        ["Primary Actor", "Student Body"],
        ["Secondary Actors", "Bursar / Finance Officer, Commercial Banking Gateway"],
        ["Trigger", "Student requests payment reference for tuition, functional fees, accommodation, or graduation."],
        ["Preconditions", "1. Student has an active ledger invoice in student_ledgers.\n2. Bank API integration endpoints are active and reachable."],
        ["Main Success Scenario", "1. Student selects fee items to pay in Student Portal -> Financial Hub.\n2. System computes total invoice amount, validates fee components, and generates a unique Payment Reference Number (PRN).\n3. System registers PRN with the commercial banking switch via secure REST API.\n4. Student presents PRN at any partnered commercial bank branch, agent, or mobile money platform.\n5. Bank processes payment and dispatches an asynchronous signed webhook callback to the JBI Finance endpoint.\n6. JBI system verifies webhook HMAC signature, validates transaction amount against registered PRN, and updates fee_records.\n7. System recalculates student financial balance, unlocks registration/examination holds, and sends receipt notification."],
        ["Extensions & Exceptions", "3a. Banking switch unavailable: System falls back to offline PRN format and queues synchronization job.\n5a. Underpayment received: System records partial payment, updates remaining balance, and maintains partial hold.\n6a. Invalid webhook signature: System logs security anomaly, alerts IT SuperAdmin, and rejects transaction."],
        ["Postconditions", "1. Payment record committed to fee_records with status = 'Cleared'.\n2. Student ledger balance decremented.\n3. Examination and course registration holds lifted if threshold met."],
        ["Security & Permissions", "HMAC-SHA256 signature verification on callbacks; Finance Officer requires 'finance.reconcile' permission."]
    ]
    add_styled_table(doc, uc07_headers, uc07_data, [Inches(1.8), Inches(4.7)])

    # UC-08
    add_heading_3(doc, "UC-08: Campus Facility & Residence Hall Allocation Management")
    uc08_headers = ["Attribute", "Specification Details"]
    uc08_data = [
        ["Use Case ID & Name", "UC-08: Campus Facility & Residence Hall Allocation Management"],
        ["Primary Actor", "Estates & Facilities Manager"],
        ["Secondary Actors", "Student Body, Academic Lecturer"],
        ["Trigger", "Student applies for campus hostel accommodation OR Lecturer books a laboratory / lecture theater."],
        ["Preconditions", "1. Residence halls and room inventories configured in halls_of_residence and hostel_rooms tables.\n2. Academic timetable requirements published."],
        ["Main Success Scenario", "1. Student navigates to Student Portal -> Accommodation and selects preferred hall and room type.\n2. System verifies room capacity and student gender eligibility.\n3. Student pays accommodation fee via PRN; system commits room reservation.\n4. Estates Manager reviews hostel allocations and issues room check-in keys.\n5. Simultaneously, Estates Manager schedules lecture halls and computer laboratories based on academic course enrollments.\n6. System detects and prevents room scheduling conflicts through automated overlap validation."],
        ["Extensions & Exceptions", "2a. Hall capacity exhausted: Student alerted and offered alternative hall or placed on waiting list.\n6a. Room schedule conflict detected: System highlights double-booking and proposes adjacent available facilities."],
        ["Postconditions", "1. Room occupancy updated in hostel_allocations table.\n2. Room scheduling committed in facility_bookings table."],
        ["Security & Permissions", "Requires 'facilities.manage' permission granted to Estates Manager and SuperAdmin."]
    ]
    add_styled_table(doc, uc08_headers, uc08_data, [Inches(1.8), Inches(4.7)])

    # ==========================================
    # SECTION 5: SEQUENCE WORKFLOWS & DETAILED MESSAGE TRACE TABLES
    # ==========================================
    add_heading_1(doc, "5. System Sequence Workflows & Message Trace Specifications")
    add_paragraph(doc, "Sequence diagrams capture the precise temporal order of messages, method invocations, database transactions, and cryptographic validations executing across system boundaries. The following four sequence specifications model the most critical operational workflows in the university.")

    # 5.1 Admission Sequence
    add_heading_2(doc, "5.1. Student Admission & Matriculation Workflow")
    add_paragraph(doc, "This sequence governs the entire lifecycle of prospective student onboarding, from initial portal submission to programmatic generation of student registration credentials and ledger provisioning.")

    add_image_with_caption(doc, "diagram_sequence_admission.png", "Sequence Diagram: Student Admission & Matriculation Pipeline")

    add_paragraph(doc, "Below is the complete message trace specification detailing every boundary, method call, payload, and database transaction involved in the admission pipeline:")

    trace_adm_headers = ["Step #", "Source Boundary", "Target Boundary / Service", "Method / Invocation", "Payload & Data Attributes", "Database State & Audit Effect"]
    trace_adm_data = [
        ["1", "Applicant (Browser)", "Public Admissions Portal", "POST /admissions/apply", "biodata, program_id, qualifications, certificate_blobs", "Uploads files to storage/app/admissions; creates pending record."],
        ["2", "Admissions Portal", "EligibilityEngine", "evaluateQualifications()", "qualifications[], program.minimum_cutoffs", "Evaluates weighted score against minimum program threshold."],
        ["3", "EligibilityEngine", "Admissions Portal", "return eligibilityStatus", "status = ELIGIBLE, score = 84.5", "Logs eligibility scoring telemetry in application_evaluations."],
        ["4", "Admissions Portal", "PRN Gateway", "generateApplicationPRN()", "applicant_id, amount = 50.00, currency = USD", "Registers PRN in fee_invoices; returns PRN reference string."],
        ["5", "Applicant", "Banking Switch", "payApplicationFee()", "PRN, paymentMethod = MobileMoney/Card", "Bank captures funds and issues transactional settlement token."],
        ["6", "Banking Switch", "Admissions Webhook", "POST /api/webhooks/prn", "prn, amount, hmac_signature, bank_ref", "Verifies signature; updates applications.payment_status = 'PAID'."],
        ["7", "Admissions Officer", "Registrar Dashboard", "GET /admissions/queue", "filters: [status='PAID', program_id=12]", "Retrieves pending application queue for documentary verification."],
        ["8", "Admissions Officer", "AdmissionsController", "POST /admissions/{id}/admit", "application_id, admission_category = 'Direct'", "Sets applications.status = 'Admitted'; generates Admission Letter."],
        ["9", "Admissions Officer", "MatriculationService", "matriculateStudent()", "application_id, academic_year_id", "ACID Transaction: Allocates reg_no, creates users row, student_profiles row."],
        ["10", "MatriculationService", "LedgerService", "initializeStudentLedger()", "user_id, program_id, fee_structure_id", "Generates semester billing invoice in student_ledgers."],
        ["11", "MatriculationService", "NotificationService", "sendActivationNotice()", "user.email, temp_password, portal_url", "Dispatches transactional onboarding email & SMS."]
    ]
    add_styled_table(doc, trace_adm_headers, trace_adm_data, [Inches(0.6), Inches(1.3), Inches(1.4), Inches(1.5), Inches(1.7)])

    # 5.2 Multi-Role Sequence
    add_heading_2(doc, "5.2. Multi-Role Authorization & Permission Resolution Pipeline")
    add_paragraph(doc, "This sequence illustrates how the JBI authorization kernel evaluates incoming HTTP requests for users holding multiple concurrent appointments (e.g., Lecturer serving as Dean).")

    add_paragraph(doc, "Below is the complete message trace specification detailing every boundary, method call, payload, and database transaction involved in the multi-role authorization pipeline:")

    trace_mr_headers = ["Step #", "Source Boundary", "Target Boundary / Service", "Method / Invocation", "Payload & Data Attributes", "Database State & Audit Effect"]
    trace_mr_data = [
        ["1", "Authenticated User", "Web Browser / Route", "GET /faculty/dean/budget", "Bearer token / Laravel session_id", "User requests Dean divisional financial summary."],
        ["2", "Route Dispatcher", "Authenticate Middleware", "handle($request)", "session_cookie", "Resolves user model instance via Auth::guard('web')."],
        ["3", "Authenticate", "MultiRoleCheck Middleware", "handle($request, 'dean')", "required_role = 'dean', user = User#42", "Initiates multi-role evaluation pipeline."],
        ["4", "MultiRoleCheck", "User Model", "hasRole('dean')", "role_slug = 'dean'", "Queries primary role users.role AND pivot role_user."],
        ["5", "User Model", "DynamicResolver", "isDean()", "user_id = 42", "Checks faculties.where('dean_id', 42).exists(). Returns true."],
        ["6", "MultiRoleCheck", "Gate Kernel", "allows('dean_administration.view')", "user, ability = 'view', module = 'dean_admin'", "Invokes composite permission evaluator across all roles."],
        ["7", "Gate Kernel", "RolePermission Model", "hasPermission()", "aggregated_role_ids: [2, 7]", "Evaluates joined permissions across Lecturer and Dean roles."],
        ["8", "MultiRoleCheck", "DeanController", "index()", "faculty_id = 3", "Access granted; Controller queries faculty budget balances."],
        ["9", "DeanController", "AuditLogger", "logAccess()", "user_id = 42, role_context = 'dean', action = 'view_budget'", "Writes structured log to audit_logs table."]
    ]
    add_styled_table(doc, trace_mr_headers, trace_mr_data, [Inches(0.6), Inches(1.3), Inches(1.4), Inches(1.5), Inches(1.7)])

    # 5.3 Grading Sequence
    add_heading_2(doc, "5.3. Academic Assessment, Moderation & Dean Approval Sequence")
    add_paragraph(doc, "The grading sequence implements a 3-tier checks-and-balances workflow ensuring no grade can be released to students or printed on transcripts without multi-stakeholder moderation and executive dean signoff.")

    add_image_with_caption(doc, "diagram_sequence_grading.png", "Sequence Diagram: Continuous Assessment, Examination Grading & Dean Endorsement")

    add_paragraph(doc, "Below is the complete message trace specification detailing every boundary, method call, payload, and database transaction involved in the academic grading and moderation lifecycle:")

    trace_gr_headers = ["Step #", "Source Boundary", "Target Boundary / Service", "Method / Invocation", "Payload & Data Attributes", "Database State & Audit Effect"]
    trace_gr_data = [
        ["1", "Course Lecturer", "Grading Controller", "POST /grades/batch-store", "course_id, grades: [{student_id, ca_mark, exam_mark}]", "Inserts/updates draft rows in grades table with status = 'DRAFT'."],
        ["2", "Grading Controller", "GradingEngine", "computeFinalMarks()", "ca_mark (40%), exam_mark (60%)", "Calculates total = CA + Exam; maps grade_point (0-5) and letter (A-F)."],
        ["3", "Course Lecturer", "Grading Controller", "POST /grades/submit-moderation", "course_id, lecturer_remarks", "Transitions grades.status from 'DRAFT' to 'SUBMITTED_TO_HOD'."],
        ["4", "HOD", "Department Moderation UI", "GET /grades/department-review", "department_id = 5, semester_id = 2", "Retrieves grade distributions, class average, standard deviation."],
        ["5", "HOD", "ModerationService", "approveDepartmentGrades()", "course_id, hod_signoff_notes, hod_id = 18", "Updates grades.status = 'MODERATED'; timestamps hod_approved_at."],
        ["6", "Dean of Faculty", "Faculty Dean Portal", "GET /faculty/dean/grades", "faculty_id = 2, status = 'MODERATED'", "Loads consolidated divisional grade sheets for faculty audit."],
        ["7", "Dean of Faculty", "DeanApprovalService", "endorseDivisionalGrades()", "faculty_id, dean_id = 42, endorsement_token", "Transitions grades.status = 'ENDORSED_BY_DEAN'; generates faculty seal."],
        ["8", "DeanApprovalService", "SenateBoard Service", "submitToSenate()", "batch_id, endorsement_record", "Submits consolidated faculty results to Academic Senate Board."],
        ["9", "Senate Board", "SenateService", "publishFinalResults()", "batch_id, senate_resolution_no", "Updates grades.status = 'PUBLISHED'; triggers GPA recomputation in student_profiles."]
    ]
    add_styled_table(doc, trace_gr_headers, trace_gr_data, [Inches(0.6), Inches(1.3), Inches(1.4), Inches(1.5), Inches(1.7)])

    # 5.4 E-Voting Sequence
    add_heading_2(doc, "5.4. Student Union E-Voting with Academic Retake Vetting Engine")
    add_paragraph(doc, "This sequence models the strict academic integrity vetting algorithm enforcing University constitutional standards, followed by anonymized cryptographic ballot processing.")

    add_image_with_caption(doc, "diagram_sequence_evoting_vetting.png", "Sequence Diagram: Student Union E-Voting & Academic Retake Vetting Engine")

    add_paragraph(doc, "Below is the complete message trace specification detailing every boundary, method call, payload, and database transaction involved in the e-voting and vetting pipeline:")

    trace_ev_headers = ["Step #", "Source Boundary", "Target Boundary / Service", "Method / Invocation", "Payload & Data Attributes", "Database State & Audit Effect"]
    trace_ev_data = [
        ["1", "Student Candidate", "E-Voting Portal", "POST /elections/nominate", "position_id = 1 (Guild President), student_id = 104", "Candidate initiates nomination for executive guild office."],
        ["2", "E-Voting Controller", "AcademicVettingService", "vetCandidateAcademicRecord()", "student_id = 104, position_id = 1", "Initiates statutory academic clearance check."],
        ["3", "AcademicVettingService", "Grades Data Store", "SELECT FROM grades WHERE ...", "student_id = 104, (grade_letter = 'F' OR status = 'retake')", "Queries student historical academic performance."],
        ["4a", "Grades Data Store", "AcademicVettingService", "return retakeCount = 1", "Found failing record: CS201 (Score: 38, Grade: F)", "Active retake detected on student transcript."],
        ["4b", "AcademicVettingService", "E-Voting Controller", "rejectNomination()", "status = DISQUALIFIED, reason = 'Active Retake in CS201'", "Nominations record saved with status = 'Disqualified'; audit log created."],
        ["4c", "E-Voting Controller", "Candidate (UI)", "Display Disqualification Error", "HTTP 422: Disqualified under Guild Constitution 14(b)", "Candidate informed of academic disqualification."],
        ["5", "Cleared Voter", "Voting Booth (UI)", "POST /elections/cast-ballot", "voting_session_id, ballots: [{position_id, candidate_id}]", "Eligible student casts secret ballot on election day."],
        ["6", "Voting Controller", "VoterRegistryService", "hasAlreadyVoted()", "session_id = 3, student_id = 108", "Queries voter_participations table to prevent duplicate voting."],
        ["7", "Voting Controller", "BallotEncryptionService", "anonymizeAndHashBallot()", "candidate_id, position_id, timestamp", "Generates SHA-256 hash of vote; detaches voter identity."],
        ["8", "Voting Controller", "Votes Data Store", "INSERT INTO votes ...", "session_id, position_id, candidate_id, vote_hash", "ACID Transaction: Commits anonymized ballot and updates candidate tallies."],
        ["9", "Voting Controller", "VoterRegistryService", "markVoterParticipated()", "session_id, student_id = 108, ip_address, timestamp", "Inserts audit row in voter_participations; prevents further ballot access."]
    ]
    add_styled_table(doc, trace_ev_headers, trace_ev_data, [Inches(0.6), Inches(1.3), Inches(1.4), Inches(1.5), Inches(1.7)])

    # ==========================================
    # SECTION 6: DATA FLOW ARCHITECTURE (DFD LEVELS 0, 1, 2)
    # ==========================================
    add_heading_1(doc, "6. Data Flow Architecture (DFD Levels 0, 1 & 2)")
    add_paragraph(doc, "Data Flow Diagrams (DFDs) trace the movement of data through the JBI University Management System, mapping external entity interfaces, transformational processes, and relational data repositories. The architecture is decomposed hierarchically across Context Level 0, Decomposition Level 1, and Detailed Subsystem Level 2.")

    # 6.1 DFD Level 0
    add_heading_2(doc, "6.1. Level 0: Context Level Data Flow Diagram")
    add_paragraph(doc, "The Level 0 Context Diagram represents the entire JBI university platform as a single centralized processing core interacting with 11 external human actors and external banking/telephony gateways.")

    add_image_with_caption(doc, "diagram_dfd_level_0.png", "DFD Level 0: Master System Context Diagram")

    add_paragraph(doc, "Below is the Data Flow Dictionary defining each input and output stream crossing the system boundary at Level 0:")

    dfd0_headers = ["External Entity", "Direction", "Data Flow Label", "Carried Data Elements & Schema Content"]
    dfd0_data = [
        ["Prospective Applicant", "Inbound", "Application Submission", "Personal Biodata, Program Choice, Certified Grade Documents, Contact Details"],
        ["Prospective Applicant", "Outbound", "Admission Decision / Letter", "Admission Status, Matriculation Reg No, Official Signed Admission Letter PDF"],
        ["Commercial Bank Gateway", "Inbound", "Settlement Notification Webhook", "PRN Reference, Settlement Amount, Bank Transaction Token, HMAC SHA-256 Signature"],
        ["Commercial Bank Gateway", "Outbound", "PRN Registration Request", "Generated PRN, Invoice Amount, Student Identifier, Expiration Timestamp"],
        ["Student Body", "Inbound", "Course Registration & Voting", "Course Selection Payload, Secret E-Voting Ballot, Assignment Submissions, QR Scans"],
        ["Student Body", "Outbound", "Academic Transcripts & Invoices", "Approved Semester Grades, CGPA, Financial Statement, Digital Receipts, Voting Receipts"],
        ["Academic Lecturer", "Inbound", "Assessments & CA Marks", "Continuous Assessment Scores, Exam Marks, Course Syllabus, Dynamic QR Code Requests"],
        ["Academic Lecturer", "Outbound", "Course Rosters & Attendance", "Enrolled Student Lists, Real-time QR Attendance Telemetry, Moderation Status Reports"],
        ["Head of Department (HOD)", "Inbound", "Workload Allocation & Moderation", "Lecturer Course Assignments, Moderated Grade Adjustments, Course Approvals"],
        ["Head of Department (HOD)", "Outbound", "Departmental Analytics", "Curriculum Progress Reports, Department Grade Distribution Curves, Faculty Workload"],
        ["Dean of Faculty", "Inbound", "Divisional Endorsements & Budgets", "Endorsed Grade Batches, Faculty Budget Line Approvals, Strategic Program Proposals"],
        ["Dean of Faculty", "Outbound", "Executive Faculty Dashboards", "Cross-Departmental Performance KPIs, Accreditation Compliance Metrics, Budget Balances"],
        ["Registrar", "Inbound", "Matriculation & Senate Decisions", "Admissions Approvals, Matriculation Numbers, Senate Graduation Resolutions"],
        ["Registrar", "Outbound", "Certified University Registers", "Official Matriculated Student Register, Degree Award Lists, Transcript Records"],
        ["Bursar / Finance", "Inbound", "Fee Structures & Payment Clearance", "Academic Year Tuition Schedules, Manual Bank Reconciliations, Payroll Authorizations"],
        ["Bursar / Finance", "Outbound", "Financial Ledgers & Audit Logs", "Revenue Collection Reports, Unpaid Balance Aging Analysis, General Ledger Journal"],
        ["Estates & Facilities", "Inbound", "Space Allocation & Maintenance", "Classroom Allocations, Residence Hall Configurations, Maintenance Work Orders"],
        ["Estates & Facilities", "Outbound", "Facility Occupancy & Timetable", "Master Campus Timetable, Hostel Occupancy Registers, Maintenance SLA Reports"],
        ["Electoral Commission", "Inbound", "Election Configuration & Vetting", "Election Season Setup, Position Catalog, Candidate Vetting Signoffs"],
        ["Electoral Commission", "Outbound", "Certified Election Tallies", "Real-Time Aggregated Ballots, Candidate Vote Counts, Cryptographic Election Audit Log"],
        ["Super Administrator", "Inbound", "System Maintenance & Multi-Role", "Role Definitions, User Account Provisioning, Role Assignments, System Configs"],
        ["Super Administrator", "Outbound", "Telemetry & Security Logs", "System Exception Traces, User Login Audits, Database Backup Reports, Queue Status"]
    ]
    add_styled_table(doc, dfd0_headers, dfd0_data, [Inches(1.5), Inches(0.8), Inches(1.8), Inches(2.4)])

    # 6.2 DFD Level 1
    add_heading_2(doc, "6.2. Level 1: System Process Decomposition Diagram")
    add_paragraph(doc, "The Level 1 DFD decomposes the monolithic system into five specialized transactional processes and six centralized relational data stores.")

    add_image_with_caption(doc, "diagram_dfd_level_1.png", "DFD Level 1: System Process Decomposition & Data Store Architecture")

    add_paragraph(doc, "Below are formal Process Specifications (P-Specs) detailing the mathematical and functional logic of each Level 1 process:")

    # P-Spec 1.0
    add_heading_3(doc, "Process Specification 1.0: Admissions & Student Identity Provisioning")
    add_paragraph(doc, "Processes incoming prospective applications, validates documentary evidence, coordinates fee clearance, and provisions verified student user accounts.", bold_prefix="Purpose: ")
    add_paragraph(doc, "Application Submissions, Certificate Blobs, Application Fee Clearance, Registrar Approval Tokens.", bold_prefix="Inputs: ")
    add_paragraph(doc, "Admission Letters, Generated Matriculation Numbers, Student User Accounts, Identity Activation Links.", bold_prefix="Outputs: ")
    add_paragraph(doc, "D1: Users & Multi-Role Auth, D2: Student Master Records, D3: Financial Ledgers.", bold_prefix="Data Stores: ")
    add_callout(doc, "P-Spec 1.0 Algorithmic Logic", 
        "1. Receive applicant credentials and compute program qualification index: Q_idx = sum(weight_i * grade_i).\n"
        "2. If Q_idx >= Cutoff(program_id):\n"
        "     Store application in D2 with status = 'PENDING_PAYMENT'.\n"
        "     Trigger PRN generation via Process 2.0.\n"
        "3. Upon receipt of payment webhook:\n"
        "     Update application status to 'UNDER_REVIEW'.\n"
        "4. When Registrar issues approval:\n"
        "     Lock admissions counter with SELECT FOR UPDATE.\n"
        "     reg_no = 'ADM-' + CurrentYear + '-' + PadZero(counter + 1, 4).\n"
        "     Insert user into D1 with role = 'student'.\n"
        "     Insert student profile into D2 with gpa = 0.0, status = 'active'.\n"
        "     Trigger email notification with one-time activation token.",
        bg_hex='F8FAFC', border_hex='3B82F6', title_color=COLOR_BLUE)

    # P-Spec 2.0
    add_heading_3(doc, "Process Specification 2.0: Fee Billing & Payment Reconciliation Engine")
    add_paragraph(doc, "Administers student fee schedules, computes semester billing invoices, generates PRN tokens, and reconciles commercial bank callbacks.", bold_prefix="Purpose: ")
    add_paragraph(doc, "Fee Structure Definitions, Student Ledger Requests, Bank Callback Webhooks.", bold_prefix="Inputs: ")
    add_paragraph(doc, "PRN Registration Tokens, Student Fee Statements, Cleared Payment Receipts, Financial Ledger Entries.", bold_prefix="Outputs: ")
    add_paragraph(doc, "D2: Student Master Records, D3: Financial Ledgers.", bold_prefix="Data Stores: ")
    add_callout(doc, "P-Spec 2.0 Algorithmic Logic", 
        "1. For each registered student at semester commencement:\n"
        "     InvoiceAmount = BaseTuition(program_id) + FunctionalFees(program_id) + HostelFee(if allocated).\n"
        "     Write ledger_invoice into D3 with balance_due = InvoiceAmount.\n"
        "2. When student requests payment:\n"
        "     PRN = GeneratePRN(student_id, Amount).\n"
        "     Register PRN with Commercial Banking Switch API.\n"
        "3. On receiving webhook POST /api/webhooks/prn:\n"
        "     Verify HMAC_SHA256(Payload, SecretKey) == HeaderSignature.\n"
        "     If valid: Commit payment record to D3; balance_due = balance_due - PaidAmount.\n"
        "     If balance_due <= Threshold: Lift financial registration hold on D2.",
        bg_hex='F8FAFC', border_hex='3B82F6', title_color=COLOR_BLUE)

    # P-Spec 3.0
    add_heading_3(doc, "Process Specification 3.0: Curriculum, Course Registration & Assessment Engine")
    add_paragraph(doc, "Governs academic program structures, course enrollments, prerequisite verification, continuous assessments, examination grading, and dean endorsements.", bold_prefix="Purpose: ")
    add_paragraph(doc, "Course Registration Requests, CA Scores, Exam Marks, HOD Moderation Signoffs, Dean Endorsements.", bold_prefix="Inputs: ")
    add_paragraph(doc, "Course Rosters, Grade Distribution Sheets, Certified Academic Transcripts, Student CGPA Updates.", bold_prefix="Outputs: ")
    add_paragraph(doc, "D1: Users & Multi-Role Auth, D2: Student Master Records, D4: Curriculum & Assessment.", bold_prefix="Data Stores: ")
    add_callout(doc, "P-Spec 3.0 Algorithmic Logic", 
        "1. Course Registration: For each course_id in student request:\n"
        "     Validate prerequisites: Ensure prerequisite_courses exist in D4 with grade != 'F'.\n"
        "     Validate credit load: Ensure sum(credits) <= MaxCreditLoad (24 CU).\n"
        "     Commit pending enrollment to D4.\n"
        "2. Assessment Lifecycle: Lecturer submits CA (40%) and Exam (60%).\n"
        "     Total = CA + Exam. Map to Grade Point (0.0 - 5.0) and Letter Grade.\n"
        "     HOD reviews and moderates. Dean reviews and endorses.\n"
        "     Upon Senate approval: Update D2 student CGPA using cumulative weighted formula:\n"
        "     CGPA = sum(GradePoint_i * CreditUnits_i) / sum(CreditUnits_i).",
        bg_hex='F8FAFC', border_hex='3B82F6', title_color=COLOR_BLUE)

    # P-Spec 4.0
    add_heading_3(doc, "Process Specification 4.0: Student Union E-Voting & Academic Integrity Vetting")
    add_paragraph(doc, "Facilitates transparent democratic student elections while enforcing strict constitutional academic vetting to disqualify students with failing grades.", bold_prefix="Purpose: ")
    add_paragraph(doc, "Candidate Nominations, Manifesto Data, Secret Voter Ballots, Electoral Commission Rules.", bold_prefix="Inputs: ")
    add_paragraph(doc, "Vetting Clearance Reports, Anonymized Cryptographic Ballots, Certified Election Tallies.", bold_prefix="Outputs: ")
    add_paragraph(doc, "D1: Users & Multi-Role Auth, D2: Student Master Records, D4: Curriculum & Assessment, D5: E-Voting & Ballots.", bold_prefix="Data Stores: ")
    add_callout(doc, "P-Spec 4.0 Algorithmic Logic", 
        "1. Candidate Vetting: When student applies for executive guild office:\n"
        "     Query D4: SELECT COUNT(*) FROM grades WHERE student_id = candidate_id AND (grade_letter = 'F' OR status = 'retake').\n"
        "     If retakeCount > 0:\n"
        "         Reject nomination. Set status = 'DISQUALIFIED' with statutory notice.\n"
        "     Else:\n"
        "         Approve nomination. Add candidate to D5 candidate roster.\n"
        "2. Secret Balloting: When voter submits ballot:\n"
        "     Verify D5 voter_participations: Ensure student_id has not already voted.\n"
        "     Hash ballot = SHA256(candidate_id + session_id + timestamp + salt).\n"
        "     Insert ballot into D5 votes table without student_id.\n"
        "     Insert voter_id into D5 voter_participations table to prevent duplicate voting.",
        bg_hex='F8FAFC', border_hex='3B82F6', title_color=COLOR_BLUE)

    # P-Spec 5.0
    add_heading_3(doc, "Process Specification 5.0: Campus Infrastructure & Estates Management")
    add_paragraph(doc, "Coordinates lecture theater allocations, student residence hall allocations, and campus maintenance work orders.", bold_prefix="Purpose: ")
    add_paragraph(doc, "Room Allocation Requests, Timetable Requirements, Maintenance Trouble Tickets.", bold_prefix="Inputs: ")
    add_paragraph(doc, "Classroom Timetables, Hostel Room Keys, Work Order Assignments, Facility Occupancy Reports.", bold_prefix="Outputs: ")
    add_paragraph(doc, "D2: Student Master Records, D6: Campus Facilities & Residence Halls.", bold_prefix="Data Stores: ")
    add_callout(doc, "P-Spec 5.0 Algorithmic Logic", 
        "1. Timetable Scheduling: For each course section:\n"
        "     Query D6: Find rooms where capacity >= expected_enrollment AND room_type == required_type.\n"
        "     Verify no overlapping bookings: start_time < existing_end AND end_time > existing_start.\n"
        "     Commit booking to D6 facility_bookings.\n"
        "2. Hostel Allocation: When student requests room:\n"
        "     Verify gender compatibility and fee payment in D3.\n"
        "     Decrement available_beds in D6 hostel_rooms.\n"
        "     Create allocation record in D6 with check_in_status = 'Pending'.",
        bg_hex='F8FAFC', border_hex='3B82F6', title_color=COLOR_BLUE)

    # 6.3 DFD Level 2
    add_heading_2(doc, "6.3. Level 2: Subsystem Process Decomposition (Academic Assessment)")
    add_paragraph(doc, "The Level 2 DFD decomposes Process 3.0 (Curriculum, Course Registration & Assessment Engine) into its four core constituent sub-processes, modeling the exact journey of continuous assessment, final examination grading, departmental moderation, and dean executive endorsement.")

    add_image_with_caption(doc, "diagram_dfd_level_2_grading.png", "DFD Level 2: Academic Assessment & Moderation Subsystem Decomposition")

    add_paragraph(doc, "Below are the detailed Process Specifications for sub-processes 3.1 through 3.4:")

    # P-Spec 3.1 to 3.4 Table
    p_sub_headers = ["Sub-Process ID", "Sub-Process Name", "Inputs", "Outputs", "Functional / Algorithmic Rules"]
    p_sub_data = [
        ["3.1", "Continuous Assessment & Attendance Aggregation", "Daily QR Attendance Scans, Assignment Submissions, Quiz Marks", "Aggregate CA Score (Max 40%), Attendance Percentage", "Aggregates raw coursework and lab submissions. If attendance < 75%, flags student record with 'ATTENDANCE_BARRED' for final exam."],
        ["3.2", "Examination Score Capture & Letter Grade Computation", "CA Score (40%), Final Exam Score (60%), Course Scale", "Composite Mark (100%), Letter Grade (A-F), Grade Point (0.0-5.0)", "Total = CA + Exam. Applies institutional grade boundaries: 80-100% (A, 5.0), 75-79% (B+, 4.5), 70-74% (B, 4.0), 65-69% (C+, 3.5), 60-64% (C, 3.0), 55-59% (D+, 2.5), 50-54% (D, 2.0), <50% (F, 0.0 - Retake)."],
        ["3.3", "Departmental Moderation & Variance Auditing", "Draft Grade Sheets, Lecturer Submissions, Historical Department Norms", "Moderated Grade Sheets, Variance Flags, HOD Endorsement Seal", "HOD inspects grade distribution curve. Detects abnormal skew (e.g., >80% As or >50% Fs). Requests lecturer justification or adjusts marks within statutory variance (+/- 3%)."],
        ["3.4", "Dean Endorsement & Senate Transcript Publication", "Moderated Grade Sheets, Faculty Quality Standards, Senate Resolution", "Endorsed Divisional Grades, Published Student Transcripts, CGPA Updates", "Dean of Faculty audits divisional compliance. Signs off digital endorsement seal. Submits to Senate. Upon Senate approval, updates student_profiles.cgpa and publishes results to student portal."]
    ]
    add_styled_table(doc, p_sub_headers, p_sub_data, [Inches(1.0), Inches(1.4), Inches(1.3), Inches(1.3), Inches(1.5)])

    print("Part 2 built successfully!")
