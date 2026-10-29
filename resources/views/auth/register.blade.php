@extends('layouts.auth')
@section('auth_mode', 'client')
@section('auth_brand_kicker', 'Mwanafunzi Investor · Client portal')
@section('auth_brand_title', 'Build a better process.')
@section('auth_brand_foot', 'Student of Money. Probability. Systems. Discipline.')
@section('auth_back_url', route('home'))
@section('auth_back_label', 'Back to website')
@section('title', 'Create a client account')
@section('content')
<div class="auth-form-wrap">
    <div class="auth-form-heading"><p class="auth-form-kicker">Join Mwanafunzi Investor</p><h2>Create your account.</h2><p>Keep your learning, discussions, purchases and workspace in one place.</p></div>
    <form class="auth-form client-auth-form client-register-form" method="post" action="{{ route('register.store') }}" data-auth-form>
        @csrf
        @if(request()->filled('redirect'))<input type="hidden" name="redirect" value="{{ request('redirect') }}">@endif
        @include('components.recaptcha', ['action' => 'register'])
        <label class="auth-field" for="client-name">Name<input id="client-name" type="text" name="name" value="{{ old('name') }}" required autocomplete="name"></label>
        <label class="auth-field" for="client-email">Email<input id="client-email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email"></label>
        <label class="auth-field" for="client-password">Password<div class="auth-password-field"><input id="client-password" type="password" name="password" required autocomplete="new-password"><x-password-toggle class="client-password-toggle" /></div></label>
        <label class="auth-field" for="client-password-confirmation">Confirm password<div class="auth-password-field"><input id="client-password-confirmation" type="password" name="password_confirmation" required autocomplete="new-password"><x-password-toggle class="client-password-toggle" /></div></label>
        <label class="auth-consent" for="client-terms"><input id="client-terms" type="checkbox" name="terms" value="1" required {{ old('terms') ? 'checked' : '' }}><span>I accept the <a href="{{ route('legal', 'terms') }}">Terms</a>, acknowledge the <a href="{{ route('legal', 'privacy-policy') }}">Privacy Policy</a> and understand the <a href="{{ route('legal', 'risk-disclosure') }}">risk disclosure</a>.</span></label>
        <button class="button auth-primary client-auth-submit" type="submit" data-auth-submit><span data-auth-submit-label>Create account</span> <span aria-hidden="true">↗</span></button>
    </form>
    <div class="auth-alternatives" aria-label="Alternative registration"><div class="auth-divider"><span>or</span></div><x-google-sign-in :redirect="request('redirect')" /><p class="auth-switch">Already have an account? <a href="{{ route('login', array_filter(['redirect' => request('redirect')])) }}">Sign in</a></p></div>
</div>
@endsection
