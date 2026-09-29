@php
    $unreadEnquiries = auth()->check() ? \App\Models\ContactMessage::whereNull('read_at')->count() : 0;
    $notificationMessages = auth()->check() ? \App\Models\ContactMessage::with('businessUnit')->latest()->limit(5)->get() : collect();
    $validationErrors = view()->shared('errors', new \Illuminate\Support\ViewErrorBag());
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#202a27">
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
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
                    <img class="brand-icon" src="{{ asset('favicon.svg') }}" alt="">
                    <span><strong>MWANAFUNZI</strong><small>ADMIN DESK</small></span>
                </a>
                <button class="admin-sidebar-close" type="button" aria-label="Close admin navigation" data-admin-sidebar-close>×</button>
            </div>
            <div class="admin-sidebar-scroll">
                <p class="portal-section-label">Overview</p>
                <nav class="portal-nav" aria-label="Overview navigation">
                    <a class="{{ request()->routeIs('admin.dashboard') ? 'is-active' : '' }}" href="{{ route('admin.dashboard') }}"><span class="portal-nav-icon" aria-hidden="true">⌂</span>Dashboard</a>
                </nav>
                <div class="admin-nav-group">
                    <p class="portal-section-label">Content</p>
                <nav class="portal-nav" aria-label="Content navigation">
                    <a class="{{ request()->routeIs('admin.courses*') ? 'is-active' : '' }}" href="{{ route('admin.courses') }}"><span class="portal-nav-icon" aria-hidden="true">▤</span>Courses</a>
                    <a class="{{ request()->routeIs('admin.topics*') ? 'is-active' : '' }}" href="{{ route('admin.topics') }}"><span class="portal-nav-icon" aria-hidden="true">◌</span>Learning topics</a>
                    <a class="{{ request()->routeIs('admin.articles*') ? 'is-active' : '' }}" href="{{ route('admin.articles') }}"><span class="portal-nav-icon" aria-hidden="true">▥</span>Journal</a>
                    <a class="{{ request()->routeIs('admin.categories*') ? 'is-active' : '' }}" href="{{ route('admin.categories') }}"><span class="portal-nav-icon" aria-hidden="true">#</span>Categories</a>
                    <a class="{{ request()->routeIs('admin.tags*') ? 'is-active' : '' }}" href="{{ route('admin.tags') }}"><span class="portal-nav-icon" aria-hidden="true">⌘</span>Tags</a>
                    <a class="{{ request()->routeIs('admin.faqs*') ? 'is-active' : '' }}" href="{{ route('admin.faqs') }}"><span class="portal-nav-icon" aria-hidden="true">?</span>FAQs</a>
                    <a class="{{ request()->routeIs('admin.media*') ? 'is-active' : '' }}" href="{{ route('admin.media') }}"><span class="portal-nav-icon" aria-hidden="true">▧</span>Media library</a>
                </nav>
                </div>
                <div class="admin-nav-group">
                    <p class="portal-section-label">Commerce</p>
                <nav class="portal-nav" aria-label="Commerce navigation">
                    <a class="{{ request()->routeIs('admin.products*') ? 'is-active' : '' }}" href="{{ route('admin.products') }}"><span class="portal-nav-icon" aria-hidden="true">◈</span>Products</a>
                    <a class="{{ request()->routeIs('admin.commerce.dashboard') ? 'is-active' : '' }}" href="{{ route('admin.commerce.dashboard') }}"><span class="portal-nav-icon" aria-hidden="true">$</span>Commerce overview</a>
                    <a class="admin-nav-child {{ request()->routeIs('admin.commerce.orders*') ? 'is-active' : '' }}" href="{{ route('admin.commerce.orders') }}"><span class="portal-nav-icon" aria-hidden="true">↳</span>Orders</a>
                    <a class="admin-nav-child {{ request()->routeIs('admin.commerce.payments*') ? 'is-active' : '' }}" href="{{ route('admin.commerce.payments') }}"><span class="portal-nav-icon" aria-hidden="true">↳</span>Payments</a>
                    <a class="admin-nav-child {{ request()->routeIs('admin.commerce.entitlements*') ? 'is-active' : '' }}" href="{{ route('admin.commerce.entitlements') }}"><span class="portal-nav-icon" aria-hidden="true">↳</span>Entitlements</a>
                    <a class="admin-nav-child {{ request()->routeIs('admin.commerce.waitlists*') ? 'is-active' : '' }}" href="{{ route('admin.commerce.waitlists') }}"><span class="portal-nav-icon" aria-hidden="true">↳</span>Waitlists</a>
                    <a class="{{ request()->routeIs('admin.customers*') ? 'is-active' : '' }}" href="{{ route('admin.customers') }}"><span class="portal-nav-icon" aria-hidden="true">◎</span>Customers</a>
                </nav>
                </div>
                <div class="admin-nav-group">
                    <p class="portal-section-label">Platform</p>
                <nav class="portal-nav" aria-label="Platform navigation">
                    <a class="{{ request()->routeIs('admin.pages*') ? 'is-active' : '' }}" href="{{ route('admin.pages') }}"><span class="portal-nav-icon" aria-hidden="true">▣</span>Pages</a>
                    <a class="{{ request()->routeIs('admin.policies*') ? 'is-active' : '' }}" href="{{ route('admin.policies') }}"><span class="portal-nav-icon" aria-hidden="true">§</span>Policies</a>
                    <a class="{{ request()->routeIs('admin.redirects*') ? 'is-active' : '' }}" href="{{ route('admin.redirects') }}"><span class="portal-nav-icon" aria-hidden="true">↪</span>Redirects</a>
                    <a class="{{ request()->routeIs('admin.navigation*') ? 'is-active' : '' }}" href="{{ route('admin.navigation') }}"><span class="portal-nav-icon" aria-hidden="true">≡</span>Navigation</a>
                    <a class="{{ request()->routeIs('admin.business-units*') ? 'is-active' : '' }}" href="{{ route('admin.business-units') }}"><span class="portal-nav-icon" aria-hidden="true">◈</span>Business modules</a>
                    <a class="{{ request()->routeIs('admin.messages*') ? 'is-active' : '' }}" href="{{ route('admin.messages') }}"><span class="portal-nav-icon" aria-hidden="true">✉</span>Enquiries</a>
                    <a class="{{ request()->routeIs('admin.settings*', 'admin.social-links*') ? 'is-active' : '' }}" href="{{ route('admin.settings') }}"><span class="portal-nav-icon" aria-hidden="true">⚙</span>Settings</a>
                    <a class="{{ request()->routeIs('admin.social-links*') ? 'is-active' : '' }}" href="{{ route('admin.social-links') }}"><span class="portal-nav-icon" aria-hidden="true">↗</span>Social links</a>
                    <a class="{{ request()->routeIs('admin.audit*') ? 'is-active' : '' }}" href="{{ route('admin.audit') }}"><span class="portal-nav-icon" aria-hidden="true">◷</span>Activity log</a>
                    <a class="{{ request()->routeIs('admin.administrators*') ? 'is-active' : '' }}" href="{{ route('admin.administrators') }}"><span class="portal-nav-icon" aria-hidden="true">◎</span>Administrators</a>
                </nav>
                </div>
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
                <button class="admin-sidebar-toggle" type="button" aria-label="Toggle admin navigation" aria-controls="admin-sidebar" aria-expanded="true" data-admin-sidebar-toggle><span aria-hidden="true">☰</span></button>
                <div class="portal-topbar-title"><span class="portal-mobile-label">Admin desk</span><strong>@yield('portal-heading', 'Dashboard')</strong></div>
                <div class="admin-breadcrumbs" aria-label="Breadcrumb"><a href="{{ route('admin.dashboard') }}">Dashboard</a><span aria-hidden="true">/</span><span>@yield('portal-heading', 'Overview')</span></div>
                <form class="portal-search admin-search" method="get" action="{{ route('admin.search') }}" data-admin-search-form data-suggestions-url="{{ route('admin.search.suggestions') }}" role="search" autocomplete="off"><label class="sr-only" for="admin-search">Search the desk</label><span class="admin-search-icon" aria-hidden="true">⌕</span><input id="admin-search" name="q" value="{{ request('q') }}" placeholder="Search the desk" data-admin-search-input aria-controls="admin-search-suggestions" aria-expanded="false"><kbd class="admin-search-shortcut" aria-label="Keyboard shortcut Control K">Ctrl K</kbd><button class="admin-search-clear" type="button" aria-label="Clear search" data-admin-search-clear hidden>×</button><span class="admin-search-loading" aria-hidden="true" data-admin-search-loading hidden></span><div class="admin-search-suggestions" id="admin-search-suggestions" role="listbox" hidden data-admin-search-suggestions></div></form><button class="admin-mobile-search-button admin-icon-button" type="button" title="Search the desk" aria-label="Open search" data-admin-mobile-search>⌕</button>
                <div class="portal-topbar-actions">
                    @auth
                        <details class="admin-quick-actions">
                            <summary class="button button-primary button-small"><span aria-hidden="true">+</span> New</summary>
                            <div class="admin-quick-menu"><a href="{{ route('admin.articles.create') }}">Journal article</a><a href="{{ route('admin.courses.create') }}">Course</a><a href="{{ route('admin.products.create') }}">Product</a><a href="{{ route('admin.media') }}">Media asset</a></div>
                        </details>
                        <button class="admin-topbar-action admin-icon-button" type="button" title="Enter fullscreen" aria-label="Enter fullscreen" data-admin-fullscreen><svg aria-hidden="true" data-admin-fullscreen-icon viewBox="0 0 24 24"><path d="M8 3H3v5M16 3h5v5M21 16v5h-5M3 16v5h5"/></svg></button>
                        <div class="admin-notification-menu" data-admin-notifications><button class="admin-topbar-action admin-icon-button" type="button" title="Notifications" aria-label="Notifications" aria-expanded="false" aria-controls="admin-notification-menu" data-admin-notifications-toggle><svg aria-hidden="true" viewBox="0 0 24 24"><path d="M18 9a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9M10 21h4"/></svg>@if($unreadEnquiries)<span class="admin-notification-count">{{ $unreadEnquiries }}</span>@endif</button><div class="admin-notification-popover" id="admin-notification-menu" hidden data-admin-notifications-menu><div class="admin-popover-heading"><strong>Notifications</strong>@if($unreadEnquiries)<form method="post" action="{{ route('admin.notifications.read-all') }}">@csrf<button type="submit">Mark all as read</button></form>@endif</div><div class="admin-notification-list">@forelse($notificationMessages as $notification)<a class="admin-notification-item {{ $notification->read_at ? '' : 'is-unread' }}" href="{{ route('admin.messages.show', $notification) }}"><span class="admin-notification-dot" aria-hidden="true"></span><span><strong>{{ $notification->name }} sent an enquiry</strong><small>{{ \Illuminate\Support\Str::limit($notification->message, 72) }}</small><time datetime="{{ $notification->created_at->toIso8601String() }}">{{ $notification->created_at->diffForHumans() }}</time></span></a>@empty<x-admin.empty-state title="No notifications" description="New enquiry activity will appear here." />@endforelse</div><a class="admin-popover-footer" href="{{ route('admin.notifications') }}">View all notifications <span aria-hidden="true">→</span></a></div></div>
                        <div class="portal-profile" data-admin-profile><button class="portal-profile-toggle" type="button" aria-expanded="false" aria-controls="admin-profile-menu" data-admin-profile-toggle>@if(auth()->user()->avatar_url)<img class="portal-avatar portal-avatar-image" src="{{ auth()->user()->avatar_url }}" alt="">@else<span class="portal-avatar portal-avatar-admin" aria-hidden="true">{{ auth()->user()->initials() }}</span>@endif<span class="portal-user-name">{{ auth()->user()->name }}</span><span class="portal-profile-caret" aria-hidden="true">⌄</span></button><div class="portal-profile-menu admin-profile-menu" id="admin-profile-menu" hidden data-admin-profile-menu><div class="admin-profile-menu-head">@if(auth()->user()->avatar_url)<img class="portal-avatar portal-avatar-image" src="{{ auth()->user()->avatar_url }}" alt="">@else<span class="portal-avatar portal-avatar-admin" aria-hidden="true">{{ auth()->user()->initials() }}</span>@endif<span><strong>{{ auth()->user()->name }}</strong><small>{{ auth()->user()->email }}</small></span></div><div class="admin-profile-menu-group"><a href="{{ route('admin.profile') }}"><span aria-hidden="true">○</span>My profile</a><a href="{{ route('admin.settings') }}"><span aria-hidden="true">⚙</span>Site settings</a></div><div class="admin-profile-menu-group"><a href="{{ route('account.dashboard') }}"><span aria-hidden="true">◎</span>Student portal</a><a href="{{ route('home') }}" target="_blank" rel="noopener"><span aria-hidden="true">↗</span>View public site</a></div><form class="admin-profile-menu-group" method="post" action="{{ route('admin.logout') }}">@csrf<button type="submit" class="admin-profile-signout"><span aria-hidden="true">↪</span>Sign out</button></form></div></div>
                    @else
                        <span class="portal-avatar portal-avatar-admin" aria-hidden="true">A</span><span class="portal-user-name">Admin desk</span>
                    @endauth
                </div>
            </header>
            <main class="portal-content" id="admin-main-content">
                <x-admin.flash />
                @if($validationErrors->any())<div class="form-errors" role="alert"><strong>Please correct the highlighted fields.</strong><ul>@foreach($validationErrors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
                @yield('content')
            </main>
        </div>
    </div>
    <div class="admin-confirm-modal" data-confirm-modal hidden role="dialog" aria-modal="true" aria-labelledby="admin-confirm-title">
        <div class="admin-confirm-card">
            <span class="admin-confirm-icon" aria-hidden="true">!</span>
            <h2 id="admin-confirm-title">Confirm action</h2>
            <p data-confirm-message>Please confirm this action.</p>
            <div class="admin-confirm-actions"><button class="button button-secondary" type="button" data-confirm-cancel>Cancel</button><button class="button button-danger" type="button" data-confirm-accept>Continue</button></div>
        </div>
    </div>
</body>
</html>
