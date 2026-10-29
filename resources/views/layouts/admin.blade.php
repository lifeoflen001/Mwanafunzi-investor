@php
    $unreadEnquiries = auth()->check() ? \App\Models\ContactMessage::whereNull('read_at')->whereIn('status', ['new', 'in_progress'])->count() : 0;
    $notificationMessages = auth()->check() ? \App\Models\ContactMessage::with('businessUnit')->latest()->limit(5)->get() : collect();
    $validationErrors = view()->shared('errors', new \Illuminate\Support\ViewErrorBag());
    $adminPageHeading = trim($__env->yieldContent('portal-heading') ?: $__env->yieldContent('title') ?: 'Dashboard');
@endphp
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
                    <img class="portal-brand-logo" src="{{ asset('images/brand/mwanafunzi-logo-light.webp') }}" alt="Mwanafunzi Investor">
                    <img class="portal-brand-mark" src="{{ asset('web-app-manifest-512x512.png') }}" alt="">
                </a>
                <button class="admin-sidebar-close" type="button" aria-label="Close admin navigation" data-admin-sidebar-close>×</button>
            </div>
            <div class="admin-sidebar-scroll">
                <p class="portal-section-label">Overview</p>
                <nav class="portal-nav" aria-label="Overview navigation">
                    <a class="{{ request()->routeIs('admin.dashboard') ? 'is-active' : '' }}" href="{{ route('admin.dashboard') }}"><span class="portal-nav-icon" aria-hidden="true">⌂</span><span>Dashboard</span></a>
                </nav>

                <div class="admin-nav-group">
                    <p class="portal-section-label">Content</p>
                    <nav class="portal-nav" aria-label="Content navigation">
                        <div class="admin-nav-parent {{ request()->routeIs('admin.articles*', 'admin.journal*') ? 'is-active-parent' : '' }}" data-admin-nav-parent data-nav-key="posts">
                            <div class="admin-nav-parent-row"><a class="{{ request()->routeIs('admin.articles*', 'admin.journal') ? 'is-active' : '' }}" href="{{ route('admin.journal') }}"><span class="portal-nav-icon" aria-hidden="true">▥</span><span>Posts</span></a><button type="button" class="admin-nav-toggle" aria-label="Expand Posts navigation" aria-expanded="{{ request()->routeIs('admin.articles*', 'admin.journal*') ? 'true' : 'false' }}" aria-controls="admin-nav-posts" data-admin-nav-toggle data-nav-active="{{ request()->routeIs('admin.articles*', 'admin.journal*') ? 'true' : 'false' }}" data-nav-label="Posts"><span aria-hidden="true">⌄</span></button></div>
                            <div class="admin-nav-submenu" id="admin-nav-posts" @if(!request()->routeIs('admin.articles*', 'admin.journal*')) hidden @endif><a class="{{ request()->routeIs('admin.articles*', 'admin.journal') ? 'is-active' : '' }}" href="{{ route('admin.journal') }}">All posts</a><a class="{{ request()->routeIs('admin.articles.create', 'admin.journal.create') ? 'is-active' : '' }}" href="{{ route('admin.journal.create') }}">New post</a><a class="{{ request()->routeIs('admin.journal.daily-updates*') ? 'is-active' : '' }}" href="{{ route('admin.journal.daily-updates.create') }}">Daily update</a></div>
                        </div>
                        <a class="{{ request()->routeIs('admin.comments*') ? 'is-active' : '' }}" href="{{ route('admin.comments') }}"><span class="portal-nav-icon" aria-hidden="true">◌</span><span>Comments</span></a>
                        <a class="{{ request()->routeIs('admin.categories*') ? 'is-active' : '' }}" href="{{ route('admin.categories') }}"><span class="portal-nav-icon" aria-hidden="true">#</span><span>Categories</span></a>
                        <a class="{{ request()->routeIs('admin.tags*') ? 'is-active' : '' }}" href="{{ route('admin.tags') }}"><span class="portal-nav-icon" aria-hidden="true">⌘</span><span>Tags</span></a>
                        <a class="{{ request()->routeIs('admin.faqs*') ? 'is-active' : '' }}" href="{{ route('admin.faqs') }}"><span class="portal-nav-icon" aria-hidden="true">?</span><span>FAQs</span></a>
                        <a class="{{ request()->routeIs('admin.media*') ? 'is-active' : '' }}" href="{{ route('admin.media') }}"><span class="portal-nav-icon" aria-hidden="true">▧</span><span>Media library</span></a>
                    </nav>
                </div>

                <div class="admin-nav-group">
                    <p class="portal-section-label">Learning</p>
                    <nav class="portal-nav" aria-label="Learning navigation">
                        <div class="admin-nav-parent {{ request()->routeIs('admin.courses*') ? 'is-active-parent' : '' }}" data-admin-nav-parent data-nav-key="courses">
                            <div class="admin-nav-parent-row"><a class="{{ request()->routeIs('admin.courses*') ? 'is-active' : '' }}" href="{{ route('admin.courses') }}"><span class="portal-nav-icon" aria-hidden="true">▤</span><span>Courses</span></a><button type="button" class="admin-nav-toggle" aria-label="Expand Courses navigation" aria-expanded="{{ request()->routeIs('admin.courses*') ? 'true' : 'false' }}" aria-controls="admin-nav-courses" data-admin-nav-toggle data-nav-active="{{ request()->routeIs('admin.courses*') ? 'true' : 'false' }}" data-nav-label="Courses"><span aria-hidden="true">⌄</span></button></div>
                            <div class="admin-nav-submenu" id="admin-nav-courses" @if(!request()->routeIs('admin.courses*')) hidden @endif><a class="{{ request()->routeIs('admin.courses') ? 'is-active' : '' }}" href="{{ route('admin.courses') }}">All courses</a><a class="{{ request()->routeIs('admin.courses.create') ? 'is-active' : '' }}" href="{{ route('admin.courses.create') }}">Create course</a></div>
                        </div>
                        <a class="{{ request()->routeIs('admin.topics*') ? 'is-active' : '' }}" href="{{ route('admin.topics') }}"><span class="portal-nav-icon" aria-hidden="true">◌</span><span>Learning topics</span></a>
                    </nav>
                </div>

                <div class="admin-nav-group">
                    <p class="portal-section-label">Commerce</p>
                    <nav class="portal-nav" aria-label="Commerce navigation">
                        <div class="admin-nav-parent {{ request()->routeIs('admin.commerce*', 'admin.products*', 'admin.customers*') ? 'is-active-parent' : '' }}" data-admin-nav-parent data-nav-key="commerce">
                            <div class="admin-nav-parent-row"><a class="{{ request()->routeIs('admin.commerce.dashboard') ? 'is-active' : '' }}" href="{{ route('admin.commerce.dashboard') }}"><span class="portal-nav-icon" aria-hidden="true">$</span><span>Commerce</span></a><button type="button" class="admin-nav-toggle" aria-label="Expand Commerce navigation" aria-expanded="{{ request()->routeIs('admin.commerce*', 'admin.products*', 'admin.customers*') ? 'true' : 'false' }}" aria-controls="admin-nav-commerce" data-admin-nav-toggle data-nav-active="{{ request()->routeIs('admin.commerce*', 'admin.products*', 'admin.customers*') ? 'true' : 'false' }}" data-nav-label="Commerce"><span aria-hidden="true">⌄</span></button></div>
                            <div class="admin-nav-submenu" id="admin-nav-commerce" @if(!request()->routeIs('admin.commerce*', 'admin.products*', 'admin.customers*')) hidden @endif><a class="{{ request()->routeIs('admin.commerce.dashboard') ? 'is-active' : '' }}" href="{{ route('admin.commerce.dashboard') }}">Commerce overview</a><a class="{{ request()->routeIs('admin.products*') ? 'is-active' : '' }}" href="{{ route('admin.products') }}">Products</a><a class="{{ request()->routeIs('admin.commerce.orders*') ? 'is-active' : '' }}" href="{{ route('admin.commerce.orders') }}">Orders</a><a class="{{ request()->routeIs('admin.commerce.payments*') ? 'is-active' : '' }}" href="{{ route('admin.commerce.payments') }}">Payments</a><a class="{{ request()->routeIs('admin.commerce.entitlements*') ? 'is-active' : '' }}" href="{{ route('admin.commerce.entitlements') }}">Entitlements</a><a class="{{ request()->routeIs('admin.commerce.waitlists*') ? 'is-active' : '' }}" href="{{ route('admin.commerce.waitlists') }}">Waitlists</a><a class="{{ request()->routeIs('admin.customers*') ? 'is-active' : '' }}" href="{{ route('admin.customers') }}">Customers</a></div>
                        </div>
                    </nav>
                </div>

                <div class="admin-nav-group">
                    <p class="portal-section-label">Communication</p>
                    <nav class="portal-nav" aria-label="Communication navigation">
                        <div class="admin-nav-parent {{ request()->routeIs('admin.newsletter*', 'admin.settings.newsletter*') ? 'is-active-parent' : '' }}" data-admin-nav-parent data-nav-key="newsletter">
                            <div class="admin-nav-parent-row"><a class="{{ request()->routeIs('admin.newsletter') ? 'is-active' : '' }}" href="{{ route('admin.newsletter') }}"><span class="portal-nav-icon" aria-hidden="true">✉</span><span>Newsletter</span></a><button type="button" class="admin-nav-toggle" aria-label="Expand Newsletter navigation" aria-expanded="{{ request()->routeIs('admin.newsletter*', 'admin.settings.newsletter*') ? 'true' : 'false' }}" aria-controls="admin-nav-newsletter" data-admin-nav-toggle data-nav-active="{{ request()->routeIs('admin.newsletter*', 'admin.settings.newsletter*') ? 'true' : 'false' }}" data-nav-label="Newsletter"><span aria-hidden="true">⌄</span></button></div>
                            <div class="admin-nav-submenu" id="admin-nav-newsletter" @if(!request()->routeIs('admin.newsletter*', 'admin.settings.newsletter*')) hidden @endif><a class="{{ request()->routeIs('admin.newsletter') ? 'is-active' : '' }}" href="{{ route('admin.newsletter') }}">Newsletter inbox</a><a href="{{ route('admin.newsletter.export') }}">Export subscribers</a><a class="{{ request()->routeIs('admin.settings.newsletter*') ? 'is-active' : '' }}" href="{{ route('admin.settings.newsletter') }}">Newsletter settings</a></div>
                        </div>
                        <a class="{{ request()->routeIs('admin.messages*') ? 'is-active' : '' }}" href="{{ route('admin.messages') }}"><span class="portal-nav-icon" aria-hidden="true">✉</span><span>Enquiries</span></a>
                        <a class="{{ request()->routeIs('admin.studio.inquiries*') ? 'is-active' : '' }}" href="{{ route('admin.studio.inquiries') }}"><span class="portal-nav-icon" aria-hidden="true">◫</span><span>Studio enquiries</span></a>
                    </nav>
                </div>

                <div class="admin-nav-group">
                    <p class="portal-section-label">Website</p>
                    <nav class="portal-nav" aria-label="Website navigation">
                        <div class="admin-nav-parent {{ request()->routeIs('admin.services*', 'admin.projects*', 'admin.team*', 'admin.testimonials*', 'admin.pages*', 'admin.redirects*', 'admin.navigation*', 'admin.business-units*', 'admin.seo*') ? 'is-active-parent' : '' }}" data-admin-nav-parent data-nav-key="website">
                            <div class="admin-nav-parent-row"><a class="{{ request()->routeIs('admin.pages*') ? 'is-active' : '' }}" href="{{ route('admin.pages') }}"><span class="portal-nav-icon" aria-hidden="true">▣</span><span>Website</span></a><button type="button" class="admin-nav-toggle" aria-label="Expand Website navigation" aria-expanded="{{ request()->routeIs('admin.services*', 'admin.projects*', 'admin.team*', 'admin.testimonials*', 'admin.pages*', 'admin.redirects*', 'admin.navigation*', 'admin.business-units*') ? 'true' : 'false' }}" aria-controls="admin-nav-website" data-admin-nav-toggle data-nav-active="{{ request()->routeIs('admin.services*', 'admin.projects*', 'admin.team*', 'admin.testimonials*', 'admin.pages*', 'admin.redirects*', 'admin.navigation*', 'admin.business-units*') ? 'true' : 'false' }}" data-nav-label="Website"><span aria-hidden="true">⌄</span></button></div>
                            <div class="admin-nav-submenu" id="admin-nav-website" @if(!request()->routeIs('admin.services*', 'admin.projects*', 'admin.team*', 'admin.testimonials*', 'admin.pages*', 'admin.redirects*', 'admin.navigation*', 'admin.business-units*', 'admin.seo*')) hidden @endif><a class="{{ request()->routeIs('admin.pages*') ? 'is-active' : '' }}" href="{{ route('admin.pages') }}">Pages</a><a class="{{ request()->routeIs('admin.services*') ? 'is-active' : '' }}" href="{{ route('admin.services') }}">Services</a><a class="{{ request()->routeIs('admin.projects*') ? 'is-active' : '' }}" href="{{ route('admin.projects') }}">Projects</a><a class="{{ request()->routeIs('admin.team*') ? 'is-active' : '' }}" href="{{ route('admin.team') }}">Team</a><a class="{{ request()->routeIs('admin.testimonials*') ? 'is-active' : '' }}" href="{{ route('admin.testimonials') }}">Testimonials</a><a class="{{ request()->routeIs('admin.navigation*') ? 'is-active' : '' }}" href="{{ route('admin.navigation') }}">Navigation</a><a class="{{ request()->routeIs('admin.redirects*') ? 'is-active' : '' }}" href="{{ route('admin.redirects') }}">Redirects</a><a class="{{ request()->routeIs('admin.business-units*') ? 'is-active' : '' }}" href="{{ route('admin.business-units') }}">Business modules</a><a class="{{ request()->routeIs('admin.seo*') ? 'is-active' : '' }}" href="{{ route('admin.seo') }}">SEO &amp; sitemap</a></div>
                        </div>
                    </nav>
                </div>

                <div class="admin-nav-group">
                    <p class="portal-section-label">System</p>
                    <nav class="portal-nav" aria-label="System navigation">
                        <div class="admin-nav-parent {{ request()->routeIs('admin.policies*', 'admin.compliance', 'admin.settings*', 'admin.social-links*') ? 'is-active-parent' : '' }}" data-admin-nav-parent data-nav-key="system">
                            <div class="admin-nav-parent-row"><a class="{{ request()->routeIs('admin.settings') ? 'is-active' : '' }}" href="{{ route('admin.settings') }}"><span class="portal-nav-icon" aria-hidden="true">⚙</span><span>Settings</span></a><button type="button" class="admin-nav-toggle" aria-label="Expand Settings navigation" aria-expanded="{{ request()->routeIs('admin.policies*', 'admin.compliance', 'admin.settings*', 'admin.social-links*') ? 'true' : 'false' }}" aria-controls="admin-nav-system" data-admin-nav-toggle data-nav-active="{{ request()->routeIs('admin.policies*', 'admin.compliance', 'admin.settings*', 'admin.social-links*') ? 'true' : 'false' }}" data-nav-label="Settings"><span aria-hidden="true">⌄</span></button></div>
                            <div class="admin-nav-submenu" id="admin-nav-system" @if(!request()->routeIs('admin.policies*', 'admin.compliance', 'admin.settings*', 'admin.social-links*')) hidden @endif><a class="{{ request()->routeIs('admin.settings') ? 'is-active' : '' }}" href="{{ route('admin.settings') }}">Site settings</a><a class="{{ request()->routeIs('admin.policies*') ? 'is-active' : '' }}" href="{{ route('admin.policies') }}">Policies</a><a class="{{ request()->routeIs('admin.compliance') ? 'is-active' : '' }}" href="{{ route('admin.compliance') }}">Compliance &amp; privacy</a><a class="{{ request()->routeIs('admin.settings.security*') ? 'is-active' : '' }}" href="{{ route('admin.settings.security') }}">Security</a><a class="{{ request()->routeIs('admin.social-links*') ? 'is-active' : '' }}" href="{{ route('admin.social-links') }}">Social links</a><a class="{{ request()->routeIs('admin.settings.cookies*') ? 'is-active' : '' }}" href="{{ route('admin.settings.cookies') }}">Cookies &amp; privacy</a><a class="{{ request()->routeIs('admin.settings.cookies.registry*') ? 'is-active' : '' }}" href="{{ route('admin.settings.cookies.registry') }}">Cookie registry</a></div>
                        </div>
                        <div class="admin-nav-parent {{ request()->routeIs('admin.audit*', 'admin.administrators*') ? 'is-active-parent' : '' }}" data-admin-nav-parent data-nav-key="administration">
                            <div class="admin-nav-parent-row"><a class="{{ request()->routeIs('admin.administrators*') ? 'is-active' : '' }}" href="{{ route('admin.administrators') }}"><span class="portal-nav-icon" aria-hidden="true">◎</span><span>Administration</span></a><button type="button" class="admin-nav-toggle" aria-label="Expand Administration navigation" aria-expanded="{{ request()->routeIs('admin.audit*', 'admin.administrators*') ? 'true' : 'false' }}" aria-controls="admin-nav-administration" data-admin-nav-toggle data-nav-active="{{ request()->routeIs('admin.audit*', 'admin.administrators*') ? 'true' : 'false' }}" data-nav-label="Administration"><span aria-hidden="true">⌄</span></button></div>
                            <div class="admin-nav-submenu" id="admin-nav-administration" @if(!request()->routeIs('admin.audit*', 'admin.administrators*')) hidden @endif><a class="{{ request()->routeIs('admin.administrators*') ? 'is-active' : '' }}" href="{{ route('admin.administrators') }}">Administrators</a><a class="{{ request()->routeIs('admin.audit*') ? 'is-active' : '' }}" href="{{ route('admin.audit') }}">Activity log</a></div>
                        </div>
                    </nav>
                </div>
            </div>
            <div class="portal-sidebar-footer">
                <span class="portal-status portal-status-accent"><i></i> System online</span>
                @auth
                    <form method="post" action="{{ route('admin.logout') }}">@csrf<button class="portal-signout" type="submit">Sign out</button></form>
                @else
                    <a href="{{ \App\Support\PortalLinks::adminLogin() }}" target="_blank" rel="noopener">Admin sign in</a>
                @endauth
            </div>
        </aside>
        <div class="admin-sidebar-backdrop" data-admin-sidebar-close></div>
        <div class="portal-main">
            <header class="portal-topbar admin-topbar">
                <button class="admin-sidebar-toggle" type="button" aria-label="Toggle admin navigation" aria-controls="admin-sidebar" aria-expanded="true" data-admin-sidebar-toggle><span aria-hidden="true">☰</span></button>
                <span class="sr-only">Admin desk navigation and search</span>
                <form class="portal-search admin-search" method="get" action="{{ route('admin.search') }}" data-admin-search-form data-suggestions-url="{{ route('admin.search.suggestions') }}" role="search" autocomplete="off"><label class="sr-only" for="admin-search">Search the desk</label><span class="admin-search-icon" aria-hidden="true">⌕</span><input id="admin-search" name="q" value="{{ request('q') }}" placeholder="Search the desk" data-admin-search-input aria-controls="admin-search-suggestions" aria-expanded="false"><kbd class="admin-search-shortcut" aria-label="Keyboard shortcut Control K">Ctrl K</kbd><button class="admin-search-clear" type="button" aria-label="Clear search" data-admin-search-clear hidden>×</button><span class="admin-search-loading" aria-hidden="true" data-admin-search-loading hidden></span><div class="admin-search-suggestions" id="admin-search-suggestions" role="listbox" hidden data-admin-search-suggestions></div><div class="admin-search-loading-panel" data-admin-search-loading-panel hidden aria-hidden="true"><x-skeleton variant="text" width="72%" height="12px" /><x-skeleton variant="text" width="52%" height="12px" /><x-skeleton variant="text" width="64%" height="12px" /></div><span class="sr-only" aria-live="polite" data-admin-search-status></span></form><button class="admin-mobile-search-button admin-icon-button" type="button" title="Search the desk" aria-label="Open search" data-admin-mobile-search>⌕</button>
                <div class="portal-topbar-actions">
                    @auth
                        <button class="admin-topbar-action admin-icon-button" type="button" title="Enter fullscreen" aria-label="Enter fullscreen" data-admin-fullscreen><svg aria-hidden="true" data-admin-fullscreen-icon viewBox="0 0 24 24"><path d="M8 3H3v5M16 3h5v5M21 16v5h-5M3 16v5h5"/></svg></button>
                        <div class="admin-notification-menu" data-admin-notifications><button class="admin-topbar-action admin-icon-button" type="button" title="Notifications" aria-label="Notifications" aria-expanded="false" aria-controls="admin-notification-menu" data-admin-notifications-toggle><svg aria-hidden="true" viewBox="0 0 24 24"><path d="M18 9a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9M10 21h4"/></svg>@if($unreadEnquiries)<span class="admin-notification-count">{{ $unreadEnquiries }}</span>@endif</button><div class="admin-notification-popover" id="admin-notification-menu" hidden data-admin-notifications-menu><div class="admin-popover-heading"><strong>Notifications</strong>@if($unreadEnquiries)<form method="post" action="{{ route('admin.notifications.read-all') }}">@csrf<button type="submit">Mark all as read</button></form>@endif</div><div class="admin-notification-list">@forelse($notificationMessages as $notification)<a class="admin-notification-item {{ $notification->read_at ? '' : 'is-unread' }}" href="{{ route('admin.messages.show', $notification) }}"><span class="admin-notification-dot" aria-hidden="true"></span><span><strong>{{ $notification->name }} sent an enquiry</strong><small>{{ $notification->preview(72) }}</small><time datetime="{{ $notification->created_at->toIso8601String() }}">{{ $notification->created_at->diffForHumans() }}</time></span></a>@empty<x-admin.empty-state title="No notifications" description="New enquiry activity will appear here." />@endforelse</div><a class="admin-popover-footer" href="{{ route('admin.notifications') }}">View all notifications <span aria-hidden="true">→</span></a></div></div>
                        <div class="portal-profile" data-admin-profile><button class="portal-profile-toggle" type="button" aria-expanded="false" aria-controls="admin-profile-menu" data-admin-profile-toggle>@if(auth()->user()->avatar_url)<img class="portal-avatar portal-avatar-image" src="{{ auth()->user()->avatar_url }}" alt="">@else<span class="portal-avatar portal-avatar-admin" aria-hidden="true">{{ auth()->user()->initials() }}</span>@endif<span class="portal-user-name">{{ auth()->user()->name }}</span><span class="portal-profile-caret" aria-hidden="true">⌄</span></button><div class="portal-profile-menu admin-profile-menu" id="admin-profile-menu" hidden data-admin-profile-menu><div class="admin-profile-menu-head">@if(auth()->user()->avatar_url)<img class="portal-avatar portal-avatar-image" src="{{ auth()->user()->avatar_url }}" alt="">@else<span class="portal-avatar portal-avatar-admin" aria-hidden="true">{{ auth()->user()->initials() }}</span>@endif<span><strong>{{ auth()->user()->name }}</strong><small>{{ auth()->user()->email }}</small></span></div><div class="admin-profile-menu-group"><a href="{{ route('admin.profile') }}"><span aria-hidden="true">○</span>My profile</a><a href="{{ route('admin.settings') }}"><span aria-hidden="true">⚙</span>Site settings</a></div><div class="admin-profile-menu-group"><a href="{{ route('account.dashboard') }}"><span aria-hidden="true">◎</span>Student portal</a><a href="{{ route('home') }}" target="_blank" rel="noopener"><span aria-hidden="true">↗</span>View public site</a></div><form class="admin-profile-menu-group" method="post" action="{{ route('admin.logout') }}">@csrf<button type="submit" class="admin-profile-signout"><span aria-hidden="true">↪</span>Sign out</button></form></div></div>
                    @else
                        <span class="portal-avatar portal-avatar-admin" aria-hidden="true">A</span><span class="portal-user-name">Admin desk</span>
                    @endauth
                </div>
            </header>
            <main class="portal-content" id="admin-main-content">
                <x-admin.flash />
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
