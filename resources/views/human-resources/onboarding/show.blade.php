@extends('layouts.app')
@section('title', 'Staff onboarding')
@section('content')
<div class="container-fluid py-4">
    <div class="d-flex flex-wrap justify-content-between gap-3 mb-4">
        <div><h2>Onboarding: {{ $onboarding->user->name }}</h2>
            <p class="text-muted mb-0">{{ $onboarding->template_name }} · Due {{ $onboarding->due_date?->format('d M Y') }} · {{ $onboarding->status }} · {{ $onboarding->progress_percentage }}% complete</p>
        </div>
        <div class="d-flex gap-2 align-items-start">
            @if(auth()->user()->hasPermission('hr_core', 'edit'))
                <a class="btn btn-outline-primary" href="{{ route('human-resources.staff.edit', $onboarding->user) }}">Edit employee / workspace</a>
            @endif
            <a class="btn btn-outline-secondary" href="{{ route('human-resources.section', 'onboarding') }}">All onboarding</a>
        </div>
    </div>
    <div class="alert alert-info">Work down the checklist. Assign the responsible officer and add a completion note or attachment. Password setup and workspace tasks also check the employee record.</div>
    @if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif
    <div class="card mb-3"><div class="card-body">
        <strong>Account:</strong> {{ $onboarding->user->email }} · Password {{ $onboarding->user->must_change_password ? 'setup pending' : 'ready' }}
        @can('update', $onboarding->user)
            <form action="{{ route('admin.users.reset-password', $onboarding->user) }}" method="POST" class="d-inline ms-2">@csrf<button class="btn btn-sm btn-outline-primary">Resend password invitation</button></form>
        @endcan
        <div class="small text-muted mt-2">Workspace: {{ $onboarding->user->hrProfile?->workspace ?: 'Not assigned' }}. Invitation: {{ $onboarding->user->setup_email_error ?: ($onboarding->user->setup_email_sent_at ? 'Email sent to mail server' : 'No successful send recorded') }}.</div>
    </div></div>
    @foreach($onboarding->tasks as $task)
        <div class="card mb-3"><div class="card-body">
            <h5>{{ $task->task_name }} <span class="badge {{ $task->status === 'Completed' ? 'bg-success' : 'bg-secondary' }}">{{ $task->status }}</span></h5>
            @if(auth()->user()->hasPermission('hr_onboarding', 'edit'))
                <form action="{{ route('human-resources.onboarding.tasks.update', $task) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-4"><label class="form-label" for="owner-{{ $task->id }}">Responsible officer</label><select class="form-select" id="owner-{{ $task->id }}" name="assigned_to"><option value="">Unassigned</option>@foreach($assignees as $assignee)<option value="{{ $assignee->id }}" @selected($task->assigned_to === $assignee->id)>{{ $assignee->name }}</option>@endforeach</select></div>
                        <div class="col-md-4"><label class="form-label" for="date-{{ $task->id }}">Due date</label><input class="form-control" type="date" id="date-{{ $task->id }}" name="due_date" value="{{ $task->due_date?->format('Y-m-d') }}"></div>
                        <div class="col-md-4"><label class="form-label" for="status-{{ $task->id }}">Status</label><select class="form-select" id="status-{{ $task->id }}" name="status"><option @selected($task->status === 'Pending')>Pending</option><option @selected($task->status === 'Completed')>Completed</option></select></div>
                        <div class="col-md-8"><label class="form-label" for="note-{{ $task->id }}">Completion note / follow-up</label><textarea class="form-control" id="note-{{ $task->id }}" name="comments" maxlength="4000" rows="2">{{ $task->comments }}</textarea></div>
                        <div class="col-md-4"><label class="form-label" for="attachment-{{ $task->id }}">Supporting attachment</label><input class="form-control" type="file" id="attachment-{{ $task->id }}" name="attachment" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"><small class="text-muted">Private file, up to 5 MB.</small></div>
                    </div>
                    <button class="btn btn-primary btn-sm mt-3">Save task</button>
                </form>
            @else
                <p>{{ $task->comments ?: 'No completion note yet.' }}</p><p class="text-muted">Responsible officer: {{ $task->assignee?->name ?: 'Unassigned' }}</p>
            @endif
            @if($task->attachment_path)<a class="d-block mt-2" href="{{ route('human-resources.onboarding.tasks.attachment', $task) }}">Download supporting attachment</a>@endif
        </div></div>
    @endforeach
</div>
@endsection
