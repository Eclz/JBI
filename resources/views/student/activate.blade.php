@extends('layouts.admissions')
@section('title', 'Student portal — activate your account')
@section('brand-label', 'Student portal')
@section('content')
<div class="activation-welcome"><span class="eyebrow">JBI UNIVERSITY · STUDENT PORTAL</span><h1>Your journey starts here.</h1><p>One quick step to secure your account, then you’re ready to apply.</p></div>
<div class="activation-layout">
@include('auth.partials.activation')
<aside class="activation-next" aria-label="Your next steps">
    <h2>What happens next</h2>
    <ol class="activation-steps"><li class="complete"><span>✓</span><div><strong>Account created</strong><p>Your details are safely saved.</p></div></li><li class="current"><span>2</span><div><strong>Activate your account</strong><p>Open your email and select Activate my account.</p></div></li><li><span>3</span><div><strong>Complete your application</strong><p>Choose your programme and upload your documents.</p></div></li><li><span>4</span><div><strong>Track your admission</strong><p>Follow your progress from this portal.</p></div></li></ol>
    <div class="locked-services"><strong>Student services are locked</strong><p>Applications, payments, profile updates and course services become available after activation.</p></div>
    <p class="live-message" data-activation-feedback role="status" aria-live="polite">Waiting for email activation…</p>
</aside></div>
@endsection
