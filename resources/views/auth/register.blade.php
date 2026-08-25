@extends('layouts.app')

@section('title', 'Register - JBI University')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card border-0 shadow-lg overflow-hidden" style="border-radius: 16px;">
                <div class="row g-0">
                    <!-- Left Side - Simplified Form -->
                    <div class="col-md-7 p-4 p-lg-5 bg-white">
                        <div class="mb-4">
                            <h3 class="fw-bold text-primary mb-1">Create an Account</h3>
                            <p class="text-muted small">Register below to begin your student admission journey at JBI.</p>
                        </div>

                        @if($errors->any())
                            <div class="alert alert-danger py-2 px-3 mb-3 small" style="border-radius: 8px;">
                                <ul class="mb-0 ps-3">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if(!($registrationWindow['isOpen'] ?? true))
                            <div class="alert alert-warning">
                                <h6 class="alert-heading"><i class="bi bi-calendar-x me-2"></i>Registration is currently closed</h6>
                                @if(($registrationWindow['status'] ?? '') === 'scheduled' && $registrationWindow['start'])
                                    <p class="mb-0">Registration opens {{ $registrationWindow['start']->format('d M Y, H:i') }} {{ $registrationWindow['timezone'] }}.</p>
                                @elseif(($registrationWindow['status'] ?? '') === 'closed' && $registrationWindow['end'])
                                    <p class="mb-0">The registration window closed {{ $registrationWindow['end']->format('d M Y, H:i') }} {{ $registrationWindow['timezone'] }}.</p>
                                @else
                                    <p class="mb-0">Please contact the admissions office for the next registration period.</p>
                                @endif
                            </div>
                        @endif

                        <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data" id="registrationForm">
                            @csrf

                            <!-- Personal Details Section -->
                            <h6 class="text-uppercase text-primary fw-semibold mb-3" style="font-size: 0.8rem; letter-spacing: 1px;">
                                <i class="bi bi-person-fill me-2"></i>Account Details
                            </h6>
                            
                            <div class="row g-3 mb-4">
                                <div class="col-sm-6">
                                    <label for="first_name" class="form-label small fw-medium">First Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-sm @error('first_name') is-invalid @enderror" id="first_name" name="first_name" value="{{ old('first_name') }}" required placeholder="John">
                                    @error('first_name')
                                        <div class="invalid-feedback small">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-sm-6">
                                    <label for="last_name" class="form-label small fw-medium">Last Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-sm @error('last_name') is-invalid @enderror" id="last_name" name="last_name" value="{{ old('last_name') }}" required placeholder="Doe">
                                    @error('last_name')
                                        <div class="invalid-feedback small">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-sm-6">
                                    <label for="email" class="form-label small fw-medium">Email Address <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control form-control-sm @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required placeholder="john.doe@example.com">
                                    @error('email')
                                        <div class="invalid-feedback small">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-sm-6">
                                    <label for="phone" class="form-label small fw-medium">Phone Number <span class="text-danger">*</span></label>
                                    <input type="tel" class="form-control form-control-sm @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone') }}" required placeholder="+1 (555) 000-0000">
                                    @error('phone')
                                        <div class="invalid-feedback small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Security Section -->
                            <h6 class="text-uppercase text-primary fw-semibold mb-3" style="font-size: 0.8rem; letter-spacing: 1px;">
                                <i class="bi bi-shield-lock-fill me-2"></i>Account Security
                            </h6>
                            <div class="row g-3 mb-4">
                                <div class="col-sm-6">
                                    <label for="password" class="form-label small fw-medium">Password <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="password" class="form-control form-control-sm @error('password') is-invalid @enderror" id="password" name="password" required>
                                        <button class="btn btn-outline-secondary btn-sm toggle-password" type="button" data-target="password" tabindex="-1">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </div>
                                    @error('password')
                                        <div class="invalid-feedback d-block small">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-sm-6">
                                    <label for="password_confirmation" class="form-label small fw-medium">Confirm Password <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="password" class="form-control form-control-sm" id="password_confirmation" name="password_confirmation" required>
                                        <button class="btn btn-outline-secondary btn-sm toggle-password" type="button" data-target="password_confirmation" tabindex="-1">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="form-check mb-4">
                                <input class="form-check-input" type="checkbox" id="terms" name="terms" required>
                                <label class="form-check-label text-muted small" for="terms">
                                    I agree to the <a href="#" class="text-primary text-decoration-none">Terms and Conditions</a> & <a href="#" class="text-primary text-decoration-none">Privacy Policy</a> *
                                </label>
                            </div>

                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-primary" style="border-radius: 8px;" {{ !($registrationWindow['isOpen'] ?? true) ? 'disabled' : '' }}>
                                    <i class="bi bi-person-plus-fill me-2"></i>Register Account
                                </button>
                            </div>

                            <div class="text-center">
                                <p class="small text-muted mb-0">Already have an account? 
                                    <a href="{{ route('login') }}" class="text-primary text-decoration-none fw-medium">Sign In</a>
                                </p>
                            </div>
                        </form>
                    </div>

                    <!-- Right Side - Info Panel -->
                    <div class="col-md-5 d-none d-md-flex align-items-center justify-content-center text-white" style="background: linear-gradient(135deg, #1e3a8a 0%, #1a2236 100%);">
                        <div class="text-center p-5">
                            <img src="{{ asset('images/jbi-blue.webp') }}" alt="JBI University Logo" class="mb-4" style="height: 100px;">
                            <h4 class="fw-bold mb-2">Welcome to JBI</h4>
                            <p class="text-white-50 small mb-4">Begin your student admission process directly from within your dashboard.</p>

                            <div class="text-start mx-auto" style="max-width: 280px;">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="bg-white bg-opacity-10 text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 32px; height: 32px; font-weight: bold; font-size: 0.85rem;">1</div>
                                    <span class="small">Create an Account</span>
                                </div>
                                <div class="d-flex align-items-center mb-3">
                                    <div class="bg-white bg-opacity-10 text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 32px; height: 32px; font-weight: bold; font-size: 0.85rem;">2</div>
                                    <span class="small">Log In to Apply</span>
                                </div>
                                <div class="d-flex align-items-center mb-3">
                                    <div class="bg-white bg-opacity-10 text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 32px; height: 32px; font-weight: bold; font-size: 0.85rem;">3</div>
                                    <span class="small">Submit Academic Profile</span>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="bg-white bg-opacity-10 text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 32px; height: 32px; font-weight: bold; font-size: 0.85rem;">4</div>
                                    <span class="small">Get Admitted by Admin</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
