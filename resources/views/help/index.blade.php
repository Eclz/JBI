@extends('layouts.app')

@section('title', 'Help Centre')

@section('content')
@php
    $role = auth()->user()->role;
    $guides = [
        'getting-started' => [
            'icon' => 'bi-rocket-takeoff',
            'body' => 'Sign in with the email and password supplied by the institution. If this is your first sign-in you may be asked to change your password. Use the sidebar to move between the areas available to your role, and the bell icon for notifications.',
        ],
        'courses' => [
            'icon' => 'bi-book',
            'body' => 'Students can view their programme, register for eligible courses and open course materials. Faculty manage their assigned courses and learning materials. Course changes that you cannot make yourself must be requested from the Registrar.',
        ],
        'assignments' => [
            'icon' => 'bi-journal-check',
            'body' => 'Open an assignment from your course to read the brief and due date, upload your submission before the deadline and track the feedback. Late submissions are accepted only where the lecturer has allowed them.',
        ],
        'grades' => [
            'icon' => 'bi-bar-chart',
            'body' => 'Published grades appear under Grades. Your transcript lists all completed courses and your cumulative GPA. Contact the Registrar if you believe a published result is wrong.',
        ],
        'fees' => [
            'icon' => 'bi-cash-coin',
            'body' => 'View your fee statement under Fees, pay using the references provided and upload the payment evidence. Receipts become available once Finance has confirmed the payment, and can be verified publicly using the receipt number.',
        ],
        'profile' => [
            'icon' => 'bi-person-gear',
            'body' => 'Update your contact details, photo and password from My Profile. Name and registration details are managed by administration; request changes through Support.',
        ],
        'technical-support' => [
            'icon' => 'bi-tools',
            'body' => 'If something is not working, submit a request through Support with a short description, the page address and what you expected to happen. Include a screenshot where possible.',
        ],
    ];
@endphp

<div class="mb-4">
    <h1 class="h3 mb-1 text-primary"><i class="bi bi-question-circle me-2"></i>Help Centre</h1>
    <p class="text-muted mb-0">Quick guides for using the JBI University portal.</p>
</div>

<div class="accordion mb-4" id="helpAccordion">
    @foreach($helpTopics as $slug => $title)
        <div class="accordion-item">
            <h2 class="accordion-header" id="heading-{{ $slug }}">
                <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }}" type="button"
                        data-bs-toggle="collapse" data-bs-target="#topic-{{ $slug }}"
                        aria-expanded="{{ $loop->first ? 'true' : 'false' }}" aria-controls="topic-{{ $slug }}">
                    <i class="bi {{ $guides[$slug]['icon'] ?? 'bi-info-circle' }} me-2"></i>{{ $title }}
                </button>
            </h2>
            <div id="topic-{{ $slug }}" class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}"
                 aria-labelledby="heading-{{ $slug }}" data-bs-parent="#helpAccordion">
                <div class="accordion-body">{{ $guides[$slug]['body'] ?? 'Guide coming soon.' }}</div>
            </div>
        </div>
    @endforeach
</div>

<div class="card">
    <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <strong>Still need help?</strong>
            <div class="text-muted small">Send a request to the support team and we will get back to you.</div>
        </div>
        <a href="{{ route('support.index') }}" class="btn btn-primary"><i class="bi bi-headset me-1"></i>Contact Support</a>
    </div>
</div>
@endsection
