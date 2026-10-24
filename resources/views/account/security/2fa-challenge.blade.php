@extends('layouts.account')
@section('title', 'Verify your sign-in — Mwanafunzi Investor')
@section('portal-heading', 'Verify sign-in')
@section('content')
<section class="student-page-heading"><div><p class="student-eyebrow">Security / Verification</p><h1>One more step.</h1><p>Enter the code from your authenticator app, or use one of your unused recovery codes.</p></div></section>
<div class="student-security-layout"><div class="student-security-main"><x-portal-card class="student-panel"><form class="student-form" method="post" action="{{ route('account.security.challenge.verify') }}">@csrf<label>Authenticator or recovery code<input type="text" name="code" autocomplete="one-time-code" required autofocus></label><div class="student-form-actions"><button class="student-button student-button-dark" type="submit">Continue <span aria-hidden="true">↗</span></button></div></form></x-portal-card></div><aside class="student-security-aside"><x-portal-card class="student-panel student-panel-soft"><p class="student-eyebrow">Having trouble?</p><h2>Use a recovery code.</h2><p>Each recovery code can be used once. If you have lost all of them, contact the support desk after confirming your identity.</p><a class="student-text-link" href="{{ route('contact') }}">Contact support <span aria-hidden="true">→</span></a></x-portal-card></aside></div>
@endsection
