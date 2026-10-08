@extends('layouts.guest')

@section('title', 'Application submitted')

@section('content')
<div class="container py-5">
    <div class="card border-0 shadow-sm mx-auto" style="max-width: 680px;">
        <div class="card-body p-4 p-md-5 text-center">
            <i class="bi bi-check-circle-fill text-success display-4"></i>
            <h1 class="h3 fw-bold mt-3">Application received</h1>
            <p class="text-muted">Thank you, {{ $applicant->first_name }}. Your application for <strong>{{ $applicant->vacancy->position_title }}</strong> has been submitted.</p>
            <div class="alert alert-warning text-start">
                <strong>Save this private tracking link.</strong> It is the only way to check your application status without an account. Anyone with the link can view the status, so do not share it.
                <div class="input-group mt-3">
                    <input id="tracking-link" class="form-control" value="{{ $trackingUrl }}" readonly>
                    <button class="btn btn-outline-secondary" type="button" onclick="navigator.clipboard.writeText(document.getElementById('tracking-link').value)">Copy link</button>
                </div>
            </div>
            <a href="{{ route('careers.index') }}" class="btn btn-primary mt-2">Return to careers</a>
        </div>
    </div>
</div>
@endsection
