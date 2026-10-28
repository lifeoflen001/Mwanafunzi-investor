@extends('layouts.auth')
@section('auth_mode', 'client')
@section('auth_brand_kicker', 'Client portal · Account security')
@section('auth_brand_title', 'Return to your process.')
@section('auth_brand_foot', 'Student of Money. Probability. Systems. Discipline.')
@section('title', 'Choose a new password')
@section('content')
<div class="auth-form-wrap"><div class="auth-form-heading"><p class="auth-form-kicker">Account security</p><h2>Choose a new password.</h2><p>Use at least ten characters and do not reuse a password from another service.</p></div><form class="auth-form client-auth-form" method="post" action="{{ route('password.update') }}">@csrf @include('components.recaptcha', ['action' => 'password_reset'])<input type="hidden" name="token" value="{{ $token }}"><label class="auth-field" for="reset-email">Email<input id="reset-email" type="email" name="email" value="{{ old('email', request('email')) }}" required autocomplete="email"></label><label class="auth-field" for="reset-password">Password<div class="auth-password-field"><input id="reset-password" type="password" name="password" required autocomplete="new-password"><x-password-toggle class="client-password-toggle" /></div></label><label class="auth-field" for="reset-password-confirmation">Confirm password<div class="auth-password-field"><input id="reset-password-confirmation" type="password" name="password_confirmation" required autocomplete="new-password"><x-password-toggle class="client-password-toggle" /></div></label><button class="button auth-primary client-auth-submit" type="submit">Update password <span aria-hidden="true">↗</span></button></form></div>
@endsection
