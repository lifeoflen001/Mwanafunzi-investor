@extends('layouts.auth')
@section('auth_mode', 'client')
@section('auth_brand_kicker', 'Mwanafunzi Investor · Client portal')
@section('auth_brand_title', 'A clearer place to do the work.')
@section('auth_brand_foot', 'Student of Money. Probability. Systems. Discipline.')
@section('auth_back_url', route('home'))
@section('auth_back_label', 'Back to website')
@section('title', 'Client login')
@section('content')
<div class="auth-form-wrap">
    <div class="auth-form-heading"><p class="auth-form-kicker">Client portal</p><h2>Welcome back.</h2><p>Sign in to continue learning, joining discussions and using your personal workspace.</p></div>
    <form class="auth-form client-auth-form" method="post" action="{{ route('login.store') }}" data-auth-form>
        @csrf
        @if(request()->filled('redirect'))<input type="hidden" name="redirect" value="{{ request('redirect') }}">@endif
        @include('components.recaptcha', ['action' => 'login'])
        <label class="auth-field" for="client-email">Email<input id="client-email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" aria-describedby="client-email-error">@error('email')<small class="auth-field-error" id="client-email-error">{{ $message }}</small>@enderror</label>
        <label class="auth-field" for="client-password">Password<div class="auth-password-field"><input id="client-password" type="password" name="password" required autocomplete="current-password" aria-describedby="client-password-error"><x-password-toggle class="client-password-toggle" /></div>@error('password')<small class="auth-field-error" id="client-password-error">{{ $message }}</small>@enderror</label>
        <div class="auth-options"><label><input type="checkbox" name="remember" value="1"> <span>Remember me</span></label><a href="{{ route('password.request') }}">Forgot password?</a></div>
        <button class="button auth-primary client-auth-submit" type="submit" data-auth-submit><span data-auth-submit-label>Sign in</span> <span aria-hidden="true">↗</span></button>
    </form>
    <div class="auth-alternatives" aria-label="Alternative sign in"><div class="auth-divider"><span>or</span></div><x-google-sign-in :redirect="request('redirect')" /><p class="auth-switch">New here? <a href="{{ route('register', array_filter(['redirect' => request('redirect')])) }}">Create an account</a></p></div>
</div>
@endsection
