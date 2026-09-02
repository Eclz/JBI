@extends('emails.layouts.master')

@section('title', ($notification->title ?? 'New Notification') . ' - ' . config('app.name', 'JBI University'))

@section('header_badge')
    <div style="margin-top: 14px;">
        @php
            $priority = strtolower($notification->priority ?? 'normal');
            $badgeClass = match($priority) {
                'urgent' => 'badge-urgent',
                'high' => 'badge-high',
                'success' => 'badge-success',
                default => 'badge-primary',
            };
            $priorityLabel = match($priority) {
                'urgent' => '⚡ Urgent Notice',
                'high' => '⚠️ High Priority',
                'low' => 'ℹ️ Low Priority',
                default => '📌 Official Notification',
            };
        @endphp
        <span class="badge {{ $badgeClass }}">{{ $priorityLabel }}</span>
    </div>
@endsection

@section('content')
    @php
        $roleName = $user->role_name ?? ucfirst($user->role ?? 'User');
        $userRole = strtolower($user->role ?? '');
        $firstName = $user->first_name ?? $user->name ?? 'User';
        
        $roleGreeting = match(true) {
            $userRole === 'faculty' || str_contains($roleName, 'Lecturer') || str_contains($roleName, 'Professor') => "Dear Faculty Member {$firstName}",
            $userRole === 'admin' || $userRole === 'super_admin' => "Dear Administrator {$firstName}",
            str_contains($roleName, 'Dean') || str_contains($roleName, 'Head') => "Dear {$firstName} ({$roleName})",
            str_contains($roleName, 'Officer') || str_contains($roleName, 'Staff') || str_contains($roleName, 'Registrar') => "Dear {$firstName} ({$roleName})",
            $userRole === 'student' => "Dear {$firstName}",
            default => "Dear {$firstName}",
        };
    @endphp

    <h2 class="greeting">{{ $roleGreeting }},</h2>

    <p style="margin-bottom: 16px; color: #4a5568; font-size: 15px;">
        You have received a new institutional notification on your <strong>{{ config('app.name', 'JBI University') }}</strong> account.
    </p>

    <!-- Notification Content Card -->
    <div class="callout @if($priority === 'urgent') callout-urgent @elseif($priority === 'high') callout-warning @endif">
        <h3 style="margin: 0 0 10px 0; font-size: 17px; color: #1e293b;">
            {{ $notification->title ?? 'System Notification' }}
        </h3>
        <div style="font-size: 15px; color: #334155; line-height: 1.6; white-space: pre-line;">
            {{ $notification->message }}
        </div>
    </div>

    <!-- Additional Metadata Table -->
    <table role="presentation" class="meta-table" cellpadding="0" cellspacing="0">
        <tr>
            <td>Notification Type</td>
            <td><strong>{{ ucfirst(str_replace('_', ' ', $notification->type ?? 'General')) }}</strong></td>
        </tr>
        <tr>
            <td>Priority Level</td>
            <td>
                <span style="font-weight: 600; text-transform: capitalize; color: @if($priority === 'urgent') #dc2626 @elseif($priority === 'high') #d97706 @else #3b5bdb @endif;">
                    {{ $priority }}
                </span>
            </td>
        </tr>
        <tr>
            <td>Recipient Role</td>
            <td>{{ $roleName }}</td>
        </tr>
        <tr>
            <td>Date & Time</td>
            <td>{{ $notification->created_at ? $notification->created_at->format('M d, Y \a\t h:i A') : date('M d, Y \a\t h:i A') }}</td>
        </tr>
    </table>

    <!-- CTA Button -->
    @if(!empty($notification->action_url))
    <div class="button-wrapper">
        <a href="{{ $notification->action_url }}" class="btn-primary" target="_blank">
            View Details in Portal &rarr;
        </a>
    </div>
    @else
    <div class="button-wrapper">
        <a href="{{ config('app.url', url('/')) }}/dashboard" class="btn-primary" target="_blank">
            Go to Your Dashboard &rarr;
        </a>
    </div>
    @endif

    <p style="font-size: 13px; color: #64748b; margin-top: 25px; line-height: 1.5;">
        You can review, manage, and archive all past notifications by navigating to the <strong>Notifications</strong> tab within your account dashboard.
    </p>
@endsection
