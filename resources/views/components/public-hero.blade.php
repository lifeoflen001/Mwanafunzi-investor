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
    'textColor' => 'light',
    'compact' => false,
    'metadata' => null,
    'breadcrumbs' => [],
])
@php
    $configuredImage = $setting ? \App\Models\SiteSetting::getValue($setting) : null;
    $defaultImage = \App\Models\SiteSetting::getValue('default_public_hero');
    $hero = \App\Support\PublicHero::resolve([$image, $configuredImage, $fallbackImage, $defaultImage, config('public.hero_defaults.default')]);
@endphp
<section {{ $attributes->class(['public-hero', 'public-hero-compact' => $compact, 'public-hero-'.$alignment, 'public-hero-'.$textColor]) }} style="--hero-image-position: {{ $hero['position'] ?? 'center center' }}; --hero-overlay-strength: {{ $overlay }};">
    @if($hero)
        <picture class="public-hero-media" aria-hidden="true">
            @if($hero['srcset'])<img src="{{ $hero['url'] }}" srcset="{{ $hero['srcset'] }}" sizes="100vw" alt="" fetchpriority="high">@else<img src="{{ $hero['url'] }}" alt="" fetchpriority="high">@endif
        </picture>
    @endif
    <div class="public-hero-overlay" aria-hidden="true"></div>
    <div class="container public-hero-inner">
        @if($breadcrumbs)
            <nav class="public-hero-breadcrumbs" aria-label="Breadcrumb">
                @foreach($breadcrumbs as $breadcrumb)
                    @if(! $loop->first)<span aria-hidden="true">/</span>@endif
                    @if(! empty($breadcrumb['url']))<a href="{{ $breadcrumb['url'] }}">{{ $breadcrumb['label'] }}</a>@else<span>{{ $breadcrumb['label'] }}</span>@endif
                @endforeach
            </nav>
        @endif
        @if($eyebrow)<p class="eyebrow {{ $textColor === 'light' ? 'eyebrow-light' : '' }}"><span class="eyebrow-line"></span>{{ $eyebrow }}</p>@endif
        @if($titleHtml)<h1>{!! $titleHtml !!}</h1>@elseif($title)<h1>{{ $title }}</h1>@endif
        @if($summary)<p class="lede">{{ $summary }}</p>@endif
        @if($metadata)<div class="public-hero-metadata">@foreach((array) $metadata as $item)<span>{{ $item }}</span>@endforeach</div>@endif
        {{ $slot }}
    </div>
    @isset($aside){{ $aside }}@endisset
</section>
