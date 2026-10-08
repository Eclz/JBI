@extends('layouts.guest')

@section('title', 'Careers')

@section('content')
<div class="container py-5">
    <div class="text-center mb-5">
        <span class="badge text-bg-primary mb-3">Join our team</span>
        <h1 class="fw-bold">Careers at JBI University</h1>
        <p class="text-muted mx-auto" style="max-width: 680px;">Explore current opportunities and apply securely. You can submit an application without creating an account.</p>
    </div>

    @if($vacancies->isEmpty())
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="bi bi-briefcase fs-1 text-muted"></i>
                <h2 class="h5 mt-3">No open vacancies right now</h2>
                <p class="text-muted mb-0">Please check back later for new opportunities.</p>
            </div>
        </div>
    @else
        <div class="row g-4">
            @foreach($vacancies as $vacancy)
                <div class="col-md-6">
                    <article class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between gap-3 align-items-start">
                                <div>
                                    <h2 class="h5 fw-bold mb-2">{{ $vacancy->position_title }}</h2>
                                    <div class="text-muted">{{ $vacancy->departmentRecord?->name ?? $vacancy->department ?? 'JBI University' }}</div>
                                </div>
                                <span class="badge text-bg-light border">{{ $vacancy->employment_type }}</span>
                            </div>
                            <p class="text-muted mt-3 mb-3">{{ \Illuminate\Support\Str::limit(strip_tags($vacancy->job_description), 180) }}</p>
                            <div class="small text-muted mb-3">
                                <i class="bi bi-geo-alt me-1"></i>{{ $vacancy->location ?: 'JBI University' }}
                                @if($vacancy->closing_date)
                                    <span class="mx-2">·</span>Closes {{ $vacancy->closing_date->format('M j, Y') }}
                                @endif
                            </div>
                            <a href="{{ route('careers.show', $vacancy) }}" class="btn btn-primary">View vacancy</a>
                        </div>
                    </article>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
