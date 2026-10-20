@extends('layouts.app')
@section('title', 'Onboarding')
@section('content')
<div class="container-fluid py-4">
    <h2>Onboarding</h2><p class="text-muted">Work through the next action for each applicant or employee.</p>
    <div class="card mb-4"><div class="card-body">
        <h5>Registration and email readiness</h5>
        @forelse($semesters as $semester)
            <p>{{ $semester->name }}: {{ $semester->registration_start?->format('d M Y') }} to {{ $semester->registration_end?->format('d M Y') }} · {{ $semester->is_active ? 'Active' : 'Inactive' }}
                @if(auth()->user()->hasPermission('semesters', 'edit'))<a href="{{ route('admin.semesters.edit', $semester) }}">Edit dates</a>@endif</p>
        @empty <p class="text-danger">No current semester is configured.</p> @endforelse
        <p class="mb-1">Your alert emails, last 7 days: @forelse($emailCounts as $status => $count)<span class="badge bg-secondary">{{ ucfirst($status) }}: {{ $count }}</span> @empty No alerts yet. @endforelse</p>
        <a href="{{ route('notifications.index') }}">View email status / retry failed alerts</a>
        <p class="small text-muted mb-0 mt-2">Sent means accepted by the mail server; inbox delivery still depends on the recipient's mail provider.</p>
    </div></div>
    @if($admissions)
        <div class="d-flex justify-content-between mb-2"><h4>Student admissions</h4><a href="{{ route('admin.applications.index') }}">All applications</a></div>
        <p class="text-muted">1. Verify receipt → 2. Verify documents → 3. Assign programme → 4. Confirm admission. The student then acknowledges their letter and registers for courses.</p>
        <div class="table-responsive"><table class="table table-striped"><thead><tr><th>Applicant</th><th>Next action</th><th></th></tr></thead><tbody>
            @forelse($applications as $application)
                @php $next = !$application->isPaymentApproved() ? ($application->payment_proof ? 'Verify receipt' : 'Waiting for payment receipt') : (!$application->areDocumentsVerified() ? 'Review and verify documents' : (!$application->hasAssignedCourse() ? 'Assign programme' : 'Confirm admission')); @endphp
                <tr><td>{{ $application->full_name }}<div class="small text-muted">{{ $application->application_number }}</div></td><td>{{ $next }}</td><td><a class="btn btn-sm btn-primary" href="{{ route('admin.applications.show', $application) }}">Open application</a></td></tr>
            @empty <tr><td colspan="3">No pending applications.</td></tr> @endforelse
        </tbody></table></div>
    @endif
    @if($hr)
        <div class="d-flex justify-content-between mt-4 mb-2"><h4>Staff onboarding</h4><a href="{{ route('human-resources.section', 'onboarding') }}">All checklists</a></div>
        <p class="text-muted">For new recruits use Recruitment → Offer → Approve hire. Existing employees can be prepared below without creating a duplicate account.</p>
        @foreach($onboardings as $onboarding)<div class="card mb-2"><div class="card-body d-flex flex-wrap justify-content-between gap-2"><span>{{ $onboarding->user->name }} · {{ $onboarding->status }} · {{ $onboarding->progress_percentage }}% · Due {{ $onboarding->due_date?->format('d M Y') }}</span><a href="{{ route('human-resources.onboarding.show', $onboarding) }}">Continue checklist</a></div></div>@endforeach
        @if(auth()->user()->hasPermission('hr_onboarding', 'create'))
            <details class="mt-3"><summary>Prepare an existing staff account</summary><div class="table-responsive"><table class="table"><thead><tr><th>Staff</th><th>HR record</th><th></th></tr></thead><tbody>
                @foreach($staff as $employee)<tr><td>{{ $employee->name }}</td><td>{{ $employee->hrProfile ? 'Linked' : 'Missing' }}</td><td><form method="POST" action="{{ route('onboarding.prepare-staff', $employee) }}">@csrf<button class="btn btn-sm btn-outline-primary">Open / prepare onboarding</button></form></td></tr>@endforeach
            </tbody></table></div></details>
        @endif
    @endif
</div>
@endsection
