@extends('emails.layouts.master')

@section('title', 'Welcome to ' . config('app.name', 'JBI University') . ' - Set Up Your Password')

@section('header_badge')
    <div style="margin-top: 14px;">
        <span class="badge badge-success">🎉 Account Created &bull; Action Required</span>
    </div>
@endsection

@section('content')
    @php
        $firstName = $user->first_name ?? $user->name ?? 'Colleague';
        $roleTitle = $user->role_name ?? ucfirst($user->role ?? 'User');
    @endphp

    <h2 class="greeting">Welcome to {{ config('app.name', 'JBI University') }}, {{ $firstName }}!</h2>

    <p style="margin-bottom: 16px; color: #4a5568; font-size: 15px; line-height: 1.6;">
        An institutional user account has been created for you on the <strong>{{ config('app.name', 'JBI University') }} Portal</strong> with the role of <strong>{{ $roleTitle }}</strong>.
    </p>

    <div class="callout callout-success">
        <h3 style="margin: 0 0 8px 0; font-size: 16px; color: #065f46;">
            🔐 Secure First-Time Account Activation
        </h3>
        <p style="margin: 0; font-size: 14px; color: #047857; line-height: 1.5;">
            To ensure maximum security and privacy, your account password is not set by administrators. Please use the button below to verify your email and create your own confidential password.
        </p>
    </div>

    <!-- Account Details Table -->
    <table role="presentation" class="meta-table" cellpadding="0" cellspacing="0">
        <tr>
            <td>Email Address (Username)</td>
            <td><strong>{{ $user->email }}</strong></td>
        </tr>
        <tr>
            <td>Assigned Role</td>
            <td>{{ $roleTitle }}</td>
        </tr>
        @if(!empty($user->employee_id))
        <tr>
            <td>Employee / Staff ID</td>
            <td>{{ $user->employee_id }}</td>
        </tr>
        @endif
        @if($user->facultyProfile && $user->facultyProfile->department)
        <tr>
            <td>Department</td>
            <td>{{ $user->facultyProfile->department->name }}</td>
        </tr>
        @endif
        <tr>
            <td>Account Status</td>
            <td><span style="color: #059669; font-weight: 600;">Pending Password Creation</span></td>
        </tr>
    </table>

    <!-- CTA Button -->
    <div class="button-wrapper">
        <a href="{{ $setupUrl }}" class="btn-primary" target="_blank" style="font-size: 16px; padding: 14px 36px;">
            Set Up Password & Activate Account &rarr;
        </a>
    </div>

    <div style="background-color: #f8fafc; border-radius: 6px; padding: 14px 18px; margin-top: 25px; border: 1px dashed #cbd5e1; font-size: 13px; color: #64748b;">
        <p style="margin: 0 0 6px 0;"><strong>Security Note:</strong> This activation link will expire in <strong>60 minutes</strong>.</p>
        <p style="margin: 0;">If you did not anticipate an invitation to this portal, or if you encounter any difficulty, please reach out to the JBI University IT Support Desk.</p>
    </div>
@endsection
