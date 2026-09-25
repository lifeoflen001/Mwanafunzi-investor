@php
    $brandName = \App\Models\SiteSetting::getValue('brand_name', 'Mwanafunzi Investor');
    $brandLogo = \App\Models\SiteSetting::getValue('logo');
    $brandLogoData = $brandLogo ? \App\Support\PublicHero::candidate($brandLogo) : null;
    $email = \App\Models\SiteSetting::getValue('contact_email', 'mwanafunziinvestor@outlook.com');
    $phone = \App\Models\SiteSetting::getValue('contact_phone', '+255 787 172 686');
    $location = \App\Models\SiteSetting::getValue('contact_location', 'Tanzania');
    $footerCopy = \App\Models\SiteSetting::getValue('footer_copy', 'Student of Money. Probability. Systems. Discipline.');
    $disclaimer = \App\Models\SiteSetting::getValue('risk_disclaimer', 'Educational content only. This is not personalised financial advice. Trading involves risk.');
    $favicon = \App\Models\SiteSetting::getValue('favicon', 'favicon.svg') ?: 'favicon.svg';
    $faviconUrl = str_starts_with($favicon, 'http') || str_starts_with($favicon, '/') || $favicon === 'favicon.svg' ? asset($favicon) : asset('storage/'.$favicon);
    $socialImage = \App\Models\SiteSetting::getValue('default_social_image') ?: (request()->routeIs('legal') ? config('public.hero_defaults.legal') : config('public.hero_defaults.default'));
    $socialImageData = \App\Support\PublicHero::candidate($socialImage);
    $socialImageUrl = $socialImageData['url'] ?? asset(config('public.hero_defaults.default'));
    $businessUnits = \App\Models\BusinessUnit::query()->active()->orderBy('sort_order')->get();
    $headerNavigation = \App\Models\NavigationItem::with('children')->visible()->where('location', 'header')->whereNull('parent_id')->get();
    $footerNavigation = \App\Models\NavigationItem::with('children')->visible()->where('location', 'footer')->whereNull('parent_id')->orderBy('menu_group')->get()->groupBy('menu_group');
    $socialLinks = \App\Models\SocialLink::query()->where('is_visible', true)->orderBy('sort_order')->get();
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
    <meta property="og:image" content="@yield('og_image', $socialImageUrl)">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:image" content="@yield('og_image', $socialImageUrl)">
    <link rel="icon" href="{{ $faviconUrl }}">
    <title>@yield('title', $brandName)</title>
    <script type="application/ld+json">{!! json_encode(['@context' => 'https://schema.org', '@type' => 'Organization', 'name' => $brandName, 'url' => url('/'), 'email' => $email, 'telephone' => $phone, 'address' => ['@type' => 'PostalAddress', 'addressCountry' => 'TZ']], JSON_UNESCAPED_SLASHES) !!}</script>
    @yield('structured_data')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('components.design-tokens')
</head>
<body class="internal-page">
    @if(!empty($preview))<div class="preview-banner" role="status">Private draft preview · This link expires automatically and is not publicly discoverable.</div>@endif
    <a class="skip-link" href="#main-content">Skip to content</a>
    <header class="site-header internal-header" data-header>
        <div class="header-inner container">
            <a class="brand" href="{{ route('home') }}" aria-label="{{ $brandName }} home">@if($brandLogoData)<img class="brand-image" src="{{ $brandLogoData['url'] }}" alt="{{ $brandName }}">@else<span class="brand-mark" aria-hidden="true"><span></span><span></span><span></span></span><span class="brand-copy"><strong>MWANAFUNZI</strong><small>INVESTOR</small></span>@endif</a>
            <nav class="desktop-nav" aria-label="Primary navigation">
                @forelse($headerNavigation as $item)<x-navigation-links :item="$item" />@empty @foreach($businessUnits as $businessUnit)<a class="{{ request()->routeIs($businessUnit->route_name) ? 'active' : '' }}" href="{{ route($businessUnit->route_name) }}">{{ $businessUnit->name }}</a>@endforeach<a href="{{ route('about') }}">About</a>@endforelse
            </nav>
            <div class="header-actions"><a class="header-contact" href="{{ auth()->check() ? route('account.dashboard') : route('login') }}">{{ auth()->check() ? 'Account' : 'Sign in' }}</a><a class="button button-small button-light" href="{{ route('contact') }}">Start a conversation <span aria-hidden="true">↗</span></a><button class="menu-toggle" type="button" aria-expanded="false" aria-controls="mobile-menu" data-menu-toggle><span class="sr-only">Open menu</span><span></span><span></span></button></div>
        </div>
        <div class="mobile-menu" id="mobile-menu" data-mobile-menu><nav aria-label="Mobile navigation">
            @forelse($headerNavigation as $item)<x-navigation-links :item="$item" />@empty @foreach($businessUnits as $businessUnit)<a href="{{ route($businessUnit->route_name) }}">{{ $businessUnit->name }} <span>{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span></a>@endforeach<a href="{{ route('about') }}">About <span>{{ str_pad($businessUnits->count() + 1, 2, '0', STR_PAD_LEFT) }}</span></a>@endforelse
        </nav><a class="button button-dark" href="{{ route('contact') }}">Contact the desk <span aria-hidden="true">↗</span></a></div>
    </header>
    <main id="main-content">@yield('content')</main>
    <footer class="site-footer"><div class="container"><div class="footer-top"><div class="footer-brand"><a class="brand" href="{{ route('home') }}">@if($brandLogoData)<img class="brand-image" src="{{ $brandLogoData['url'] }}" alt="{{ $brandName }}">@else<span class="brand-mark" aria-hidden="true"><span></span><span></span><span></span></span><span class="brand-copy"><strong>MWANAFUNZI</strong><small>INVESTOR</small></span>@endif</a><p>{!! nl2br(e($footerCopy)) !!}</p></div><div class="footer-nav">
        @forelse($footerNavigation as $group => $items)<div><span>{{ ucfirst($group) }}</span>@foreach($items as $item)<a href="{{ $item->href() }}" target="{{ $item->target }}">{{ $item->label }}</a>@foreach($item->children->where('is_visible', true)->sortBy('sort_order') as $child)<a class="footer-sub-link" href="{{ $child->href() }}" target="{{ $child->target }}">{{ $child->label }}</a>@endforeach @endforeach</div>@empty<div><span>Explore</span><a href="{{ route('learn') }}">Learn</a><a href="{{ route('courses') }}">Courses</a><a href="{{ route('tools') }}">Tools</a></div><div><span>Company</span><a href="{{ route('journal') }}">Journal</a><a href="{{ route('about') }}">About</a><a href="{{ route('contact') }}">Contact</a></div>@endforelse
        <div class="footer-contact"><span>Contact</span><a href="mailto:{{ $email }}">{{ $email }}</a><a href="tel:{{ preg_replace('/\D+/', '', $phone) }}">{{ $phone }}</a><p>{{ $location }}</p>@if($socialLinks->isNotEmpty())<div class="footer-social"><span>Follow</span>@foreach($socialLinks as $social)<a href="{{ $social->url }}" target="_blank" rel="noopener noreferrer">{{ $social->label }} <span aria-hidden="true">↗</span></a>@endforeach</div>@endif</div></div></div><div class="footer-bottom"><span>© {{ date('Y') }} {{ $brandName }}</span><div>@if(isset($footerNavigation['legal'])) @foreach($footerNavigation['legal'] as $item)<a href="{{ $item->href() }}">{{ $item->label }}</a>@endforeach @else<a href="{{ route('legal', 'privacy-policy') }}">Privacy</a><a href="{{ route('legal', 'terms') }}">Terms</a><a href="{{ route('legal', 'risk-disclosure') }}">Risk disclosure</a>@endif</div><strong>ALWAYS A MWANAFUNZI.</strong></div><p class="site-disclaimer">{{ $disclaimer }}</p></div></footer>
</body>
</html>
