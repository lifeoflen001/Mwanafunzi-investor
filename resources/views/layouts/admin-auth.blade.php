<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#202521">
    <title>@yield('title', 'Admin sign in') — Mwanafunzi Investor</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('components.design-tokens')
</head>
<body class="admin-auth-page">
    <main class="admin-auth-shell">
        <section class="admin-auth-brand" aria-label="Mwanafunzi Investor">
            <a class="admin-auth-logo" href="{{ route('home') }}" aria-label="Mwanafunzi Investor home">
                <span class="brand-mark" aria-hidden="true"><span></span><span></span><span></span></span>
                <span><strong>MWANAFUNZI</strong><small>INVESTOR · ADMIN DESK</small></span>
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
