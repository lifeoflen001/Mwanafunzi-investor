@php
    $brandName = \App\Models\SiteSetting::getValue('brand_name', 'Mwanafunzi Investor');
    $email = \App\Models\SiteSetting::getValue('contact_email', 'mwanafunziinvestor@outlook.com');
    $phone = \App\Models\SiteSetting::getValue('contact_phone', '+255 787 172 686');
    $location = \App\Models\SiteSetting::getValue('contact_location', 'Tanzania');
    $footerCopy = \App\Models\SiteSetting::getValue('footer_copy', 'Student of Money. Probability. Systems. Discipline.');
    $disclaimer = \App\Models\SiteSetting::getValue('risk_disclaimer', 'Educational content only. This is not personalised financial advice. Trading involves risk.');
    $favicon = \App\Models\SiteSetting::getValue('favicon', 'favicon.svg') ?: 'favicon.svg';
    $faviconUrl = str_starts_with($favicon, 'http') || str_starts_with($favicon, '/') || $favicon === 'favicon.svg' ? asset($favicon) : asset('storage/'.$favicon);
    $businessUnits = \App\Models\BusinessUnit::query()->active()->orderBy('sort_order')->get();
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="@yield('description', \App\Models\SiteSetting::getValue('default_seo_description', 'Financial education for systematic trading, probability, risk management and disciplined portfolio thinking.'))">
    <meta name="robots" content="{{ !empty($preview) ? 'noindex,nofollow,noarchive' : 'index,follow' }}">
    <link rel="canonical" href="{{ url()->current() }}">
    <meta property="og:title" content="@yield('title', $brandName)">
    <meta property="og:description" content="@yield('description', $disclaimer)">
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta name="twitter:card" content="summary_large_image">
    <link rel="icon" href="{{ $faviconUrl }}">
    <title>@yield('title', $brandName)</title>
    <script type="application/ld+json">{!! json_encode(['@context' => 'https://schema.org', '@type' => 'Organization', 'name' => $brandName, 'url' => url('/'), 'email' => $email, 'telephone' => $phone, 'address' => ['@type' => 'PostalAddress', 'addressCountry' => 'TZ']], JSON_UNESCAPED_SLASHES) !!}</script>
    @yield('structured_data')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="internal-page">
    @if(!empty($preview))<div class="preview-banner" role="status">Private draft preview · This link expires automatically and is not publicly discoverable.</div>@endif
    <a class="skip-link" href="#main-content">Skip to content</a>
    <header class="site-header internal-header" data-header>
        <div class="header-inner container">
            <a class="brand" href="{{ route('home') }}" aria-label="{{ $brandName }} home"><span class="brand-mark" aria-hidden="true"><span></span><span></span><span></span></span><span class="brand-copy"><strong>MWANAFUNZI</strong><small>INVESTOR</small></span></a>
            <nav class="desktop-nav" aria-label="Primary navigation">@foreach ($businessUnits as $businessUnit)<a class="{{ request()->routeIs($businessUnit->route_name) ? 'active' : '' }}" href="{{ route($businessUnit->route_name) }}" @if(request()->routeIs($businessUnit->route_name)) aria-current="page" @endif>{{ $businessUnit->name }}</a>@endforeach<a class="{{ request()->routeIs('about') ? 'active' : '' }}" href="{{ route('about') }}" @if(request()->routeIs('about')) aria-current="page" @endif>About</a></nav>
            <div class="header-actions"><a class="header-contact" href="{{ auth()->check() ? route('account.dashboard') : route('login') }}">{{ auth()->check() ? 'Account' : 'Sign in' }}</a><a class="button button-small button-light" href="{{ route('contact') }}">Start a conversation <span aria-hidden="true">↗</span></a><button class="menu-toggle" type="button" aria-expanded="false" aria-controls="mobile-menu" data-menu-toggle><span class="sr-only">Open menu</span><span></span><span></span></button></div>
        </div>
        <div class="mobile-menu" id="mobile-menu" data-mobile-menu><nav aria-label="Mobile navigation">@foreach ($businessUnits as $businessUnit)<a href="{{ route($businessUnit->route_name) }}">{{ $businessUnit->name }} <span>{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span></a>@endforeach<a href="{{ route('about') }}">About <span>{{ str_pad($businessUnits->count() + 1, 2, '0', STR_PAD_LEFT) }}</span></a></nav><a class="button button-dark" href="{{ route('contact') }}">Contact the desk <span aria-hidden="true">↗</span></a></div>
    </header>
    <main id="main-content">@yield('content')</main>
    <footer class="site-footer"><div class="container"><div class="footer-top"><div class="footer-brand"><a class="brand" href="{{ route('home') }}"><span class="brand-mark" aria-hidden="true"><span></span><span></span><span></span></span><span class="brand-copy"><strong>MWANAFUNZI</strong><small>INVESTOR</small></span></a><p>{!! nl2br(e($footerCopy)) !!}</p></div><div class="footer-nav"><div><span>Explore</span><a href="{{ route('learn') }}">Learn</a><a href="{{ route('courses') }}">Courses</a><a href="{{ route('tools') }}">Tools</a></div><div><span>Modules</span>@foreach($businessUnits as $businessUnit)<a href="{{ route($businessUnit->route_name) }}">{{ $businessUnit->name }}</a>@endforeach</div><div><span>Company</span><a href="{{ route('journal') }}">Journal</a><a href="{{ route('about') }}">About</a><a href="{{ route('contact') }}">Contact</a></div><div class="footer-contact"><span>Contact</span><a href="mailto:{{ $email }}">{{ $email }}</a><a href="tel:{{ preg_replace('/\D+/', '', $phone) }}">{{ $phone }}</a><p>{{ $location }}</p></div></div></div><div class="footer-bottom"><span>© {{ date('Y') }} {{ $brandName }}</span><div><a href="{{ route('legal', 'privacy-policy') }}">Privacy</a><a href="{{ route('legal', 'terms') }}">Terms</a><a href="{{ route('legal', 'risk-disclosure') }}">Risk disclosure</a></div><strong>ALWAYS A MWANAFUNZI.</strong></div><p class="site-disclaimer">{{ $disclaimer }}</p></div></footer>
</body>
</html>
