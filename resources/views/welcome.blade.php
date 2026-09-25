<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to JBI University</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root {
            --jbi-primary: #001d48;
            --jbi-accent: #d89b00;
        }
        .hero-section {
            background: linear-gradient(135deg, var(--jbi-primary) 0%, #00102a 100%);
            color: white;
            padding: 100px 0;
            min-height: 600px;
            display: flex;
            align-items: center;
        }
        .feature-card {
            transition: transform 0.3s ease;
            border: none;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .feature-card:hover {
            transform: translateY(-5px);
        }
        .btn-apply {
            padding: 15px 40px;
            font-size: 1.2rem;
            font-weight: 600;
            background-color: var(--jbi-accent);
            border-color: var(--jbi-accent);
            color: white !important;
        }
        .btn-apply:hover {
            background-color: #b88400;
            border-color: #b88400;
        }
        .btn-primary, .bg-primary {
            background-color: var(--jbi-primary) !important;
            border-color: var(--jbi-primary) !important;
        }
        .text-primary, .bi {
            color: var(--jbi-accent) !important;
        }
        .btn-outline-primary {
            color: var(--jbi-accent);
            border-color: var(--jbi-accent);
        }
        .btn-outline-primary:hover {
            background-color: var(--jbi-accent);
            color: white !important;
        }
        .stats-section {
            background: #f8f9fa;
            padding: 60px 0;
        }
        .stat-item {
            text-align: center;
        }
        .stat-number {
            font-size: 3rem;
            font-weight: bold;
            color: var(--jbi-accent);
        }
        .program-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border-radius: 14px;
            background: #ffffff;
        }
        .program-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 14px 28px rgba(0,0,0,0.08) !important;
        }
        .program-icon-box {
            width: 46px;
            height: 46px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background-color: rgba(216, 155, 0, 0.12);
            color: var(--jbi-accent);
            font-size: 1.35rem;
        }
        .filter-btn.active {
            background-color: var(--jbi-primary) !important;
            border-color: var(--jbi-primary) !important;
            color: #ffffff !important;
        }
        .btn-gold {
            background-color: var(--jbi-accent);
            border-color: var(--jbi-accent);
            color: white;
        }
        .btn-gold:hover {
            background-color: #b88400;
            border-color: #b88400;
            color: white;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}">
                <img src="{{ asset('images/jbi-blue.webp') }}" alt="JBI University" height="40" class="me-2">
                {{-- <span class="fw-bold">JBI University</span> --}}
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#about">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#programs">Programs</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact">Contact</a>
                    </li>
                    @auth
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('dashboard') }}">Dashboard</a>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="btn btn-primary" href="{{ route('login') }}">Sign In</a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-gold ms-2" href="{{ route('applications.create') }}">Apply Now</a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section" style="background: url('{{ asset('images/jbi-png.png') }}') no-repeat center center; background-size: cover; background-position: center;" height="100px">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="display-3 fw-bold mb-4">Welcome to JBI University</h1>
                    <p class="lead mb-4">Empowering individuals with wisdom, skills, and innovation to shape the future of governance, business, technology, and ministry.</p>
                    <div class="d-flex gap-3">
                        <a href="{{ route('applications.create') }}" class="btn btn-light btn-apply">
                            <i class="bi bi-file-earmark-text me-2"></i>Apply Now
                        </a>
                        <a href="#programs" class="btn btn-outline-light btn-apply">
                            View Programs
                        </a>
                    </div>
                </div>
                <div class="col-lg-6">
                    {{-- <img src="{{ asset('images/jbi-blue.webp') }}" alt="JBI University" class="img-fluid"> --}}
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section">
        <div class="container">
            <div class="row">
                <div class="col-md-3">
                    <div class="stat-item">
                        <div class="stat-number"><i class="bi bi-globe2"></i></div>
                        <p class="text-muted">Global Vision</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-item">
                        <div class="stat-number"><i class="bi bi-heart"></i></div>
                        <p class="text-muted">Faith-Based Learning</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-item">
                        <div class="stat-number"><i class="bi bi-lightbulb"></i></div>
                        <p class="text-muted">Innovation</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-item">
                        <div class="stat-number"><i class="bi bi-people"></i></div>
                        <p class="text-muted">Leadership</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-5" id="about">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-5 fw-bold">Why Choose JBI University?</h2>
                <p class="lead text-muted">Discover what makes us stand out</p>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card feature-card h-100">
                        <div class="card-body text-center p-4">
                            <i class="bi bi-mortarboard text-primary" style="font-size: 3rem;"></i>
                            <h4 class="card-title mt-3">Affordability</h4>
                            <p class="card-text text-muted">Accessible quality education with affordable tuition and flexible payment options.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card feature-card h-100">
                        <div class="card-body text-center p-4">
                            <i class="bi bi-building text-primary" style="font-size: 3rem;"></i>
                            <h4 class="card-title mt-3">Top Instructors</h4>
                            <p class="card-text text-muted">Learn from experienced faith leaders, industry experts, and academic professionals.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card feature-card h-100">
                        <div class="card-body text-center p-4">
                            <i class="bi bi-people text-primary" style="font-size: 3rem;"></i>
                            <h4 class="card-title mt-3">Faith-Based Learning</h4>
                            <p class="card-text text-muted">Grow spiritually and professionally through courses designed to equip you for impact.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @php
        $programs = $programs ?? \App\Models\Program::with(['department', 'level'])
            ->withCount('courses')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        $levels = $programs->pluck('level')->filter()->unique('id');
    @endphp

    <!-- Programs Section -->
    <section class="py-5 bg-light" id="programs">
        <div class="container">
            <div class="text-center mb-5">
                <span class="badge bg-primary-subtle text-primary fw-semibold px-3 py-2 mb-2 rounded-pill text-uppercase" style="letter-spacing: 1px; font-size: 0.8rem;">Academic Offerings</span>
                <h2 class="display-5 fw-bold">Our Programs</h2>
                <p class="lead text-muted">Explore our wide range of accredited academic and professional programs</p>
            </div>

            @if($levels->count() > 1)
                <div class="d-flex justify-content-center flex-wrap gap-2 mb-5">
                    <button type="button" class="btn btn-sm btn-primary rounded-pill px-4 filter-btn active" data-filter="all">
                        All Programs ({{ $programs->count() }})
                    </button>
                    @foreach($levels as $lvl)
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 filter-btn" data-filter="level-{{ $lvl->id }}">
                            {{ $lvl->name }}
                        </button>
                    @endforeach
                </div>
            @endif

            <div class="row g-4" id="programsGrid">
                @forelse($programs as $program)
                    @php
                        $levelFilterClass = $program->level ? 'level-' . $program->level->id : 'level-none';
                        $nameLower = strtolower($program->name . ' ' . ($program->department->name ?? ''));
                        
                        if (str_contains($nameLower, 'theolog') || str_contains($nameLower, 'bibl') || str_contains($nameLower, 'divin') || str_contains($nameLower, 'ministry')) {
                            $icon = 'bi-book';
                        } elseif (str_contains($nameLower, 'comput') || str_contains($nameLower, 'tech') || str_contains($nameLower, 'softw') || str_contains($nameLower, 'it') || str_contains($nameLower, 'cyber')) {
                            $icon = 'bi-laptop';
                        } elseif (str_contains($nameLower, 'busin') || str_contains($nameLower, 'admin') || str_contains($nameLower, 'manag') || str_contains($nameLower, 'econom') || str_contains($nameLower, 'account')) {
                            $icon = 'bi-briefcase';
                        } elseif (str_contains($nameLower, 'health') || str_contains($nameLower, 'nurs') || str_contains($nameLower, 'medic')) {
                            $icon = 'bi-heart-pulse';
                        } elseif (str_contains($nameLower, 'educat') || str_contains($nameLower, 'teach')) {
                            $icon = 'bi-mortarboard';
                        } else {
                            $icon = 'bi-journal-bookmark';
                        }
                    @endphp
                    <div class="col-md-6 col-lg-4 program-item {{ $levelFilterClass }}">
                        <div class="card h-100 program-card border-0 shadow-sm">
                            <div class="card-body d-flex flex-column p-4">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div class="program-icon-box">
                                        <i class="bi {{ $icon }}"></i>
                                    </div>
                                    <div class="d-flex flex-column align-items-end gap-1">
                                        @if($program->level)
                                            <span class="badge bg-light text-primary border border-primary-subtle rounded-pill px-2.5 py-1 small fw-semibold">
                                                {{ $program->level->name }}
                                            </span>
                                        @endif
                                        <span class="badge bg-secondary-subtle text-secondary rounded-pill px-2 py-0.5" style="font-size: 0.72rem;">
                                            Code: {{ $program->code }}
                                        </span>
                                    </div>
                                </div>

                                <h5 class="card-title fw-bold text-dark mb-2">{{ $program->name }}</h5>

                                @if($program->department)
                                    <div class="text-muted small mb-3 d-flex align-items-center">
                                        <i class="bi bi-diagram-3 me-1.5 text-primary"></i>
                                        <span>{{ $program->department->name }}</span>
                                    </div>
                                @endif

                                <p class="card-text text-muted flex-grow-1 small mb-4">
                                    {{ $program->description ?: 'Comprehensive academic curriculum equipping students with practical skills, rigorous knowledge, and leadership excellence.' }}
                                </p>

                                <div class="pt-3 border-top d-flex justify-content-between align-items-center mt-auto">
                                    <span class="text-muted small">
                                        <i class="bi bi-journals me-1 text-primary"></i>
                                        {{ $program->courses_count }} {{ \Illuminate\Support\Str::plural('Course', $program->courses_count) }}
                                    </span>
                                    <a href="{{ route('applications.create') }}" class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-medium">
                                        Apply Now <i class="bi bi-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <div class="p-5 bg-white rounded-3 shadow-sm">
                            <i class="bi bi-mortarboard text-muted" style="font-size: 3rem;"></i>
                            <h4 class="mt-3 text-dark">Programs are currently being updated</h4>
                            <p class="text-muted mb-4">Our curriculum is being refreshed. Please check back shortly or reach out to our admissions office.</p>
                            <a href="#contact" class="btn btn-primary rounded-pill px-4">Contact Admissions</a>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-5 bg-primary text-white" style="background-image: url('{{ asset('images/banner-gold.png') }}'); background-repeat: no-repeat; background-size: cover; background-position: center;" height="100px">
        <div class="container text-center">
            <h2 class="display-5 fw-bold mb-4 text-white">Ready to Start Your Journey?</h2>
            <p class="lead mb-4 text-white">Apply now and take the first step towards your future</p>
            <a href="{{ route('applications.create') }}" class="btn btn-gold btn-lg">
                <i class="bi bi-file-earmark-text me-2 text-white"></i>Apply for Admission
            </a>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="py-5" id="contact">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-5 fw-bold">Contact Us</h2>
                <p class="lead text-muted">Get in touch with our admissions team</p>
            </div>
            <div class="row">
                <div class="col-md-4 text-center mb-4">
                    <i class="bi bi-geo-alt text-primary" style="font-size: 2rem;"></i>
                    <h5 class="mt-3">Address</h5>
                    <p class="text-muted">South Africa</p>
                </div>
                <div class="col-md-4 text-center mb-4">
                    <i class="bi bi-telephone text-primary" style="font-size: 2rem;"></i>
                    <h5 class="mt-3">WhatsApp</h5>
                    <p class="text-muted">+27 68 443 8415</p>
                </div>
                <div class="col-md-4 text-center mb-4">
                    <i class="bi bi-envelope text-primary" style="font-size: 2rem;"></i>
                    <h5 class="mt-3">Email</h5>
                    <p class="text-muted">admission@jbiuniversity.com<br>info@jbiuniversity.com</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4">
        <div class="container text-center">
            <p class="mb-0">&copy; {{ date('Y') }} JBI University. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.querySelectorAll('.filter-btn').forEach(function(button) {
            button.addEventListener('click', function() {
                document.querySelectorAll('.filter-btn').forEach(function(b) {
                    b.classList.remove('btn-primary', 'active');
                    b.classList.add('btn-outline-secondary');
                });
                this.classList.add('btn-primary', 'active');
                this.classList.remove('btn-outline-secondary');

                var filter = this.getAttribute('data-filter');
                document.querySelectorAll('.program-item').forEach(function(item) {
                    if (filter === 'all' || item.classList.contains(filter)) {
                        item.classList.remove('d-none');
                    } else {
                        item.classList.add('d-none');
                    }
                });
            });
        });
    </script>
</body>
</html>
