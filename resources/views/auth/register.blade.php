@extends('layouts.public')
@section('title', 'Create a client account — Mwanafunzi Investor')
@section('body_class', 'client-auth-page client-register-page')
@section('content')
<section class="client-auth-shell">
    <div class="container client-auth-container">
        <div class="client-auth-intro">
            <p class="eyebrow"><span class="eyebrow-line"></span>Client portal</p>
            <h1>Create your account.</h1>
            <p>One account for purchases, downloads and course access.</p>
        </div>

        <div class="client-auth-grid">
            <aside class="client-auth-context" aria-label="Your account">
                <p class="eyebrow">Your account</p>
                <p>Keep your learning, purchases and useful resources together in one considered space.</p>
                <ul>
                    <li>Save your course progress</li>
                    <li>Access purchased tools</li>
                    <li>View orders and receipts</li>
                    <li>Manage your profile</li>
                </ul>
            </aside>

            <form class="client-auth-form client-register-form" method="post" action="{{ route('register.store') }}" data-auth-form>
                @csrf
                @include('partials.form-feedback')
                @include('components.recaptcha', ['action' => 'register'])

                <label class="client-register-field-wide" for="client-name">Name
                    <input id="client-name" type="text" name="name" value="{{ old('name') }}" required autocomplete="name" aria-describedby="client-name-error">
                    @error('name')<small class="client-field-error" id="client-name-error">{{ $message }}</small>@enderror
                </label>

                <label class="client-register-field-wide" for="client-email">Email
                    <input id="client-email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" aria-describedby="client-email-error">
                    @error('email')<small class="client-field-error" id="client-email-error">{{ $message }}</small>@enderror
                </label>

                <label for="client-password">Password
                    <div class="client-password-field">
                        <input id="client-password" type="password" name="password" required autocomplete="new-password" aria-describedby="client-password-error">
                        <x-password-toggle class="client-password-toggle" />
                    </div>
                    @error('password')<small class="client-field-error" id="client-password-error">{{ $message }}</small>@enderror
                </label>

                <label for="client-password-confirmation">Confirm password
                    <div class="client-password-field">
                        <input id="client-password-confirmation" type="password" name="password_confirmation" required autocomplete="new-password" aria-describedby="client-password-confirmation-error">
                        <x-password-toggle class="client-password-toggle" />
                    </div>
                    @error('password_confirmation')<small class="client-field-error" id="client-password-confirmation-error">{{ $message }}</small>@enderror
                </label>

                <label for="client-phone">Phone <span>(optional)</span>
                    <input id="client-phone" type="tel" name="phone" value="{{ old('phone') }}" autocomplete="tel" aria-describedby="client-phone-error">
                    @error('phone')<small class="client-field-error" id="client-phone-error">{{ $message }}</small>@enderror
                </label>

                <label for="client-country">Country code <span>(optional)</span>
                    <input id="client-country" type="text" name="country" value="{{ old('country') }}" maxlength="2" placeholder="TZ" autocomplete="country" aria-describedby="client-country-error">
                    @error('country')<small class="client-field-error" id="client-country-error">{{ $message }}</small>@enderror
                </label>

                <label class="consent client-register-consent" for="client-terms">
                    <input id="client-terms" type="checkbox" name="terms" value="1" required {{ old('terms') ? 'checked' : '' }}>
                    <span>I accept the <a href="{{ route('legal', 'terms') }}">Terms</a> and understand the <a href="{{ route('legal', 'risk-disclosure') }}">risk disclosure</a>.</span>
                </label>
                @error('terms')<small class="client-field-error client-register-terms-error">{{ $message }}</small>@enderror

                <button class="button button-dark client-auth-submit client-register-submit" type="submit" data-auth-submit><span data-auth-submit-label>Create account</span> <span aria-hidden="true">↗</span></button>
                <p class="client-register-prompt">Already have an account? <a href="{{ route('login') }}">Sign in</a></p>
            </form>
        </div>
    </div>
</section>
@endsection
