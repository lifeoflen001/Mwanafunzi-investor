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
    $homePage = \App\Models\Page::published()->with('sections')->where('key', 'home')->first();
    $homeSections = $homePage?->sections?->keyBy('key') ?? collect();
    $homeHeroPrimaryLabel = $homePage?->hero_primary_label ?: 'Start Learning';
    $homeHeroPrimaryUrl = $homePage?->hero_primary_url ?: '#courses';
    $homeHeroSecondaryLabel = $homePage?->hero_secondary_label ?: 'Explore Trading Tools';
    $homeHeroSecondaryUrl = $homePage?->hero_secondary_url ?: '#tools';
    $homeHeroNote = $homePage?->hero_note ?: 'Forex Education • Risk Management • Trading Systems • Portfolio Thinking';
    $homeHeroAside = $homePage?->hero_aside ?: "For the person who wants\nto understand, not predict.";
    $homeHeroAsideIndex = $homePage?->hero_aside_index ?: '01 / 04';
    $homeSeoTitle = $homePage?->seo_title ?: \App\Models\SiteSetting::getValue('default_seo_title', $brandName.' — Become a Student of Money');
    $homeSeoDescription = $homePage?->seo_description ?: $seoDescription;
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ $homeSeoDescription }}">
    <meta name="theme-color" content="#171817">
    <meta property="og:title" content="{{ $homeSeoTitle }}">
    <meta property="og:description" content="{{ $homeSeoDescription }}">
    <meta property="og:type" content="website">
    <meta property="og:locale" content="en_TZ">
    <meta property="og:image" content="{{ $socialImageUrl }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:image" content="{{ $socialImageUrl }}">
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
            <nav class="desktop-nav" aria-label="Primary navigation">@forelse($headerNavigation as $item)<x-navigation-links :item="$item" />@empty @foreach($businessUnits as $businessUnit)<a class="{{ $businessUnit->slug === 'forex' ? 'active' : '' }}" href="{{ route($businessUnit->route_name) }}">{{ $businessUnit->name }}</a>@endforeach<a href="#about">About</a>@endforelse</nav>
            <div class="header-actions"><a class="header-contact" href="{{ route('contact') }}">Contact</a><a class="button button-small button-light" href="{{ route('contact') }}">Start a conversation <span aria-hidden="true">↗</span></a><button class="menu-toggle" type="button" aria-expanded="false" aria-controls="mobile-menu" data-menu-toggle><span class="sr-only">Open menu</span><span></span><span></span></button></div>
        </div>
        <div class="mobile-menu" id="mobile-menu" data-mobile-menu><nav aria-label="Mobile navigation">@forelse($headerNavigation as $item)<x-navigation-links :item="$item" />@empty @foreach($businessUnits as $businessUnit)<a href="{{ route($businessUnit->route_name) }}">{{ $businessUnit->name }}</a>@endforeach<a href="#about">About</a>@endforelse</nav><a class="button button-dark" href="{{ route('contact') }}">Contact the desk <span aria-hidden="true">↗</span></a></div>
    </header>
    <main id="main-content">
        <x-public-hero id="top" class="hero hero-home-hero" eyebrow="Financial education for the long game" summary="Systematic Trading. Probability. Risk. Discipline. Learn to understand markets, develop mechanical trading rules and protect your capital without the hype." setting="hero_home_image" :fallback-image="config('public.hero_defaults.home')" overlay="strong">
            <div class="hero-buttons">
                <a class="button button-accent" href="{{ $homeHeroPrimaryUrl }}">{{ $homeHeroPrimaryLabel }} <span aria-hidden="true">↗</span></a>
                <a class="text-link text-link-light" href="{{ $homeHeroSecondaryUrl }}">{{ $homeHeroSecondaryLabel }} <span aria-hidden="true">→</span></a>
            </div>
            <p class="hero-note">{{ $homeHeroNote }}</p>
            <x-slot:aside>
                <div class="hero-aside">
                    <div class="hero-aside-rule"></div>
                    <p>{!! nl2br(e($homeHeroAside)) !!}</p>
                    <span>{{ $homeHeroAsideIndex }}</span>
                </div>
            </x-slot:aside>
            <a class="scroll-cue" href="#philosophy"><span>Scroll to explore</span><i aria-hidden="true">↓</i></a>
        </x-public-hero>
        @if($homeSections->get('philosophy')?->is_enabled ?? true) @include('public.home.philosophy', ['section' => $homeSections->get('philosophy')]) @endif
        @if($homeSections->get('learning')?->is_enabled ?? true) @include('public.home.learning', ['section' => $homeSections->get('learning'), 'learningTopics' => $learningTopics]) @endif
        @if($homeSections->get('framework')?->is_enabled ?? true) @include('public.home.framework', ['section' => $homeSections->get('framework')]) @endif
        @if($homeSections->get('tools')?->is_enabled ?? true) @include('public.home.tools', ['section' => $homeSections->get('tools'), 'products' => $products]) @endif
        @if($homeSections->get('probability')?->is_enabled ?? true) @include('public.home.probability', ['section' => $homeSections->get('probability')]) @endif
        @if($homeSections->get('discipline')?->is_enabled ?? true) @include('public.home.discipline', ['section' => $homeSections->get('discipline')]) @endif
        @if($homeSections->get('journal')?->is_enabled ?? true) @include('public.home.journal', ['section' => $homeSections->get('journal'), 'featuredArticle' => $featuredArticle]) @endif
        @if($homeSections->get('courses')?->is_enabled ?? true) @include('public.home.courses', ['section' => $homeSections->get('courses'), 'courses' => $courses]) @endif
        @if($homeSections->get('final_cta')?->is_enabled ?? true) @include('public.home.final-cta', ['section' => $homeSections->get('final_cta'), 'email' => $email]) @endif
    </main>
    <footer class="site-footer"><div class="container"><div class="footer-top"><div class="footer-brand"><a class="brand" href="#top">@if($brandLogoData)<img class="brand-image" src="{{ $brandLogoData['url'] }}" alt="{{ $brandName }}">@else<span class="brand-mark" aria-hidden="true"><span></span><span></span><span></span></span><span class="brand-copy"><strong>MWANAFUNZI</strong><small>INVESTOR</small></span>@endif</a><p>{!! nl2br(e($footerCopy)) !!}</p></div><div class="footer-nav">@forelse($footerNavigation as $group => $items)<div><span>{{ ucfirst($group) }}</span>@foreach($items as $item)<a href="{{ $item->href() }}" target="{{ $item->target }}">{{ $item->label }}</a>@foreach($item->children->where('is_visible', true)->sortBy('sort_order') as $child)<a class="footer-sub-link" href="{{ $child->href() }}" target="{{ $child->target }}">{{ $child->label }}</a>@endforeach @endforeach</div>@empty<div><span>Explore</span><a href="{{ route('learn') }}">Learn</a><a href="{{ route('courses') }}">Courses</a><a href="{{ route('tools') }}">Tools</a></div><div><span>Company</span><a href="{{ route('journal') }}">Journal</a><a href="{{ route('about') }}">About</a><a href="{{ route('contact') }}">Contact</a></div>@endforelse<div class="footer-contact"><span>Contact</span><a href="mailto:{{ $email }}">{{ $email }}</a><a href="tel:{{ preg_replace('/\D+/', '', $phone) }}">{{ $phone }}</a>@if($whatsapp)<a href="https://wa.me/{{ preg_replace('/\D+/', '', $whatsapp) }}" target="_blank" rel="noopener">WhatsApp {{ $whatsapp }}</a>@endif<p>{{ $location }}</p>@if($socialLinks->isNotEmpty())<div class="footer-social"><span>Follow</span>@foreach($socialLinks as $social)<a href="{{ $social->url }}" target="_blank" rel="noopener noreferrer">{{ $social->label }} <span aria-hidden="true">↗</span></a>@endforeach</div>@endif</div></div></div><div class="footer-bottom"><span>{{ $footerCopyright }}</span><div>@if(isset($footerNavigation['legal'])) @foreach($footerNavigation['legal'] as $item)<a href="{{ $item->href() }}">{{ $item->label }}</a>@endforeach @else<a href="{{ route('legal', 'privacy-policy') }}">Privacy</a><a href="{{ route('legal', 'terms') }}">Terms</a><a href="{{ route('legal', 'risk-disclosure') }}">Risk disclosure</a>@endif</div><strong>{{ $footerBottomStatement }}</strong></div><p class="site-disclaimer">{{ $riskDisclaimer }}</p></div></footer>
    <div class="toast" role="status" aria-live="polite" data-toast></div>
</body>
</html>
