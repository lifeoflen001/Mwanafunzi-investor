@php
    $portalUser = auth()->user();
    $verified = $portalUser?->hasVerifiedEmail();
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Your Mwanafunzi Investor learning and customer workspace.">
    <meta name="theme-color" content="#101c24">
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <title>@yield('title', 'Student portal') — Mwanafunzi Investor</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('components.design-tokens')
</head>
<body class="portal-page account-portal">
    <a class="skip-link" href="#account-main-content">Skip to content</a>
    <div class="portal-shell account-shell" data-admin-shell>
        <aside class="portal-sidebar account-sidebar" aria-label="Student portal navigation" id="account-sidebar" data-admin-sidebar>
            <div class="account-sidebar-head">
                <a class="account-brand" href="{{ route('account.dashboard') }}" aria-label="Mwanafunzi Investor student portal">
                    <img class="account-brand-logo" src="{{ asset('images/brand/mwanafunzi-logo-light.png') }}" alt="Mwanafunzi Investor">
                    <img class="account-brand-mark" src="{{ asset('favicon.svg') }}" alt="">
                    <span>STUDENT PORTAL</span>
                </a>
                <button class="admin-sidebar-close" type="button" aria-label="Close student navigation" data-admin-sidebar-close>×</button>
            </div>
            <div class="admin-sidebar-scroll account-sidebar-scroll">
                <p class="portal-section-label">Workspace</p>
                <nav class="portal-nav" aria-label="Workspace navigation">
                    <a class="{{ request()->routeIs('account.dashboard') ? 'is-active' : '' }}" href="{{ route('account.dashboard') }}"><span class="portal-nav-icon" aria-hidden="true">⌂</span><span>Overview</span></a>
                    <a class="{{ request()->routeIs('account.courses*') ? 'is-active' : '' }}" href="{{ route('account.courses') }}"><span class="portal-nav-icon" aria-hidden="true">▤</span><span>My courses</span></a>
                    <a class="{{ request()->routeIs('account.downloads') ? 'is-active' : '' }}" href="{{ route('account.downloads') }}"><span class="portal-nav-icon" aria-hidden="true">↓</span><span>My tools</span></a>
                    <a class="{{ request()->routeIs('account.orders*') ? 'is-active' : '' }}" href="{{ route('account.orders') }}"><span class="portal-nav-icon" aria-hidden="true">▣</span><span>Orders &amp; receipts</span></a>
                </nav>
                <p class="portal-section-label">Account</p>
                <nav class="portal-nav" aria-label="Account navigation">
                    <a class="{{ request()->routeIs('account.profile') ? 'is-active' : '' }}" href="{{ route('account.profile') }}"><span class="portal-nav-icon" aria-hidden="true">○</span><span>Profile</span></a>
                    <a class="{{ request()->routeIs('account.security') ? 'is-active' : '' }}" href="{{ route('account.security') }}"><span class="portal-nav-icon" aria-hidden="true">◇</span><span>Security</span></a>
                </nav>
                <p class="portal-section-label">Help</p>
                <nav class="portal-nav" aria-label="Help navigation"><a href="{{ route('contact') }}"><span class="portal-nav-icon" aria-hidden="true">↗</span><span>Contact the desk</span></a></nav>
            </div>
            <div class="portal-sidebar-footer">
                <span class="portal-status {{ $verified ? '' : 'portal-status-accent' }}"><i></i>{{ $verified ? 'Verified account' : 'Email verification required' }}</span>
                <a href="{{ route('home') }}" target="_blank" rel="noopener">Visit website <span aria-hidden="true">↗</span></a>
                <a href="{{ route('contact') }}">Support <span aria-hidden="true">↗</span></a>
                <form method="post" action="{{ route('logout') }}">@csrf<button class="portal-signout" type="submit">Sign out</button></form>
            </div>
        </aside>
        <div class="admin-sidebar-backdrop" data-admin-sidebar-close></div>
        <div class="portal-main">
            <header class="portal-topbar account-topbar">
                <button class="admin-sidebar-toggle" type="button" aria-label="Toggle student navigation" aria-controls="account-sidebar" aria-expanded="true" data-admin-sidebar-toggle><span aria-hidden="true">☰</span></button>
                <div class="portal-topbar-title"><span class="portal-mobile-label">Student portal</span><strong>@yield('portal-heading', 'Overview')</strong></div>
                <div class="account-topbar-context">A calm workspace for learning and account management</div>
                <div class="portal-topbar-actions">
                    <a class="account-topbar-link" href="{{ route('home') }}" target="_blank" rel="noopener">Visit website <span aria-hidden="true">↗</span></a>
                    <div class="portal-profile" data-admin-profile>
                        <button class="portal-profile-toggle" type="button" aria-expanded="false" aria-controls="account-profile-menu" data-admin-profile-toggle><x-portal-avatar :user="$portalUser" size="small" /><span class="portal-user-name">{{ $portalUser?->name }}</span><span class="portal-profile-caret" aria-hidden="true">⌄</span></button>
                        <div class="portal-profile-menu" id="account-profile-menu" hidden data-admin-profile-menu>
                            <div class="account-profile-menu-head"><x-portal-avatar :user="$portalUser" size="small" /><span><strong>{{ $portalUser?->name }}</strong><small>{{ $portalUser?->email }}</small></span></div>
                            <div class="account-profile-menu-group"><a href="{{ route('account.profile') }}"><span aria-hidden="true">○</span>My profile</a><a href="{{ route('account.security') }}"><span aria-hidden="true">◇</span>Security</a><a href="{{ route('account.orders') }}"><span aria-hidden="true">▣</span>Orders &amp; receipts</a></div>
                            <div class="account-profile-menu-group"><a href="{{ route('contact') }}"><span aria-hidden="true">↗</span>Contact support</a><a href="{{ route('home') }}" target="_blank" rel="noopener"><span aria-hidden="true">↗</span>Visit public site</a></div>
                            <form class="account-profile-menu-group" method="post" action="{{ route('logout') }}">@csrf<button type="submit" class="account-profile-signout"><span aria-hidden="true">↪</span>Sign out</button></form>
                        </div>
                    </div>
                </div>
            </header>
            <main class="portal-content account-content" id="account-main-content">
                @include('partials.form-feedback')
                @yield('content')
                <footer class="account-footer"><span>© {{ date('Y') }} Mwanafunzi Investor</span><span>Educational content only. Trading involves risk.</span><a href="{{ route('contact') }}">Need help? Contact the desk ↗</a></footer>
            </main>
        </div>
    </div>
</body>
</html>
