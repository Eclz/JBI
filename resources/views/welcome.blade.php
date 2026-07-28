<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to JBI University</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
            color: #667eea;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="/">
                <img src="{{ asset('images/jbi-logo.webp') }}" alt="JBI University" height="40" class="me-2">
                <span class="fw-bold">JBI University</span>
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
                            <a class="nav-link" href="{{ route('login') }}">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-primary ms-2" href="{{ route('applications.create') }}">Apply Now</a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="display-3 fw-bold mb-4">Welcome to JBI University</h1>
                    <p class="lead mb-4">Transform your future with quality education. Join thousands of students who have achieved their dreams with us.</p>
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
                    <img src="{{ asset('images/jbi-logo.webp') }}" alt="University" class="img-fluid">
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
                        <div class="stat-number">5000+</div>
                        <p class="text-muted">Students</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-item">
                        <div class="stat-number">200+</div>
                        <p class="text-muted">Faculty Members</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-item">
                        <div class="stat-number">50+</div>
                        <p class="text-muted">Programs</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-item">
                        <div class="stat-number">95%</div>
                        <p class="text-muted">Success Rate</p>
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
                            <h4 class="card-title mt-3">Quality Education</h4>
                            <p class="card-text text-muted">World-class faculty and cutting-edge curriculum designed for success.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card feature-card h-100">
                        <div class="card-body text-center p-4">
                            <i class="bi bi-building text-primary" style="font-size: 3rem;"></i>
                            <h4 class="card-title mt-3">Modern Facilities</h4>
                            <p class="card-text text-muted">State-of-the-art infrastructure and learning resources.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card feature-card h-100">
                        <div class="card-body text-center p-4">
                            <i class="bi bi-people text-primary" style="font-size: 3rem;"></i>
                            <h4 class="card-title mt-3">Vibrant Community</h4>
                            <p class="card-text text-muted">Join a diverse and supportive student community.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Programs Section -->
    <section class="py-5 bg-light" id="programs">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-5 fw-bold">Our Programs</h2>
                <p class="lead text-muted">Explore our wide range of academic programs</p>
            </div>
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title"><i class="bi bi-book text-primary me-2"></i>Theology</h5>
                            <p class="card-text">Comprehensive theological studies with biblical foundations.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title"><i class="bi bi-laptop text-primary me-2"></i>Computer Science</h5>
                            <p class="card-text">Cutting-edge technology and software development programs.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title"><i class="bi bi-briefcase text-primary me-2"></i>Business Administration</h5>
                            <p class="card-text">Develop leadership skills and business acumen.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title"><i class="bi bi-heart-pulse text-primary me-2"></i>Healthcare</h5>
                            <p class="card-text">Professional healthcare and nursing programs.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-5 bg-primary text-white">
        <div class="container text-center">
            <h2 class="display-5 fw-bold mb-4">Ready to Start Your Journey?</h2>
            <p class="lead mb-4">Apply now and take the first step towards your future</p>
            <a href="{{ route('applications.create') }}" class="btn btn-light btn-lg">
                <i class="bi bi-file-earmark-text me-2"></i>Apply for Admission
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
                    <p class="text-muted">91 Progress Road Lindhaven<br>Roodeport South Africa</p>
                </div>
                <div class="col-md-4 text-center mb-4">
                    <i class="bi bi-telephone text-primary" style="font-size: 2rem;"></i>
                    <h5 class="mt-3">Phone</h5>
                    <p class="text-muted">+27 67 965 3866</p>
                </div>
                <div class="col-md-4 text-center mb-4">
                    <i class="bi bi-envelope text-primary" style="font-size: 2rem;"></i>
                    <h5 class="mt-3">Email</h5>
                    <p class="text-muted">admission@jbiuniversity.com</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4">
        <div class="container text-center">
            <p class="mb-0">&copy; 2025 JBI University. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
