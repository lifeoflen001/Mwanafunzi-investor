<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#101c24">
    <link rel="icon" href="{{ asset('favicon-96x96.png') }}?v=favicon4" type="image/png" sizes="96x96">
    <link rel="icon" href="{{ asset('favicon.svg') }}?v=favicon4" type="image/svg+xml" sizes="any">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}?v=favicon4" sizes="180x180">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}?v=favicon4">
    <title>@yield('title', 'Admin sign in') — Mwanafunzi Investor</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @php($recaptcha = app(\App\Services\RecaptchaService::class))
    @if($recaptcha->isEnabled() && filled($recaptcha->siteKey()))
        <script>window.mwanafunziRecaptcha = { siteKey: @js($recaptcha->siteKey()) };</script>
        <script src="https://www.google.com/recaptcha/api.js?render={{ urlencode($recaptcha->siteKey()) }}" async defer></script>
    @endif
    @include('components.design-tokens')
</head>
<body class="admin-auth-page">
    <main class="admin-auth-shell">
        <section class="admin-auth-brand" aria-label="Mwanafunzi Investor">
            <a class="admin-auth-logo" href="{{ route('home') }}" aria-label="Mwanafunzi Investor home">
                <img class="admin-auth-logo-image" src="{{ asset('images/brand/mwanafunzi-logo-light.webp') }}" alt="Mwanafunzi Investor">
                <span><small>INVESTOR · ADMIN DESK</small></span>
            </a>
            <div class="admin-auth-brand-copy"><p class="admin-eyebrow">Operational workspace</p><h1>Run the platform<br><em>with intention.</em></h1><p>Manage content, customers and publishing from one controlled workspace.</p></div>
            <p class="admin-auth-brand-foot">Student of Money. Systems. Discipline.</p>
        </section>
        <section class="admin-auth-panel">
            <div class="admin-auth-panel-head"><p class="admin-eyebrow">Mwanafunzi Investor</p><span class="admin-status-dot"><i></i> Secure access</span></div>
            @if($errors->any())<div class="admin-alert admin-alert-danger" role="alert"><strong>Sign in unsuccessful</strong><span>{{ $errors->first() }}</span></div>@endif
            @yield('content')
        </section>
    </main>
</body>
</html>
