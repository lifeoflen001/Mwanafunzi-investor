@extends('layouts.account')
@section('title', 'Set up two-factor authentication — Mwanafunzi Investor')
@section('portal-heading', 'Two-factor authentication')
@section('content')
<section class="student-page-heading">
    <div><p class="student-eyebrow">Security / Authenticator</p><h1>Protect your account.</h1><p>Scan this setup key with an authenticator app, then confirm the current six-digit code.</p></div>
</section>
<div class="student-security-layout">
    <div class="student-security-main">
        <x-portal-card class="student-panel">
            <p class="student-eyebrow">Step 1</p>
            <h2>Connect your authenticator.</h2>
            <p class="student-muted">Use any TOTP authenticator app. If you cannot scan a QR code, enter this key manually:</p>
            <code class="student-secret-key">{{ $secret }}</code>
            <p class="student-muted student-breakable">{{ $provisioningUri }}</p>
            <form class="student-form" method="post" action="{{ route('account.security.2fa.setup.store') }}">
                @csrf
                @if(auth()->user()->password !== null)
                    <label>Current password<input type="password" name="current_password" autocomplete="current-password" required></label>
                @endif
                <label>Authenticator code<input type="text" inputmode="numeric" pattern="[0-9]{6}" name="code" maxlength="6" autocomplete="one-time-code" required></label>
                <div class="student-form-actions"><button class="student-button student-button-dark" type="submit">Enable two-factor authentication <span aria-hidden="true">↗</span></button></div>
            </form>
        </x-portal-card>
    </div>
    <aside class="student-security-aside"><x-portal-card class="student-panel student-panel-soft"><p class="student-eyebrow">Why enable it?</p><h2>A second proof of access.</h2><p>Even if your password is exposed, the authenticator code or a recovery code is still required for your client workspace.</p></x-portal-card></aside>
</div>
@endsection
