@php
    $brandName = \App\Models\SiteSetting::getValue('brand_name', 'Mwanafunzi Investor');
    $brandLogo = \App\Models\SiteSetting::getValue('logo');
    $brandLogoData = $brandLogo ? \App\Support\PublicHero::candidate($brandLogo) : null;
    $brandLogoDarkUrl = asset('images/brand/mwanafunzi-logo-dark.png');
    $brandLogoLightUrl = asset('images/brand/mwanafunzi-logo-light.png');
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
        'forex-academy' => 'learn',
        'digital-software', 'digital-software.products', 'digital-software.projects', 'digital-software.testimonials', 'digital-systems' => 'development',
        'creative-studio', 'creative-studio.services', 'creative-studio.projects', 'creative-studio.testimonials' => 'studio',
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
    $appleTouchIcon = \App\Models\SiteSetting::getValue('apple_touch_icon');
    $faviconUrl = asset('favicon.svg');
    $socialImage = $seoPage?->og_image ?: \App\Models\SiteSetting::getValue('default_social_image') ?: (request()->routeIs('legal') ? config('public.hero_defaults.legal') : config('public.hero_defaults.default'));
    $socialImageData = \App\Support\PublicHero::candidate($socialImage);
    $socialImageUrl = $socialImageData['url'] ?? asset(config('public.hero_defaults.default'));
    $headOgImage = trim($__env->yieldContent('og_image')) ?: $socialImageUrl;
    $businessUnits = \App\Support\PublicSiteData::businessUnits();
    $serviceRoutes = ['forex' => route('forex-academy'), 'development' => route('digital-systems'), 'studio' => route('creative-studio')];
    $serviceContext = trim($__env->yieldContent('service_context'));
    if ($serviceContext === '') {
        $serviceContext = request()->is('financial-academy*') ? 'forex' : (request()->is('digital-software*', 'development*') ? 'development' : (request()->is('creative-studio*', 'studio*') ? 'studio' : ''));
    }
    $serviceHomeUrl = ['forex' => route('forex-academy'), 'development' => route('digital-systems'), 'studio' => route('creative-studio')][$serviceContext] ?? route('home');
    $serviceContactUrl = $serviceContext ? route('contact', ['module' => $serviceContext]) : route('contact');
    $headerNavigation = \App\Support\PublicSiteData::headerNavigation();
    $hasJournalNavigation = $headerNavigation->contains(function ($item) {
        return in_array(strtolower(trim((string) $item->label)), ['blog', 'journal'], true)
            || (string) $item->route_name === 'journal'
            || rtrim((string) $item->url, '/') === '/journal';
    });
    $footerNavigation = \App\Support\PublicSiteData::footerNavigation();
    $socialLinks = \App\Support\PublicSiteData::socialLinks();
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
    <meta property="og:url" content="{{ $headCanonical }}">
    <meta property="og:image" content="{{ $headOgImage }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $headOgTitle }}">
    <meta name="twitter:description" content="{{ $headOgDescription }}">
    <meta name="twitter:image" content="{{ $headOgImage }}">
    <link rel="icon" href="{{ $faviconUrl }}">
    <link rel="alternate" type="application/rss+xml" title="Mwanafunzi Investor Journal" href="{{ route('feed') }}">
    @if($appleTouchIcon)<link rel="apple-touch-icon" href="{{ asset('storage/'.$appleTouchIcon) }}">@endif
    <title>{{ $headTitle }}</title>
    @yield('article_meta')
    @php($schemaContext = chr(64).'context')
    <script type="application/ld+json">{!! json_encode([$schemaContext => 'https://schema.org', '@type' => 'Organization', 'name' => $brandName, 'url' => url('/'), 'email' => $email, 'telephone' => $phone, 'address' => ['@type' => 'PostalAddress', 'addressCountry' => 'TZ']], JSON_UNESCAPED_SLASHES) !!}</script>
    @yield('structured_data')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('components.design-tokens')
</head>
<body class="internal-page @yield('body_class'){{ $serviceContext ? ' service-context-'.$serviceContext : '' }}">
    @if(!empty($preview))<div class="preview-banner" role="status">Private draft preview · This link expires automatically and is not publicly discoverable.</div>@endif
    <a class="skip-link" href="#main-content">Skip to content</a>
    <header class="site-header internal-header" data-header>
        <div class="header-inner container">
            <a class="brand" href="{{ $serviceHomeUrl }}" aria-label="{{ $serviceContext ? ucfirst($serviceContext).' home' : $brandName.' home' }}"><img class="brand-image brand-logo-dark" src="{{ $brandLogoDarkUrl }}" alt="{{ $brandName }}"><img class="brand-image brand-logo-light" src="{{ $brandLogoLightUrl }}" alt=""></a>
            @if(request()->routeIs('login', 'register'))
                <div class="header-actions"><a class="header-contact auth-back-link" href="{{ route('home') }}">Back to website <span aria-hidden="true">↗</span></a></div>
            @else
                <nav class="desktop-nav {{ $serviceContext ? 'service-desktop-nav' : '' }}" aria-label="{{ $serviceContext ? 'Service navigation' : 'Primary navigation' }}">
                    @if($serviceContext)
                        @include('partials.service-navigation', ['serviceContext' => $serviceContext])
                    @elseif($headerNavigation->isNotEmpty())
                        @foreach($headerNavigation as $item)<x-navigation-links :item="$item" />@endforeach
                    @else
                        @foreach($businessUnits as $businessUnit)<a class="{{ request()->routeIs($businessUnit->route_name, $serviceRoutes[$businessUnit->slug] ?? null) ? 'active' : '' }}" href="{{ $serviceRoutes[$businessUnit->slug] ?? route($businessUnit->route_name) }}">{{ $businessUnit->name }}</a>@endforeach<a href="{{ route('about') }}">About</a>
                    @endif
                    @unless($hasJournalNavigation)<a class="{{ request()->routeIs('journal*') ? 'active' : '' }}" href="{{ route('journal') }}">Blog</a>@endunless
                </nav>
                <div class="header-actions"><a class="header-contact header-client-link {{ request()->routeIs('login') ? 'active' : '' }}" href="{{ auth()->check() ? route('account.dashboard') : route('login') }}">{{ auth()->check() ? 'Client portal' : 'Client login' }}</a><a class="button button-small button-light" href="{{ $serviceContactUrl }}">{{ $serviceContext === 'studio' ? 'Plan a shoot' : ($serviceContext === 'development' ? 'Start a project' : 'Start a conversation') }} <span aria-hidden="true">↗</span></a><button class="menu-toggle" type="button" aria-expanded="false" aria-controls="mobile-menu" data-menu-toggle><span class="sr-only">Open menu</span><span></span><span></span></button></div>
            @endif
        </div>
        @if(!request()->routeIs('login', 'register'))
            <div class="mobile-menu" id="mobile-menu" data-mobile-menu><nav aria-label="{{ $serviceContext ? 'Service navigation' : 'Mobile navigation' }}">
                @if($serviceContext)
                    @include('partials.service-navigation', ['serviceContext' => $serviceContext])
                @elseif($headerNavigation->isNotEmpty())
                    @foreach($headerNavigation as $item)<x-navigation-links :item="$item" />@endforeach
                @else
                    @foreach($businessUnits as $businessUnit)<a href="{{ $serviceRoutes[$businessUnit->slug] ?? route($businessUnit->route_name) }}">{{ $businessUnit->name }} <span>{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span></a>@endforeach<a href="{{ route('about') }}">About <span>{{ str_pad($businessUnits->count() + 1, 2, '0', STR_PAD_LEFT) }}</span></a>
                @endif
                @unless($hasJournalNavigation)<a class="{{ request()->routeIs('journal*') ? 'active' : '' }}" href="{{ route('journal') }}">Blog <span aria-hidden="true">↗</span></a>@endunless
                <a class="mobile-client-link" href="{{ auth()->check() ? route('account.dashboard') : route('login') }}">{{ auth()->check() ? 'Client portal' : 'Client login' }}</a>
            </nav><a class="button button-dark" href="{{ $serviceContactUrl }}">{{ $serviceContext === 'studio' ? 'Plan a shoot' : ($serviceContext === 'development' ? 'Start a project' : 'Start a conversation') }} <span aria-hidden="true">↗</span></a></div>
        @endif
    </header>
    <main id="main-content">@yield('content')</main>
    @include('partials.public-footer')
</body>
</html>
