@extends('emails.layouts.master')

@section('title', 'Reset Password - ' . config('app.name', 'JBI University'))

@section('header_badge')
    <div style="margin-top: 14px;">
        <span class="badge badge-high">🔑 Password Reset Request</span>
    </div>
@endsection

@section('content')
    @php
        $firstName = $user->first_name ?? $user->name ?? 'User';
    @endphp

    <h2 class="greeting">Hello {{ $firstName }},</h2>

    <p style="margin-bottom: 16px; color: #4a5568; font-size: 15px; line-height: 1.6;">
        You are receiving this email because we received a password reset request for your account on the <strong>{{ config('app.name', 'JBI University') }} Portal</strong>.
    </p>

    <div class="callout callout-warning">
        <h3 style="margin: 0 0 8px 0; font-size: 16px; color: #92400e;">
            Reset Your Password
        </h3>
        <p style="margin: 0; font-size: 14px; color: #b45309; line-height: 1.5;">
            Click the button below to choose a new password. If you did not request a password reset, no further action is required and your account remains secure.
        </p>
    </div>

    <!-- CTA Button -->
    <div class="button-wrapper">
        <a href="{{ $resetUrl }}" class="btn-primary" target="_blank" style="font-size: 16px; padding: 14px 36px;">
            Reset Your Password &rarr;
        </a>
    </div>

    <div style="background-color: #f8fafc; border-radius: 6px; padding: 14px 18px; margin-top: 25px; border: 1px dashed #cbd5e1; font-size: 13px; color: #64748b;">
        <p style="margin: 0 0 6px 0;"><strong>Security Note:</strong> This password reset link will expire in <strong>60 minutes</strong>.</p>
        <p style="margin: 0;">If you're having trouble clicking the "Reset Your Password" button, copy and paste the URL below into your web browser:<br>
        <a href="{{ $resetUrl }}" style="word-break: break-all; font-size: 12px; color: #3b5bdb;">{{ $resetUrl }}</a></p>
    </div>
@endsection
