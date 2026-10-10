@extends('layouts.admin-auth')
@section('title', 'Set up administrator MFA')
@section('content')
<div class="admin-auth-form-wrap">
    <div class="admin-auth-title"><p class="admin-eyebrow">Required security step</p><h2>Protect the admin desk.</h2><p>Set up an authenticator app before continuing. Your secret is shown only during this setup.</p></div>
    @include('components.admin.flash')
    <div class="admin-mfa-secret"><span class="admin-eyebrow">Manual setup key</span><code>{{ $secret }}</code><p>In your authenticator app, add an account for {{ auth()->user()->email }} using this key. The provisioning URI is also available for QR-capable password managers:</p><code class="admin-mfa-uri">{{ $provisioningUri }}</code></div>
    <form class="admin-form admin-auth-form" method="post" action="{{ route('admin.mfa.setup.store') }}">
        @csrf
        <label>Current password<input type="password" name="current_password" autocomplete="current-password" required></label>
        <label>Authenticator code<input inputmode="numeric" pattern="[0-9]{6}" maxlength="6" name="code" autocomplete="one-time-code" required></label>
        <button class="button button-primary button-wide" type="submit">Enable MFA <span aria-hidden="true">↗</span></button>
    </form>
    <a class="admin-auth-back" href="{{ route('home') }}">← Return to public site</a>
</div>
@endsection
