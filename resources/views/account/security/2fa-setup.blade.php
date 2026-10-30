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
            <div class="student-mfa-setup-card">
                <div>
                    <span class="student-eyebrow">Authenticator setup</span>
                    <p class="student-muted">Use any TOTP authenticator app, then confirm the current six-digit code.</p>
                </div>
                <button class="student-mfa-setup-trigger" type="button" data-mfa-setup-open="student-mfa-manual-setup">
                    Show setup details <span aria-hidden="true">↗</span>
                </button>
            </div>
            <dialog class="student-mfa-dialog" id="student-mfa-manual-setup" aria-labelledby="student-mfa-dialog-title">
                <div class="student-mfa-dialog-card">
                    <div class="student-mfa-dialog-header">
                        <div>
                            <span class="student-eyebrow">Manual setup key</span>
                            <h2 id="student-mfa-dialog-title">Connect your authenticator.</h2>
                        </div>
                        <button class="student-mfa-dialog-close" type="button" data-mfa-setup-close aria-label="Close setup details">×</button>
                    </div>
                    <p class="student-mfa-dialog-intro">Use the key below if your authenticator cannot scan a QR code. Keep this setup information private.</p>
                    <div class="student-mfa-dialog-field">
                        <span class="student-mfa-dialog-label">Manual setup key</span>
                        <code class="student-secret-key">{{ $secret }}</code>
                    </div>
                    <div class="student-mfa-dialog-field">
                        <span class="student-mfa-dialog-label">Provisioning URI</span>
                        <code class="student-mfa-uri">{{ $provisioningUri }}</code>
                    </div>
                    <div class="student-mfa-dialog-actions">
                        <button class="student-button student-button-dark" type="button" data-mfa-setup-close>Done</button>
                    </div>
                </div>
            </dialog>
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
