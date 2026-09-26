@props(['index', 'title', 'summary', 'href', 'image', 'eyebrow', 'tone' => ''])
@php($media = \App\Support\PublicHero::candidate($image))
<article class="mother-service-feature {{ $tone }}">
    <a class="mother-service-media" href="{{ $href }}" aria-label="Explore {{ $title }}">
        @if($media)<img src="{{ $media['url'] }}" alt="" loading="lazy">@endif
        <span class="mother-service-media-arrow" aria-hidden="true">↗</span>
    </a>
    <div class="mother-service-copy">
        <p class="eyebrow"><span class="eyebrow-line"></span>{{ $eyebrow }}</p>
        <span class="mother-service-index">{{ $index }}</span>
        <h2>{{ $title }}</h2>
        <p>{{ $summary }}</p>
        <a class="text-link" href="{{ $href }}">Explore {{ $title }} <span aria-hidden="true">→</span></a>
    </div>
</article>
