@php
    $brandName = \App\Models\SiteSetting::getValue('brand_name', 'Mwanafunzi Investor');
    $brandLogo = \App\Models\SiteSetting::getValue('logo');
    $brandLogoData = $brandLogo ? \App\Support\PublicHero::candidate($brandLogo) : null;
    $brandLogoDarkUrl = asset('images/brand/mwanafunzi-logo-dark.webp');
    $brandLogoLightUrl = asset('images/brand/mwanafunzi-logo-light.webp');
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
    $headTitle = \App\Support\Seo::title(trim($__env->yieldContent('title')), $seoPage?->seo_title ?: $brandName);
    $headDescription = \App\Support\Seo::description(trim($__env->yieldContent('description')), $seoPage?->seo_description ?: \App\Models\SiteSetting::getValue('default_seo_description', config('seo.default_description')));
    $headCanonical = \App\Support\Seo::resolve(trim($__env->yieldContent('canonical')) ?: ($seoPage?->canonical_url ?: null), \App\Support\Seo::canonical(request()->path()));
    $headRobots = \App\Support\Seo::robots(trim($__env->yieldContent('robots')) ?: ($seoPage?->robots ?: null), !empty($preview));
    $headOgTitle = trim($__env->yieldContent('og_title')) ?: ($seoPage?->og_title ?: $headTitle);
    $headOgDescription = trim($__env->yieldContent('og_description')) ?: ($seoPage?->og_description ?: $headDescription);
    $faviconUrl = asset('favicon.svg').'?v=favicon4';
    $faviconPngUrl = asset('favicon-96x96.png').'?v=favicon4';
    $appleTouchIconUrl = asset('apple-touch-icon.png').'?v=favicon4';
    $socialImage = $seoPage?->og_image ?: \App\Models\SiteSetting::getValue('default_social_image') ?: (request()->routeIs('legal') ? config('public.hero_defaults.legal') : config('public.hero_defaults.default'));
    $socialImageData = \App\Support\PublicHero::candidate($socialImage);
    $socialImageUrl = $socialImageData['url'] ?? asset(config('public.hero_defaults.default'));
    $headOgImage = trim($__env->yieldContent('og_image')) ?: $socialImageUrl;
    $googleVerification = \App\Models\SiteSetting::getValue('google_site_verification');
    $bingVerification = \App\Models\SiteSetting::getValue('bing_site_verification');
    $businessUnits = \App\Support\PublicSiteData::businessUnits();
    $serviceRoutes = ['forex' => route('forex-academy'), 'development' => route('digital-systems'), 'studio' => route('creative-studio')];
    $serviceContext = trim($__env->yieldContent('service_context'));
    if ($serviceContext === '') {
        $serviceContext = request()->is('financial-academy*') ? 'forex' : (request()->is('digital-software*', 'development*') ? 'development' : (request()->is('creative-studio*', 'studio*') ? 'studio' : ''));
    }
    $serviceHomeUrl = ['forex' => route('forex-academy'), 'development' => route('digital-systems'), 'studio' => route('creative-studio')][$serviceContext] ?? route('home');
    $serviceContactUrl = $serviceContext ? route('contact', ['module' => $serviceContext]) : route('contact');
    $headerNavigation = \App\Support\PublicSiteData::headerNavigation();
    /* Keep Blog as a canonical global header link, immediately after About. */
    $globalHeaderNavigation = $headerNavigation->reject(fn ($item) => $item->publicRouteName() === 'blog')->values();
    $footerNavigation = \App\Support\PublicSiteData::footerNavigation();
    $socialLinks = \App\Support\PublicSiteData::socialLinks();
@endphp
<!DOCTYPE html>
<html lang="en-TZ">
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
    <link rel="icon" href="{{ $faviconPngUrl }}" type="image/png" sizes="96x96">
    <link rel="icon" href="{{ $faviconUrl }}" type="image/svg+xml" sizes="any">
    <link rel="apple-touch-icon" href="{{ $appleTouchIconUrl }}" sizes="180x180">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}?v=favicon4">
    <link rel="alternate" type="application/rss+xml" title="Mwanafunzi Investor Blog" href="{{ route('feed') }}">
    <title>{{ $headTitle }}</title>
    @yield('article_meta')
    <meta property="og:site_name" content="{{ $brandName }}">
    <meta property="og:locale" content="{{ config('seo.locale', 'en_TZ') }}">
    @if($googleVerification)<meta name="google-site-verification" content="{{ $googleVerification }}">@endif
    @if($bingVerification)<meta name="msvalidate.01" content="{{ $bingVerification }}">@endif
    @php($schemaContext = chr(64).'context')
    @php($organizationSchema = [$schemaContext => 'https://schema.org', '@type' => 'Organization', 'name' => $brandName, 'url' => \App\Support\Seo::siteUrl(), 'email' => $email, 'telephone' => $phone, 'address' => ['@type' => 'PostalAddress', 'addressCountry' => 'TZ']])
    @php($websiteSchema = [$schemaContext => 'https://schema.org', '@type' => 'WebSite', 'name' => $brandName, 'url' => \App\Support\Seo::siteUrl(), 'inLanguage' => 'en-TZ', 'publisher' => ['@type' => 'Organization', 'name' => $brandName, 'url' => \App\Support\Seo::siteUrl()]])
    <script type="application/ld+json">{!! json_encode($organizationSchema, JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}</script>
    @if(request()->routeIs('home'))<script type="application/ld+json">{!! json_encode($websiteSchema, JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}</script>@endif
    @yield('structured_data')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @php($recaptcha = app(\App\Services\RecaptchaService::class))
    @if($recaptcha->isEnabled() && filled($recaptcha->siteKey()))
        <script>window.mwanafunziRecaptcha = { siteKey: @js($recaptcha->siteKey()) };</script>
        <script src="https://www.google.com/recaptcha/api.js?render={{ urlencode($recaptcha->siteKey()) }}" async defer></script>
    @endif
    @include('components.design-tokens')
</head>
<body class="internal-page @yield('body_class'){{ $serviceContext ? ' service-context-'.$serviceContext : '' }}">
    @if(!empty($preview))<div class="preview-banner" role="status">Private draft preview · This link expires automatically and is not publicly discoverable.</div>@endif
    <a class="skip-link" href="#main-content">Skip to content</a>
    <header class="site-header internal-header" data-header>
        <div class="header-inner container">
            <a class="brand" href="{{ $serviceHomeUrl }}" aria-label="{{ $serviceContext ? ucfirst($serviceContext).' home' : $brandName.' home' }}"><img class="brand-image brand-logo-dark" src="{{ $brandLogoDarkUrl }}" alt="{{ $brandName }}"><img class="brand-image brand-logo-light" src="{{ $brandLogoLightUrl }}" alt=""></a>
            <nav class="desktop-nav {{ $serviceContext ? 'service-desktop-nav' : '' }}" aria-label="{{ $serviceContext ? 'Service navigation' : 'Primary navigation' }}">
                @if($serviceContext)
                    @include('partials.service-navigation', ['serviceContext' => $serviceContext])
                @elseif($globalHeaderNavigation->isNotEmpty())
                    @foreach($globalHeaderNavigation as $item)
                        <x-navigation-links :item="$item" />
                        @if($item->publicRouteName() === 'about')<a class="{{ request()->routeIs('blog*') ? 'active' : '' }}" href="{{ route('blog') }}">Blog</a>@endif
                    @endforeach
                    @unless($globalHeaderNavigation->contains(fn ($item) => $item->publicRouteName() === 'about'))<a class="{{ request()->routeIs('blog*') ? 'active' : '' }}" href="{{ route('blog') }}">Blog</a>@endunless
                @else
                    @foreach($businessUnits as $businessUnit)<a class="{{ request()->routeIs($businessUnit->route_name, $serviceRoutes[$businessUnit->slug] ?? null) ? 'active' : '' }}" href="{{ $serviceRoutes[$businessUnit->slug] ?? route($businessUnit->route_name) }}">{{ $businessUnit->name }}</a>@endforeach<a href="{{ route('about') }}">About</a><a class="{{ request()->routeIs('blog*') ? 'active' : '' }}" href="{{ route('blog') }}">Blog</a>
                @endif
            </nav>
            <div class="header-actions">@if(auth()->check() && ! auth()->user()->is_admin)<x-public-account-menu :user="auth()->user()" />@else<a class="header-contact header-client-link {{ request()->routeIs('login') ? 'active' : '' }}" href="{{ route('login') }}">Client login</a>@endif<a class="button button-small button-light" href="{{ $serviceContactUrl }}">Contact us <span aria-hidden="true">↗</span></a><button class="menu-toggle" type="button" aria-expanded="false" aria-controls="mobile-menu" data-menu-toggle><span class="sr-only">Open menu</span><span></span><span></span></button></div>
        </div>
        <div class="mobile-menu" id="mobile-menu" data-mobile-menu><nav aria-label="{{ $serviceContext ? 'Service navigation' : 'Mobile navigation' }}">
                @if($serviceContext)
                    @include('partials.service-navigation', ['serviceContext' => $serviceContext])
                @elseif($globalHeaderNavigation->isNotEmpty())
                    @foreach($globalHeaderNavigation as $item)
                        <x-navigation-links :item="$item" />
                        @if($item->publicRouteName() === 'about')<a class="{{ request()->routeIs('blog*') ? 'active' : '' }}" href="{{ route('blog') }}">Blog <span aria-hidden="true">↗</span></a>@endif
                    @endforeach
                    @unless($globalHeaderNavigation->contains(fn ($item) => $item->publicRouteName() === 'about'))<a class="{{ request()->routeIs('blog*') ? 'active' : '' }}" href="{{ route('blog') }}">Blog <span aria-hidden="true">↗</span></a>@endunless
                @else
                    @foreach($businessUnits as $businessUnit)<a href="{{ $serviceRoutes[$businessUnit->slug] ?? route($businessUnit->route_name) }}">{{ $businessUnit->name }} <span>{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span></a>@endforeach<a href="{{ route('about') }}">About <span>{{ str_pad($businessUnits->count() + 1, 2, '0', STR_PAD_LEFT) }}</span></a><a class="{{ request()->routeIs('blog*') ? 'active' : '' }}" href="{{ route('blog') }}">Blog <span aria-hidden="true">↗</span></a>
                @endif
                <a class="mobile-client-link" href="{{ auth()->check() && ! auth()->user()->is_admin ? route('account.dashboard') : route('login') }}">{{ auth()->check() && ! auth()->user()->is_admin ? 'My dashboard' : 'Client login' }}</a>
            </nav><a class="button button-dark" href="{{ $serviceContactUrl }}">Contact us <span aria-hidden="true">↗</span></a></div>
    </header>
    <main id="main-content">@yield('content')</main>
    @include('partials.public-footer')
    @include('partials.cookie-consent')
    @include('partials.tawk-widget')
</body>
</html>
