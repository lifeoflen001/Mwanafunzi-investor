@php
    $authMode = trim($__env->yieldContent('auth_mode')) ?: 'client';
    $isAdmin = $authMode === 'admin';
    $brandName = \App\Models\SiteSetting::getValue('brand_name', 'Mwanafunzi Investor');
    $brandCopy = $isAdmin
        ? 'Manage content, customers and publishing from one controlled workspace.'
        : 'Learn, participate and keep your Mwanafunzi Investor workspace close.';
    $brandKicker = trim($__env->yieldContent('auth_brand_kicker')) ?: ($isAdmin ? 'Mwanafunzi Investor · Admin desk' : 'Mwanafunzi Investor · Client portal');
    $brandTitle = trim($__env->yieldContent('auth_brand_title')) ?: ($isAdmin ? 'Run the platform with intention.' : 'A clearer place to do the work.');
    $brandFoot = trim($__env->yieldContent('auth_brand_foot')) ?: 'Student of Money. Systems. Discipline.';
    $backUrl = trim($__env->yieldContent('auth_back_url')) ?: route('home');
    $backLabel = trim($__env->yieldContent('auth_back_label')) ?: 'Back to website';
@endphp
<!DOCTYPE html>
<html lang="en-TZ">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#111211">
    <link rel="icon" href="{{ asset('favicon-96x96.png') }}?v=favicon4" type="image/png" sizes="96x96">
    <link rel="icon" href="{{ asset('favicon.svg') }}?v=favicon4" type="image/svg+xml" sizes="any">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}?v=favicon4" sizes="180x180">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}?v=favicon4">
    <title>@yield('title', $isAdmin ? 'Admin sign in' : 'Client sign in') — {{ $brandName }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @php($recaptcha = app(\App\Services\RecaptchaService::class))
    @if($recaptcha->isEnabled() && filled($recaptcha->siteKey()))
        <script>window.mwanafunziRecaptcha = { siteKey: @js($recaptcha->siteKey()) };</script>
        <script src="https://www.google.com/recaptcha/api.js?render={{ urlencode($recaptcha->siteKey()) }}" async defer></script>
    @endif
    @include('components.design-tokens')
</head>
<body class="auth-page auth-page-{{ $authMode }}">
    <main class="auth-shell {{ $isAdmin ? 'admin-auth-shell' : 'client-auth-layout' }}">
        <section class="auth-brand {{ $isAdmin ? 'admin-auth-brand' : 'client-auth-showcase' }}" aria-label="{{ $brandName }}">
            <div class="auth-brand-inner">
                <a class="auth-logo {{ $isAdmin ? 'admin-auth-logo' : '' }}" href="{{ route('home') }}" aria-label="{{ $brandName }} home">
                    <img class="auth-logo-image {{ $isAdmin ? 'admin-auth-logo-image' : '' }}" src="{{ asset('images/brand/mwanafunzi-logo-light.webp') }}" alt="{{ $brandName }}">
                    <span class="auth-logo-context">{{ $isAdmin ? 'ADMIN DESK' : 'CLIENT PORTAL' }}</span>
                </a>
                <div class="auth-brand-copy">
                    <p class="auth-eyebrow">{{ $brandKicker }}</p>
                    <h1>{{ $brandTitle }}</h1>
                    <p>{{ $brandCopy }}</p>
                </div>
            </div>
            <div class="auth-brand-foot"><span>{{ $brandFoot }}</span><span class="auth-brand-mark" aria-hidden="true">M</span></div>
        </section>
        <section class="auth-panel {{ $isAdmin ? 'admin-auth-panel' : 'client-auth-panel' }}">
            <div class="auth-panel-inner">
                <div class="auth-panel-top">
                    <span>{{ $isAdmin ? 'Secure access' : 'Your workspace' }}</span>
                    <a class="auth-back-link" href="{{ $backUrl }}">{{ $backLabel }} <span aria-hidden="true">↗</span></a>
                </div>
                @include('partials.form-feedback', ['show_validation_details' => trim($__env->yieldContent('auth_validation_details')) !== 'false'])
                <div class="auth-content">@yield('content')</div>
                <div class="auth-legal"><span>Protected by Mwanafunzi Investor security controls.</span><a href="{{ route('legal', 'privacy-policy') }}">Privacy</a><a href="{{ route('legal', 'terms') }}">Terms</a></div>
            </div>
        </section>
    </main>
</body>
</html>
