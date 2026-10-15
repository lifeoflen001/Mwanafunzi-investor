@extends('layouts.admin-auth')
@section('title', 'Reset admin password')
@section('content')
<div class="admin-auth-form-wrap">
    <div class="admin-auth-title"><h2>Reset administrator access.</h2><p>Enter your authorised admin email and we will send a secure, time-limited reset link.</p></div>
    <form class="admin-form admin-auth-form" method="post" action="{{ route('admin.password.email') }}">
        @csrf
        @include('partials.form-feedback')
        @include('components.recaptcha', ['action' => 'password_reset'])
        <label for="admin-reset-email">Email address<input id="admin-reset-email" type="email" name="email" required autofocus value="{{ old('email') }}" autocomplete="username"></label>
        <button class="button button-primary button-wide" type="submit">Send reset link <span aria-hidden="true">↗</span></button>
    </form>
    <a class="admin-auth-back" href="{{ route('admin.login') }}">← Return to admin sign in</a>
</div>
@endsection
