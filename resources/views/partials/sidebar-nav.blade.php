<div class="sidebar-wrapper">
    <div class="sidebar-header">
        <div class="logo-container">
            <img src="{{ asset('images/jbi.png') }}" alt="JBI University" class="logo-img">
            {{-- <span class="logo-text">JBI University</span> --}}
        </div>
        <button id="sidebar-toggle-btn" class="sidebar-toggle d-md-none">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>

    @auth
    <div class="user-profile">
        <div class="user-avatar">
            <img src="{{ auth()->user()->profile_picture_url }}" alt="{{ auth()->user()->full_name }}">
        </div>
        <div class="user-info">
            <h6 class="user-name">{{ auth()->user()->full_name }}</h6>
            <span class="user-role">{{ ucfirst(auth()->user()->role) }}</span>
        </div>
    </div>
    @endauth

    @guest
    <div class="user-profile">
        <div class="user-avatar">
            <img src="https://ui-avatars.com/api/?name=Guest&color=1e3a8a&background=e0e7ff" alt="Guest">
        </div>
        <div class="user-info">
            <h6 class="user-name">Guest User</h6>
            <span class="user-role">Not Logged In</span>
        </div>
    </div>
    @endguest

    @auth
    <ul class="sidebar-menu">
        <li class="menu-header">Main Navigation</li>

        {{-- Show role-specific dashboard links --}}
        @if(auth()->user()->isAdmin())
        <li class="menu-item {{ request()->routeIs('dashboard') || request()->routeIs('dashboard') ? 'active' : '' }}">
            <a href="{{ route('dashboard') }}" class="menu-link">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
            </a>
        </li>
        @elseif(auth()->user()->role === 'student')
        <li class="menu-item {{ request()->routeIs('student.dashboard') ? 'active' : '' }}">
            <a href="{{ route('student.dashboard') }}" class="menu-link">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
            </a>
        </li>
        @elseif(auth()->user()->role === 'faculty')
        <li class="menu-item {{ request()->routeIs('faculty.dashboard') ? 'active' : '' }}">
            <a href="{{ route('faculty.dashboard') }}" class="menu-link">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
            </a>
        </li>
        @else
        <li class="menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <a href="{{ route('dashboard') }}" class="menu-link">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
            </a>
        </li>
        @endif

        @if(auth()->user()->isAdmin())
        <li class="menu-header">Administration</li>

        <li class="menu-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
            <a href="{{ route('admin.users.index') }}" class="menu-link">
                <i class="bi bi-people"></i>
                <span>User Management</span>
            </a>
        </li>

        <li class="menu-item {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
            <a href="{{ route('admin.roles.index') }}" class="menu-link">
                <i class="bi bi-shield-lock"></i>
                <span>Roles & Permissions</span>
            </a>
        </li>

        <li class="menu-item {{ request()->routeIs('admin.applications.*') ? 'active' : '' }}">
            <a href="{{ route('admin.applications.index') }}" class="menu-link">
                <i class="bi bi-file-earmark-check"></i>
                <span>Applications</span>
                @php
                    try {
                        $pendingApplicationsCount = \App\Models\Application::where('status', 'pending')->count();

                        if ($pendingApplicationsCount > 0) {
                            echo '<span class="badge bg-danger rounded-pill ms-auto">' . $pendingApplicationsCount . '</span>';
                        }
                    } catch (\Exception $e) {
                        // Silently fail if there's a database error
                    }
                @endphp
            </a>
        </li>

        <li class="menu-item {{ request()->routeIs('admin.students.*') ? 'active' : '' }}">
            <a href="{{ route('admin.students.index') }}" class="menu-link">
                <i class="bi bi-mortarboard"></i>
                <span>Student Management</span>
            </a>
        </li>

        <li class="menu-item {{ request()->routeIs('admin.faculty-staff.*') ? 'active' : '' }}">
            <a href="{{ route('admin.faculty-staff.index') }}" class="menu-link">
                <i class="bi bi-person-badge"></i>
                <span>Faculty Management</span>
            </a>
        </li>

        <li class="menu-item {{ request()->routeIs('admin.courses.*') ? 'active' : '' }}">
            <a href="{{ route('admin.courses.index') }}" class="menu-link">
                <i class="bi bi-journal-bookmark"></i>
                <span>Course Management</span>
            </a>
        </li>

        <li class="menu-item {{ request()->routeIs('admin.faculties.*') ? 'active' : '' }}">
            <a href="{{ route('admin.faculties.index') }}" class="menu-link">
                <i class="bi bi-buildings"></i>
                <span>Faculties Management</span>
            </a>
        </li>

        <li class="menu-item {{ request()->routeIs('admin.departments.*') ? 'active' : '' }}">
            <a href="{{ route('admin.departments.index') }}" class="menu-link">
                <i class="bi bi-building"></i>
                <span>Department Management</span>
            </a>
        </li>

        <li class="menu-item {{ request()->routeIs('admin.programs.*') ? 'active' : '' }}">
            <a href="{{ route('admin.programs.index') }}" class="menu-link">
                <i class="bi bi-journal-bookmark"></i>
                <span>Program Management</span>
            </a>
        </li>

        <li class="menu-item {{ request()->routeIs('admin.program-levels.*') ? 'active' : '' }}">
            <a href="{{ route('admin.program-levels.index') }}" class="menu-link">
                <i class="bi bi-layers"></i>
                <span>Program Levels</span>
            </a>
        </li>

        <li class="menu-item {{ request()->routeIs('admin.program-changes.*') ? 'active' : '' }}">
            <a href="{{ route('admin.program-changes.index') }}" class="menu-link">
                <i class="bi bi-arrow-repeat"></i>
                <span>Program Change Requests</span>
            </a>
        </li>

        <li class="menu-item {{ request()->routeIs('admin.academic-years.*') ? 'active' : '' }}">
            <a href="{{ route('admin.academic-years.index') }}" class="menu-link">
                <i class="bi bi-calendar3"></i>
                <span>Academic Years</span>
            </a>
        </li>

        <li class="menu-item {{ request()->routeIs('admin.semesters.*') ? 'active' : '' }}">
            <a href="{{ route('admin.semesters.index') }}" class="menu-link">
                <i class="bi bi-calendar2"></i>
                <span>Semesters</span>
            </a>
        </li>

        <li class="menu-header">Finance & Governance</li>

        <li class="menu-item {{ request()->routeIs('admin.finance.*') || request()->routeIs('admin.fees.*') ? 'active' : '' }}">
            <a href="{{ route('admin.finance.dashboard') }}" class="menu-link fw-bold text-primary">
                <i class="bi bi-bank"></i>
                <span>Finance & Bursar Hub</span>
            </a>
        </li>

        <li class="menu-item {{ request()->routeIs('admin.fees.structures.*') ? 'active' : '' }}">
            <a href="{{ route('admin.fees.structures.index') }}" class="menu-link ps-4">
                <i class="bi bi-file-earmark-spreadsheet"></i>
                <span>Fee Structures</span>
            </a>
        </li>

        <li class="menu-item {{ request()->routeIs('admin.fees.index') ? 'active' : '' }}">
            <a href="{{ route('admin.fees.index') }}" class="menu-link ps-4">
                <i class="bi bi-receipt"></i>
                <span>Student Fee Records</span>
            </a>
        </li>

        <li class="menu-item {{ request()->routeIs('admin.finance.revenue.*') ? 'active' : '' }}">
            <a href="{{ route('admin.finance.revenue.index') }}" class="menu-link ps-4">
                <i class="bi bi-currency-exchange"></i>
                <span>Revenue & Income</span>
            </a>
        </li>

        <li class="menu-item {{ request()->routeIs('admin.finance.budgets.*') ? 'active' : '' }}">
            <a href="{{ route('admin.finance.budgets.index') }}" class="menu-link ps-4">
                <i class="bi bi-pie-chart"></i>
                <span>Department Budgets</span>
            </a>
        </li>

        <li class="menu-item {{ request()->routeIs('admin.finance.expenses.*') ? 'active' : '' }}">
            <a href="{{ route('admin.finance.expenses.index') }}" class="menu-link ps-4">
                <i class="bi bi-cart-check"></i>
                <span>Expenditures</span>
            </a>
        </li>

        <li class="menu-item {{ request()->routeIs('admin.finance.payables.*') ? 'active' : '' }}">
            <a href="{{ route('admin.finance.payables.index') }}" class="menu-link ps-4">
                <i class="bi bi-truck"></i>
                <span>Accounts Payable</span>
            </a>
        </li>

        <li class="menu-item {{ request()->routeIs('admin.finance.receivables.*') ? 'active' : '' }}">
            <a href="{{ route('admin.finance.receivables.index') }}" class="menu-link ps-4">
                <i class="bi bi-person-lines-fill"></i>
                <span>Accounts Receivable</span>
            </a>
        </li>

        <li class="menu-item {{ request()->routeIs('admin.finance.payroll.*') ? 'active' : '' }}">
            <a href="{{ route('admin.finance.payroll.index') }}" class="menu-link ps-4">
                <i class="bi bi-person-badge"></i>
                <span>Payroll Management</span>
            </a>
        </li>

        <li class="menu-item {{ request()->routeIs('admin.finance.assets.*') ? 'active' : '' }}">
            <a href="{{ route('admin.finance.assets.index') }}" class="menu-link ps-4">
                <i class="bi bi-qr-code-scan"></i>
                <span>Asset Management</span>
            </a>
        </li>

        <li class="menu-item {{ request()->routeIs('admin.finance.banking.*') ? 'active' : '' }}">
            <a href="{{ route('admin.finance.banking.index') }}" class="menu-link ps-4">
                <i class="bi bi-piggy-bank"></i>
                <span>Banking & Cash</span>
            </a>
        </li>

        <li class="menu-item {{ request()->routeIs('admin.finance.reports.*') ? 'active' : '' }}">
            <a href="{{ route('admin.finance.reports.index') }}" class="menu-link ps-4">
                <i class="bi bi-journal-text"></i>
                <span>Financial Statements</span>
            </a>
        </li>

        <li class="menu-item {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
            <a href="{{ route('admin.reports.index') }}" class="menu-link">
                <i class="bi bi-file-earmark-bar-graph"></i>
                <span>Reports</span>
            </a>
        </li>

        <li class="menu-item {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
            <a href="{{ route('admin.settings') }}" class="menu-link">
                <i class="bi bi-gear"></i>
                <span>System Settings</span>
            </a>
        </li>

        <li class="menu-item {{ request()->routeIs('admin.timetables.*') ? 'active' : '' }}">
            <a href="{{ route('admin.timetables.index') }}" class="menu-link">
                <i class="bi bi-calendar3"></i>
                <span>Timetable Management</span>
            </a>
        </li>

        <li class="menu-item {{ request()->routeIs('admin.evoting.*') ? 'active' : '' }}">
            <a href="{{ route('admin.evoting.index') }}" class="menu-link">
                <i class="bi bi-check2-square"></i>
                <span>E-Voting Management</span>
            </a>
        </li>

        <li class="menu-item {{ request()->routeIs('admin.evaluation-surveys.*') ? 'active' : '' }}">
            <a href="{{ route('admin.evaluation-surveys.index') }}" class="menu-link">
                <i class="bi bi-clipboard2-check"></i>
                <span>Evaluation Surveys</span>
            </a>
        </li>
        @endif


        @if(auth()->user()->isFaculty())
        <li class="menu-header">FACULTY</li>

        <li class="menu-item {{ request()->routeIs('faculty.courses.*') ? 'active' : '' }}">
            <a href="{{ route('faculty.courses.index') }}" class="menu-link">
                <i class="bi bi-journal-text"></i>
                <span>My Courses</span>
            </a>
        </li>

        <li class="menu-item {{ request()->routeIs('faculty.lms.*') ? 'active' : '' }}">
            <a href="{{ route('faculty.lms.index') }}" class="menu-link">
                <i class="bi bi-bar-chart-line"></i>
                <span>LMS Analytics</span>
            </a>
        </li>

        {{-- Added Assignments menu item --}}
        <li class="menu-item {{ request()->routeIs('faculty.assignments.*') ? 'active' : '' }}">
            <a href="{{ route('faculty.assignments.index') }}" class="menu-link">
                <i class="bi bi-file-earmark-text"></i>
                <span>Assignments</span>
            </a>
        </li>

        {{-- Added Exams menu item --}}
        <li class="menu-item {{ request()->routeIs('faculty.exams.*') ? 'active' : '' }}">
            <a href="{{ route('faculty.exams.index') }}" class="menu-link">
                <i class="bi bi-pencil-square"></i>
                <span>Exams</span>
            </a>
        </li>

        {{-- Added Quizzes menu item --}}
        <li class="menu-item {{ request()->routeIs('faculty.quizzes.*') ? 'active' : '' }}">
            <a href="{{ route('faculty.quizzes.index') }}" class="menu-link">
                <i class="bi bi-clipboard-check"></i>
                <span>Quizzes</span>
            </a>
        </li>

        <li class="menu-item {{ request()->routeIs('faculty.attendance.*') ? 'active' : '' }}">
            <a href="{{ route('faculty.attendance.index') }}" class="menu-link">
                <i class="bi bi-calendar-check"></i>
                <span>Attendance</span>
            </a>
        </li>

        <li class="menu-item {{ request()->routeIs('faculty.grading.*') ? 'active' : '' }}">
            <a href="{{ route('faculty.grading.index') }}" class="menu-link">
                <i class="bi bi-award"></i>
                <span>Grading</span>
            </a>
        </li>

        <li class="menu-item {{ request()->routeIs('faculty.materials.*') ? 'active' : '' }}">
            <a href="{{ route('faculty.materials.index') }}" class="menu-link">
                <i class="bi bi-file-earmark-text"></i>
                <span>Course Materials</span>
            </a>
        </li>
        @endif

        @if(auth()->user()->isStudent() && auth()->user()->isAdmitted())
        <li class="menu-header">Student</li>

        <li class="menu-item {{ request()->routeIs('student.courses.*') && !request()->routeIs('student.courses.enrollments') ? 'active' : '' }}">
            <a href="{{ route('student.courses.index') }}" class="menu-link">
                <i class="bi bi-journal-text"></i>
                <span>My Courses</span>
            </a>
        </li>

        <li class="menu-item {{ request()->routeIs('student.lms.*') ? 'active' : '' }}">
            <a href="{{ route('student.lms.index') }}" class="menu-link">
                <i class="bi bi-play-circle"></i>
                <span>My Learning</span>
            </a>
        </li>

        {{-- Updating assignment route link --}}
        <li class="menu-item {{ request()->routeIs('student.assignments.*') ? 'active' : '' }}">
            <a href="{{ route('student.assignments.index') }}" class="menu-link">
                <i class="bi bi-file-earmark-text"></i>
                <span>Assignments</span>
            </a>
        </li>

        <li class="menu-item {{ request()->routeIs('student.program-changes.*') ? 'active' : '' }}">
            <a href="{{ route('student.program-changes.index') }}" class="menu-link">
                <i class="bi bi-arrow-repeat"></i>
                <span>Program Change</span>
            </a>
        </li>

        {{-- Adding exams menu item --}}
        <li class="menu-item {{ request()->routeIs('student.exams.*') ? 'active' : '' }}">
            <a href="{{ route('student.exams.index') }}" class="menu-link">
                <i class="bi bi-pencil-square"></i>
                <span>Exams</span>
                @php
                    $activeExamCount = 0;
                    try {
                        $student = Auth::user();
                        $enrolledCourseIds = $student->courseEnrollments()
                            ->where('status', 'enrolled')
                            ->pluck('course_id');
                        $now = \Carbon\Carbon::now();
                        $activeExams = \App\Models\Exam::whereIn('course_id', $enrolledCourseIds)
                            ->where('start_time', '<=', $now)
                            ->where('end_time', '>=', $now)
                            ->with(['attempts' => function ($query) use ($student) {
                                $query->where('user_id', $student->id);
                            }])
                            ->get();

                        $activeExamCount = $activeExams->filter(function ($exam) use ($now) {
                            $attempt = $exam->attempts->first();
                            if (!$attempt) {
                                return true;
                            }
                            if (in_array($attempt->status, ['submitted', 'graded'], true)) {
                                return false;
                            }
                            if (!$attempt->started_at) {
                                return true;
                            }
                            $byDuration = $attempt->started_at->copy()->addMinutes($exam->duration_minutes);
                            $deadline = $exam->end_time;
                            $effectiveEnd = $deadline && $deadline->lt($byDuration) ? $deadline : $byDuration;
                            return $effectiveEnd->gt($now);
                        })->count();
                    } catch (\Exception $e) {
                        $activeExamCount = 0;
                    }
                @endphp
                @if($activeExamCount > 0)
                    <span class="badge bg-danger rounded-pill ms-auto">{{ $activeExamCount }}</span>
                @endif
            </a>
        </li>

        {{-- Updating grades route link --}}
        <li class="menu-item {{ request()->routeIs('student.grades.*') ? 'active' : '' }}">
            <a href="{{ route('student.grades.index') }}" class="menu-link">
                <i class="bi bi-award"></i>
                <span>Grades</span>
            </a>
        </li>

        {{-- Updating attendance route link --}}
        <li class="menu-item {{ request()->routeIs('student.attendance.*') ? 'active' : '' }}">
            <a href="{{ route('student.attendance.index') }}" class="menu-link">
                <i class="bi bi-calendar-check"></i>
                <span>Attendance</span>
            </a>
        </li>

        {{-- My Programme & Enrollment --}}
        <li class="menu-item {{ request()->routeIs('student.my-programme') ? 'active' : '' }}">
            <a href="{{ route('student.my-programme') }}" class="menu-link">
                <i class="bi bi-journal-bookmark" style="color: #3a7bd5;"></i>
                <span>My Programme</span>
            </a>
        </li>

        <li class="menu-item {{ request()->routeIs('student.enrollment.*') ? 'active' : '' }}">
            <a href="{{ route('student.enrollment.index') }}" class="menu-link">
                <i class="bi bi-person-plus-fill" style="color: #3a7bd5;"></i>
                <span>Enrollment & Registration</span>
            </a>
        </li>

        {{-- Single Timetable Link --}}
        <li class="menu-item {{ request()->routeIs('student.timetables.*') ? 'active' : '' }}">
            <a href="{{ route('student.timetables.teaching') }}" class="menu-link">
                <i class="bi bi-calendar3" style="color: #3a7bd5;"></i>
                <span>Timetables</span>
            </a>
        </li>

        {{-- Mailbox & Academic Calendar --}}
        <li class="menu-item {{ request()->routeIs('messages.*') ? 'active' : '' }}">
            <a href="{{ route('messages.index') }}" class="menu-link">
                <i class="bi bi-envelope-paper" style="color: #3a7bd5;"></i>
                <span>Mailbox</span>
            </a>
        </li>

        <li class="menu-item {{ request()->routeIs('academic-calendar.*') ? 'active' : '' }}">
            <a href="{{ route('academic-calendar.index') }}" class="menu-link">
                <i class="bi bi-calendar-week" style="color: #3a7bd5;"></i>
                <span>Academic Calendar</span>
            </a>
        </li>

        @php
            $isPaymentsActive = request()->routeIs('student.fees.*');
        @endphp
        {{-- Collapsible Payments Menu for Student --}}
        <li class="menu-item-dropdown {{ $isPaymentsActive ? 'active' : '' }}">
            <a href="javascript:void(0);" class="menu-link dropdown-toggle-btn d-flex align-items-center" onclick="toggleDropdownMenu(this)" style="{{ $isPaymentsActive ? 'background-color: #3a7bd5; color: #ffffff;' : '' }}">
                <i class="bi bi-cash-coin" style="{{ $isPaymentsActive ? 'color: #ffffff;' : 'color: #3a7bd5;' }}"></i>
                <span class="{{ $isPaymentsActive ? 'text-white' : '' }} fw-bold text-uppercase">Payments</span>
                <i class="bi bi-chevron-up ms-auto dropdown-chevron" style="font-size: 0.8rem; {{ $isPaymentsActive ? 'color: #ffffff;' : 'color: #3a7bd5;' }}"></i>
            </a>
            <ul class="submenu-list" style="list-style: none; padding-left: 2rem; margin: 0; display: block;">
                <li class="submenu-item py-1">
                    <a href="{{ route('student.fees.index') }}" class="submenu-link d-flex align-items-center py-2 text-decoration-none" style="font-size: 0.825rem; font-weight: 600; color: {{ request()->routeIs('student.fees.index') ? '#ffffff' : 'rgba(255, 255, 255, 0.8)' }}; transition: color 0.2s;">
                        <i class="bi bi-paperclip me-2" style="font-size: 1rem; color: {{ request()->routeIs('student.fees.index') ? '#3a7bd5' : 'inherit' }}"></i>
                        <span>MY BILLS/INVOICES</span>
                    </a>
                </li>
                <li class="submenu-item py-1">
                    <a href="{{ route('student.fees.ledger') }}" class="submenu-link d-flex align-items-center py-2 text-decoration-none" style="font-size: 0.825rem; font-weight: 600; color: {{ request()->routeIs('student.fees.ledger') ? '#ffffff' : 'rgba(255, 255, 255, 0.8)' }}; transition: color 0.2s;">
                        <i class="bi bi-file-earmark-check me-2" style="font-size: 1rem;"></i>
                        <span>MY TRANSACTIONS</span>
                    </a>
                </li>
                <li class="submenu-item py-1">
                    <a href="{{ route('student.fees.ledger') }}" class="submenu-link d-flex align-items-center py-2 text-decoration-none" style="font-size: 0.825rem; font-weight: 600; color: {{ request()->routeIs('student.fees.ledger') ? '#ffffff' : 'rgba(255, 255, 255, 0.8)' }}; transition: color 0.2s;">
                        <i class="bi bi-file-earmark-text me-2" style="font-size: 1rem; color: {{ request()->routeIs('student.fees.ledger') ? '#3a7bd5' : 'inherit' }}"></i>
                        <span>MY STUDENT LEDGER</span>
                    </a>
                </li>
                <li class="submenu-item py-1">
                    <a href="{{ route('student.fees.index') }}" class="submenu-link d-flex align-items-center py-2 text-decoration-none" style="font-size: 0.825rem; font-weight: 600; color: {{ request()->routeIs('student.fees.index') ? '#ffffff' : 'rgba(255, 255, 255, 0.8)' }}; transition: color 0.2s;">
                        <i class="bi bi-file-earmark-richtext me-2" style="font-size: 1rem;"></i>
                        <span>MY FEES STRUCTURE</span>
                    </a>
                </li>
            </ul>
        </li>

        {{-- E-Voting --}}
        <li class="menu-item {{ request()->routeIs('student.evoting.*') ? 'active' : '' }}">
            <a href="{{ route('student.evoting.index') }}" class="menu-link">
                <i class="bi bi-check2-square" style="color: #3a7bd5;"></i>
                <span>E-Voting</span>
            </a>
        </li>

        {{-- My MailBox --}}
        <li class="menu-item {{ request()->routeIs('messages.*') ? 'active' : '' }}">
            <a href="{{ route('messages.index') }}" class="menu-link">
                <i class="bi bi-envelope" style="color: #3a7bd5;"></i>
                <span>My MailBox</span>
            </a>
        </li>

        {{-- Academic Calendar --}}
        <li class="menu-item {{ request()->routeIs('academic-calendar.*') ? 'active' : '' }}">
            <a href="{{ route('academic-calendar.index') }}" class="menu-link">
                <i class="bi bi-calendar-event" style="color: #3a7bd5;"></i>
                <span>Academic Calendar</span>
            </a>
        </li>

        {{-- Evaluation Survey --}}
        <li class="menu-item {{ request()->routeIs('student.evaluation-surveys.*') ? 'active' : '' }}">
            <a href="{{ route('student.evaluation-surveys.index') }}" class="menu-link">
                <i class="bi bi-clipboard2-check" style="color: #3a7bd5;"></i>
                <span>Evaluation Survey</span>
            </a>
        </li>
        @endif


        <li class="menu-header">Common</li>

        <li class="menu-item {{ request()->routeIs('profile.*') ? 'active' : '' }}">
            <a href="{{ route('profile.show') }}" class="menu-link">
                <i class="bi bi-file-earmark-person"></i>
                <span>My Bio Data & Profile</span>
            </a>
        </li>

        <li class="menu-item {{ request()->routeIs('forums.*') ? 'active' : '' }}">
            <a href="{{ route('forums.index') }}" class="menu-link">
                <i class="bi bi-chat-dots"></i>
                <span>Forums</span>
            </a>
        </li>

        <li class="menu-item {{ request()->routeIs('notifications.*') ? 'active' : '' }}">
            <a href="{{ route('notifications.index') }}" class="menu-link">
                <i class="bi bi-bell"></i>
                <span>Notifications</span>
                @php
                    $unreadCount = Auth::check() ? Auth::user()->notifications()->where('is_read', false)->count() : 0;
                @endphp
                @if($unreadCount > 0)
                    <span class="badge bg-danger rounded-pill ms-auto">{{ $unreadCount }}</span>
                @endif
            </a>
        </li>

        <li class="menu-item {{ request()->routeIs('help.*') ? 'active' : '' }}">
            <a href="{{ route('support.index') }}" class="menu-link">
                <i class="bi bi-question-circle"></i>
                <span>Help & Support</span>
            </a>
        </li>

        <li class="menu-item">
            <a href="{{ route('logout') }}" class="menu-link"
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="bi bi-box-arrow-right"></i>
                <span>Logout</span>
            </a>
        </li>
    </ul>
    @endauth

    @guest
    <ul class="sidebar-menu guest-menu">
        <li class="menu-header">Welcome</li>

        <li class="menu-item {{ request()->routeIs('login') ? 'active' : '' }}">
            <a href="{{ route('login') }}" class="menu-link">
                <i class="bi bi-box-arrow-in-right"></i>
                <span>Login</span>
            </a>
        </li>

        <li class="menu-item {{ request()->routeIs('register') ? 'active' : '' }}">
            <a href="{{ route('register') }}" class="menu-link">
                <i class="bi bi-person-plus"></i>
                <span>Register</span>
            </a>
        </li>

        <li class="menu-item">
            <a href="{{ url('/#about') }}" class="menu-link">
                <i class="bi bi-info-circle"></i>
                <span>About JBI University</span>
            </a>
        </li>

        <li class="menu-item">
            <a href="{{ url('/#contact') }}" class="menu-link">
                <i class="bi bi-telephone"></i>
                <span>Contact Us</span>
            </a>
        </li>
    </ul>
    @endguest

    <div class="sidebar-footer">
        <p class="text-center mb-0">&copy; {{ date('Y') }} JBI University</p>
        <p class="text-center mb-0"><small>Version 1.0.0</small></p>
    </div>
</div>

<form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
    @csrf
</form>

<style>
/* Sidebar Styles */
.sidebar-wrapper {
    height: 100%;
    width: 100%;
    display: flex;
    flex-direction: column;
    background-color: #1a2236;
    color: #e4e6eb;
    transition: all 0.3s ease;
}

.sidebar-header {
    padding: 1rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.logo-container {
    display: flex;
    align-items: center;
}

.logo-img {
    height: 40px;
    width: auto;
    margin-right: 0.5rem;
}

.logo-text {
    font-size: 1.2rem;
    font-weight: 600;
    color: #ffffff;
}

.sidebar-toggle {
    background: transparent;
    border: none;
    color: #ffffff;
    font-size: 1.25rem;
    cursor: pointer;
}

.user-profile {
    padding: 1rem;
    display: flex;
    align-items: center;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.user-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    overflow: hidden;
    margin-right: 0.75rem;
    border: 2px solid rgba(255, 255, 255, 0.2);
}

.user-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.user-info {
    flex: 1;
}

.user-name {
    margin: 0;
    font-size: 0.9rem;
    font-weight: 600;
    color: #ffffff;
}

.user-role {
    font-size: 0.75rem;
    color: rgba(255, 255, 255, 0.7);
}

.sidebar-menu {
    list-style: none;
    padding: 0;
    margin: 0;
    overflow-y: auto;
    flex: 1;
}

.menu-header {
    padding: 0.75rem 1rem 0.5rem;
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: rgba(255, 255, 255, 0.5);
    font-weight: 600;
}

.menu-item {
    position: relative;
}

.menu-link {
    display: flex;
    align-items: center;
    padding: 0.75rem 1rem;
    color: rgba(255, 255, 255, 0.7);
    text-decoration: none;
    transition: all 0.2s ease;
}

.menu-link:hover {
    color: #ffffff;
    background-color: rgba(255, 255, 255, 0.1);
}

.menu-item.active .menu-link {
    color: #ffffff;
    background-color: #3a7bd5;
    font-weight: 500;
}

.menu-link i {
    margin-right: 0.75rem;
    font-size: 1.1rem;
    width: 20px;
    text-align: center;
}

.badge {
    font-size: 0.65rem;
    padding: 0.25rem 0.5rem;
    border-radius: 10px;
}

.bg-warning {
    background-color: #ffc107 !important;
    color: #000;
}

.bg-danger {
    background-color: #dc3545 !important;
    color: #fff;
}

.sidebar-footer {
    padding: 1rem;
    font-size: 0.75rem;
    color: rgba(255, 255, 255, 0.5);
    border-top: 1px solid rgba(255, 255, 255, 0.1);
}

/* Mobile Responsiveness - Enhanced for Guest Users */
@media (max-width: 767.98px) {
    .sidebar-wrapper {
        position: fixed;
        top: 0;
        left: 0;
        bottom: 0;
        width: 280px;
        z-index: 1040;
        transform: translateX(-100%);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        transition: transform 0.3s ease;
    }

    .sidebar-wrapper.show {
        transform: translateX(0);
    }

    /* Mobile overlay for closing sidebar */
    .sidebar-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 1030;
        display: none;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .sidebar-overlay.show {
        display: block;
        opacity: 1;
    }

    /* Guest menu specific mobile styles */
    .guest-menu .menu-link {
        padding: 1rem 1.5rem;
        font-size: 1rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .guest-menu .menu-link:last-child {
        border-bottom: none;
    }

    /* Mobile toggle button enhancement */
    .sidebar-toggle {
        padding: 0.5rem;
        border-radius: 4px;
        transition: background-color 0.2s ease;
    }

    .sidebar-toggle:hover {
        background-color: rgba(255, 255, 255, 0.1);
    }

    /* Ensure proper spacing on mobile */
    .user-profile {
        padding: 1.5rem 1rem;
    }

    .sidebar-header {
        padding: 1.5rem 1rem;
    }
}

/* Desktop Styles */
@media (min-width: 768px) {
    .sidebar-wrapper {
        width: 250px;
        position: fixed;
        top: 0;
        left: 0;
        bottom: 0;
        z-index: 1030;
    }

    .sidebar-toggle {
        display: none;
    }

    main {
        margin-left: 250px;
    }
}

/* Mobile Navigation Bar for Guest Users */
.mobile-nav {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    background: linear-gradient(135deg, var(--jbi-primary) 0%, var(--jbi-secondary) 100%);
    z-index: 1050;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.mobile-nav-content {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.75rem 1rem;
    height: 60px;
}

.mobile-nav-toggle {
    background: transparent;
    border: none;
    color: white;
    font-size: 1.5rem;
    padding: 0.5rem;
    border-radius: 4px;
    transition: background-color 0.2s ease;
}

.mobile-nav-toggle:hover {
    background-color: rgba(255, 255, 255, 0.1);
}

.mobile-nav-brand {
    display: flex;
    align-items: center;
    flex: 1;
    justify-content: center;
    margin: 0 1rem;
}

.mobile-logo {
    height: 32px;
    width: auto;
    margin-right: 0.5rem;
}

.mobile-brand-text {
    color: white;
    font-weight: 600;
    font-size: 1rem;
}

.mobile-nav-actions {
    display: flex;
    align-items: center;
}

.mobile-nav-actions .btn {
    font-size: 0.875rem;
    padding: 0.375rem 0.75rem;
}

/* Adjust main content for mobile nav */
@media (max-width: 767.98px) {
    body.guest-user {
        padding-top: 60px;
    }

    .sidebar-wrapper {
        top: 60px;
        height: calc(100vh - 60px);
    }
}

/* Submenu hover styles */
.submenu-link:hover {
    color: #ffffff !important;
    background-color: rgba(255, 255, 255, 0.05);
    border-radius: 4px;
}
.submenu-link {
    padding-left: 0.5rem;
}
</style>

<script>
// Enhanced mobile sidebar functionality
document.addEventListener('DOMContentLoaded', function() {
    const sidebarToggleBtn = document.getElementById('sidebar-toggle-btn');
    const sidebar = document.querySelector('.sidebar-wrapper');
    let sidebarOverlay = document.querySelector('.sidebar-overlay');

    // Create overlay if it doesn't exist (for guest users)
    if (!sidebarOverlay) {
        sidebarOverlay = document.createElement('div');
        sidebarOverlay.className = 'sidebar-overlay';
        document.body.appendChild(sidebarOverlay);
    }

    // Toggle sidebar function
    function toggleSidebar() {
        const isOpen = sidebar.classList.contains('show');

        if (isOpen) {
            closeSidebar();
        } else {
            openSidebar();
        }
    }

    function openSidebar() {
        sidebar.classList.add('show');
        sidebarOverlay.classList.add('show');
        document.body.style.overflow = 'hidden'; // Prevent background scrolling

        if (sidebarToggleBtn) {
            sidebarToggleBtn.innerHTML = '<i class="bi bi-x-lg"></i>';
        }
    }

    function closeSidebar() {
        sidebar.classList.remove('show');
        sidebarOverlay.classList.remove('show');
        document.body.style.overflow = ''; // Restore scrolling

        if (sidebarToggleBtn) {
            sidebarToggleBtn.innerHTML = '<i class="bi bi-list"></i>';
        }
    }

    // Toggle button event listener
    if (sidebarToggleBtn) {
        sidebarToggleBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            toggleSidebar();
        });
    }

    // Overlay click to close
    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', function() {
            closeSidebar();
        });
    }

    // Close sidebar when clicking menu links on mobile (for guest users)
    const menuLinks = document.querySelectorAll('.sidebar-menu .menu-link');
    menuLinks.forEach(function(link) {
        link.addEventListener('click', function() {
            if (window.innerWidth < 768) {
                setTimeout(closeSidebar, 150); // Small delay for better UX
            }
        });
    });

    // Handle window resize
    window.addEventListener('resize', function() {
        if (window.innerWidth >= 768) {
            closeSidebar();
        }
    });

    // Handle escape key to close sidebar
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && sidebar.classList.contains('show')) {
            closeSidebar();
        }
    });

    // Prevent sidebar from closing when clicking inside it
    sidebar.addEventListener('click', function(e) {
        e.stopPropagation();
    });
});

function toggleDropdownMenu(btn) {
    const parent = btn.parentElement;
    const submenu = parent.querySelector('.submenu-list');
    const chevron = btn.querySelector('.dropdown-chevron');
    
    if (submenu.style.display === 'none' || submenu.style.display === '') {
        submenu.style.display = 'block';
        chevron.classList.replace('bi-chevron-down', 'bi-chevron-up');
    } else {
        submenu.style.display = 'none';
        chevron.classList.replace('bi-chevron-up', 'bi-chevron-down');
    }
}
</script>
