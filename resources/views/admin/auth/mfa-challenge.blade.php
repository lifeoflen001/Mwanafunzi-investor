@extends('layouts.admin-auth')
@section('title', 'Verify administrator MFA')
@section('content')
<div class="admin-auth-form-wrap">
    <div class="admin-auth-title"><p class="admin-eyebrow">Secure access</p><h2>Verify your sign-in.</h2><p>Enter the six-digit code from your authenticator app, or use one unused recovery code.</p></div>
    @include('components.admin.flash')
    <form class="admin-form admin-auth-form" method="post" action="{{ route('admin.mfa.challenge.verify') }}">
        @csrf
        <label>Authenticator or recovery code<input inputmode="numeric" name="code" autocomplete="one-time-code" required autofocus></label>
        <button class="button button-primary button-wide" type="submit">Continue <span aria-hidden="true">↗</span></button>
    </form>
    <form method="post" action="{{ route('admin.logout') }}">@csrf<button class="admin-auth-back" type="submit">Sign out</button></form>
</div>
@endsection
