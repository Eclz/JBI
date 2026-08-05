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

    <!-- Updated Font Awesome to version 6 for better icon support -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

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
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        /* Enhanced navbar component styles */
        .navbar .search-wrapper {
            position: relative;
        }

        .navbar .search-wrapper input {
            border-radius: 20px;
            padding-left: 2.5rem;
            border: 1px solid #e0e0e0;
            transition: all 0.3s ease;
            width: 250px;
        }

        .navbar .search-wrapper input:focus {
            width: 300px;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(59, 91, 219, 0.15);
        }

        .navbar .search-wrapper .search-icon {
            position: absolute;
            left: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
            pointer-events: none;
        }

        .navbar .notification-btn,
        .navbar .user-menu-btn {
            position: relative;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 0.5rem 0.75rem;
            background: white;
            transition: all 0.2s ease;
        }

        .navbar .notification-btn:hover,
        .navbar .user-menu-btn:hover {
            background: #f8f9fa;
            border-color: var(--primary-color);
        }

        .navbar .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            min-width: 20px;
            height: 20px;
            border-radius: 10px;
            background: #dc3545;
            color: white;
            font-size: 0.7rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            border: 2px solid white;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.1);
            }
        }

        .navbar .dropdown-menu {
            border: 1px solid #e0e0e0;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            margin-top: 0.5rem;
        }

        .navbar .notification-item {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #f0f0f0;
            transition: background-color 0.2s ease;
        }

        .navbar .notification-item:hover {
            background-color: #f8f9fa;
        }

        .navbar .notification-item.unread {
            background-color: #f0f4ff;
        }

        .navbar .notification-icon {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
        }

        .navbar .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .navbar .quick-actions {
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }

        .navbar .quick-action-btn {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 0.5rem 0.75rem;
            background: white;
            color: #495057;
            text-decoration: none;
            transition: all 0.2s ease;
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        .navbar .quick-action-btn:hover {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        /* Mobile navbar enhancements */
        #mobile-menu-toggle {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 0.5rem;
        }

        #mobile-menu-toggle:hover {
            background: #f8f9fa;
            border-color: var(--primary-color);
        }

        /* Breadcrumb in navbar */
        .navbar-breadcrumb {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #6c757d;
            font-size: 0.875rem;
        }

        .navbar-breadcrumb a {
            color: var(--primary-color);
            text-decoration: none;
        }

        .navbar-breadcrumb a:hover {
            text-decoration: underline;
        }

        /* Responsive navbar adjustments */
        @media (max-width: 992px) {
            .navbar .search-wrapper input {
                width: 180px;
            }

            .navbar .search-wrapper input:focus {
                width: 220px;
            }

            .navbar .quick-actions {
                display: none;
            }
        }

        @media (max-width: 768px) {
            .navbar .search-wrapper {
                display: none;
            }

            .top-header .d-flex > div:last-child {
                display: none !important;
            }
        }

        /* Guest Content Area */
        .guest-content-area {
            flex: 1;
            background-color: #f8f9fa;
            min-height: calc(100vh - var(--mobile-header-height));
        }

        .main-content {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .content-area {
            flex: 1 0 auto;
        }

        /* Fixed Sticky Bottom Footer Styles */
        .footer {
            margin-top: auto;
            position: sticky;
            bottom: 0;
            left: 0;
            right: 0;
            width: 100%;
            z-index: 1000;
            background-color: #ffffff;
            border-top: 1px solid #e9ecef;
            padding: 0.75rem 1.5rem;
            font-size: 0.875rem;
            color: #6c757d;
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.05);
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

        .sidebar .nav-item {
            margin: 0.25rem 0;
        }

        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.75);
            padding: 0.5rem 1rem;
            border-radius: 0.25rem;
            display: flex;
            align-items: center;
            transition: all 0.2s ease;
        }

        .sidebar .nav-link:hover {
            color: #fff;
            background-color: rgba(255, 255, 255, 0.1);
        }

        .sidebar .nav-link.active {
            color: #fff;
            background-color: var(--primary-color);
        }

        .sidebar .nav-link i {
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
                    <!-- Enhanced mobile menu toggle -->
                    <button id="mobile-menu-toggle" class="btn d-md-none">
                        <i class="bi bi-list fs-4"></i>
                    </button>

                    <!-- Added breadcrumb navigation -->
                    <div class="navbar-breadcrumb d-none d-lg-flex">
                        @yield('breadcrumbs')
                    </div>

                    <div class="ms-auto d-flex align-items-center gap-2">
                        @if(Auth::check() && Auth::user()->isStudent())
                            <!-- Student Programme and Status Banner -->
                            <div class="d-none d-lg-flex align-items-center gap-2 bg-light px-3 py-1.5 rounded-pill border">
                                <span class="badge bg-secondary text-uppercase px-2 py-1" style="font-size: 0.7rem;">PROGRAMME</span>
                                <span class="fw-bold text-dark small text-uppercase me-1">{{ Auth::user()->studentProfile?->program ?? 'BACHELOR OF SCIENCE IN SOFTWARE ENGINEERING' }}</span>
                                <span class="badge bg-primary px-2 py-1 text-uppercase" style="font-size: 0.7rem;">{{ strtoupper(Auth::user()->studentProfile?->status ?? 'ACTIVE') }}</span>
                            </div>
                        @else
                            <!-- Enhanced search with icon for staff/admin -->
                            <div class="search-wrapper d-none d-lg-block">
                                <i class="bi bi-search search-icon"></i>
                                <input type="search" class="form-control form-control-sm" placeholder="Search students, courses, staff...">
                            </div>
                        @endif

                        <!-- Quick action buttons for common tasks -->
                        <div class="quick-actions d-none d-xl-flex">
                            @if(Auth::user()->role === 'admin' || Auth::user()->role === 'faculty')
                                <a href="{{ route('admin.students.create') }}" class="quick-action-btn" title="Add Student">
                                    <i class="bi bi-person-plus"></i>
                                </a>
                                <a href="{{ route('admin.courses.create') }}" class="quick-action-btn" title="Add Course">
                                    <i class="bi bi-journal-plus"></i>
                                </a>
                            @endif
                        </div>

                        <!-- Enhanced notifications dropdown with real database data -->
                        <div class="dropdown">
                            <button class="notification-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-bell fs-5"></i>
                                @if(isset($unreadNotificationsCount) && $unreadNotificationsCount > 0)
                                    <span class="notification-badge">{{ $unreadNotificationsCount }}</span>
                                @endif
                            </button>
                            <div class="dropdown-menu dropdown-menu-end p-0" style="width: 360px;">
                                <div class="p-3 border-bottom bg-light">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0 fw-semibold">Notifications</h6>
                                        @if(isset($unreadNotificationsCount) && $unreadNotificationsCount > 0)
                                            <a href="#" class="text-primary text-decoration-none small" onclick="markAllAsRead(event)">
                                                <i class="bi bi-check-all me-1"></i>Mark all read
                                            </a>
                                        @endif
                                    </div>
                                </div>
                                <div class="notification-list" style="max-height: 400px; overflow-y: auto;">
                                    @if(isset($headerNotifications) && $headerNotifications->count() > 0)
                                        @foreach($headerNotifications as $notification)
                                            <a href="{{ $notification->action_url ?? route('notifications.index') }}"
                                               class="notification-item {{ !$notification->is_read ? 'unread' : '' }} text-decoration-none d-block"
                                               data-notification-id="{{ $notification->id }}">
                                                <div class="d-flex gap-3">
                                                    <div class="notification-icon
                                                        @if($notification->priority === 'urgent') bg-danger
                                                        @elseif($notification->priority === 'high') bg-warning
                                                        @elseif($notification->type === 'grade_posted') bg-success
                                                        @elseif($notification->type === 'payment') bg-info
                                                        @else bg-primary
                                                        @endif
                                                        text-white flex-shrink-0">
                                                        @if($notification->type === 'assignment_due')
                                                            <i class="bi bi-file-earmark-text"></i>
                                                        @elseif($notification->type === 'grade_posted')
                                                            <i class="bi bi-trophy"></i>
                                                        @elseif($notification->type === 'payment')
                                                            <i class="bi bi-credit-card"></i>
                                                        @elseif($notification->type === 'announcement')
                                                            <i class="bi bi-megaphone"></i>
                                                        @elseif($notification->type === 'enrollment')
                                                            <i class="bi bi-person-check"></i>
                                                        @else
                                                            <i class="bi bi-bell"></i>
                                                        @endif
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <p class="mb-1 fw-medium text-dark">{{ $notification->title }}</p>
                                                        <p class="mb-1 small text-muted">{{ Str::limit($notification->message, 60) }}</p>
                                                        <small class="text-muted">
                                                            <i class="bi bi-clock me-1"></i>
                                                            {{ $notification->created_at->diffForHumans() }}
                                                        </small>
                                                    </div>
                                                    @if(!$notification->is_read)
                                                        <div class="flex-shrink-0">
                                                            <span class="badge bg-primary rounded-pill">New</span>
                                                        </div>
                                                    @endif
                                                </div>
                                            </a>
                                        @endforeach
                                    @else
                                        <div class="text-center py-5">
                                            <i class="bi bi-bell-slash text-muted" style="font-size: 3rem;"></i>
                                            <p class="text-muted mt-3 mb-0">No notifications</p>
                                        </div>
                                    @endif
                                </div>
                                <div class="p-3 border-top bg-light text-center">
                                    <a href="{{ route('notifications.index') }}" class="text-primary text-decoration-none small fw-medium">
                                        View All Notifications <i class="bi bi-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Enhanced user menu with dynamic user data -->
                        <div class="dropdown">
                            <button class="user-menu-btn d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                @if(Auth::user()->profile_picture)
                                    <img src="{{ asset('storage/' . Auth::user()->profile_picture) }}" alt="User" class="user-avatar">
                                @else
                                    <div class="user-avatar">
                                        {{ Auth::user()->initials }}
                                    </div>
                                @endif
                                <div class="d-none d-lg-block text-start">
                                    <div class="fw-medium small">{{ Auth::user()->full_name }}</div>
                                    <div class="text-muted" style="font-size: 0.75rem;">{{ ucfirst(Auth::user()->role) }}</div>
                                </div>
                                <i class="bi bi-chevron-down small"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end">
                                <div class="px-3 py-2 border-bottom">
                                    <div class="fw-medium">{{ Auth::user()->full_name }}</div>
                                    <div class="small text-muted">{{ Auth::user()->email }}</div>
                                    <span class="badge bg-primary mt-1">{{ ucfirst(Auth::user()->role) }}</span>
                                </div>
                                <a class="dropdown-item" href="{{ route('profile.show') }}">
                                    <i class="bi bi-person me-2"></i>My Profile
                                </a>
                                <a class="dropdown-item" href="{{ route('settings.index') }}">
                                    <i class="bi bi-gear me-2"></i>Settings
                                </a>
                                <a class="dropdown-item" href="{{ route('notifications.index') }}">
                                    <i class="bi bi-bell me-2"></i>Notifications
                                    @if(isset($unreadNotificationsCount) && $unreadNotificationsCount > 0)
                                        <span class="badge bg-danger ms-1">{{ $unreadNotificationsCount }}</span>
                                    @endif
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="{{ route('support.index') }}">
                                    <i class="bi bi-question-circle me-2"></i>Help & Support
                                </a>
                                <div class="dropdown-divider"></div>
                                <form method="POST" action="{{ route('logout') }}" class="mb-0">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="bi bi-box-arrow-right me-2"></i>Logout
                                    </button>
                                </form>
                            </div>
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

    <script>

        function markAllAsRead(event) {
            event.preventDefault();
            // Add AJAX call to mark notifications as read
            fetch('{{ route("notifications.mark-all-read") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Remove unread class from all notifications
                    document.querySelectorAll('.notification-item.unread').forEach(item => {
                        item.classList.remove('unread');
                    });
                    // Update badge
                    const badge = document.querySelector('.notification-badge');
                    if (badge) badge.textContent = '0';
                }
            });
        }

        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();
                const searchInput = document.querySelector('.navbar .search-wrapper input');
                if (searchInput) {
                    searchInput.focus();
                }
            }
        });

        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>

    <!-- Page specific JS -->
    @stack('scripts')
</body>
</html>
