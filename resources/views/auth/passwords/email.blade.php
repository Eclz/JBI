@extends('layouts.admissions')
@section('title', 'Reset your password')
@section('content')
<section class="notice-card"><div class="notice-icon" aria-hidden="true">↗</div><h1>Forgot your password?</h1><p>Enter the email you used to register. We’ll send a link to choose a new password.</p>
    @if(session('status'))<div class="alert alert-success" role="status">{{ session('status') }} Check your inbox and spam folder.</div>@endif
    @if(session('warning'))<div class="alert alert-warning" role="alert">{{ session('warning') }}</div>@endif
    @if($errors->any())<div class="alert alert-error" role="alert">{{ $errors->first() }}</div>@endif
    <form method="POST" action="{{ route('password.email') }}" data-submit-form>@csrf<div class="field"><label for="email">Email address</label><input id="email" type="email" name="email" value="{{ old('email') }}" autocomplete="email" inputmode="email" autocapitalize="none" spellcheck="false" maxlength="254" required></div><button type="submit" class="primary-button" style="margin-top:24px" data-ready-text="Send password reset link" data-busy-text="Sending reset link…">Send password reset link</button></form>
    <p class="form-bottom"><a href="{{ route('login') }}">Back to sign in</a> · <a href="{{ route('register') }}">Create an account</a></p>
</section>
@endsection
