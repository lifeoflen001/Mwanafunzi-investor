<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#202a27">
    <title>@yield('title', 'Admin') — Mwanafunzi Investor</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('components.design-tokens')
</head>
<body class="portal-page admin-page admin-portal">
    <a class="skip-link" href="#admin-main-content">Skip to content</a>
    <div class="portal-shell admin-shell" data-admin-shell>
        <aside class="portal-sidebar admin-sidebar" aria-label="Admin navigation" id="admin-sidebar" data-admin-sidebar>
            <div class="admin-sidebar-head">
                <a class="portal-brand" href="{{ route('admin.dashboard') }}" aria-label="Mwanafunzi Investor admin portal">
                    <span class="brand-mark" aria-hidden="true"><span></span><span></span><span></span></span>
                    <span><strong>MWANAFUNZI</strong><small>ADMIN DESK</small></span>
                </a>
                <button class="admin-sidebar-close" type="button" aria-label="Close admin navigation" data-admin-sidebar-close>×</button>
            </div>
            <div class="admin-sidebar-scroll">
                <p class="portal-section-label">Overview</p>
                <nav class="portal-nav" aria-label="Overview navigation">
                    <a class="{{ request()->routeIs('admin.dashboard') ? 'is-active' : '' }}" href="{{ route('admin.dashboard') }}"><span class="portal-nav-icon" aria-hidden="true">⌂</span>Dashboard</a>
                </nav>
                <p class="portal-section-label">Content</p>
                <nav class="portal-nav" aria-label="Content navigation">
                    <a class="{{ request()->routeIs('admin.courses*') ? 'is-active' : '' }}" href="{{ route('admin.courses') }}"><span class="portal-nav-icon" aria-hidden="true">▤</span>Courses</a>
                    <a class="{{ request()->routeIs('admin.topics*') ? 'is-active' : '' }}" href="{{ route('admin.topics') }}"><span class="portal-nav-icon" aria-hidden="true">◌</span>Learning topics</a>
                    <a class="{{ request()->routeIs('admin.articles*') ? 'is-active' : '' }}" href="{{ route('admin.articles') }}"><span class="portal-nav-icon" aria-hidden="true">▥</span>Journal</a>
                    <a class="{{ request()->routeIs('admin.categories*') ? 'is-active' : '' }}" href="{{ route('admin.categories') }}"><span class="portal-nav-icon" aria-hidden="true">#</span>Categories</a>
                    <a class="{{ request()->routeIs('admin.tags*') ? 'is-active' : '' }}" href="{{ route('admin.tags') }}"><span class="portal-nav-icon" aria-hidden="true">⌘</span>Tags</a>
                    <a class="{{ request()->routeIs('admin.media*') ? 'is-active' : '' }}" href="{{ route('admin.media') }}"><span class="portal-nav-icon" aria-hidden="true">▧</span>Media library</a>
                </nav>
                <p class="portal-section-label">Commerce</p>
                <nav class="portal-nav" aria-label="Commerce navigation">
                    <a class="{{ request()->routeIs('admin.products*') ? 'is-active' : '' }}" href="{{ route('admin.products') }}"><span class="portal-nav-icon" aria-hidden="true">◈</span>Products</a>
                    <a class="{{ request()->routeIs('admin.commerce*') ? 'is-active' : '' }}" href="{{ route('admin.commerce.dashboard') }}"><span class="portal-nav-icon" aria-hidden="true">$</span>Commerce</a>
                </nav>
                <p class="portal-section-label">Platform</p>
                <nav class="portal-nav" aria-label="Platform navigation">
                    <a class="{{ request()->routeIs('admin.pages*') ? 'is-active' : '' }}" href="{{ route('admin.pages') }}"><span class="portal-nav-icon" aria-hidden="true">▣</span>Pages</a>
                    <a class="{{ request()->routeIs('admin.navigation*') ? 'is-active' : '' }}" href="{{ route('admin.navigation') }}"><span class="portal-nav-icon" aria-hidden="true">≡</span>Navigation</a>
                    <a class="{{ request()->routeIs('admin.messages*') ? 'is-active' : '' }}" href="{{ route('admin.messages') }}"><span class="portal-nav-icon" aria-hidden="true">✉</span>Enquiries</a>
                    <a class="{{ request()->routeIs('admin.settings*', 'admin.social-links*') ? 'is-active' : '' }}" href="{{ route('admin.settings') }}"><span class="portal-nav-icon" aria-hidden="true">⚙</span>Settings</a>
                    <a class="{{ request()->routeIs('admin.social-links*') ? 'is-active' : '' }}" href="{{ route('admin.social-links') }}"><span class="portal-nav-icon" aria-hidden="true">↗</span>Social links</a>
                    <a class="{{ request()->routeIs('admin.audit*') ? 'is-active' : '' }}" href="{{ route('admin.audit') }}"><span class="portal-nav-icon" aria-hidden="true">◷</span>Activity log</a>
                </nav>
            </div>
            <div class="portal-sidebar-footer">
                <span class="portal-status portal-status-accent"><i></i> System online</span>
                @auth
                    <form method="post" action="{{ route('admin.logout') }}">@csrf<button class="portal-signout" type="submit">Sign out</button></form>
                @else
                    <a href="{{ route('admin.login') }}">Admin sign in</a>
                @endauth
            </div>
        </aside>
        <div class="admin-sidebar-backdrop" data-admin-sidebar-close></div>
        <div class="portal-main">
            <header class="portal-topbar admin-topbar">
                <button class="admin-sidebar-toggle" type="button" aria-label="Open admin navigation" aria-controls="admin-sidebar" aria-expanded="false" data-admin-sidebar-toggle>☰</button>
                <div class="portal-topbar-title"><span class="portal-mobile-label">Admin desk</span><strong>@yield('portal-heading', 'Dashboard')</strong></div>
                <div class="admin-breadcrumbs" aria-label="Breadcrumb"><a href="{{ route('admin.dashboard') }}">Dashboard</a><span aria-hidden="true">/</span><span>@yield('portal-heading', 'Overview')</span></div>
                <form class="portal-search" method="get" action="{{ route('admin.search') }}"><label class="sr-only" for="admin-search">Search the desk</label><span aria-hidden="true">⌕</span><input id="admin-search" name="q" value="{{ request('q') }}" placeholder="Search the desk"></form>
                <div class="portal-topbar-actions"><button class="admin-topbar-action" type="button" title="Notifications" aria-label="Notifications">◌</button><span class="portal-avatar portal-avatar-admin" aria-hidden="true">A</span><span class="portal-user-name">Admin desk</span></div>
            </header>
            <main class="portal-content" id="admin-main-content">
                @if(session('success'))<div class="form-success" role="status">{{ session('success') }}</div>@endif
                @if($errors->any())<div class="form-errors" role="alert"><strong>Please correct the highlighted fields.</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
