@php
$brandName = \App\Models\SiteSetting::getValue('brand_name', 'Mwanafunzi Investor');
$brandLogoLightUrl = \App\Support\BrandAssets::logo('light');
$brandLogoDarkUrl = \App\Support\BrandAssets::logo('dark');
$email = \App\Models\SiteSetting::getValue('contact_email', 'mwanafunziinvestor@outlook.com');
$phone = \App\Models\SiteSetting::getValue('contact_phone', '+255 787 172 686');
$whatsapp = \App\Models\SiteSetting::getValue('contact_whatsapp');
$location = \App\Models\SiteSetting::getValue('contact_location', 'Tanzania');
$footerCopy = \App\Models\SiteSetting::getValue('footer_copy', 'Student of Money.\nProbability. Systems. Discipline.');
$footerCopyright = \App\Models\SiteSetting::getValue('footer_copyright', '© '.date('Y').' '.$brandName);
$footerBottomStatement = \App\Models\SiteSetting::getValue('footer_bottom_statement', 'ALWAYS A MWANAFUNZI.');
$headerContactLabel = \App\Models\SiteSetting::getValue('header_contact_label', 'Contact Us');
$faviconUrl = asset('favicon.svg').'?v=favicon4';
$faviconPngUrl = asset('favicon-96x96.png').'?v=favicon4';
$appleTouchIconUrl = asset('apple-touch-icon.png').'?v=favicon4';
$socialImage = \App\Models\SiteSetting::getValue('default_social_image') ?: config('public.hero_defaults.home');
$socialImageData = \App\Support\PublicHero::candidate($socialImage);
$socialImageUrl = $socialImageData['url'] ?? asset(config('public.hero_defaults.home'));
$seoDescription = \App\Models\SiteSetting::getValue('default_seo_description', 'Financial education for systematic trading, probability, risk management and disciplined portfolio thinking.');
$riskDisclaimer = \App\Models\SiteSetting::getValue('risk_disclaimer', 'Educational content only. This is not personalised financial advice. Trading involves risk.');
$businessUnits = \App\Models\BusinessUnit::query()->active()->orderBy('sort_order')->get();
$headerNavigation = \App\Models\NavigationItem::with('children')->visible()->where('location', 'header')->whereNull('parent_id')->get();
$globalHeaderNavigation = $headerNavigation->reject(fn ($item) => $item->publicRouteName() === 'blog')->values();
$footerNavigation = \App\Models\NavigationItem::with('children')->visible()->where('location', 'footer')->whereNull('parent_id')->orderBy('menu_group')->get()->groupBy('menu_group');
$socialLinks = \App\Support\PublicSiteData::socialLinks();
$homeSections = $homePage?->sections?->keyBy('key') ?? collect();
$brandIntro = $homeSections->get('philosophy');
$divisions = $homeSections->get('divisions');
$learning = $homeSections->get('learning');
$probability = $homeSections->get('probability');
$discipline = $homeSections->get('discipline');
$framework = $homeSections->get('framework');
$toolsSection = $homeSections->get('tools');
$journal = $homeSections->get('journal');
$coursesSection = $homeSections->get('courses');
$finalCta = $homeSections->get('final_cta');
$homeFinalSecondaryUrl = $finalCta?->payload['secondary_cta_url'] ?? null;
if ($homeFinalSecondaryUrl === '#tools') $homeFinalSecondaryUrl = $toolsSection?->is_enabled ? route('tools') : null;
$serviceRoutes = ['forex' => route('forex-academy'), 'development' => route('digital-systems'), 'studio' => route('creative-studio')];
$serviceImages = ['forex' => config('public.hero_defaults.learn'), 'development' => config('public.hero_defaults.tools'), 'studio' => config('public.hero_defaults.about')];
$serviceEyebrows = ['forex' => 'Learn', 'development' => 'Build', 'studio' => 'Create'];
$homeTitleHtml = $homePage?->hero_title && $homePage?->hero_highlight ? e($homePage->hero_title).'<br> <em>'.e($homePage->hero_highlight).'</em>' : ($homePage?->hero_title ? null : 'Become a<br> <em>Student of Money.</em>');
$homeHeroPrimaryLabel = $homePage?->hero_primary_label;
$homeHeroPrimaryUrl = $homePage?->hero_primary_url;
$homeHeroSecondaryLabel = $homePage?->hero_secondary_label;
$homeHeroSecondaryUrl = $homePage?->hero_secondary_url;
if ($homeHeroSecondaryUrl === '#tools') $homeHeroSecondaryUrl = $toolsSection?->is_enabled ? route('tools') : null;
$homeHeroNote = $homePage?->hero_note;
$homeHeroAside = $homePage?->hero_aside;
$homeHeroAsideIndex = $homePage?->hero_aside_index;
$homeSeoTitle = $homePage?->seo_title ?: \App\Models\SiteSetting::getValue('default_seo_title');
$homeSeoDescription = $homePage?->seo_description ?: $seoDescription;
$homeCanonical = $homePage?->canonical_url ?: \App\Support\Seo::canonical('/');
$homeRobots = $homePage?->robots ?: 'index,follow';
$homeOgTitle = $homePage?->og_title ?: $homeSeoTitle;
$homeOgDescription = $homePage?->og_description ?: $homeSeoDescription;
$homeOgImage = $homePage?->og_image ? \App\Support\PublicHero::candidate($homePage->og_image) : null;
$homeOgImageUrl = $homeOgImage['url'] ?? $socialImageUrl;
@endphp
<!DOCTYPE html>
<html lang="en-TZ">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ \App\Support\Seo::description($homeSeoDescription) }}">
    <meta name="robots" content="{{ \App\Support\Seo::robots($homeRobots) }}">
    <link rel="canonical" href="{{ $homeCanonical }}">
    <meta name="theme-color" content="#171817">
    <meta property="og:title" content="{{ $homeOgTitle }}">
    <meta property="og:description" content="{{ $homeOgDescription }}">
    <meta property="og:type" content="website">
    <meta property="og:locale" content="en_TZ">
    <meta property="og:site_name" content="{{ $brandName }}">
    <meta property="og:url" content="{{ $homeCanonical }}">
    <meta property="og:image" content="{{ $homeOgImageUrl }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $homeOgTitle }}">
    <meta name="twitter:description" content="{{ $homeOgDescription }}">
    <meta name="twitter:image" content="{{ $homeOgImageUrl }}">
    @if($googleVerification = \App\Models\SiteSetting::getValue('google_site_verification'))<meta name="google-site-verification" content="{{ $googleVerification }}">@endif
    @if($bingVerification = \App\Models\SiteSetting::getValue('bing_site_verification'))<meta name="msvalidate.01" content="{{ $bingVerification }}">@endif
    <link rel="icon" href="{{ $faviconPngUrl }}" type="image/png" sizes="96x96">
    <link rel="icon" href="{{ $faviconUrl }}" type="image/svg+xml" sizes="any">
    <link rel="apple-touch-icon" href="{{ $appleTouchIconUrl }}" sizes="180x180">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}?v=favicon4">
    <link rel="alternate" type="application/rss+xml" title="Mwanafunzi Investor Blog" href="{{ route('feed') }}">
    <title>{{ $homeSeoTitle }}</title>
    @php($schemaContext = chr(64).'context')
    <script type="application/ld+json">{!! json_encode([$schemaContext => 'https://schema.org', '@type' => 'Organization', 'name' => $brandName, 'url' => \App\Support\Seo::siteUrl(), 'email' => $email, 'telephone' => $phone, 'address' => ['@type' => 'PostalAddress', 'addressCountry' => 'TZ']], JSON_UNESCAPED_SLASHES) !!}</script>
    <script type="application/ld+json">{!! json_encode([$schemaContext => 'https://schema.org', '@type' => 'WebSite', 'name' => $brandName, 'url' => \App\Support\Seo::siteUrl(), 'inLanguage' => 'en-TZ', 'publisher' => ['@type' => 'Organization', 'name' => $brandName]], JSON_UNESCAPED_SLASHES) !!}</script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('components.design-tokens')
</head>

<body>
    <a class="skip-link" href="#main-content">Skip to content</a>
    <header class="site-header" data-header>
        <div class="header-inner container">
            <a class="brand" href="#top" aria-label="{{ $brandName }} home"><img class="brand-image brand-logo-light" src="{{ $brandLogoLightUrl }}" alt="{{ $brandName }}"><img class="brand-image brand-logo-dark" src="{{ $brandLogoDarkUrl }}" alt=""></a>
            <nav class="desktop-nav" aria-label="Primary navigation">@forelse($globalHeaderNavigation as $item)<x-navigation-links :item="$item" />@if($item->publicRouteName() === 'about')<a class="{{ request()->routeIs('blog*') ? 'active' : '' }}" href="{{ route('blog') }}">Blog</a>@endif @empty @foreach($businessUnits as $businessUnit)<a class="{{ $businessUnit->slug === 'forex' ? 'active' : '' }}" href="{{ $serviceRoutes[$businessUnit->slug] ?? route($businessUnit->route_name) }}">{{ $businessUnit->name }}</a>@endforeach<a href="{{ route('about') }}">About</a><a class="{{ request()->routeIs('blog*') ? 'active' : '' }}" href="{{ route('blog') }}">Blog</a>@endforelse</nav>
            <div class="header-actions">@if(auth()->check() && ! auth()->user()->is_admin)<x-public-account-menu :user="auth()->user()" />@else<a class="header-contact header-client-link" href="{{ route('login') }}">Client login</a>@endif<a class="button button-small button-light" href="{{ route('contact') }}">{{ $headerContactLabel }}<span aria-hidden="true">↗</span></a><button class="menu-toggle" type="button" aria-expanded="false" aria-controls="mobile-menu" data-menu-toggle><span class="sr-only">Open menu</span><span></span><span></span></button></div>
        </div>
        <div class="mobile-menu" id="mobile-menu" data-mobile-menu>
            <nav aria-label="Mobile navigation">@forelse($globalHeaderNavigation as $item)<x-navigation-links :item="$item" />@if($item->publicRouteName() === 'about')<a class="{{ request()->routeIs('blog*') ? 'active' : '' }}" href="{{ route('blog') }}">Blog <span aria-hidden="true">↗</span></a>@endif @empty @foreach($businessUnits as $businessUnit)<a href="{{ $serviceRoutes[$businessUnit->slug] ?? route($businessUnit->route_name) }}">{{ $businessUnit->name }}</a>@endforeach<a href="{{ route('about') }}">About us</a><a class="{{ request()->routeIs('blog*') ? 'active' : '' }}" href="{{ route('blog') }}">Blog <span aria-hidden="true">↗</span></a>@endforelse<a class="mobile-client-link" href="{{ auth()->check() && ! auth()->user()->is_admin ? route('account.dashboard') : route('login') }}">{{ auth()->check() && ! auth()->user()->is_admin ? 'My dashboard' : 'Client login' }}</a></nav><a class="button button-dark" href="{{ route('contact') }}">{{ $headerContactLabel }} <span aria-hidden="true">↗</span></a>
        </div>
    </header>
    <main id="main-content" class="mother-home">
        <x-public-hero id="top" class="hero hero-home-hero" :eyebrow="$homePage?->hero_eyebrow ?: 'BUILD • LEARN • CREATE • INVEST'" :title="$homePage?->hero_title ?: 'Become a Student of Money.'" :title-html="$homeTitleHtml" :summary="$homePage?->hero_summary ?: 'Learn carefully. Build useful systems. Create work with a point of view.'" setting="hero_home_image" :fallback-image="config('public.hero_defaults.home')" :overlay="$homePage?->hero_overlay ?: 'strong'">
            <div class="hero-buttons">
                @if($homeHeroPrimaryLabel && $homeHeroPrimaryUrl)<a class="button button-accent" href="{{ \App\Support\Seo::resolve($homeHeroPrimaryUrl, '#') }}" target="{{ $homePage?->hero_primary_target ?: '_self' }}" @if(($homePage?->hero_primary_target ?: '_self') === '_blank') rel="noopener" @endif>{{ $homeHeroPrimaryLabel }} <span aria-hidden="true">↗</span></a>@endif
                @if($homeHeroSecondaryLabel && $homeHeroSecondaryUrl)<a class="text-link text-link-light" href="{{ \App\Support\Seo::resolve($homeHeroSecondaryUrl, '#') }}" target="{{ $homePage?->hero_secondary_target ?: '_self' }}" @if(($homePage?->hero_secondary_target ?: '_self') === '_blank') rel="noopener" @endif>{{ $homeHeroSecondaryLabel }} <span aria-hidden="true">→</span></a>@endif
            </div>
            @if($homeHeroNote)<p class="hero-note">{{ $homeHeroNote }}</p>@else<p class="hero-note">Systems <i></i> Learning <i></i> Creative work</p>@endif
            <x-slot:aside>
                <div class="hero-aside">
                    <div class="hero-aside-rule"></div>@if($homeHeroAside)<p>{!! nl2br(e($homeHeroAside)) !!}@else
                    <p>One mother brand.<br>Three ways to move.</p>@endif @if($homeHeroAsideIndex)<span>{{ $homeHeroAsideIndex }}</span>@else<span>01 / 03</span>@endif
                </div>
            </x-slot:aside>
        </x-public-hero>

        @if($brandIntro && $brandIntro->is_enabled && !empty($brandIntro->payload['cards']))
        <section class="mother-intro section" id="brand-intro">
            <div class="container mother-principles-strip">
                @foreach($brandIntro->payload['cards'] as $card)
                    <div>
                        <span>{{ $card['index'] ?? str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                        <strong>{{ $card['title'] ?? '' }}</strong>
                        <p>{{ $card['body'] ?? '' }}</p>
                    </div>
                @endforeach
            </div>
        </section>
        @endif

        @if($divisions && $divisions->is_enabled)
        <section class="mother-divisions section" id="{{ \App\Support\CmsContent::value($divisions, 'anchor_id', 'divisions') }}">
            <div class="container">
                <div class="split-heading">
                    <div>
                        <p class="eyebrow"><span class="eyebrow-line"></span>{{ \App\Support\CmsContent::value($divisions, 'eyebrow', 'The Mwanafunzi ecosystem') }}</p>
                        <h2>{{ $divisions->heading }}</h2>
                    </div>
                    @if($divisions->body)<p class="body-copy">{{ $divisions->body }}</p>@endif
                </div>
                <div class="mother-service-list">@foreach($businessUnits as $businessUnit)<x-service-preview :index="str_pad($loop->iteration, 2, '0', STR_PAD_LEFT)" :title="$businessUnit->name" :summary="$businessUnit->description" :href="$serviceRoutes[$businessUnit->slug] ?? route($businessUnit->route_name)" :image="$serviceImages[$businessUnit->slug] ?? config('public.hero_defaults.default')" :eyebrow="$serviceEyebrows[$businessUnit->slug] ?? 'Explore'" :tone="$businessUnit->slug" />@endforeach</div>
            </div>
        </section>
        @endif

        @if($featuredTestimonials->isNotEmpty())
        <section class="mother-home-proof section testimonial-section" id="client-perspective" aria-labelledby="client-perspective-heading">
            <div class="container">
                <div class="testimonial-section-heading">
                    <div>
                        <p class="eyebrow"><span class="eyebrow-line"></span>Client perspective</p>
                        <h2 id="client-perspective-heading">What the work felt like from the other side.</h2>
                    </div>
                    <p class="testimonial-section-description">Approved perspectives from work made with real people, real constraints and a clear purpose.</p>
                </div>
                @include('partials.testimonial-carousel', ['testimonials' => $featuredTestimonials, 'carouselId' => 'home-testimonials'])
            </div>
        </section>
        @endif

        @if($learning && $learning->is_enabled)
        @include('public.home.learning', ['section' => $learning, 'learningTopics' => $learningTopics])
        @endif

        @if($probability && $probability->is_enabled)
        @include('public.home.probability', ['section' => $probability])
        @endif

        @if($discipline && $discipline->is_enabled)
        @include('public.home.discipline', ['section' => $discipline])
        @endif

        @if($toolsSection && $toolsSection->is_enabled)
        @include('public.home.tools', ['section' => $toolsSection, 'products' => $products])
        @endif

        @if($framework && $framework->is_enabled)
        @include('public.home.framework', ['section' => $framework])
        @endif

        @if($journal && $journal->is_enabled)
        @include('public.home.journal', ['section' => $journal, 'featuredArticle' => $featuredArticle])
        @endif

        @if($coursesSection && $coursesSection->is_enabled)
        @include('public.home.courses', ['section' => $coursesSection, 'courses' => $courses])
        @endif

        @if($finalCta && $finalCta->is_enabled)
        <section class="mother-final-cta final-cta">
            <div class="container final-cta-inner">
                <p class="eyebrow eyebrow-light"><span class="eyebrow-line"></span>{{ $finalCta->payload['eyebrow'] ?? 'Come back to the process' }}</p>
                <h2>{{ $finalCta->heading }}</h2>
                <p>{{ $finalCta->body }}</p>
                <div class="hero-buttons">@if($finalCta->cta_label && $finalCta->cta_url)<a class="button button-accent" href="{{ \App\Support\Seo::resolve($finalCta->cta_url, '#') }}" target="{{ \App\Support\CmsContent::value($finalCta, 'cta_target', '_self') }}" @if(\App\Support\CmsContent::value($finalCta, 'cta_target', '_self') === '_blank') rel="noopener" @endif>{{ $finalCta->cta_label }} <span aria-hidden="true">↗</span></a>@endif @if(!empty($finalCta->payload['secondary_cta_label']) && $homeFinalSecondaryUrl)<a class="text-link text-link-light" href="{{ $homeFinalSecondaryUrl }}">{{ $finalCta->payload['secondary_cta_label'] }} <span aria-hidden="true">→</span></a>@endif</div>
            </div>
        </section>
        @endif
    </main>
    @include('partials.public-footer')
    @include('partials.cookie-consent')
    @include('partials.tawk-widget')
    <div class="toast" role="status" aria-live="polite" data-toast></div>
</body>

</html>
