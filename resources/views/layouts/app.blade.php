<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'JBI University') }} - @yield('title', 'University Management System')</title>

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <!-- Page specific CSS -->
    @stack('styles')

    <style>
        :root {
            --sidebar-width: 250px;
            --header-height: 60px;
            --mobile-header-height: 60px;
            --primary-color: #3b5bdb;
            --secondary-color: #1a2236;
            --jbi-primary: #3b5bdb;
            --jbi-secondary: #1a2236;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
            color: #212529;
            margin: 0;
            padding: 0;
        }

        /* Guest User Body Class */
        body.guest-user {
            padding-top: var(--mobile-header-height);
        }

        /* Mobile Navigation for Guest Users */
        .mobile-nav {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: linear-gradient(135deg, var(--jbi-primary) 0%, var(--jbi-secondary) 100%);
            z-index: 1050;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            display: none;
        }

        .mobile-nav-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.75rem 1rem;
            height: var(--mobile-header-height);
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
            text-decoration: none;
        }

        .mobile-nav-actions {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .mobile-nav-actions .btn {
            font-size: 0.875rem;
            padding: 0.375rem 0.75rem;
            border-radius: 20px;
        }

        /* Sidebar Styles */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background-color: var(--secondary-color);
            color: #fff;
            z-index: 1030;
            overflow-y: auto;
            transition: transform 0.3s ease;
        }

        /* Main Content Styles */
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Header Styles */
        .top-header {
            height: var(--header-height);
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            display: flex;
            align-items: center;
            padding: 0 1rem;
        }

        /* Navbar Styles */
        .navbar {
            height: var(--header-height);
            background-color: white;
            border-bottom: 1px solid #e9ecef;
            padding: 0 1rem;
        }

        /* Content Area */
        .content-area {
            flex: 1;
            padding: 2rem;
            background-color: #f8f9fa;
        }

        /* Guest Content Area */
        .guest-content-area {
            flex: 1;
            background-color: #f8f9fa;
            min-height: calc(100vh - var(--mobile-header-height));
        }

        /* Footer Styles */
        .footer {
            background-color: white;
            border-top: 1px solid #e9ecef;
            padding: 0.75rem 1.5rem;
            font-size: 0.875rem;
            color: #6c757d;
        }

        /* Responsive Styles */
        @media (max-width: 768px) {
            /* Show mobile nav for guests */
            body.guest-user .mobile-nav {
                display: block;
            }

            /* Authenticated users mobile */
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .content-area {
                padding: 1rem;
            }

            /* Guest users mobile adjustments */
            body.guest-user .sidebar {
                top: var(--mobile-header-height);
                height: calc(100vh - var(--mobile-header-height));
            }
        }

        @media (min-width: 769px) {
            body.guest-user {
                padding-top: 0;
            }

            body.guest-user .mobile-nav {
                display: none;
            }

            body.guest-user .main-content {
                margin-left: var(--sidebar-width);
            }
        }

        /* Utility Classes */
        .avatar-sm {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #e9ecef;
            color: #495057;
            font-weight: 600;
            font-size: 0.875rem;
        }

        .nav-section {
            padding: 0.75rem 1rem;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.5);
            letter-spacing: 0.05em;
        }

        .nav-item {
            margin: 0.25rem 0;
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.75);
            padding: 0.5rem 1rem;
            border-radius: 0.25rem;
            display: flex;
            align-items: center;
            transition: all 0.2s ease;
        }

        .nav-link:hover {
            color: #fff;
            background-color: rgba(255, 255, 255, 0.1);
        }

        .nav-link.active {
            color: #fff;
            background-color: var(--primary-color);
        }

        .nav-link i {
            margin-right: 0.5rem;
            font-size: 1.25rem;
            width: 1.5rem;
            text-align: center;
        }

        .badge-notification {
            position: absolute;
            top: 10px;
            right: 10px;
            padding: 0.25rem 0.5rem;
            border-radius: 50%;
            font-size: 0.75rem;
        }

        /* Overlay for mobile sidebar */
        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1025;
            display: none;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .sidebar-overlay.show {
            display: block;
            opacity: 1;
        }

        @media (max-width: 768px) {
            body.guest-user .sidebar-overlay {
                top: var(--mobile-header-height);
            }
        }
    </style>
</head>
<body class="{{ auth()->guest() ? 'guest-user' : 'authenticated-user' }}">
    <div id="app">
        <!-- Mobile Navigation for Guest Users -->
        @guest
        <nav class="mobile-nav">
            <div class="mobile-nav-content">
                <button id="mobile-menu-toggle-guest" class="mobile-nav-toggle">
                    <i class="bi bi-list"></i>
                </button>

                <div class="mobile-nav-brand">
                    <img src="{{ asset('images/jbi-logo.webp') }}" alt="JBI University" class="mobile-logo">
                    <a href="{{ url('/') }}" class="mobile-brand-text">JBI University</a>
                </div>

                <div class="mobile-nav-actions">
                    @if(!request()->routeIs('login'))
                        <a href="{{ route('login') }}" class="btn btn-sm btn-outline-light">Login</a>
                    @endif
                    @if(!request()->routeIs('register'))
                        <a href="{{ route('register') }}" class="btn btn-sm btn-light">Register</a>
                    @endif
                </div>
            </div>
        </nav>
        @endguest

        <!-- Sidebar -->
        <aside class="sidebar">
            @auth
            <div class="p-3 border-bottom border-secondary">
                <div class="d-flex align-items-center">
                    <img src="{{ asset('images/jbi-logo.webp') }}" alt="JBI University" height="30" class="me-2">
                    <div>
                        <h5 class="mb-0 fw-bold">JBI</h5>
                        <div class="small">University</div>
                    </div>
                </div>
            </div>

            <div class="p-3 border-bottom border-secondary">
                <div class="d-flex align-items-center">
                    <div class="avatar-sm bg-light text-dark me-2">
                        {{ substr(Auth::user()->first_name ?? 'U', 0, 1) }}{{ substr(Auth::user()->last_name ?? 'U', 0, 1) }}
                    </div>
                    <div>
                        <div class="fw-medium">{{ Auth::user()->first_name ?? 'System' }} {{ Auth::user()->last_name ?? 'Administrator' }}</div>
                        <div class="small text-light">{{ ucfirst(Auth::user()->role ?? 'Admin') }}</div>
                    </div>
                </div>
            </div>
            @endauth

            @include('partials.sidebar-nav')
        </aside>

        <!-- Sidebar Overlay for Mobile -->
        <div class="sidebar-overlay" id="sidebar-overlay"></div>

        <!-- Main Content -->
        <div class="main-content">
            @auth
            <!-- Top Header (Blue Bar) for Authenticated Users -->
            <div class="top-header">
                <div class="d-flex align-items-center justify-content-between w-100">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-mortarboard me-2 fs-4"></i>
                        <span class="fw-semibold">JBI University Management System</span>
                    </div>
                    <div class="d-flex align-items-center">
                        <span class="small me-3">Academic Year {{ date('Y') }}-{{ date('Y')+1 }}</span>
                        <div class="vr me-3"></div>
                        <span class="small">{{ now()->format('l, F j, Y') }}</span>
                    </div>
                </div>
            </div>

            <!-- Navbar for Authenticated Users -->
            <nav class="navbar navbar-expand-lg navbar-light">
                <div class="container-fluid px-0">
                    <button id="mobile-menu-toggle" class="btn btn-sm d-md-none">
                        <i class="bi bi-list fs-4"></i>
                    </button>

                    <div class="ms-auto d-flex align-items-center">
                        <!-- Search Form -->
                        <div class="me-3 d-none d-lg-block">
                            <div class="input-group">
                                <input type="search" class="form-control form-control-sm" placeholder="Search...">
                                <button class="btn btn-sm btn-outline-secondary" type="submit">
                                    <i class="bi bi-search"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Notifications -->
                        <div class="dropdown me-2 position-relative">
                            <button class="btn btn-sm" type="button" data-bs-toggle="dropdown">
                                <i class="bi bi-bell fs-5"></i>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    3
                                </span>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end p-0" style="width: 320px;">
                                <div class="p-2 border-bottom d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">Notifications</h6>
                                    <a href="#" class="text-decoration-none small">Mark all as read</a>
                                </div>
                                <div class="notification-list" style="max-height: 300px; overflow-y: auto;">
                                    <a href="#" class="dropdown-item p-2 border-bottom">
                                        <div class="d-flex">
                                            <div class="flex-shrink-0">
                                                <div class="bg-primary text-white rounded-circle p-1">
                                                    <i class="bi bi-envelope"></i>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1 ms-2">
                                                <p class="mb-0 small">New assignment posted in CS301</p>
                                                <small class="text-muted">2 hours ago</small>
                                            </div>
                                        </div>
                                    </a>
                                    <a href="#" class="dropdown-item p-2 border-bottom">
                                        <div class="d-flex">
                                            <div class="flex-shrink-0">
                                                <div class="bg-success text-white rounded-circle p-1">
                                                    <i class="bi bi-check-circle"></i>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1 ms-2">
                                                <p class="mb-0 small">Your assignment has been graded</p>
                                                <small class="text-muted">1 day ago</small>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                                <div class="p-2 border-top text-center">
                                    <a href="#" class="text-decoration-none small">View all notifications</a>
                                </div>
                            </div>
                        </div>

                        <!-- User Menu -->
                        <div class="dropdown">
                            <button class="btn btn-sm dropdown-toggle d-flex align-items-center" type="button" data-bs-toggle="dropdown">
                                <div class="avatar-sm bg-light text-dark me-2">
                                    {{ substr(Auth::user()->first_name ?? 'S', 0, 1) }}{{ substr(Auth::user()->last_name ?? 'A', 0, 1) }}
                                </div>
                                <span class="d-none d-lg-inline">{{ Auth::user()->first_name ?? 'System' }} {{ Auth::user()->last_name ?? 'Administrator' }}</span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="{{ route('profile.show') }}">
                                    <i class="bi bi-person me-2"></i> My Profile
                                </a></li>
                                <li><a class="dropdown-item" href="#">
                                    <i class="bi bi-gear me-2"></i> Settings
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="bi bi-box-arrow-right me-2"></i> Logout
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Content Area for Authenticated Users -->
            <div class="content-area">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @yield('content')
            </div>
            @endauth

            @guest
            <!-- Content Area for Guest Users -->
            <div class="guest-content-area">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @yield('content')
            </div>
            @endguest

            <!-- Footer -->
            <footer class="footer">
                <div class="d-flex justify-content-between align-items-center">
                    <div>&copy; {{ date('Y') }} JBI University. All rights reserved.</div>
                    <div class="d-flex align-items-center">
                        <span class="me-3">Version 1.0.0</span>
                        @guest
                        <div class="d-none d-md-flex">
                            <a href="#" class="text-decoration-none me-3 small">Privacy Policy</a>
                            <a href="#" class="text-decoration-none me-3 small">Terms of Service</a>
                            <a href="#" class="text-decoration-none small">Contact Support</a>
                        </div>
                        @endguest
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- jQuery (for plugins that require it) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- Custom JS -->
    <script src="{{ asset('js/app.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.querySelector('.sidebar');
            const sidebarOverlay = document.getElementById('sidebar-overlay');

            // Mobile menu toggle for authenticated users
            const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
            if (mobileMenuToggle) {
                mobileMenuToggle.addEventListener('click', function() {
                    toggleSidebar();
                });
            }

            // Mobile menu toggle for guest users
            const mobileMenuToggleGuest = document.getElementById('mobile-menu-toggle-guest');
            if (mobileMenuToggleGuest) {
                mobileMenuToggleGuest.addEventListener('click', function() {
                    toggleSidebar();
                });
            }

            // Sidebar toggle function
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

                // Update toggle button icons
                updateToggleIcons('close');
            }

            function closeSidebar() {
                sidebar.classList.remove('show');
                sidebarOverlay.classList.remove('show');
                document.body.style.overflow = '';

                // Update toggle button icons
                updateToggleIcons('open');
            }

            function updateToggleIcons(state) {
                const toggleButtons = [mobileMenuToggle, mobileMenuToggleGuest].filter(btn => btn);
                toggleButtons.forEach(btn => {
                    const icon = btn.querySelector('i');
                    if (icon) {
                        icon.className = state === 'close' ? 'bi bi-x-lg' : 'bi bi-list';
                    }
                });
            }

            // Overlay click to close
            if (sidebarOverlay) {
                sidebarOverlay.addEventListener('click', function() {
                    closeSidebar();
                });
            }

            // Close sidebar when clicking menu links on mobile
            const menuLinks = document.querySelectorAll('.sidebar-menu .menu-link');
            menuLinks.forEach(function(link) {
                link.addEventListener('click', function() {
                    if (window.innerWidth < 768) {
                        setTimeout(closeSidebar, 150);
                    }
                });
            });

            // Handle window resize
            window.addEventListener('resize', function() {
                if (window.innerWidth >= 768) {
                    closeSidebar();
                }
            });

            // Handle escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && sidebar.classList.contains('show')) {
                    closeSidebar();
                }
            });

            // Prevent sidebar from closing when clicking inside it
            sidebar.addEventListener('click', function(e) {
                e.stopPropagation();
            });

            // Initialize tooltips
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Auto-hide alerts after 5 seconds
            setTimeout(function() {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(function(alert) {
                    if (alert.querySelector('.btn-close')) {
                        const bsAlert = new bootstrap.Alert(alert);
                        bsAlert.close();
                    }
                });
            }, 5000);
        });
    </script>

    <!-- Page specific JS -->
    @stack('scripts')
</body>
</html>
