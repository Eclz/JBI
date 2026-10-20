@extends('layouts.admissions')
@section('title', 'Request a new verification link')
@section('content')
<section class="notice-card"><div class="notice-icon" aria-hidden="true">✉</div><h1>This link is no longer valid</h1><p>Verification links expire after 24 hours. The link may also be invalid if you changed your email address.</p>@auth<a class="primary-button" href="{{ route('verification.notice') }}">Check verification and request a new link</a>@else<p>Sign in to your existing account to request a new link. You don’t need to register again.</p><a class="primary-button" href="{{ route('login') }}">Sign in to request a new link</a><p class="form-bottom"><a href="{{ route('password.request') }}">Forgot your password?</a></p>@endauth</section>
@endsection
