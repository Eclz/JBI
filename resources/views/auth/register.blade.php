@extends('layouts.admissions')
@section('title', 'Create your student account')
@section('content')
<div class="admissions-card">
    <section class="form-panel" aria-labelledby="registration-title">
        <div class="eyebrow">Begin your application</div>
        <h1 id="registration-title">Create your student account</h1>
        <p class="intro">Start with your contact details. You’ll verify your email before choosing your programme and uploading documents.</p>
        <p class="mobile-steps"><strong>1. Create account</strong> &nbsp;→&nbsp; 2. Verify email &nbsp;→&nbsp; 3. Apply</p>
        @if($errors->any())
            <div class="alert alert-error" id="registration-errors" role="alert" tabindex="-1"><strong>Please check the following:</strong><ul>@foreach($errors->messages() as $field => $messages)<li><a href="#{{ $field === 'password.confirmed' ? 'password_confirmation' : $field }}">{{ $messages[0] }}</a></li>@endforeach</ul></div>
        @endif
        @if(!($admissionWindow['isOpen'] ?? true))
            <div class="alert alert-warning" role="status"><strong>Admissions are currently closed.</strong>
                @if(($admissionWindow['status'] ?? '') === 'scheduled' && $admissionWindow['start'])
                    <p>Applications open {{ $admissionWindow['start']->format('d M Y, H:i') }} ({{ $admissionWindow['timezone'] }}).</p>
                @else <p>Contact <a href="mailto:admission@jbiuniversity.com">admissions</a> for the next intake.</p> @endif
                Already registered? <a href="{{ route('login') }}">Sign in to check your application.</a>
            </div>
        @endif
        <form method="POST" action="{{ route('register') }}" id="registrationForm" data-submit-form>
            @csrf
            <div class="account-fields">
                <div class="field"><label for="first_name">First name</label><input id="first_name" name="first_name" type="text" autocomplete="given-name" maxlength="255" value="{{ old('first_name') }}" required @error('first_name') aria-invalid="true" aria-describedby="first_name-error" @enderror>@error('first_name')<p class="field-error" id="first_name-error">{{ $message }}</p>@enderror</div>
                <div class="field"><label for="last_name">Last name</label><input id="last_name" name="last_name" type="text" autocomplete="family-name" maxlength="255" value="{{ old('last_name') }}" required @error('last_name') aria-invalid="true" aria-describedby="last_name-error" @enderror>@error('last_name')<p class="field-error" id="last_name-error">{{ $message }}</p>@enderror</div>
                <div class="field field-full"><label for="email">Email address</label><input id="email" name="email" type="email" inputmode="email" autocomplete="email" autocapitalize="none" spellcheck="false" maxlength="254" value="{{ old('email') }}" required aria-describedby="email-help @error('email') email-error @enderror" @error('email') aria-invalid="true" @enderror><p id="email-help" class="hint">Use an email you can open. We’ll send your verification link here.</p>@error('email')<p class="field-error" id="email-error">{{ $message }} <a href="{{ route('login') }}">Sign in</a> or <a href="{{ route('password.request') }}">reset your password</a>.</p>@enderror</div>
                <div class="field field-full"><label for="phone">Phone number</label><input id="phone" name="phone" type="tel" inputmode="tel" autocomplete="tel" maxlength="30" placeholder="e.g. +27 68 443 8415" value="{{ old('phone') }}" required aria-describedby="phone-help @error('phone') phone-error @enderror" @error('phone') aria-invalid="true" @enderror><p id="phone-help" class="hint">Include your country code if you’re outside South Africa.</p>@error('phone')<p class="field-error" id="phone-error">{{ $message }}</p>@enderror</div>
                <div class="field field-full"><label for="password">Password</label><div class="password-field"><input id="password" name="password" type="password" autocomplete="new-password" minlength="8" maxlength="72" required aria-describedby="password-help @error('password') password-error @enderror" @error('password') aria-invalid="true" @enderror><button class="password-toggle" type="button" data-password-toggle="password" aria-label="Show password" aria-controls="password" aria-pressed="false">Show</button></div><p id="password-help" class="hint">Use at least 8 characters. A longer phrase is easier to remember.</p>@error('password')<p class="field-error" id="password-error">{{ $message }}</p>@enderror</div>
                <div class="field field-full"><label for="password_confirmation">Confirm password</label><div class="password-field"><input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" minlength="8" maxlength="72" required @error('password_confirmation') aria-invalid="true" aria-describedby="password_confirmation-error" @enderror><button class="password-toggle" type="button" data-password-toggle="password_confirmation" aria-label="Show confirmed password" aria-controls="password_confirmation" aria-pressed="false">Show</button></div>@error('password_confirmation')<p class="field-error" id="password_confirmation-error">{{ $message }}</p>@enderror</div>
            </div>
            <div class="consent"><input id="terms" name="terms" type="checkbox" value="1" @checked(old('terms')) required @error('terms') aria-invalid="true" aria-describedby="terms-error" @enderror><label for="terms">I confirm these details are correct and this is my email address.</label></div>
            @error('terms')<p class="field-error" id="terms-error">{{ $message }}</p>@enderror
            <button type="submit" class="primary-button" data-ready-text="Create account" data-busy-text="Creating your account…" data-closed="{{ !($admissionWindow['isOpen'] ?? true) ? 'true' : 'false' }}" @disabled(!($admissionWindow['isOpen'] ?? true))>Create account <span aria-hidden="true">→</span></button>
            <p class="form-bottom">Already registered? <a href="{{ route('login') }}">Sign in</a><br><a href="{{ route('password.request') }}">Forgot your password?</a></p>
        </form>
    </section>
    <aside class="journey-panel" aria-label="Your application journey">
        <h2>Your next chapter<br>starts here.</h2><p>One account to apply, track your admission, and access your student portal.</p>
        <ol class="journey-steps"><li><span class="step-number">1</span><div><strong>Create your account</strong><p>Enter your name, email, phone number and password.</p></div></li><li><span class="step-number">2</span><div><strong>Verify your email</strong><p>Open the link we send to confirm your email address.</p></div></li><li><span class="step-number">3</span><div><strong>Complete your application</strong><p>Choose your preferred programmes, upload documents and submit your payment receipt.</p></div></li></ol>
        <div class="journey-support">Need help getting started?<br><a href="mailto:admission@jbiuniversity.com">admission@jbiuniversity.com</a></div>
    </aside>
</div>
@endsection
