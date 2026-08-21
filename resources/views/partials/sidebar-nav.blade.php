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

        {{-- Show role-specific dashboard links (Anchor Point, always top-level) --}}
        @if(auth()->user()->isAdmin())
        <li class="menu-item {{ request()->routeIs('dashboard') || request()->routeIs('admin.dashboard') ? 'active' : '' }}">
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

        {{-- ==================== ADMIN 5 COLLAPSIBLE GROUPS ==================== --}}
        @if(auth()->user()->isAdmin())
        @php
            // Group 1: Administration
            $isAdminActive = request()->routeIs('admin.users.*') ||
                             request()->routeIs('admin.roles.*') ||
                             request()->routeIs('admin.settings*');

            // Group 2: Admissions & People
            $isAdmissionsActive = request()->routeIs('admin.applications.*') ||
                                  request()->routeIs('admin.students.*') ||
                                  request()->routeIs('admin.faculty-staff.*');

            // Group 3: Academic Structure
            $isAcademicActive = request()->routeIs('admin.courses.*') ||
                                request()->routeIs('admin.programs.*') ||
                                request()->routeIs('admin.program-levels.*') ||
                                request()->routeIs('admin.program-changes.*') ||
                                request()->routeIs('admin.faculties.*') ||
                                request()->routeIs('admin.departments.*') ||
                                request()->routeIs('admin.academic-years.*') ||
                                request()->routeIs('admin.semesters.*');

            // Group 4: Finance & Governance
            $isFinanceActive = request()->routeIs('admin.finance.*') ||
                               request()->routeIs('admin.fees.*') ||
                               request()->routeIs('admin.reports.*');

            // Group 5: Operations & Engagement
            $isOperationsActive = request()->routeIs('admin.timetables.*') ||
                                  request()->routeIs('admin.evoting.*') ||
                                  request()->routeIs('admin.evaluation-surveys.*');
        @endphp

        {{-- Group 1: Administration (collapsible) --}}
        <li class="sidebar-group {{ $isAdminActive ? 'has-active-child' : '' }}" data-group-id="admin-administration">
            <button type="button" class="sidebar-group-toggle {{ $isAdminActive ? 'has-active-child is-open' : '' }}" onclick="toggleSidebarGroup(this, 'admin-administration')">
                <i class="bi bi-shield-shaded group-icon"></i>
                <span class="group-title">Administration</span>
                <i class="bi bi-chevron-down group-chevron"></i>
            </button>
            <ul class="sidebar-group-menu" style="{{ $isAdminActive ? 'display: block;' : 'display: none;' }}">
                <li class="submenu-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.users.index') }}" class="submenu-link">
                        <i class="bi bi-people"></i>
                        <span>User Management</span>
                    </a>
                </li>
                <li class="submenu-item {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.roles.index') }}" class="submenu-link">
                        <i class="bi bi-shield-lock"></i>
                        <span>Roles & Permissions</span>
                    </a>
                </li>
                <li class="submenu-item {{ request()->routeIs('admin.settings*') ? 'active' : '' }}">
                    <a href="{{ route('admin.settings') }}" class="submenu-link">
                        <i class="bi bi-sliders"></i>
                        <span>System Settings</span>
                    </a>
                </li>
            </ul>
        </li>

        {{-- Group 2: Admissions & People (collapsible) --}}
        <li class="sidebar-group {{ $isAdmissionsActive ? 'has-active-child' : '' }}" data-group-id="admin-admissions">
            <button type="button" class="sidebar-group-toggle {{ $isAdmissionsActive ? 'has-active-child is-open' : '' }}" onclick="toggleSidebarGroup(this, 'admin-admissions')">
                <i class="bi bi-people-fill group-icon"></i>
                <span class="group-title">Admissions & People</span>
                <i class="bi bi-chevron-down group-chevron"></i>
            </button>
            <ul class="sidebar-group-menu" style="{{ $isAdmissionsActive ? 'display: block;' : 'display: none;' }}">
                <li class="submenu-item {{ request()->routeIs('admin.applications.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.applications.index') }}" class="submenu-link">
                        <i class="bi bi-file-earmark-check"></i>
                        <span>Applications</span>
                        @php
                            try {
                                $pendingApplicationsCount = \App\Models\Application::where('status', 'pending')->count();
                                if ($pendingApplicationsCount > 0) {
                                    echo '<span class="badge bg-danger rounded-pill ms-auto">' . $pendingApplicationsCount . '</span>';
                                }
                            } catch (\Exception $e) {}
                        @endphp
                    </a>
                </li>
                <li class="submenu-item {{ request()->routeIs('admin.students.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.students.index') }}" class="submenu-link">
                        <i class="bi bi-mortarboard"></i>
                        <span>Student Management</span>
                    </a>
                </li>
                <li class="submenu-item {{ request()->routeIs('admin.faculty-staff.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.faculty-staff.index') }}" class="submenu-link">
                        <i class="bi bi-person-badge"></i>
                        <span>Faculty Staff Management</span>
                    </a>
                </li>
            </ul>
        </li>

        {{-- Group 3: Academic Structure (collapsible) --}}
        <li class="sidebar-group {{ $isAcademicActive ? 'has-active-child' : '' }}" data-group-id="admin-academics">
            <button type="button" class="sidebar-group-toggle {{ $isAcademicActive ? 'has-active-child is-open' : '' }}" onclick="toggleSidebarGroup(this, 'admin-academics')">
                <i class="bi bi-diagram-3-fill group-icon"></i>
                <span class="group-title">Academic Structure</span>
                <i class="bi bi-chevron-down group-chevron"></i>
            </button>
            <ul class="sidebar-group-menu" style="{{ $isAcademicActive ? 'display: block;' : 'display: none;' }}">
                <li class="submenu-item {{ request()->routeIs('admin.courses.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.courses.index') }}" class="submenu-link">
                        <i class="bi bi-journal-bookmark"></i>
                        <span>Course Management</span>
                    </a>
                </li>
                <li class="submenu-item {{ request()->routeIs('admin.programs.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.programs.index') }}" class="submenu-link">
                        <i class="bi bi-collection"></i>
                        <span>Program Management</span>
                    </a>
                </li>
                <li class="submenu-item {{ request()->routeIs('admin.program-levels.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.program-levels.index') }}" class="submenu-link">
                        <i class="bi bi-layers"></i>
                        <span>Program Levels</span>
                    </a>
                </li>
                <li class="submenu-item {{ request()->routeIs('admin.program-changes.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.program-changes.index') }}" class="submenu-link">
                        <i class="bi bi-arrow-repeat"></i>
                        <span>Program Change Requests</span>
                    </a>
                </li>
                <li class="submenu-item {{ request()->routeIs('admin.faculties.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.faculties.index') }}" class="submenu-link">
                        <i class="bi bi-buildings"></i>
                        <span>Faculties Management</span>
                    </a>
                </li>
                <li class="submenu-item {{ request()->routeIs('admin.departments.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.departments.index') }}" class="submenu-link">
                        <i class="bi bi-building"></i>
                        <span>Department Management</span>
                    </a>
                </li>
                <li class="submenu-item {{ request()->routeIs('admin.academic-years.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.academic-years.index') }}" class="submenu-link">
                        <i class="bi bi-calendar3"></i>
                        <span>Academic Years</span>
                    </a>
                </li>
                <li class="submenu-item {{ request()->routeIs('admin.semesters.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.semesters.index') }}" class="submenu-link">
                        <i class="bi bi-calendar2"></i>
                        <span>Semesters</span>
                    </a>
                </li>
            </ul>
        </li>

        {{-- Group 4: Finance & Governance (collapsible) --}}
        <li class="sidebar-group {{ $isFinanceActive ? 'has-active-child' : '' }}" data-group-id="admin-finance">
            <button type="button" class="sidebar-group-toggle {{ $isFinanceActive ? 'has-active-child is-open' : '' }}" onclick="toggleSidebarGroup(this, 'admin-finance')">
                <i class="bi bi-bank group-icon"></i>
                <span class="group-title">Finance & Governance</span>
                <i class="bi bi-chevron-down group-chevron"></i>
            </button>
            <ul class="sidebar-group-menu" style="{{ $isFinanceActive ? 'display: block;' : 'display: none;' }}">
                <li class="submenu-item {{ request()->routeIs('admin.finance.dashboard') ? 'active' : '' }}">
                    <a href="{{ route('admin.finance.dashboard') }}" class="submenu-link fw-semibold text-primary-light">
                        <i class="bi bi-speedometer"></i>
                        <span>Finance & Bursar Hub</span>
                    </a>
                </li>
                <li class="submenu-item {{ request()->routeIs('admin.fees.structures.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.fees.structures.index') }}" class="submenu-link">
                        <i class="bi bi-file-earmark-spreadsheet"></i>
                        <span>Fee Structures</span>
                    </a>
                </li>
                <li class="submenu-item {{ request()->routeIs('admin.fees.index') ? 'active' : '' }}">
                    <a href="{{ route('admin.fees.index') }}" class="submenu-link">
                        <i class="bi bi-receipt"></i>
                        <span>Student Fee Records</span>
                    </a>
                </li>
                <li class="submenu-item {{ request()->routeIs('admin.finance.revenue.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.finance.revenue.index') }}" class="submenu-link">
                        <i class="bi bi-currency-exchange"></i>
                        <span>Revenue & Income</span>
                    </a>
                </li>
                <li class="submenu-item {{ request()->routeIs('admin.finance.budgets.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.finance.budgets.index') }}" class="submenu-link">
                        <i class="bi bi-pie-chart"></i>
                        <span>Department Budgets</span>
                    </a>
                </li>
                <li class="submenu-item {{ request()->routeIs('admin.finance.expenses.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.finance.expenses.index') }}" class="submenu-link">
                        <i class="bi bi-cart-check"></i>
                        <span>Expenditures</span>
                    </a>
                </li>
                <li class="submenu-item {{ request()->routeIs('admin.finance.payables.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.finance.payables.index') }}" class="submenu-link">
                        <i class="bi bi-truck"></i>
                        <span>Accounts Payable</span>
                    </a>
                </li>
                <li class="submenu-item {{ request()->routeIs('admin.finance.receivables.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.finance.receivables.index') }}" class="submenu-link">
                        <i class="bi bi-person-lines-fill"></i>
                        <span>Accounts Receivable</span>
                    </a>
                </li>
                <li class="submenu-item {{ request()->routeIs('admin.finance.payroll.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.finance.payroll.index') }}" class="submenu-link">
                        <i class="bi bi-person-badge"></i>
                        <span>Payroll Management</span>
                    </a>
                </li>
                <li class="submenu-item {{ request()->routeIs('admin.finance.assets.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.finance.assets.index') }}" class="submenu-link">
                        <i class="bi bi-qr-code-scan"></i>
                        <span>Asset Management</span>
                    </a>
                </li>
                <li class="submenu-item {{ request()->routeIs('admin.finance.banking.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.finance.banking.index') }}" class="submenu-link">
                        <i class="bi bi-piggy-bank"></i>
                        <span>Banking & Cash</span>
                    </a>
                </li>
                <li class="submenu-item {{ request()->routeIs('admin.finance.reports.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.finance.reports.index') }}" class="submenu-link">
                        <i class="bi bi-journal-text"></i>
                        <span>Financial Statements</span>
                    </a>
                </li>
                <li class="submenu-item {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.reports.index') }}" class="submenu-link">
                        <i class="bi bi-file-earmark-bar-graph"></i>
                        <span>Reports</span>
                    </a>
                </li>
            </ul>
        </li>

        {{-- Group 5: Operations & Engagement (collapsible) --}}
        <li class="sidebar-group {{ $isOperationsActive ? 'has-active-child' : '' }}" data-group-id="admin-operations">
            <button type="button" class="sidebar-group-toggle {{ $isOperationsActive ? 'has-active-child is-open' : '' }}" onclick="toggleSidebarGroup(this, 'admin-operations')">
                <i class="bi bi-kanban group-icon"></i>
                <span class="group-title">Operations & Engagement</span>
                <i class="bi bi-chevron-down group-chevron"></i>
            </button>
            <ul class="sidebar-group-menu" style="{{ $isOperationsActive ? 'display: block;' : 'display: none;' }}">
                <li class="submenu-item {{ request()->routeIs('admin.timetables.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.timetables.index') }}" class="submenu-link">
                        <i class="bi bi-calendar3"></i>
                        <span>Timetable Management</span>
                    </a>
                </li>
                <li class="submenu-item {{ request()->routeIs('admin.evoting.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.evoting.index') }}" class="submenu-link">
                        <i class="bi bi-check2-square"></i>
                        <span>E-Voting Management</span>
                    </a>
                </li>
                <li class="submenu-item {{ request()->routeIs('admin.evaluation-surveys.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.evaluation-surveys.index') }}" class="submenu-link">
                        <i class="bi bi-clipboard2-check"></i>
                        <span>Evaluation Surveys</span>
                    </a>
                </li>
            </ul>
        </li>
        @endif

        {{-- ==================== FACULTY NAVIGATION ==================== --}}
        @if(auth()->user()->isFaculty())
        <li class="menu-header">Faculty</li>

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

        <li class="menu-item {{ request()->routeIs('faculty.assignments.*') ? 'active' : '' }}">
            <a href="{{ route('faculty.assignments.index') }}" class="menu-link">
                <i class="bi bi-file-earmark-text"></i>
                <span>Assignments</span>
            </a>
        </li>

        <li class="menu-item {{ request()->routeIs('faculty.exams.*') ? 'active' : '' }}">
            <a href="{{ route('faculty.exams.index') }}" class="menu-link">
                <i class="bi bi-pencil-square"></i>
                <span>Exams</span>
            </a>
        </li>

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

        {{-- ==================== STUDENT NAVIGATION ==================== --}}
        @if(auth()->user()->isStudent() && auth()->user()->isAdmitted())
        <li class="menu-header">Student</li>

        @php
            $isAcademicsActive = request()->routeIs('student.courses.*') ||
                                request()->routeIs('student.lms.*') ||
                                request()->routeIs('student.assignments.*') ||
                                request()->routeIs('student.program-changes.*') ||
                                request()->routeIs('student.exams.*') ||
                                request()->routeIs('student.attendance.*');
        @endphp
        <li class="menu-item-dropdown {{ $isAcademicsActive ? 'active' : '' }}">
            <a href="javascript:void(0);" class="menu-link dropdown-toggle-btn d-flex align-items-center" onclick="toggleDropdownMenu(this)">
                <i class="bi bi-book"></i>
                <span class="fw-bold text-uppercase">Academics</span>
                <i class="bi bi-chevron-down ms-auto dropdown-chevron" style="font-size: 0.8rem;"></i>
            </a>
            <ul class="submenu-list" style="list-style: none; padding-left: 2rem; margin: 0; display: {{ $isAcademicsActive ? 'block' : 'none' }};">
                <li class="submenu-item py-1">
                    <a href="{{ route('student.courses.index') }}" class="submenu-link d-flex align-items-center py-2 text-decoration-none" style="font-size: 0.825rem; font-weight: 600; color: {{ request()->routeIs('student.courses.*') ? '#ffffff' : 'rgba(255, 255, 255, 0.8)' }}; transition: color 0.2s;">
                        <i class="bi bi-journal-text me-2" style="font-size: 1rem;"></i>
                        <span>MY COURSES</span>
                    </a>
                </li>
                <li class="submenu-item py-1">
                    <a href="{{ route('student.lms.index') }}" class="submenu-link d-flex align-items-center py-2 text-decoration-none" style="font-size: 0.825rem; font-weight: 600; color: {{ request()->routeIs('student.lms.*') ? '#ffffff' : 'rgba(255, 255, 255, 0.8)' }}; transition: color 0.2s;">
                        <i class="bi bi-play-circle me-2" style="font-size: 1rem;"></i>
                        <span>MY LEARNING</span>
                    </a>
                </li>
                <li class="submenu-item py-1">
                    <a href="{{ route('student.assignments.index') }}" class="submenu-link d-flex align-items-center py-2 text-decoration-none" style="font-size: 0.825rem; font-weight: 600; color: {{ request()->routeIs('student.assignments.*') ? '#ffffff' : 'rgba(255, 255, 255, 0.8)' }}; transition: color 0.2s;">
                        <i class="bi bi-file-earmark-text me-2" style="font-size: 1rem;"></i>
                        <span>ASSIGNMENTS</span>
                    </a>
                </li>
                <li class="submenu-item py-1">
                    <a href="{{ route('student.program-changes.index') }}" class="submenu-link d-flex align-items-center py-2 text-decoration-none" style="font-size: 0.825rem; font-weight: 600; color: {{ request()->routeIs('student.program-changes.*') ? '#ffffff' : 'rgba(255, 255, 255, 0.8)' }}; transition: color 0.2s;">
                        <i class="bi bi-arrow-repeat me-2" style="font-size: 1rem;"></i>
                        <span>PROGRAM CHANGE</span>
                    </a>
                </li>
                <li class="submenu-item py-1">
                    <a href="{{ route('student.exams.index') }}" class="submenu-link d-flex align-items-center py-2 text-decoration-none" style="font-size: 0.825rem; font-weight: 600; color: {{ request()->routeIs('student.exams.*') ? '#ffffff' : 'rgba(255, 255, 255, 0.8)' }}; transition: color 0.2s;">
                        <i class="bi bi-pencil-square me-2" style="font-size: 1rem;"></i>
                        <span>EXAMS</span>
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
                                    if (!$attempt) return true;
                                    if (in_array($attempt->status, ['submitted', 'graded'], true)) return false;
                                    if (!$attempt->started_at) return true;
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
                <li class="submenu-item py-1">
                    <a href="{{ route('student.attendance.index') }}" class="submenu-link d-flex align-items-center py-2 text-decoration-none" style="font-size: 0.825rem; font-weight: 600; color: {{ request()->routeIs('student.attendance.*') ? '#ffffff' : 'rgba(255, 255, 255, 0.8)' }}; transition: color 0.2s;">
                        <i class="bi bi-calendar-check me-2" style="font-size: 1rem;"></i>
                        <span>ATTENDANCE</span>
                    </a>
                </li>
            </ul>
        </li>

        @php
            $isProgrammeActive = request()->routeIs('student.my-programme') ||
                                 request()->routeIs('student.enrollment.*') ||
                                 request()->routeIs('student.timetables.*') ||
                                 request()->routeIs('academic-calendar.*');
        @endphp
        <li class="menu-item-dropdown {{ $isProgrammeActive ? 'active' : '' }}">
            <a href="javascript:void(0);" class="menu-link dropdown-toggle-btn d-flex align-items-center" onclick="toggleDropdownMenu(this)">
                <i class="bi bi-journal-bookmark"></i>
                <span class="fw-bold text-uppercase">Programme & Registration</span>
                <i class="bi bi-chevron-down ms-auto dropdown-chevron" style="font-size: 0.8rem;"></i>
            </a>
            <ul class="submenu-list" style="list-style: none; padding-left: 2rem; margin: 0; display: {{ $isProgrammeActive ? 'block' : 'none' }};">
                <li class="submenu-item py-1">
                    <a href="{{ route('student.my-programme') }}" class="submenu-link d-flex align-items-center py-2 text-decoration-none" style="font-size: 0.825rem; font-weight: 600; color: {{ request()->routeIs('student.my-programme') ? '#ffffff' : 'rgba(255, 255, 255, 0.8)' }}; transition: color 0.2s;">
                        <i class="bi bi-journal-bookmark me-2" style="font-size: 1rem;"></i>
                        <span>MY PROGRAMME</span>
                    </a>
                </li>
                <li class="submenu-item py-1">
                    <a href="{{ route('student.enrollment.index') }}" class="submenu-link d-flex align-items-center py-2 text-decoration-none" style="font-size: 0.825rem; font-weight: 600; color: {{ request()->routeIs('student.enrollment.*') ? '#ffffff' : 'rgba(255, 255, 255, 0.8)' }}; transition: color 0.2s;">
                        <i class="bi bi-person-plus-fill me-2" style="font-size: 1rem;"></i>
                        <span>ENROLLMENT & REGISTRATION</span>
                    </a>
                </li>
                <li class="submenu-item py-1">
                    <a href="{{ route('student.timetables.teaching') }}" class="submenu-link d-flex align-items-center py-2 text-decoration-none" style="font-size: 0.825rem; font-weight: 600; color: {{ request()->routeIs('student.timetables.*') ? '#ffffff' : 'rgba(255, 255, 255, 0.8)' }}; transition: color 0.2s;">
                        <i class="bi bi-calendar3 me-2" style="font-size: 1rem;"></i>
                        <span>TIMETABLES</span>
                    </a>
                </li>
                <li class="submenu-item py-1">
                    <a href="{{ route('academic-calendar.index') }}" class="submenu-link d-flex align-items-center py-2 text-decoration-none" style="font-size: 0.825rem; font-weight: 600; color: {{ request()->routeIs('academic-calendar.*') ? '#ffffff' : 'rgba(255, 255, 255, 0.8)' }}; transition: color 0.2s;">
                        <i class="bi bi-calendar-week me-2" style="font-size: 1rem;"></i>
                        <span>ACADEMIC CALENDAR</span>
                    </a>
                </li>
            </ul>
        </li>

        {{-- Mailbox --}}
        <li class="menu-item {{ request()->routeIs('messages.*') ? 'active' : '' }}">
            <a href="{{ route('messages.index') }}" class="menu-link">
                <i class="bi bi-envelope-paper"></i>
                <span>Mailbox</span>
            </a>
        </li>

        @php
            $isPaymentsActive = request()->routeIs('student.fees.*');
        @endphp
        <li class="menu-item-dropdown {{ $isPaymentsActive ? 'active' : '' }}">
            <a href="javascript:void(0);" class="menu-link dropdown-toggle-btn d-flex align-items-center" onclick="toggleDropdownMenu(this)">
                <i class="bi bi-cash-coin"></i>
                <span class="fw-bold text-uppercase">Payments</span>
                <i class="bi bi-chevron-down ms-auto dropdown-chevron" style="font-size: 0.8rem;"></i>
            </a>
            <ul class="submenu-list" style="list-style: none; padding-left: 2rem; margin: 0; display: {{ $isPaymentsActive ? 'block' : 'none' }};">
                <li class="submenu-item py-1">
                    <a href="{{ route('student.fees.index') }}" class="submenu-link d-flex align-items-center py-2 text-decoration-none" style="font-size: 0.825rem; font-weight: 600; color: {{ request()->routeIs('student.fees.index') ? '#ffffff' : 'rgba(255, 255, 255, 0.8)' }}; transition: color 0.2s;">
                        <i class="bi bi-paperclip me-2" style="font-size: 1rem;"></i>
                        <span>MY BILLS/INVOICES</span>
                    </a>
                </li>
                <li class="submenu-item py-1">
                    <a href="{{ route('student.fees.ledger') }}" class="submenu-link d-flex align-items-center py-2 text-decoration-none" style="font-size: 0.825rem; font-weight: 600; color: {{ request()->routeIs('student.fees.ledger') ? '#ffffff' : 'rgba(255, 255, 255, 0.8)' }}; transition: color 0.2s;">
                        <i class="bi bi-file-earmark-check me-2" style="font-size: 1rem;"></i>
                        <span>MY TRANSACTIONS & LEDGER</span>
                    </a>
                </li>
                <li class="submenu-item py-1">
                    <a href="{{ route('student.fees.structure') }}" class="submenu-link d-flex align-items-center py-2 text-decoration-none" style="font-size: 0.825rem; font-weight: 600; color: {{ request()->routeIs('student.fees.structure') ? '#ffffff' : 'rgba(255, 255, 255, 0.8)' }}; transition: color 0.2s;">
                        <i class="bi bi-file-earmark-richtext me-2" style="font-size: 1rem;"></i>
                        <span>MY FEES STRUCTURE</span>
                    </a>
                </li>
            </ul>
        </li>

        {{-- E-Voting --}}
        <li class="menu-item {{ request()->routeIs('student.evoting.*') ? 'active' : '' }}">
            <a href="{{ route('student.evoting.index') }}" class="menu-link">
                <i class="bi bi-check2-square"></i>
                <span>E-Voting</span>
            </a>
        </li>

        {{-- Evaluation Survey --}}
        <li class="menu-item {{ request()->routeIs('student.evaluation-surveys.*') ? 'active' : '' }}">
            <a href="{{ route('student.evaluation-surveys.index') }}" class="menu-link">
                <i class="bi bi-clipboard2-check"></i>
                <span>Evaluation Survey</span>
            </a>
        </li>
        @endif

        {{-- ==================== COMMON (PINNED AT BOTTOM) ==================== --}}
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

        <li class="menu-item {{ request()->routeIs('support.*') || request()->routeIs('help.*') ? 'active' : '' }}">
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
/* Sidebar Wrapper & Core Layout */
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
    padding: 0.85rem 1rem 0.4rem;
    font-size: 0.72rem;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: rgba(255, 255, 255, 0.45);
    font-weight: 700;
}

.menu-item {
    position: relative;
}

.menu-link {
    display: flex;
    align-items: center;
    padding: 0.7rem 1rem;
    color: rgba(255, 255, 255, 0.75);
    text-decoration: none;
    transition: all 0.2s ease;
}

.menu-link:hover {
    color: #ffffff;
    background-color: rgba(255, 255, 255, 0.08);
}

.menu-item.active .menu-link {
    color: #ffffff;
    background-color: #3a7bd5;
    font-weight: 600;
}

.menu-link i {
    margin-right: 0.75rem;
    font-size: 1.1rem;
    width: 20px;
    text-align: center;
}

/* Collapsible Groups Styling */
.sidebar-group {
    position: relative;
    margin-bottom: 2px;
}

.sidebar-group-toggle {
    width: 100%;
    display: flex;
    align-items: center;
    padding: 0.7rem 1rem;
    color: rgba(255, 255, 255, 0.75);
    background: transparent;
    border: none;
    text-align: left;
    font-size: 0.875rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
    border-left: 3px solid transparent;
}

.sidebar-group-toggle:hover {
    color: #ffffff;
    background-color: rgba(255, 255, 255, 0.08);
}

/* Parent Header Highlight when Child Route Active */
.sidebar-group-toggle.has-active-child {
    color: #ffffff;
    background: rgba(58, 123, 213, 0.16);
    border-left-color: #3a7bd5;
    font-weight: 600;
}

.sidebar-group-toggle .group-icon {
    margin-right: 0.75rem;
    font-size: 1.1rem;
    width: 20px;
    text-align: center;
    color: rgba(255, 255, 255, 0.6);
    transition: color 0.2s ease;
}

.sidebar-group-toggle.has-active-child .group-icon,
.sidebar-group-toggle:hover .group-icon {
    color: #60a5fa;
}

.sidebar-group-toggle .group-title {
    flex: 1;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.sidebar-group-toggle .group-chevron {
    font-size: 0.75rem;
    transition: transform 0.25s ease;
    opacity: 0.7;
}

.sidebar-group-toggle.is-open .group-chevron {
    transform: rotate(180deg);
    opacity: 1;
}

.sidebar-group-menu {
    list-style: none;
    padding: 0.25rem 0 0.5rem 0;
    margin: 0 0 0 1.25rem;
    border-left: 2px solid rgba(255, 255, 255, 0.12);
}

.submenu-item {
    position: relative;
}

.submenu-link {
    display: flex;
    align-items: center;
    padding: 0.45rem 0.85rem;
    color: rgba(255, 255, 255, 0.7);
    text-decoration: none;
    font-size: 0.825rem;
    font-weight: 500;
    border-radius: 4px;
    margin: 1px 0.5rem 1px 0.5rem;
    transition: all 0.2s ease;
}

.submenu-link i {
    margin-right: 0.65rem;
    font-size: 0.95rem;
    width: 18px;
    text-align: center;
    opacity: 0.85;
}

.submenu-link:hover {
    color: #ffffff;
    background-color: rgba(255, 255, 255, 0.1);
}

.submenu-item.active .submenu-link {
    color: #ffffff;
    background-color: #3a7bd5;
    font-weight: 600;
}

.text-primary-light {
    color: #93c5fd !important;
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

/* Mobile Responsiveness */
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

    .guest-menu .menu-link {
        padding: 1rem 1.5rem;
        font-size: 1rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .guest-menu .menu-link:last-child {
        border-bottom: none;
    }

    .sidebar-toggle {
        padding: 0.5rem;
        border-radius: 4px;
        transition: background-color 0.2s ease;
    }

    .sidebar-toggle:hover {
        background-color: rgba(255, 255, 255, 0.1);
    }

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

@media (max-width: 767.98px) {
    body.guest-user {
        padding-top: 60px;
    }

    .sidebar-wrapper {
        top: 60px;
        height: calc(100vh - 60px);
    }
}
</style>

<script>
// Toggle Admin Collapsible Groups
function toggleSidebarGroup(button, groupId) {
    const parentGroup = button.closest('.sidebar-group');
    const submenu = parentGroup.querySelector('.sidebar-group-menu');
    const isOpen = button.classList.contains('is-open');

    // Close other collapsible groups (Single-group open UX rule)
    document.querySelectorAll('.sidebar-group').forEach(group => {
        if (group !== parentGroup) {
            const btn = group.querySelector('.sidebar-group-toggle');
            const menu = group.querySelector('.sidebar-group-menu');
            if (btn && menu) {
                btn.classList.remove('is-open');
                menu.style.display = 'none';
            }
        }
    });

    if (isOpen) {
        button.classList.remove('is-open');
        submenu.style.display = 'none';
        localStorage.setItem('jbi_sidebar_active_group', '');
    } else {
        button.classList.add('is-open');
        submenu.style.display = 'block';
        localStorage.setItem('jbi_sidebar_active_group', groupId);
    }
}

// Student Dropdown Menu toggle
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

// Enhanced mobile sidebar & persistence functionality
document.addEventListener('DOMContentLoaded', function() {
    // Restore collapsible group persistence
    const activeGroupWithChild = document.querySelector('.sidebar-group.has-active-child');
    if (activeGroupWithChild) {
        // Current active page belongs to this group -> auto expand
        const btn = activeGroupWithChild.querySelector('.sidebar-group-toggle');
        const menu = activeGroupWithChild.querySelector('.sidebar-group-menu');
        if (btn && menu) {
            btn.classList.add('is-open');
            menu.style.display = 'block';
        }
    } else {
        // Restore manual user toggle from localStorage
        const savedGroupId = localStorage.getItem('jbi_sidebar_active_group');
        if (savedGroupId) {
            const savedGroup = document.querySelector(`.sidebar-group[data-group-id="${savedGroupId}"]`);
            if (savedGroup) {
                const btn = savedGroup.querySelector('.sidebar-group-toggle');
                const menu = savedGroup.querySelector('.sidebar-group-menu');
                if (btn && menu) {
                    btn.classList.add('is-open');
                    menu.style.display = 'block';
                }
            }
        }
    }

    // Mobile Sidebar Elements
    const sidebarToggleBtn = document.getElementById('sidebar-toggle-btn');
    const sidebar = document.querySelector('.sidebar-wrapper');
    let sidebarOverlay = document.querySelector('.sidebar-overlay');

    if (!sidebarOverlay) {
        sidebarOverlay = document.createElement('div');
        sidebarOverlay.className = 'sidebar-overlay';
        document.body.appendChild(sidebarOverlay);
    }

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
        document.body.style.overflow = 'hidden';

        if (sidebarToggleBtn) {
            sidebarToggleBtn.innerHTML = '<i class="bi bi-x-lg"></i>';
        }
    }

    function closeSidebar() {
        sidebar.classList.remove('show');
        sidebarOverlay.classList.remove('show');
        document.body.style.overflow = '';

        if (sidebarToggleBtn) {
            sidebarToggleBtn.innerHTML = '<i class="bi bi-list"></i>';
        }
    }

    if (sidebarToggleBtn) {
        sidebarToggleBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            toggleSidebar();
        });
    }

    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', function() {
            closeSidebar();
        });
    }

    const menuLinks = document.querySelectorAll('.sidebar-menu a:not(.sidebar-group-toggle):not(.dropdown-toggle-btn)');
    menuLinks.forEach(function(link) {
        link.addEventListener('click', function() {
            if (window.innerWidth < 768) {
                setTimeout(closeSidebar, 150);
            }
        });
    });

    window.addEventListener('resize', function() {
        if (window.innerWidth >= 768) {
            closeSidebar();
        }
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && sidebar.classList.contains('show')) {
            closeSidebar();
        }
    });

    sidebar.addEventListener('click', function(e) {
        e.stopPropagation();
    });
});
</script>
