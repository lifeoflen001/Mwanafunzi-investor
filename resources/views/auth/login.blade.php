@extends('layouts.public')
@section('title', 'Client login — Mwanafunzi Investor')
@section('body_class', 'client-auth-page')
@section('content')
<section class="client-auth-shell">
    <div class="container client-auth-container">
        <div class="client-auth-layout">
            <div class="client-auth-panel">
                <div class="client-auth-intro">
                    <p class="eyebrow"><span class="eyebrow-line"></span>Client portal</p>
                    <h1>Welcome back.</h1>
                    <p>Sign in to continue exploring, join discussions, and access your personal workspace whenever you need it.</p>
                </div>

                <form class="client-auth-form" method="post" action="{{ route('login.store') }}" data-auth-form>
                @csrf
                @include('partials.form-feedback')
                @include('components.recaptcha', ['action' => 'login'])

                <label for="client-email">Email
                    <input id="client-email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" aria-describedby="client-email-error">
                    @error('email')<small class="client-field-error" id="client-email-error">{{ $message }}</small>@enderror
                </label>

                <label for="client-password">Password
                    <div class="client-password-field">
                        <input id="client-password" type="password" name="password" required autocomplete="current-password" aria-describedby="client-password-error">
                        <x-password-toggle class="client-password-toggle" />
                    </div>
                    @error('password')<small class="client-field-error" id="client-password-error">{{ $message }}</small>@enderror
                </label>

                <div class="client-auth-options">
                    <label class="client-remember"><input type="checkbox" name="remember" value="1"> <span>Remember me</span></label>
                    <a href="{{ route('password.request') }}">Forgot password?</a>
                </div>

                <button class="button button-dark client-auth-submit" type="submit" data-auth-submit><span data-auth-submit-label>Sign in</span> <span aria-hidden="true">↗</span></button>
                </form>

                <div class="client-auth-social client-login-social" aria-label="Alternative sign in">
                <div class="auth-divider"><span>or continue with</span></div>
                <x-google-sign-in :redirect="request('redirect')" />
                <p class="client-register-prompt">New here? <a href="{{ route('register', array_filter(['redirect' => request('redirect')])) }}">Create an account</a></p>
                </div>
            </div>

            @include('partials.client-auth-showcase')
        </div>
    </div>
</section>
@endsection
