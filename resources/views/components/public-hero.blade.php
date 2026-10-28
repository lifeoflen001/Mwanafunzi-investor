@props([
    'eyebrow' => null,
    'title' => null,
    'titleHtml' => null,
    'summary' => null,
    'image' => null,
    'fallbackImage' => null,
    'setting' => null,
    'overlay' => 'medium',
    'alignment' => 'left',
    'focalPoint' => 'center center',
    'textColor' => 'light',
    'compact' => false,
    'metadata' => null,
    'breadcrumbs' => [],
])
@php
    $pageKey = match (request()->route()?->getName()) {
        'home' => 'home',
        'learn', 'forex-academy' => 'learn',
        'courses' => 'courses',
        'tools' => 'tools',
        'journal' => 'journal',
        'about' => 'about',
        'faqs' => 'faqs',
        'contact' => 'contact',
        'student-of-money' => 'student-of-money',
        'development', 'digital-systems' => 'development',
        'studio', 'creative-studio' => 'studio',
        'legal' => request()->route('page'),
        default => null,
    };
    $cmsPage = $pageKey ? \App\Models\Page::published()->where('key', $pageKey)->first() : null;
    $eyebrow = $cmsPage?->hero_eyebrow ?: $eyebrow;
    $title = $cmsPage?->hero_title ?: $title;
    if ($cmsPage?->hero_title && $cmsPage?->hero_highlight) {
        $titleHtml = e($cmsPage->hero_title).'<br> <em>'.e($cmsPage->hero_highlight).'</em>';
    } elseif ($cmsPage?->hero_title) {
        $titleHtml = null;
    }
    $summary = $cmsPage?->hero_summary ?: $summary;
    $overlay = $cmsPage?->hero_overlay ?: $overlay;
    $overlay = in_array($overlay, ['light', 'medium', 'strong'], true) ? $overlay : 'medium';
    $alignment = $cmsPage?->hero_alignment ?: $alignment;
    $alignment = in_array($alignment, ['left', 'center', 'right'], true) ? $alignment : 'left';
    $focalPoint = \App\Support\HeroFocalPoint::normalise($cmsPage?->hero_focal_point ?: $focalPoint);
    $configuredImage = $setting ? \App\Models\SiteSetting::getValue($setting) : null;
    $defaultImage = \App\Models\SiteSetting::getValue('default_public_hero');
    $hero = \App\Support\PublicHero::resolve([$cmsPage?->hero_image, $image, $configuredImage, $fallbackImage, $defaultImage, config('public.hero_defaults.default')]);
    $breadcrumbItems = collect($breadcrumbs)->filter(fn ($item) => filled($item['label'] ?? null))->values()->all();
    if ($breadcrumbItems && ($breadcrumbItems[array_key_last($breadcrumbItems)]['label'] ?? null) !== $title) {
        $breadcrumbItems[] = ['label' => $title, 'url' => \App\Support\Seo::canonical(request()->path())];
    }
@endphp
@if(count($breadcrumbItems) > 1)<script type="application/ld+json">{!! json_encode(\App\Support\Seo::breadcrumbSchema($breadcrumbItems), JSON_UNESCAPED_SLASHES) !!}</script>@endif
<section {{ $attributes->class(['public-hero', 'public-hero-compact' => $compact, 'public-hero-'.$alignment, 'public-hero-'.$textColor]) }} style="--hero-image-position: {{ $focalPoint }}; --hero-overlay-strength: {{ $overlay }};">
    @if($hero)
        @if($hero['is_video'] ?? false)
            <video class="public-hero-media" src="{{ $hero['url'] }}" autoplay muted loop playsinline controls aria-label="{{ $title }}"></video>
        @else
            <picture class="public-hero-media" aria-hidden="true">
                @if($hero['srcset'])<img src="{{ $hero['url'] }}" srcset="{{ $hero['srcset'] }}" sizes="100vw" @if($hero['width'] ?? null) width="{{ $hero['width'] }}" height="{{ $hero['height'] }}" @endif alt="" fetchpriority="high">@else<img src="{{ $hero['url'] }}" @if($hero['width'] ?? null) width="{{ $hero['width'] }}" height="{{ $hero['height'] }}" @endif alt="" fetchpriority="high">@endif
            </picture>
        @endif
    @endif
    <div class="public-hero-overlay" aria-hidden="true"></div>
    <div class="container public-hero-inner">
        @if($eyebrow)<p class="eyebrow {{ $textColor === 'light' ? 'eyebrow-light' : '' }}"><span class="eyebrow-line"></span>{{ $eyebrow }}</p>@endif
        @if($titleHtml)<h1>{!! $titleHtml !!}</h1>@elseif($title)<h1>{{ $title }}</h1>@endif
        @if($summary)<p class="lede">{{ $summary }}</p>@endif
        @if($metadata)<div class="public-hero-metadata">@foreach((array) $metadata as $item)<span>{{ $item }}</span>@endforeach</div>@endif
        @if($slot->isEmpty() && $cmsPage?->hero_primary_label && $cmsPage?->hero_primary_url)
            <div class="hero-buttons">
                <a class="button button-accent" href="{{ \App\Support\Seo::resolve($cmsPage->hero_primary_url, '#') }}" target="{{ $cmsPage->hero_primary_target ?: '_self' }}" @if(($cmsPage->hero_primary_target ?: '_self') === '_blank') rel="noopener" @endif>{{ $cmsPage->hero_primary_label }} <span aria-hidden="true">↗</span></a>
                @if($cmsPage->hero_secondary_label && $cmsPage->hero_secondary_url)<a class="text-link text-link-light" href="{{ \App\Support\Seo::resolve($cmsPage->hero_secondary_url, '#') }}" target="{{ $cmsPage->hero_secondary_target ?: '_self' }}" @if(($cmsPage->hero_secondary_target ?: '_self') === '_blank') rel="noopener" @endif>{{ $cmsPage->hero_secondary_label }} <span aria-hidden="true">→</span></a>@endif
            </div>
        @endif
        {{ $slot }}
    </div>
    @isset($aside){{ $aside }}@endisset
</section>
