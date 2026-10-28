@extends('layouts.auth')
@section('auth_mode', 'client')
@section('auth_brand_kicker', 'Client portal · Account security')
@section('auth_brand_title', 'Return to your process.')
@section('auth_brand_foot', 'Student of Money. Probability. Systems. Discipline.')
@section('title', 'Reset your password')
@section('content')
<div class="auth-form-wrap"><div class="auth-form-heading"><p class="auth-form-kicker">Account security</p><h2>Reset your password.</h2><p>Enter your account email and we will send a secure, time-limited reset link.</p></div><form class="auth-form client-auth-form" method="post" action="{{ route('password.email') }}">@csrf @include('components.recaptcha', ['action' => 'password_reset'])<label class="auth-field" for="reset-email">Email<input id="reset-email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email"></label><button class="button auth-primary client-auth-submit" type="submit">Send reset link <span aria-hidden="true">↗</span></button></form><p class="auth-switch auth-switch-standalone"><a href="{{ route('login') }}">Return to sign in</a></p></div>
@endsection
