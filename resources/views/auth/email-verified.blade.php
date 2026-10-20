@extends('layouts.admissions')
@section('title', 'Email verified')
@section('content')
<section class="notice-card"><div class="notice-icon" aria-hidden="true">✓</div><h1>Your email is verified</h1><p>Your account is ready. Sign in to choose your programme and complete your admission application.</p>@guest<a href="{{ route('login') }}" class="primary-button">Sign in to continue</a>@else<p>You’re signed in to a different account. Sign out first, then sign in with the email you just verified.</p><form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="primary-button">Sign out</button></form>@endguest</section>
@endsection
