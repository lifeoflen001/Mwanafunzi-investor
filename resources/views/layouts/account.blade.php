@php
    $portalUser = auth()->user();
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Your Mwanafunzi Investor student portal.">
    <title>@yield('title', 'Student portal') — Mwanafunzi Investor</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('components.design-tokens')
</head>
<body class="portal-page account-portal">
    <div class="portal-shell">
        <aside class="portal-sidebar" aria-label="Student portal navigation">
            <a class="portal-brand" href="{{ route('account.dashboard') }}" aria-label="Mwanafunzi Investor student portal">
                <span class="brand-mark" aria-hidden="true"><span></span><span></span><span></span></span>
                <span><strong>MWANAFUNZI</strong><small>STUDENT PORTAL</small></span>
            </a>
            <p class="portal-section-label">Workspace</p>
            <nav class="portal-nav">
                <a class="{{ request()->routeIs('account.dashboard') ? 'is-active' : '' }}" href="{{ route('account.dashboard') }}"><span class="portal-nav-icon">⌂</span>Overview</a>
                <a class="{{ request()->routeIs('account.courses*') ? 'is-active' : '' }}" href="{{ route('account.courses') }}"><span class="portal-nav-icon">▤</span>My courses</a>
                <a class="{{ request()->routeIs('account.downloads') ? 'is-active' : '' }}" href="{{ route('account.downloads') }}"><span class="portal-nav-icon">↓</span>My tools</a>
                <a class="{{ request()->routeIs('account.orders*') ? 'is-active' : '' }}" href="{{ route('account.orders') }}"><span class="portal-nav-icon">▣</span>Orders</a>
            </nav>
            <p class="portal-section-label">Account</p>
            <nav class="portal-nav">
                <a class="{{ request()->routeIs('account.profile') ? 'is-active' : '' }}" href="{{ route('account.profile') }}"><span class="portal-nav-icon">○</span>Profile</a>
                <a class="{{ request()->routeIs('account.security') ? 'is-active' : '' }}" href="{{ route('account.security') }}"><span class="portal-nav-icon">◇</span>Security</a>
            </nav>
            <div class="portal-sidebar-footer">
                <span class="portal-status"><i></i> Account secure</span>
                <a href="{{ route('home') }}">← Back to site</a>
            </div>
        </aside>
        <div class="portal-main">
            <header class="portal-topbar">
                <div class="portal-topbar-title"><span class="portal-mobile-label">Student portal</span><strong>@yield('portal-heading', 'Overview')</strong></div>
                <div class="portal-search" aria-label="Portal search"><span>⌕</span><span>Search your portal</span></div>
                <div class="portal-topbar-actions"><span class="portal-topbar-dot" title="Notifications">◌</span><span class="portal-avatar">{{ strtoupper(substr($portalUser?->name ?: 'S', 0, 1)) }}</span><span class="portal-user-name">{{ $portalUser?->name }}</span></div>
            </header>
            <main class="portal-content">
                @include('partials.form-feedback')
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
