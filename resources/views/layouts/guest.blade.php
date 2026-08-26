<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'JBI University') }} - @yield('title', 'Welcome')</title>

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Page specific CSS -->
    @stack('styles')

    <style>
        :root {
            --jbi-primary: #3b5bdb;
            --jbi-secondary: #1a2236;
            --jbi-accent: #f59e0b;
            --jbi-success: #10b981;
            --jbi-danger: #ef4444;
            --jbi-warning: #f59e0b;
            --jbi-info: #3b82f6;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            min-height: 100vh;
            color: #1e293b;
        }

        /* Guest Navigation */
        .guest-navbar {
            background: linear-gradient(135deg, var(--jbi-primary) 0%, var(--jbi-secondary) 100%);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 1rem 0;
        }

        .guest-navbar .navbar-brand {
            color: white;
            font-weight: 700;
            font-size: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .guest-navbar .navbar-brand img {
            height: 40px;
            width: auto;
        }

        .guest-navbar .nav-link {
            color: rgba(255, 255, 255, 0.9);
            font-weight: 500;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            transition: all 0.2s ease;
        }

        .guest-navbar .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
        }

        .guest-navbar .btn-primary {
            background-color: var(--jbi-accent);
            border-color: var(--jbi-accent);
            color: white;
            font-weight: 600;
            padding: 0.5rem 1.5rem;
            border-radius: 0.5rem;
            transition: all 0.2s ease;
        }

        .guest-navbar .btn-primary:hover {
            background-color: #d97706;
            border-color: #d97706;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
        }

        .guest-navbar .btn-outline-light {
            color: white;
            border-color: rgba(255, 255, 255, 0.5);
            font-weight: 500;
            padding: 0.5rem 1.5rem;
            border-radius: 0.5rem;
            transition: all 0.2s ease;
        }

        .guest-navbar .btn-outline-light:hover {
            background-color: white;
            border-color: white;
            color: var(--jbi-primary);
        }

        /* Main Content */
        .guest-content {
            min-height: calc(100vh - 200px);
        }

        /* Footer */
        .guest-footer {
            background: var(--jbi-secondary);
            color: rgba(255, 255, 255, 0.8);
            padding: 2rem 0;
            margin-top: 4rem;
        }

        .guest-footer a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .guest-footer a:hover {
            color: white;
        }

        /* Card Styles */
        .card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
            transition: all 0.3s ease;
        }

        .card:hover {
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }

        /* Form Styles */
        .form-label {
            font-weight: 600;
            color: #334155;
            margin-bottom: 0.5rem;
        }

        .form-control, .form-select {
            border-radius: 0.5rem;
            border: 1px solid #cbd5e1;
            padding: 0.75rem 1rem;
            transition: all 0.2s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--jbi-primary);
            box-shadow: 0 0 0 0.2rem rgba(59, 91, 219, 0.15);
        }

        /* Alert Styles */
        .alert {
            border-radius: 0.75rem;
            border: none;
            padding: 1rem 1.25rem;
        }

        /* Button Styles */
        .btn {
            border-radius: 0.5rem;
            font-weight: 600;
            padding: 0.625rem 1.5rem;
            transition: all 0.2s ease;
        }

        .btn-primary {
            background-color: var(--jbi-primary);
            border-color: var(--jbi-primary);
        }

        .btn-primary:hover {
            background-color: #364fc7;
            border-color: #364fc7;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(59, 91, 219, 0.3);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .guest-navbar .navbar-brand {
                font-size: 1.25rem;
            }

            .guest-navbar .navbar-brand img {
                height: 32px;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg guest-navbar">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">
                <img src="{{ asset('images/jbi-blue.webp') }}" alt="JBI University Logo">
                <span>JBI University</span>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#guestNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="guestNavbar">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-lg-center gap-2">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/') }}">
                            <i class="bi bi-house-door me-1"></i> Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('applications.create') }}">
                            <i class="bi bi-file-earmark-text me-1"></i> Apply Now
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('support.index') }}">
                            <i class="bi bi-question-circle me-1"></i> Help
                        </a>
                    </li>
                    @guest
                        <li class="nav-item">
                            <a class="btn btn-outline-light" href="{{ route('login') }}">
                                <i class="bi bi-box-arrow-in-right me-1"></i> Login
                            </a>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="btn btn-primary" href="{{ route('dashboard') }}">
                                <i class="bi bi-speedometer2 me-1"></i> Dashboard
                            </a>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="guest-content">
        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="container mt-4">
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="container mt-4">
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="guest-footer">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-3 mb-md-0">
                    <h5 class="text-white mb-3">JBI University</h5>
                    <p class="small">At JBI University, we are committed to shaping a better future by equipping individuals with the wisdom, knowledge, and understanding needed to address national, continental, and global challenges.</p>
                </div>
                <div class="col-md-4 mb-3 mb-md-0">
                    <h6 class="text-white mb-3">Quick Links</h6>
                    <ul class="list-unstyled small">
                        <li class="mb-2"><a href="{{ url('/') }}">Home</a></li>
                        <li class="mb-2"><a href="{{ route('applications.create') }}">Apply Now</a></li>
                        <li class="mb-2"><a href="{{ route('support.index') }}">Help & Support</a></li>
                        <li class="mb-2"><a href="{{ route('login') }}">Login</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h6 class="text-white mb-3">Contact</h6>
                    <p class="small mb-1"><i class="bi bi-envelope me-2"></i> admission@jbiuniversity.com</p>
                    <p class="small mb-1"><i class="bi bi-envelope me-2"></i> info@jbiuniversity.com</p>
                    <p class="small mb-1"><i class="bi bi-whatsapp me-2"></i>+27 68 443 8415</p>
                    <p class="small mb-0"><i class="bi bi-geo-alt me-2"></i>South Africa</p>
                </div>
            </div>
            <hr class="my-3 border-secondary">
            <div class="text-center small">
                <p class="mb-1">&copy; {{ date('Y') }} JBI University. All rights reserved.</p>
                <p class="mb-0">
                    <span class="badge bg-success">Production</span>
                    <a class="ms-2" href="{{ config('app.production_url') }}">JBI University Portal</a>
                </p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Page specific JS -->
    @stack('scripts')
</body>
</html>
