@extends('emails.layouts.master')
@section('title', 'Activate your account — JBI University')
@section('preheader', 'Your JBI student portal is ready. Activate your account to begin your application.')
@section('header_badge')
<div style="margin-top:16px;color:#e4c275;font-size:12px;letter-spacing:1.5px;text-transform:uppercase;">Welcome to your next chapter</div>
@endsection
@section('content')
<p style="color:#526276;font-size:14px;margin:0 0 10px;">Hello {{ $user->first_name }},</p>
<h1 style="font-size:28px;line-height:1.25;color:#001d48;margin:0 0 20px;">Your future starts here.</h1>
<p style="font-size:16px;line-height:1.7;color:#43546a;">Welcome to <strong>JBI University</strong>. Your student portal account has been created. Activate it to choose your programme and start your admission application.</p>
<table role="presentation" cellpadding="0" cellspacing="0" style="margin:28px 0;width:100%;"><tr><td align="center"><a href="{{ $verificationUrl }}" style="background:#001d48;border:1px solid #001d48;border-radius:8px;color:#ffffff;display:inline-block;font-family:Arial,sans-serif;font-size:16px;font-weight:bold;line-height:24px;padding:16px 28px;text-align:center;text-decoration:none;">Activate my account &rarr;</a></td></tr></table>
<div style="padding:18px 20px;background:#f5f2e9;border-left:3px solid #c08a18;color:#564727;font-size:14px;line-height:1.7;">This link is valid for <strong>24 hours</strong>. Keep your student portal open — it will continue automatically after activation.</div>
<h2 style="font-size:17px;color:#001d48;margin:26px 0 12px;">Your next steps</h2>
<p style="font-size:14px;line-height:1.9;color:#43546a;margin:0;">1. Activate your account<br>2. Complete your admission application<br>3. Track your progress in your student portal</p>
<p style="font-size:13px;color:#64748b;line-height:1.7;margin:26px 0 8px;">If the button does not open, copy this link into your browser:</p>
<p style="margin:0;font-size:12px;line-height:1.7;word-break:break-all;overflow-wrap:anywhere;"><a href="{{ $verificationUrl }}" style="color:#001d48;word-break:break-all;">{{ $verificationUrl }}</a></p>
<p style="font-size:13px;line-height:1.7;color:#64748b;margin-top:24px;">Didn’t create this account? You can safely ignore this email. Need help? Contact <a href="mailto:admission@jbiuniversity.com">admission@jbiuniversity.com</a>.</p>
@endsection
@section('signoff')
<div style="border-top:1px solid #e2e8f0;padding-top:22px;margin-top:28px;color:#526276;font-size:14px;line-height:1.7;">We look forward to welcoming you.<br><strong style="color:#001d48;">JBI University Admissions</strong></div>
@endsection
