@extends('layouts.admin-auth')
@section('title', 'Set up administrator MFA')
@section('content')
<div class="admin-auth-form-wrap">
    <div class="admin-auth-title"><p class="admin-eyebrow">Required security step</p><h2>Protect the admin desk.</h2><p>Set up an authenticator app before continuing. Your secret is shown only during this setup.</p></div>
    <div class="admin-mfa-setup-card">
        <div>
            <span class="admin-eyebrow">Authenticator setup</span>
            <p>Connect your authenticator app using a QR-capable password manager or the manual setup key.</p>
        </div>
        <button class="admin-mfa-setup-trigger" type="button" data-mfa-setup-open="admin-mfa-manual-setup">
            Show setup details <span aria-hidden="true">↗</span>
        </button>
    </div>
    <dialog class="admin-mfa-dialog" id="admin-mfa-manual-setup" aria-labelledby="admin-mfa-dialog-title">
        <div class="admin-mfa-dialog-card">
            <div class="admin-mfa-dialog-header">
                <div>
                    <span class="admin-eyebrow">Manual setup key</span>
                    <h2 id="admin-mfa-dialog-title">Connect your authenticator.</h2>
                </div>
                <button class="admin-mfa-dialog-close" type="button" data-mfa-setup-close aria-label="Close setup details">×</button>
            </div>
            <p class="admin-mfa-dialog-intro">In your authenticator app, add an account for <strong>{{ auth()->user()->email }}</strong> using the key below. Keep these details private.</p>
            <div class="admin-mfa-dialog-field">
                <span class="admin-mfa-dialog-label">Manual setup key</span>
                <code class="admin-mfa-secret-value">{{ $secret }}</code>
            </div>
            <div class="admin-mfa-dialog-field">
                <span class="admin-mfa-dialog-label">Provisioning URI</span>
                <code class="admin-mfa-uri">{{ $provisioningUri }}</code>
            </div>
            <div class="admin-mfa-dialog-actions">
                <button class="button button-primary" type="button" data-mfa-setup-close>Done</button>
            </div>
        </div>
    </dialog>
    <form class="admin-form admin-auth-form" method="post" action="{{ route('admin.mfa.setup.store') }}">
        @csrf
        <label>Current password<input type="password" name="current_password" autocomplete="current-password" required></label>
        <label>Authenticator code<input inputmode="numeric" pattern="[0-9]{6}" maxlength="6" name="code" autocomplete="one-time-code" required></label>
        <button class="button button-primary button-wide" type="submit">Enable MFA <span aria-hidden="true">↗</span></button>
    </form>
    <a class="admin-auth-back" href="{{ route('home') }}">← Return to public site</a>
</div>
@endsection
