"""
Master IEEE Specification Builder
Generates:
1. docs/IEEE_SRS_SDD_SPECIFICATION.md (IEEE 830-1998 / 1016-2009 Markdown)
2. docs/IEEE_SRS_SDD_SPECIFICATION.docx (IEEE 830-1998 / 1016-2009 DOCX)
"""

import os
import json
import shutil
import docx
from docx.shared import Inches, Pt, RGBColor
from docx.enum.text import WD_ALIGN_PARAGRAPH
from docx.oxml import parse_xml
from docx.oxml.ns import nsdecls

BASE_DIR = r"e:\Projects\JBI"
DOCS_DIR = os.path.join(BASE_DIR, "docs")
DESKTOP_DIR = r"C:\Users\DIGIVERSE SOLUTIONS\Desktop\jbidocs"
SCHEMA_PATH = os.path.join(BASE_DIR, "storage", "schema_dump.json")

MD_OUTPUT_PATH = os.path.join(DOCS_DIR, "IEEE_SRS_SDD_SPECIFICATION.md")
DOCX_OUTPUT_PATH = os.path.join(DOCS_DIR, "IEEE_SRS_SDD_SPECIFICATION.docx")
DESKTOP_DOCX_PATH = os.path.join(DESKTOP_DIR, "IEEE_SRS_SDD_SPECIFICATION.docx")

os.makedirs(DOCS_DIR, exist_ok=True)
os.makedirs(DESKTOP_DIR, exist_ok=True)

with open(SCHEMA_PATH, "r", encoding="utf-8") as f:
    schema_data = json.load(f)
tables_dict = schema_data.get("tables", {})

TABLE_DOMAINS = {
    "1. Academic Core & Curriculum Domain": [
        "academic_years", "semesters", "faculties", "departments", 
        "programs", "program_levels", "courses", "course_enrollments", 
        "course_materials", "timetables"
    ],
    "2. Student Lifecycle & Admissions Domain": [
        "applications", "student_profiles", "student_notes", 
        "student_issues", "program_change_requests"
    ],
    "3. Assessment, LMS & Grading Engine Domain": [
        "assignments", "assignment_submissions", "exams", "exam_attempts", 
        "grades", "quizzes", "quiz_questions", "quiz_attempts", 
        "learning_progress", "attendance"
    ],
    "4. Faculty, Deanery & Academic Quality Domain": [
        "faculty_profiles", "faculty_evaluations", "evaluation_surveys", 
        "evaluation_questions", "evaluation_responses", "quality_reviews", 
        "research_grants", "budget_requests"
    ],
    "5. Finance, Billing & General Ledger Domain": [
        "fee_structures", "fee_records", "course_fees", "bank_accounts", 
        "payments", "payment_reference_numbers", "finance_budgets", 
        "finance_expenses", "finance_revenues", "general_ledger_entries", 
        "sponsor_invoices", "vendor_invoices", "finance_audit_logs"
    ],
    "6. Human Resources & Workforce Management Domain": [
        "hr_job_roles", "hr_employees", "hr_vacancies", "hr_applicants", 
        "hr_onboardings", "hr_onboarding_tasks", "hr_time_logs", "hr_shifts", 
        "hr_shift_assignments", "hr_expense_claims", "hr_benefit_plans", 
        "hr_benefit_enrollments", "hr_performance_goals", "hr_performance_reviews", 
        "hr_training_courses", "hr_training_enrollments", "hr_succession_plans", 
        "hr_successors", "leave_requests", "payroll_records", "salary_structures"
    ],
    "7. Campus Infrastructure, Hostels & Logistics Domain": [
        "campus_facilities", "facility_rooms", "facility_bookings", 
        "hostel_rooms", "hostel_allocations", "library_items", "library_loans", 
        "university_assets", "suppliers", "procurement_requisitions"
    ],
    "8. Democratic Governance & E-Voting Domain": [
        "electoral_commission_members", "voting_sessions", "voting_positions", 
        "voting_candidates", "votes"
    ],
    "9. Institutional Communications & Support Domain": [
        "announcements", "forums", "forum_topics", "forum_replies", 
        "messages", "notifications", "support_tickets"
    ],
    "10. System Kernel, Security & Infrastructure Domain": [
        "users", "roles", "role_user", "audit_logs", "system_settings", 
        "external_partnerships", "sessions", "password_reset_tokens", 
        "jobs", "failed_jobs", "job_batches", "cache", "cache_locks", "migrations"
    ]
}

TABLE_DESCRIPTIONS = {
    "academic_years": "Defines institutional calendar years, opening/closing dates, and active session state.",
    "semesters": "Stores academic terms (Semester 1, Semester 2, Trimester) bounded to academic years.",
    "faculties": "Top-level collegiate administrative divisions (e.g., Faculty of Science, Faculty of Law).",
    "departments": "Academic departments nested within faculties (e.g., Computer Science, Economics).",
    "programs": "Degree and diploma programs offered by departments (e.g., BSc Computer Science).",
    "program_levels": "Curriculum levels (e.g., Year 1 / 100L, Year 2 / 200L, Postgraduate).",
    "courses": "Catalog of institutional instructional courses, credit weightings, and prerequisites.",
    "course_enrollments": "Relational enrollment records linking student profiles to registered courses per semester.",
    "course_materials": "Syllabi, lecture notes, video URLs, and multimedia assets uploaded by faculty.",
    "timetables": "Instructional lecture, lab, and tutorial scheduling with hall and lecturer allocation.",
    "applications": "Prospective applicant admission dossiers, entry qualification points, and processing status.",
    "student_profiles": "Official student demographic, matriculation number, program placement, and status records.",
    "student_notes": "Official disciplinary, academic advisory, and pastoral notes recorded on student records.",
    "student_issues": "Formal grievance, complaint, and petition tracking filed by students to Deanery/Admin.",
    "program_change_requests": "Formal inter-program and inter-departmental transfer petitions and approval chain.",
    "assignments": "Lecturer-published coursework assignments with submission deadlines and weightings.",
    "assignment_submissions": "Student-submitted assignment files, submission timestamps, and feedback.",
    "exams": "Official examinations scheduled by the Examinations Office with venue and proctor definitions.",
    "exam_attempts": "Audit logs of examination entry, proctor verification, and submission metadata.",
    "grades": "Official assessment score records (Continuous Assessment, Midterm, Final, Grade Point, Letter Grade).",
    "quizzes": "Online formative assessment tests configured by lecturers with time limits.",
    "quiz_questions": "Item bank of quiz questions supporting multiple-choice, true/false, and short answer.",
    "quiz_attempts": "Student quiz execution logs, selected options, timestamps, and automated scores.",
    "learning_progress": "SCORM-style topic and module completion tracking for students in registered courses.",
    "attendance": "Classroom lecture and lab session attendance logs (Present, Absent, Excused, Tardy).",
    "faculty_profiles": "Academic credentials, faculty affiliation, academic rank, and bio for lecturers.",
    "faculty_evaluations": "Aggregated semester teaching performance evaluation scores for academic staff.",
    "evaluation_surveys": "Student evaluation of teaching (SET) surveys instrumented by the Quality Directorate.",
    "evaluation_questions": "Standardized Likert-scale and qualitative feedback survey questions.",
    "evaluation_responses": "Anonymized student feedback responses on teaching efficacy and course delivery.",
    "quality_reviews": "Internal academic audit records, syllabus compliance, and peer review assessments.",
    "research_grants": "University research grant proposals, funding allocations, and milestone tracking.",
    "budget_requests": "Departmental and faculty expenditure requests routed to the Dean and Bursar.",
    "fee_structures": "Approved institutional tuition, registration, lab, and library fee schedules per program/level.",
    "fee_records": "Individual student semester fee billing obligations and outstanding balance ledgers.",
    "course_fees": "Special laboratory, excursion, or certification fees mapped to specific courses.",
    "bank_accounts": "Authorized institutional bank accounts and clearing accounts for fee collection.",
    "payments": "Financial receipt records of payments executed by students or sponsors.",
    "payment_reference_numbers": "Payment Reference Numbers (PRN) generated for bank branch and electronic payments.",
    "finance_budgets": "Annual institutional operational and capital budgets allocated to departments.",
    "finance_expenses": "Audited expenditure disbursements categorized by cost centers and chart of accounts.",
    "finance_revenues": "Inflow ledger recording student tuition, research grants, donations, and commercial revenues.",
    "general_ledger_entries": "Double-entry bookkeeping journal entries enforcing debit and credit equilibrium.",
    "sponsor_invoices": "Formal billing statements issued to corporate, government, or scholarship sponsors.",
    "vendor_invoices": "Invoices received from external campus contractors, suppliers, and utility providers.",
    "finance_audit_logs": "Tamper-evident logs of all financial adjustments, reversals, and manual overrides.",
    "hr_job_roles": "Institutional salary grade levels, job descriptions, and hierarchical reporting lines.",
    "hr_employees": "Master employee records including contract type, national ID, department, and salary grade.",
    "hr_vacancies": "Authorized university staff recruitment requisitions published internally/externally.",
    "hr_applicants": "Job applicant dossiers, CV uploads, and interview assessment scores.",
    "hr_onboardings": "Employee induction workflows and compliance tracking for new academic and administrative staff.",
    "hr_onboarding_tasks": "Individual compliance checklists (ID issuance, email provisioning, background checks).",
    "hr_time_logs": "Staff clock-in/clock-out biometric or digital timesheet attendance records.",
    "hr_shifts": "Campus security, maintenance, library, and nursing shift schedule definitions.",
    "hr_shift_assignments": "Roster mapping staff members to specific operational work shifts.",
    "hr_expense_claims": "Staff travel, per diem, and conference expense reimbursement claims.",
    "hr_benefit_plans": "Health insurance, pension schemes, life assurance, and staff tuition discount policies.",
    "hr_benefit_enrollments": "Individual staff enrollments and dependent coverage under university benefit plans.",
    "hr_performance_goals": "Annual academic and administrative Key Performance Indicators (KPIs) set with supervisors.",
    "hr_performance_reviews": "Formal annual and probationary employee appraisal ratings and promotion recommendations.",
    "hr_training_courses": "Staff professional development, pedagogy training, and compliance workshops.",
    "hr_training_enrollments": "Staff enrollment and certification records for institutional training programs.",
    "hr_succession_plans": "Institutional contingency and leadership succession strategies for critical posts.",
    "hr_successors": "Identified staff candidates, readiness levels, and development tracks for leadership succession.",
    "leave_requests": "Annual, sick, study, maternity, and sabbatical leave requests and multi-tier approval chains.",
    "payroll_records": "Monthly payroll disbursement registers, gross pay, deductions, tax, and net pay.",
    "salary_structures": "Compensation bands, base pay, housing allowance, transport allowance, and pension splits.",
    "campus_facilities": "Master registry of campus buildings, auditoriums, sports complexes, and grounds.",
    "facility_rooms": "Classrooms, computer laboratories, lecture theatres, and seminar rooms.",
    "facility_bookings": "Event, lecture, and student club reservation management for university venues.",
    "hostel_rooms": "Student residential hall rooms, capacities, gender policies, and pricing tiers.",
    "hostel_allocations": "Residential room and bed allocation records linked to admitted/registered students.",
    "library_items": "Physical books, journals, audio-visual materials, and catalog metadata.",
    "library_loans": "Borrowing records, due dates, renewal counts, and overdue fine calculations.",
    "university_assets": "Capital asset registry (servers, projectors, vehicles, lab instruments) with serial numbers.",
    "suppliers": "Approved vendors, contractors, and corporate service providers.",
    "procurement_requisitions": "Departmental purchase requests, three-quote evaluations, and purchase orders.",
    "electoral_commission_members": "Accredited university electoral commission staff and student commissioners.",
    "voting_sessions": "Student Guild / Student Union election sessions with strict start and end timestamps.",
    "voting_positions": "Contested electoral executive offices (Guild President, Vice President, Secretary General, etc.).",
    "voting_candidates": "Approved student candidates running for specific elective positions.",
    "votes": "Cryptographically hashed and anonymized cast ballots preventing double-voting.",
    "announcements": "Broadcast bulletins targeted to specific student groups, faculties, or all campus users.",
    "forums": "Academic and campus life discussion categories.",
    "forum_topics": "Discussion threads initiated by students or faculty members.",
    "forum_replies": "Chronological responses and peer discussion contributions.",
    "messages": "Direct internal messaging between authenticated system users.",
    "notifications": "System transactional notifications (fee receipt, grade release, assignment due).",
    "support_tickets": "IT support, registrar helpdesk, and facilities maintenance ticketing queue.",
    "users": "Master authentication registry storing email, password hash, and active status.",
    "roles": "System roles (super_admin, registrar, dean, hod, lecturer, student, bursar, hr_director, etc.).",
    "role_user": "Relational many-to-many pivot assigning multiple roles to system users.",
    "audit_logs": "Comprehensive immutable audit trail of system modifications, IP addresses, and user actions.",
    "system_settings": "Global configuration key-value store (academic year, branding, currency, mail settings).",
    "external_partnerships": "Registry of affiliated academic institutions, international exchange partners, and MOUs.",
    "sessions": "Active HTTP session state store with IP address, user-agent, and serialized payload.",
    "password_reset_tokens": "Hashed tokens for time-limited user password reset workflows.",
    "jobs": "Asynchronous background job queue for email dispatch, grade compilation, and heavy reports.",
    "failed_jobs": "Exception registry for unhandled background job failures requiring operator intervention.",
    "job_batches": "Grouped asynchronous batch job tracker with progress and cancellation hooks.",
    "cache": "Key-value cache storage for accelerated query results and system configurations.",
    "cache_locks": "Atomic distributed locks preventing race conditions in high-concurrency operations.",
    "migrations": "Database versioning and schema migration execution register."
}

print("Configuration dictionaries successfully loaded.")
