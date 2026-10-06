@extends('layouts.public')
@section('title', 'Client login — Mwanafunzi Investor')
@section('body_class', 'client-auth-page')
@section('content')
<section class="client-auth-shell">
    <div class="container client-auth-container">
        <div class="client-auth-intro">
            <p class="eyebrow"><span class="eyebrow-line"></span>Client portal</p>
            <h1>Welcome back.</h1>
            <p>Sign in to view your orders, downloads and course access.</p>
        </div>

        <div class="client-auth-grid">
            <aside class="client-auth-context" aria-label="Your account">
                <p class="eyebrow">Your account</p>
                <p>One place for the resources you have purchased and the learning you are continuing.</p>
                <ul>
                    <li>Access purchased courses</li>
                    <li>Download your tools</li>
                    <li>View orders and receipts</li>
                    <li>Manage your profile</li>
                </ul>
            </aside>

            <form class="client-auth-form" method="post" action="{{ route('login.store') }}" data-auth-form>
                @csrf
                @include('partials.form-feedback')

                <label for="client-email">Email
                    <input id="client-email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" aria-describedby="client-email-error">
                    @error('email')<small class="client-field-error" id="client-email-error">{{ $message }}</small>@enderror
                </label>

                <label for="client-password">Password
                    <div class="client-password-field">
                        <input id="client-password" type="password" name="password" required autocomplete="current-password" aria-describedby="client-password-error">
                        <button class="client-password-toggle" type="button" data-password-toggle aria-label="Show password">Show</button>
                    </div>
                    @error('password')<small class="client-field-error" id="client-password-error">{{ $message }}</small>@enderror
                </label>

                <div class="client-auth-options">
                    <label class="client-remember"><input type="checkbox" name="remember" value="1"> <span>Remember me</span></label>
                </div>

                <button class="button button-dark client-auth-submit" type="submit" data-auth-submit><span data-auth-submit-label>Sign in</span> <span aria-hidden="true">↗</span></button>
                <p class="client-register-prompt">New here? <a href="{{ route('register') }}">Create an account</a></p>
            </form>
        </div>
    </div>
</section>
@endsection
