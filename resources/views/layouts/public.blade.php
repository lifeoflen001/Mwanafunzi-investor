@php
    $brandName = \App\Models\SiteSetting::getValue('brand_name', 'Mwanafunzi Investor');
    $brandLogo = \App\Models\SiteSetting::getValue('logo');
    $brandLogoData = $brandLogo ? \App\Support\PublicHero::candidate($brandLogo) : null;
    $email = \App\Models\SiteSetting::getValue('contact_email', 'mwanafunziinvestor@outlook.com');
    $phone = \App\Models\SiteSetting::getValue('contact_phone', '+255 787 172 686');
    $whatsapp = \App\Models\SiteSetting::getValue('contact_whatsapp');
    $location = \App\Models\SiteSetting::getValue('contact_location', 'Tanzania');
    $footerCopy = \App\Models\SiteSetting::getValue('footer_copy', 'Student of Money. Probability. Systems. Discipline.');
    $footerCopyright = \App\Models\SiteSetting::getValue('footer_copyright', '© '.date('Y').' '.$brandName);
    $footerBottomStatement = \App\Models\SiteSetting::getValue('footer_bottom_statement', 'ALWAYS A MWANAFUNZI.');
    $disclaimer = \App\Models\SiteSetting::getValue('risk_disclaimer', 'Educational content only. This is not personalised financial advice. Trading involves risk.');
    $seoPageKey = match (request()->route()?->getName()) {
        'learn', 'courses', 'tools', 'journal', 'about', 'contact', 'student-of-money', 'development', 'studio' => request()->route()?->getName(),
        'legal' => request()->route('page'),
        default => null,
    };
    $seoPage = $seoPageKey ? \App\Models\Page::published()->where('key', $seoPageKey)->first() : null;
    if (! $seoPage && request()->routeIs('pages.show')) $seoPage = \App\Models\Page::published()->where('slug', request()->route('slug'))->first();
    $headTitle = trim($__env->yieldContent('title')) ?: ($seoPage?->seo_title ?: $brandName);
    $headDescription = trim($__env->yieldContent('description')) ?: ($seoPage?->seo_description ?: \App\Models\SiteSetting::getValue('default_seo_description', 'Financial education for systematic trading, probability, risk management and disciplined portfolio thinking.'));
    $headCanonical = trim($__env->yieldContent('canonical')) ?: ($seoPage?->canonical_url ?: url()->current());
    $headRobots = trim($__env->yieldContent('robots')) ?: ($seoPage?->robots ?: 'index,follow');
    $headOgTitle = trim($__env->yieldContent('og_title')) ?: ($seoPage?->og_title ?: $headTitle);
    $headOgDescription = trim($__env->yieldContent('og_description')) ?: ($seoPage?->og_description ?: $headDescription);
    $favicon = \App\Models\SiteSetting::getValue('favicon', 'favicon.svg') ?: 'favicon.svg';
    $appleTouchIcon = \App\Models\SiteSetting::getValue('apple_touch_icon');
    $faviconUrl = str_starts_with($favicon, 'http') || str_starts_with($favicon, '/') || $favicon === 'favicon.svg' ? asset($favicon) : asset('storage/'.$favicon);
    $socialImage = $seoPage?->og_image ?: \App\Models\SiteSetting::getValue('default_social_image') ?: (request()->routeIs('legal') ? config('public.hero_defaults.legal') : config('public.hero_defaults.default'));
    $socialImageData = \App\Support\PublicHero::candidate($socialImage);
    $socialImageUrl = $socialImageData['url'] ?? asset(config('public.hero_defaults.default'));
    $headOgImage = trim($__env->yieldContent('og_image')) ?: $socialImageUrl;
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
    <meta name="description" content="{{ $headDescription }}">
    <meta name="robots" content="{{ !empty($preview) ? 'noindex,nofollow,noarchive' : $headRobots }}">
    <link rel="canonical" href="{{ $headCanonical }}">
    <meta property="og:title" content="{{ $headOgTitle }}">
    <meta property="og:description" content="{{ $headOgDescription }}">
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="{{ $headOgImage }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $headOgTitle }}">
    <meta name="twitter:description" content="{{ $headOgDescription }}">
    <meta name="twitter:image" content="{{ $headOgImage }}">
    <link rel="icon" href="{{ $faviconUrl }}">
    @if($appleTouchIcon)<link rel="apple-touch-icon" href="{{ asset('storage/'.$appleTouchIcon) }}">@endif
    <title>{{ $headTitle }}</title>
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
        <div class="footer-contact"><span>Contact</span><a href="mailto:{{ $email }}">{{ $email }}</a><a href="tel:{{ preg_replace('/\D+/', '', $phone) }}">{{ $phone }}</a>@if($whatsapp)<a href="https://wa.me/{{ preg_replace('/\D+/', '', $whatsapp) }}" target="_blank" rel="noopener">WhatsApp {{ $whatsapp }}</a>@endif<p>{{ $location }}</p>@if($socialLinks->isNotEmpty())<div class="footer-social"><span>Follow</span>@foreach($socialLinks as $social)<a href="{{ $social->url }}" target="_blank" rel="noopener noreferrer">{{ $social->label }} <span aria-hidden="true">↗</span></a>@endforeach</div>@endif</div></div></div><div class="footer-bottom"><span>{{ $footerCopyright }}</span><div>@if(isset($footerNavigation['legal'])) @foreach($footerNavigation['legal'] as $item)<a href="{{ $item->href() }}">{{ $item->label }}</a>@endforeach @else<a href="{{ route('legal', 'privacy-policy') }}">Privacy</a><a href="{{ route('legal', 'terms') }}">Terms</a><a href="{{ route('legal', 'risk-disclosure') }}">Risk disclosure</a>@endif</div><strong>{{ $footerBottomStatement }}</strong></div><p class="site-disclaimer">{{ $disclaimer }}</p></div></footer>
</body>
</html>
