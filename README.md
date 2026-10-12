# JBI University Management System

JBI is a web-based university management and learning platform built for Johnson Bible Institute. It brings admissions, academic administration, teaching, student services, and finance into one role-based system.

## What the system does

### Administration

- Manage users, roles, students, faculty, departments, programs, courses, academic years, and semesters
- Review applications and payment evidence
- Manage course enrollment, fees, and student records
- View academic, attendance, enrollment, faculty, course, and financial reports
- Publish announcements and configure institutional settings
- Manage campus facilities and associate rooms with named facilities
- Manage halls of residence as facilities with type `hall`; other campus facilities use type `facility`

### Faculty

- Manage assigned courses and learning materials
- Create assignments, quizzes, and examinations
- Record attendance
- Review submissions and maintain grades

### Students

- Apply online, follow application/payment status, and complete admission onboarding
- Maintain a student profile and access an admission letter and student ID card
- Review fees, submit payments, view receipts, and request a room in a residence hall
- View the programme, register for eligible courses, and check academic timetables
- Access enrolled courses, learning materials, assignments, quizzes, and examinations
- Submit coursework, view attendance and published grades, and review a transcript
- Receive announcements and notifications, contact support, message users, and participate in course forums
- Submit a program-change request, course evaluation, or eligible student-leadership application

The system also provides profile management, password recovery, support requests, discussion forums, email notifications, and receipt verification.

## Full student user story and end-to-end workflow

For a dedicated actor-by-actor explanation of who creates, reviews, approves, and updates student records, see [STUDENT.README.md](./STUDENT.README.md).

### Student persona and outcome

**Primary actor:** An applicant who becomes an admitted, active student.

**End-to-end user story:** As a JBI student, I want to apply for admission, complete onboarding, manage my fees and semester registration, participate in teaching and assessment, access student services, and review my academic record, so that I can progress through my programme in one place and know what action is expected of me at each stage.

The journey starts with an applicant and continues after admission. Admissions staff, Finance, Registrar staff, lecturers, and student-services staff perform their own review and approval actions in their authorized areas; the student can see or perform only the student-facing steps available to their account.

### 1. Apply and receive an admission decision

As a prospective student, I want to submit my application and supporting information, provide application payment evidence where required, and see the outcome, so that I can proceed with a clear admission status.

**Successful flow**

1. I submit an online application and receive an application confirmation.
2. If an application payment is required, I use the supplied payment instructions/reference and upload evidence through the application payment flow.
3. Admissions staff review my application and payment, then approve, reject, or update the programme decision.
4. When admitted, my student account/profile is activated or linked to the approved application. I sign in, acknowledge the admission onboarding step, and can view my admission letter and student ID card.

**Acceptance criteria**

- The application can be submitted without an existing student login.
- The applicant can see confirmation and complete the payment-evidence step associated with their application.
- Application status is controlled by the authorized admissions workflow and is not represented as approved merely because it was submitted.
- An admitted student is associated with a student profile and can complete the admission acknowledgement.
- A non-admitted or incomplete account is directed to the appropriate admission/onboarding view rather than being treated as an active student.

### 2. Set up my student profile and find key information

As an admitted student, I want to review my profile, programme, announcements, notifications, timetable, and support options, so that I can keep my details current and understand important dates and tasks.

**Acceptance criteria**

- I can review and update the profile fields that are editable to me and change my password.
- The student dashboard summarizes my active courses, upcoming work, attendance, grades, fee totals/balance, and unread notifications where that data exists.
- I can view published university announcements and mark my notifications as read.
- I can view teaching, test, and examination timetables relevant to my programme/year.
- I can submit a support request and use authenticated messaging where enabled.
- A missing or inactive student profile does not silently grant active-student functions; I receive the system's onboarding/status guidance.

### 3. Understand fees, pay, and keep a payment record

As a student, I want to understand the fees charged to my account, choose an available payment route, and confirm whether the payment was accepted, so that I can plan my finances and satisfy applicable registration requirements.

**Acceptance criteria**

- The fee page and ledger show invoice totals, paid amounts, outstanding balances, and recorded payments for my account.
- I can generate and view a payment reference (PRN) for an outstanding fee, subject to its validity period.
- A completed payment creates a payment record, updates the invoice paid/balance amounts and status, and is reflected in my receipt/payment history.
- A cash payment with the required proof is recorded as pending; it does not reduce the balance until an authorized reviewer approves it.
- Payment processing never allows me to pay or access another student's fee record.
- Where a registration fee is configured as a prerequisite, its confirmed payment updates the registration eligibility used by the academic workflow.

### 4. Register for my programme courses

As a student, I want to see the courses applicable to my programme and register during the allowed semester period, so that my official course enrollment is recorded before I attend classes and assessment.

**Acceptance criteria**

- I can review my programme structure, available courses, and existing enrollments.
- Registration is accepted only when the current semester is active and its registration period is open.
- Course registration follows the configured registration-fee, programme/department, year-of-study, enrollment-deadline, course-capacity, and duplicate-enrollment rules.
- A successful registration is recorded for the correct student, course, and semester and appears in my course list/LMS.
- An invalid or closed registration is rejected with a user-visible reason and does not create an enrollment.
- I can request a programme change and view my own submitted request history; a request does not itself change my approved programme.

### 5. Attend and participate in teaching

As a student enrolled in a course, I want to view course information and published learning materials, follow upcoming tasks, and monitor my attendance, so that I can keep up with my studies.

**Acceptance criteria**

- Course learning pages, learning materials, and course-specific resources are available only to a student enrolled in that course.
- Unpublished materials are not exposed through the student learning-material workflow.
- The LMS shows enrolled courses, learning progress, and upcoming published assignments, quizzes, and examinations.
- I can access my attendance summary and course attendance records where they have been recorded.
- I can participate in the course forum/discussion features exposed for the course.

### 6. Complete assignments, quizzes, and examinations

As a student, I want to complete assessments for my enrolled courses and receive a clear submission result, so that my work is recorded for lecturer review.

**Acceptance criteria**

- Only a student enrolled in the assessment's course can access or submit that course's assignment.
- I can submit assignment text or a file before the deadline, and the system records the submission against me and the correct assignment.
- The system rejects empty submissions, submissions after the deadline, and unauthorized course submissions with a clear message.
- I can access published quizzes and examinations within their configured availability and attempt rules.
- My responses/submissions are associated with my account and are available to authorized teaching staff for marking.
- A successful submission displays confirmation; a failed submission is not presented as successful.

### 7. Review published marks and academic progress

As a student, I want to review my published grades, feedback, GPA/progress summaries, and transcript, so that I know my academic standing and what I have completed.

**Acceptance criteria**

- I can see my own published assessment/course grades and available lecturer feedback.
- Unpublished grades and another student's grades are not included in my student results or transcript.
- The transcript groups available published grades by course and presents the corresponding result information.
- Academic progress and GPA summaries are based on the records available to the system and do not imply that an incomplete or unpublished result is final.
- I can print the transcript from the browser for personal use; official transcript request/issuance remains subject to the institution's process.

### 8. Use student services and take part in campus life

As a student, I want to request services relevant to my studies and participate in student activities, so that I can manage campus life through the same account.

**Acceptance criteria**

- I can view available residence halls/rooms and submit at most one pending or approved room request per current semester; the request remains pending until the authorized allocation process acts on it.
- I can complete an available course evaluation for an enrolled course.
- If an election is open and I meet the eligibility rules, I can apply for an eligible student leadership position and/or cast a ballot; duplicate or ineligible actions are rejected.
- I can view notifications, announcements, support requests, and authenticated messages intended for my account.

### 9. Continue, complete, or change my study path

As a continuing student, I want my current enrollment, fee, attendance, assessment, and published academic history to remain available across semesters, so that I can plan my next semester and verify my progress toward completion.

**Acceptance criteria**

- My student account continues to show the profile, programme, invoices/payments, course enrollments, attendance, and published results associated with my student identity.
- I can register for the next eligible semester when it is open and the configured checks are satisfied.
- I can view accumulated published results through the transcript.
- Programme changes, room allocations, cash payment approvals, and admission decisions remain decisions of the authorized staff workflow; student requests alone do not finalize them.

### End-to-end happy path

`Apply → application/payment review → admission approval → activate student profile → acknowledge admission → review fees → confirm required payment → register for eligible courses → access LMS/materials → attend and complete assessments → review published marks/transcript → register for the next semester`

At every step, the student sees their own records only. If a prerequisite is not met—such as admission not yet approved, the registration period being closed, a required fee not confirmed, or the student not being enrolled in a course—the system blocks that action and should explain what must happen next.

### Current implementation boundaries

The workflows above describe the system's current student-facing features and staff-mediated decisions; they do not mean every university process is fully automated. The single-course registration endpoint checks the registration-fee timestamp and course/program eligibility, while the bulk programme-enrollment endpoint follows a separate validation path and currently sets that timestamp during enrollment. Treat the registration-fee gate as an acceptance requirement, not as a guarantee that both routes enforce it consistently; the paths need to be aligned before relying on that control. The application also does not currently establish student-initiated official transcript/document request tracking, a Registrar-approved printable registration form, direct private lecturer messaging specifically scoped to a course, or a downloadable official PDF fee statement/transcript. Browser printing is not equivalent to an institutionally issued document. Payment gateway settlement/verification depends on the configured payment integration and staff workflow; a generated PRN alone is not proof of settlement.

### Human resources

- Publish vacancies for guest applicants and accept CVs and supporting documents securely
- Track applications through screening, interviews, offers, and hiring; succession plans can create linked vacancies
- Approve offers to provision staff accounts, HR profiles, department/workspace assignments, and onboarding checklists
- Send password setup links and route new staff to the portal for their assigned role

## Technology

- PHP 8.2 or newer
- Laravel 12
- Laravel Sanctum
- SQLite by default; MySQL and other Laravel-supported databases can also be configured
- Tailwind CSS 4
- Vite 6
- Node.js and npm

## Requirements

Install the following before setting up the project:

- PHP 8.2+
- [Composer](https://getcomposer.org/)
- Node.js 18+ and npm
- A database driver for your chosen database, such as PDO SQLite or PDO MySQL

## Local setup

1. Clone the repository and enter its directory:

   ```bash
   git clone https://github.com/Eclz/JBI.git
   cd JBI
   ```

2. Install the PHP and JavaScript dependencies:

   ```bash
   composer install
   npm install
   ```

3. Create the environment file and application key:

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. Set the application details in `.env`:

   ```env
   APP_NAME="JBI University"
   APP_URL=http://localhost:8000
   ```

5. Configure the database.

   For SQLite:

   ```bash
   touch database/database.sqlite
   ```

   Keep these values in `.env`:

   ```env
   DB_CONNECTION=sqlite
   DB_DATABASE=/absolute/path/to/JBI/database/database.sqlite
   ```

   Alternatively, configure MySQL:

   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=jbi
   DB_USERNAME=root
   DB_PASSWORD=
   ```

6. Create the database tables and public file-storage link:

   ```bash
   php artisan migrate
   php artisan storage:link
   ```

7. Build the frontend assets:

   ```bash
   npm run build
   ```

8. Start the application:

   ```bash
   php artisan serve
   ```

   Open [http://localhost:8000](http://localhost:8000).

## Development mode

To run the web server, queue worker, application log viewer, and Vite development server together:

```bash
composer run dev
```

Because the default configuration uses database-backed sessions, cache, and queues, migrations must be completed before starting the application. Keep a queue worker running when testing queued email notifications.

## Demo data and accounts

To populate a disposable development database with a realistic set of academic records:

```bash
php artisan migrate:fresh --seed
```

> This command deletes all existing database data. Use it only for a disposable development database. Accounts created by `UserFactory` use the password `password`. The fixed administrator accounts are `admin@jbiuniversity.com` and `academic@jbiuniversity.com`; faculty and student accounts have generated email addresses.

To create a smaller demo dataset with fixed role-based accounts, start with a disposable database and run:

```bash
php artisan migrate:fresh
php artisan db:seed --class=Database\\Seeders\\DemoDatabaseSeeder
```

> `migrate:fresh` deletes all existing database data. `DemoDatabaseSeeder` also deletes and recreates its fixed demo users; deleting those users may cascade to their related records. Do not run these commands against production or a database containing data you need to keep.

The fixed demo accounts all use the password `password123`:

| Role | Email |
| --- | --- |
| Administrator | `admin@jbiuniversity.com` |
| Faculty | `faculty@jbiuniversity.com` |
| Student | `student@jbiuniversity.com` |
| Parent | `parent@jbiuniversity.com` |

These credentials are for local testing only. Never deploy them to production.

### Functional-area permissions

Role permissions include dedicated modules for Halls of Residence, Registrar Hub, Dean Administration, and HR Onboarding. To add or refresh these grants in an existing database without reseeding demo data, run:

```bash
php artisan db:seed --class="Database\\Seeders\\FunctionalAreaPermissionsSeeder"
```

The seeder grants each area to its corresponding operational roles and preserves each role's permissions for other modules.

Only on a brand-new, empty production database, initialize essential academic structure and the administrator account with:

```bash
php artisan migrate:fresh --force --seeder=Database\\Seeders\\ProductionDatabaseSeeder
```

> This command drops all existing tables. For an existing production database, use `php artisan migrate --force` instead; do not use `migrate:fresh`.

## Email configuration

Email is written to the application log by default. To send real password-reset, application, and admission messages, configure the `MAIL_*` values in `.env`. For example:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.example.com
MAIL_PORT=587
MAIL_USERNAME=your-username
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=admissions@example.com
MAIL_FROM_NAME="${APP_NAME}"
```

After changing environment values, clear cached configuration:

```bash
php artisan optimize:clear
```

## Testing

Run the automated test suite with:

```bash
composer test
```

## Production notes

Before deployment:

- Set `APP_ENV=production` and `APP_DEBUG=false`
- Use a strong, unique `APP_KEY`
- Configure the production database, mail server, and filesystem
- Point the web server document root to the `public` directory
- Run migrations with `php artisan migrate --force`
- Build assets with `npm run build`
- Run a persistent queue worker
- Configure the scheduler to execute `php artisan schedule:run` every minute
- Ensure `storage` and `bootstrap/cache` are writable by the web server
- Cache configuration and routes with `php artisan optimize`

## Author

[levengalvin@gmail.com](mailto:levengalvin@gmail.com)
