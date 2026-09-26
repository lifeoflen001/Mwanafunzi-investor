@php
    $brandName = \App\Models\SiteSetting::getValue('brand_name', 'Mwanafunzi Investor');
    $brandLogo = \App\Models\SiteSetting::getValue('logo');
    $brandLogoData = $brandLogo ? \App\Support\PublicHero::candidate($brandLogo) : null;
    $email = \App\Models\SiteSetting::getValue('contact_email', 'mwanafunziinvestor@outlook.com');
    $phone = \App\Models\SiteSetting::getValue('contact_phone', '+255 787 172 686');
    $whatsapp = \App\Models\SiteSetting::getValue('contact_whatsapp');
    $location = \App\Models\SiteSetting::getValue('contact_location', 'Tanzania');
    $footerCopy = \App\Models\SiteSetting::getValue('footer_copy', 'Student of Money.\nProbability. Systems. Discipline.');
    $footerCopyright = \App\Models\SiteSetting::getValue('footer_copyright', '© '.date('Y').' '.$brandName);
    $footerBottomStatement = \App\Models\SiteSetting::getValue('footer_bottom_statement', 'ALWAYS A MWANAFUNZI.');
    $favicon = \App\Models\SiteSetting::getValue('favicon', 'favicon.svg') ?: 'favicon.svg';
    $faviconUrl = str_starts_with($favicon, 'http') || str_starts_with($favicon, '/') || $favicon === 'favicon.svg' ? asset($favicon) : asset('storage/'.$favicon);
    $appleTouchIcon = \App\Models\SiteSetting::getValue('apple_touch_icon');
    $socialImage = \App\Models\SiteSetting::getValue('default_social_image') ?: config('public.hero_defaults.home');
    $socialImageData = \App\Support\PublicHero::candidate($socialImage);
    $socialImageUrl = $socialImageData['url'] ?? asset(config('public.hero_defaults.home'));
    $seoDescription = \App\Models\SiteSetting::getValue('default_seo_description', 'Financial education for systematic trading, probability, risk management and disciplined portfolio thinking.');
    $riskDisclaimer = \App\Models\SiteSetting::getValue('risk_disclaimer', 'Educational content only. This is not personalised financial advice. Trading involves risk.');
    $businessUnits = \App\Models\BusinessUnit::query()->active()->orderBy('sort_order')->get();
    $headerNavigation = \App\Models\NavigationItem::with('children')->visible()->where('location', 'header')->whereNull('parent_id')->get();
    $footerNavigation = \App\Models\NavigationItem::with('children')->visible()->where('location', 'footer')->whereNull('parent_id')->orderBy('menu_group')->get()->groupBy('menu_group');
    $socialLinks = \App\Models\SocialLink::query()->where('is_visible', true)->orderBy('sort_order')->get();
    $homeSections = $homePage?->sections?->keyBy('key') ?? collect();
    $brandIntro = $homeSections->get('philosophy');
    $framework = $homeSections->get('framework');
    $finalCta = $homeSections->get('final_cta');
    $serviceRoutes = ['forex' => route('forex-academy'), 'development' => route('digital-systems'), 'studio' => route('creative-studio')];
    $serviceImages = ['forex' => config('public.hero_defaults.learn'), 'development' => config('public.hero_defaults.tools'), 'studio' => config('public.hero_defaults.about')];
    $serviceEyebrows = ['forex' => 'Learn', 'development' => 'Build', 'studio' => 'Create'];
    $homeTitleHtml = $homePage?->hero_title && $homePage?->hero_highlight ? e($homePage->hero_title).'<br><em>'.e($homePage->hero_highlight).'</em>' : ($homePage?->hero_title ? null : 'Become a<br><em>Student of Money.</em>');
    $homeHeroPrimaryLabel = $homePage?->hero_primary_label;
    $homeHeroPrimaryUrl = $homePage?->hero_primary_url;
    $homeHeroSecondaryLabel = $homePage?->hero_secondary_label;
    $homeHeroSecondaryUrl = $homePage?->hero_secondary_url;
    $homeHeroNote = $homePage?->hero_note;
    $homeHeroAside = $homePage?->hero_aside;
    $homeHeroAsideIndex = $homePage?->hero_aside_index;
    $homeSeoTitle = $homePage?->seo_title ?: \App\Models\SiteSetting::getValue('default_seo_title');
    $homeSeoDescription = $homePage?->seo_description ?: $seoDescription;
    $homeCanonical = $homePage?->canonical_url ?: route('home');
    $homeRobots = $homePage?->robots ?: 'index,follow';
    $homeOgTitle = $homePage?->og_title ?: $homeSeoTitle;
    $homeOgDescription = $homePage?->og_description ?: $homeSeoDescription;
    $homeOgImage = $homePage?->og_image ? \App\Support\PublicHero::candidate($homePage->og_image) : null;
    $homeOgImageUrl = $homeOgImage['url'] ?? $socialImageUrl;
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ $homeSeoDescription }}">
    <meta name="robots" content="{{ $homeRobots }}">
    <link rel="canonical" href="{{ $homeCanonical }}">
    <meta name="theme-color" content="#171817">
    <meta property="og:title" content="{{ $homeOgTitle }}">
    <meta property="og:description" content="{{ $homeOgDescription }}">
    <meta property="og:type" content="website">
    <meta property="og:locale" content="en_TZ">
    <meta property="og:image" content="{{ $homeOgImageUrl }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $homeOgTitle }}">
    <meta name="twitter:description" content="{{ $homeOgDescription }}">
    <meta name="twitter:image" content="{{ $homeOgImageUrl }}">
    <link rel="icon" href="{{ $faviconUrl }}">
    @if($appleTouchIcon)<link rel="apple-touch-icon" href="{{ asset('storage/'.$appleTouchIcon) }}">@endif
    <title>{{ $homeSeoTitle }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('components.design-tokens')
</head>
<body>
    <a class="skip-link" href="#main-content">Skip to content</a>
    <header class="site-header" data-header>
        <div class="header-inner container">
            <a class="brand" href="#top" aria-label="{{ $brandName }} home">@if($brandLogoData)<img class="brand-image" src="{{ $brandLogoData['url'] }}" alt="{{ $brandName }}">@else<span class="brand-mark" aria-hidden="true"><span></span><span></span><span></span></span><span class="brand-copy"><strong>MWANAFUNZI</strong><small>INVESTOR</small></span>@endif</a>
            <nav class="desktop-nav" aria-label="Primary navigation">@forelse($headerNavigation as $item)<x-navigation-links :item="$item" />@empty @foreach($businessUnits as $businessUnit)<a class="{{ $businessUnit->slug === 'forex' ? 'active' : '' }}" href="{{ $serviceRoutes[$businessUnit->slug] ?? route($businessUnit->route_name) }}">{{ $businessUnit->name }}</a>@endforeach<a href="{{ route('about') }}">About</a>@endforelse</nav>
            <div class="header-actions"><a class="header-contact" href="{{ route('contact') }}">Contact</a><a class="button button-small button-light" href="{{ route('contact') }}">Start a conversation <span aria-hidden="true">↗</span></a><button class="menu-toggle" type="button" aria-expanded="false" aria-controls="mobile-menu" data-menu-toggle><span class="sr-only">Open menu</span><span></span><span></span></button></div>
        </div>
        <div class="mobile-menu" id="mobile-menu" data-mobile-menu><nav aria-label="Mobile navigation">@forelse($headerNavigation as $item)<x-navigation-links :item="$item" />@empty @foreach($businessUnits as $businessUnit)<a href="{{ $serviceRoutes[$businessUnit->slug] ?? route($businessUnit->route_name) }}">{{ $businessUnit->name }}</a>@endforeach<a href="{{ route('about') }}">About</a>@endforelse</nav><a class="button button-dark" href="{{ route('contact') }}">Contact the desk <span aria-hidden="true">↗</span></a></div>
    </header>
    <main id="main-content" class="mother-home">
        <x-public-hero id="top" class="hero hero-home-hero" :eyebrow="$homePage?->hero_eyebrow ?: 'BUILD • LEARN • CREATE • INVEST'" :title="$homePage?->hero_title ?: 'Become a Student of Money.'" :title-html="$homeTitleHtml" :summary="$homePage?->hero_summary ?: 'Learn carefully. Build useful systems. Create work with a point of view.'" setting="hero_home_image" :fallback-image="config('public.hero_defaults.home')" :overlay="$homePage?->hero_overlay ?: 'strong'">
            <div class="hero-buttons">
                @if($homeHeroPrimaryLabel && $homeHeroPrimaryUrl)<a class="button button-accent" href="{{ $homeHeroPrimaryUrl }}">{{ $homeHeroPrimaryLabel }} <span aria-hidden="true">↗</span></a>@else<a class="button button-accent" href="{{ route('forex-academy') }}">Start Learning <span aria-hidden="true">↗</span></a>@endif
                @if($homeHeroSecondaryLabel && $homeHeroSecondaryUrl)<a class="text-link text-link-light" href="{{ $homeHeroSecondaryUrl }}">{{ $homeHeroSecondaryLabel }} <span aria-hidden="true">→</span></a>@else<a class="text-link text-link-light" href="#divisions">Explore What I Build <span aria-hidden="true">→</span></a>@endif
            </div>
            @if($homeHeroNote)<p class="hero-note">{{ $homeHeroNote }}</p>@else<p class="hero-note">Systems <i></i> Learning <i></i> Creative work</p>@endif
            <x-slot:aside><div class="hero-aside"><div class="hero-aside-rule"></div>@if($homeHeroAside)<p>{!! nl2br(e($homeHeroAside)) !!}@else<p>One mother brand.<br>Three ways to move.</p>@endif @if($homeHeroAsideIndex)<span>{{ $homeHeroAsideIndex }}</span>@else<span>01 / 03</span>@endif</div></x-slot:aside>
            <a class="scroll-cue" href="#brand-intro"><span>Scroll to explore</span><i aria-hidden="true">↓</i></a>
        </x-public-hero>

        @if($brandIntro && $brandIntro->is_enabled)
            <section class="mother-intro section" id="brand-intro"><div class="container mother-intro-grid"><div><p class="eyebrow"><span class="eyebrow-line"></span>{{ $brandIntro->payload['eyebrow'] ?? 'Mwanafunzi' }}</p><h2>{{ $brandIntro->heading }}</h2></div><div class="body-copy rich-copy">{!! \App\Support\RichText::render($brandIntro->body) !!}@if($brandIntro->cta_label && $brandIntro->cta_url)<a class="text-link" href="{{ $brandIntro->cta_url }}">{{ $brandIntro->cta_label }} <span aria-hidden="true">→</span></a>@endif</div></div>@if(!empty($brandIntro->payload['cards']))<div class="container mother-principles-strip">@foreach($brandIntro->payload['cards'] as $card)<div><span>{{ $card['index'] ?? str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span><strong>{{ $card['title'] ?? '' }}</strong><p>{{ $card['body'] ?? '' }}</p></div>@endforeach</div>@endif</section>
        @endif
        <section class="mother-divisions section" id="divisions"><div class="container"><div class="split-heading"><div><p class="eyebrow"><span class="eyebrow-line"></span>The Mwanafunzi ecosystem</p><h2>Choose where the work <em>takes you.</em></h2></div><p class="body-copy">One brand, three focused divisions. Start with the part of the work that matters most to you now.</p></div><div class="mother-service-list">@foreach($businessUnits as $businessUnit)<x-service-preview :index="str_pad($loop->iteration, 2, '0', STR_PAD_LEFT)" :title="$businessUnit->name" :summary="$businessUnit->description" :href="$serviceRoutes[$businessUnit->slug] ?? route($businessUnit->route_name)" :image="$serviceImages[$businessUnit->slug] ?? config('public.hero_defaults.default')" :eyebrow="$serviceEyebrows[$businessUnit->slug] ?? 'Explore'" :tone="$businessUnit->slug" />@endforeach</div></div></section>

        @php($toolsSection = $homeSections->get('tools'))
        @if($toolsSection && $toolsSection->is_enabled)
            <section class="mother-proof section" id="proof"><div class="container"><div class="split-heading"><div><p class="eyebrow"><span class="eyebrow-line"></span>{{ $toolsSection->payload['eyebrow'] ?? 'Selected work and tools' }}</p><h2>{{ $toolsSection->heading }}</h2></div><p class="body-copy">{{ $toolsSection->body }}</p></div><div class="mother-proof-grid">@foreach($products->take(3) as $product)<a class="mother-proof-item" href="{{ route('tools.show', $product) }}"><span>{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span><div><h3>{{ $product->name }}</h3><p>{{ $product->short_description }}</p></div><b aria-hidden="true">↗</b></a>@endforeach @if($featuredArticle)<a class="mother-proof-item mother-proof-note" href="{{ route('journal.show', $featuredArticle) }}"><span>NOTE</span><div><h3>{{ $featuredArticle->title }}</h3><p>{{ $featuredArticle->excerpt }}</p></div><b aria-hidden="true">↗</b></a>@endif</div></div></section>
        @endif

        @if($framework && $framework->is_enabled)
            @include('public.home.framework', ['section' => $framework])
        @endif

        @php($principleSection = $homeSections->get('probability'))
        @if($principleSection && $principleSection->is_enabled)<section class="mother-principle section"><div class="container two-column"><div><p class="eyebrow"><span class="eyebrow-line"></span>{{ $principleSection->payload['eyebrow'] ?? 'Why Mwanafunzi' }}</p><h2>{{ $principleSection->heading }}</h2></div><div class="body-copy rich-copy">{!! \App\Support\RichText::render($principleSection->body) !!}</div></div></section>@endif

        @if($finalCta && $finalCta->is_enabled)
            <section class="mother-final-cta final-cta"><div class="container final-cta-inner"><p class="eyebrow eyebrow-light"><span class="eyebrow-line"></span>{{ $finalCta->payload['eyebrow'] ?? 'Come back to the process' }}</p><h2>{{ $finalCta->heading }}</h2><p>{{ $finalCta->body }}</p><div class="hero-buttons"><a class="button button-accent" href="{{ $finalCta->cta_url ?: route('contact') }}">{{ $finalCta->cta_label ?: 'Start a conversation' }} <span aria-hidden="true">↗</span></a>@if(!empty($finalCta->payload['secondary_cta_label']) && !empty($finalCta->payload['secondary_cta_url']))<a class="text-link text-link-light" href="{{ $finalCta->payload['secondary_cta_url'] }}">{{ $finalCta->payload['secondary_cta_label'] }} <span aria-hidden="true">→</span></a>@endif</div></div></section>
        @endif
    </main>
    <footer class="site-footer"><div class="container"><div class="footer-top"><div class="footer-brand"><a class="brand" href="#top">@if($brandLogoData)<img class="brand-image" src="{{ $brandLogoData['url'] }}" alt="{{ $brandName }}">@else<span class="brand-mark" aria-hidden="true"><span></span><span></span><span></span></span><span class="brand-copy"><strong>MWANAFUNZI</strong><small>INVESTOR</small></span>@endif</a><p>{!! nl2br(e($footerCopy)) !!}</p></div><div class="footer-nav">@forelse($footerNavigation as $group => $items)<div><span>{{ ucfirst($group) }}</span>@foreach($items as $item)<a href="{{ $item->href() }}" target="{{ $item->target }}">{{ $item->label }}</a>@foreach($item->children->where('is_visible', true)->sortBy('sort_order') as $child)<a class="footer-sub-link" href="{{ $child->href() }}" target="{{ $child->target }}">{{ $child->label }}</a>@endforeach @endforeach</div>@empty<div><span>Explore</span><a href="{{ route('learn') }}">Learn</a><a href="{{ route('courses') }}">Courses</a><a href="{{ route('tools') }}">Tools</a></div><div><span>Company</span><a href="{{ route('journal') }}">Journal</a><a href="{{ route('about') }}">About</a><a href="{{ route('contact') }}">Contact</a></div>@endforelse<div class="footer-contact"><span>Contact</span><a href="mailto:{{ $email }}">{{ $email }}</a><a href="tel:{{ preg_replace('/\D+/', '', $phone) }}">{{ $phone }}</a>@if($whatsapp)<a href="https://wa.me/{{ preg_replace('/\D+/', '', $whatsapp) }}" target="_blank" rel="noopener">WhatsApp {{ $whatsapp }}</a>@endif<p>{{ $location }}</p>@if($socialLinks->isNotEmpty())<div class="footer-social"><span>Follow</span>@foreach($socialLinks as $social)<a href="{{ $social->url }}" target="_blank" rel="noopener noreferrer">{{ $social->label }} <span aria-hidden="true">↗</span></a>@endforeach</div>@endif</div></div></div><div class="footer-bottom"><span>{{ $footerCopyright }}</span><div>@if(isset($footerNavigation['legal'])) @foreach($footerNavigation['legal'] as $item)<a href="{{ $item->href() }}">{{ $item->label }}</a>@endforeach @else<a href="{{ route('legal', 'privacy-policy') }}">Privacy</a><a href="{{ route('legal', 'terms') }}">Terms</a><a href="{{ route('legal', 'risk-disclosure') }}">Risk disclosure</a>@endif</div><strong>{{ $footerBottomStatement }}</strong></div><p class="site-disclaimer">{{ $riskDisclaimer }}</p></div></footer>
    <div class="toast" role="status" aria-live="polite" data-toast></div>
</body>
</html>
