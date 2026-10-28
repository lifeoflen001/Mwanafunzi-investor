@extends('layouts.admin-auth')
@section('title', 'Choose a new admin password')
@section('content')
<div class="admin-auth-form-wrap">
    <div class="admin-auth-title"><h2>Choose a new password.</h2><p>Use at least ten characters and do not reuse a password from another service.</p></div>
    <form class="admin-form admin-auth-form" method="post" action="{{ route('admin.password.update') }}">
        @csrf
        @include('components.recaptcha', ['action' => 'password_reset'])
        <input type="hidden" name="token" value="{{ $token }}">
        <label for="admin-reset-email">Email address<input id="admin-reset-email" type="email" name="email" required autofocus value="{{ old('email', request('email')) }}" autocomplete="username"></label>
        <label for="admin-reset-password">New password<div class="admin-password-field"><input id="admin-reset-password" type="password" name="password" required autocomplete="new-password"><x-password-toggle class="admin-password-toggle" /></div></label>
        <label for="admin-reset-password-confirmation">Confirm password<div class="admin-password-field"><input id="admin-reset-password-confirmation" type="password" name="password_confirmation" required autocomplete="new-password"><x-password-toggle class="admin-password-toggle" /></div></label>
        <button class="button button-primary button-wide" type="submit">Update password <span aria-hidden="true">↗</span></button>
    </form>
    <a class="admin-auth-back" href="{{ route('admin.login') }}">← Return to admin sign in</a>
</div>
@endsection
