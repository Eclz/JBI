@extends('layouts.guest')

@section('title', 'Application status')

@section('content')
<div class="container py-5">
    <div class="card border-0 shadow-sm mx-auto" style="max-width: 680px;">
        <div class="card-body p-4 p-md-5">
            <span class="badge text-bg-light border mb-3">Application status</span>
            <h1 class="h3 fw-bold">{{ $applicant->vacancy->position_title }}</h1>
            <p class="text-muted">Applicant: {{ $applicant->first_name }} {{ $applicant->last_name }}</p>
            <div class="alert {{ $applicant->status === 'Rejected' ? 'alert-secondary' : ($applicant->status === 'Hired' ? 'alert-success' : 'alert-primary') }}">
                Current stage: <strong>{{ $applicant->status }}</strong>
            </div>
            <p class="small text-muted">Last updated {{ $applicant->updated_at->format('F j, Y') }}. For privacy, no contact details or submitted documents are shown here.</p>
            <a href="{{ route('careers.index') }}" class="btn btn-outline-primary">Browse vacancies</a>
        </div>
    </div>
</div>
@endsection
