@extends('layouts.app')

@section('title', 'Help & Support')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0 text-primary">
            <i class="bi bi-question-circle me-2"></i>Help & Support
        </h1>
        <p class="text-muted mb-0">Get help and find answers to your questions</p>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="row">
    <!-- Quick Help -->
    <div class="col-lg-8 mb-4">
        <!-- FAQ -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-patch-question me-2"></i>Frequently Asked Questions
                </h5>
            </div>
            <div class="card-body">
                <div class="accordion" id="faqAccordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                How do I reset my password?
                            </button>
                        </h2>
                        <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                You can reset your password from the login page by clicking "Forgot Password?" or by going to your Profile Settings and selecting "Change Password".
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                How do I enroll in a course?
                            </button>
                        </h2>
                        <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Navigate to the Courses page, find the course you want to enroll in, and click the "Enroll" button. Some courses may require approval from the instructor.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                Where can I view my grades?
                            </button>
                        </h2>
                        <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Go to your Dashboard and click on "Academic Records" or navigate to a specific course to view your grades for that course.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                                How do I submit an assignment?
                            </button>
                        </h2>
                        <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Go to the course page, find the assignment under "Assignments", click on it, and use the upload button to submit your work before the deadline.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq5">
                                How do I contact technical support?
                            </button>
                        </h2>
                        <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                You can use the contact form on this page or email us directly at info@jbiuniversity.com. We aim to respond within 24 hours.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Form -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-envelope me-2"></i>Contact Support
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('support.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Subject <span class="text-danger">*</span></label>
                        <input type="text" name="subject" class="form-control @error('subject') is-invalid @enderror"
                               value="{{ old('subject') }}" required>
                        @error('subject')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Priority <span class="text-danger">*</span></label>
                        <select name="priority" class="form-select @error('priority') is-invalid @enderror" required>
                            <option value="low">Low - General inquiry</option>
                            <option value="medium" selected>Medium - Need assistance</option>
                            <option value="high">High - Urgent issue</option>
                        </select>
                        @error('priority')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Message <span class="text-danger">*</span></label>
                        <textarea name="message" class="form-control @error('message') is-invalid @enderror"
                                  rows="6" required>{{ old('message') }}</textarea>
                        @error('message')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-send me-2"></i>Send Message
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Quick Links -->
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-link-45deg me-2"></i>Quick Links
                </h5>
            </div>
            <div class="list-group list-group-flush">
                <a href="#" class="list-group-item list-group-item-action">
                    <i class="bi bi-book me-2"></i>User Guide
                </a>
                <a href="#" class="list-group-item list-group-item-action">
                    <i class="bi bi-youtube me-2"></i>Video Tutorials
                </a>
                <a href="#" class="list-group-item list-group-item-action">
                    <i class="bi bi-download me-2"></i>Downloads
                </a>
                <a href="{{ route('forums.index') }}" class="list-group-item list-group-item-action">
                    <i class="bi bi-chat-dots me-2"></i>Community Forums
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-info-circle me-2"></i>Contact Information
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <strong>Email</strong>
                    <p class="mb-0">info@jbiuniversity.com</p>
                </div>
                <div class="mb-3">
                    <strong>Phone</strong>
                    <p class="mb-0">WhatsApp: +27 68 443 8415</p>
                </div>
                <div class="mb-3">
                    <strong>Office Hours</strong>
                    <p class="mb-0">Monday - Friday<br>9:00 AM - 5:00 PM</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
