@props(['testimonial', 'dark' => false, 'quoteId'])
@php
    $clientImage = $testimonial->client_photo ? \App\Support\PublicHero::candidate($testimonial->client_photo) : null;
    $quote = trim((string) $testimonial->testimonial);
    $sourceUrl = filter_var($testimonial->source_url, FILTER_VALIDATE_URL) ? $testimonial->source_url : null;
    $sourceHost = $sourceUrl ? strtolower((string) parse_url($sourceUrl, PHP_URL_HOST)) : '';
    $sourceLabel = $sourceUrl && (str_contains($sourceHost, 'google.') || str_contains($sourceHost, 'g.page'))
        ? 'Google review'
        : ($sourceUrl ? 'Review source' : null);
@endphp
<article class="testimonial-card{{ $dark ? ' testimonial-card-dark' : '' }}">
    <div class="testimonial-card-identity">
        @if($clientImage)
            <div class="testimonial-card-media">
                <img src="{{ $clientImage['url'] }}" alt="{{ $testimonial->anonymous_display ? 'Client portrait' : $testimonial->displayName() }}" loading="lazy" @if(($clientImage['width'] ?? null) && ($clientImage['height'] ?? null)) width="{{ $clientImage['width'] }}" height="{{ $clientImage['height'] }}" @endif>
            </div>
        @else
            <div class="testimonial-card-avatar" aria-hidden="true">{{ strtoupper(substr($testimonial->displayName(), 0, 1)) }}</div>
        @endif
        <cite>
            @if(filter_var($testimonial->source_author_url, FILTER_VALIDATE_URL))<a href="{{ $testimonial->source_author_url }}" target="_blank" rel="noopener"><strong>{{ $testimonial->displayName() }}</strong></a>@else<strong>{{ $testimonial->displayName() }}</strong>@endif
            @if($testimonial->testimonial_date)<time datetime="{{ $testimonial->testimonial_date->format('Y-m-d') }}">{{ $testimonial->testimonial_date->format('j F Y') }}</time>@endif
        </cite>
    </div>
    @if($testimonial->rating)
        <div class="testimonial-card-rating" aria-label="Rating: {{ $testimonial->rating }} out of 5">
            @for($star = 1; $star <= 5; $star++)<span aria-hidden="true" class="{{ $star <= $testimonial->rating ? 'is-filled' : '' }}">★</span>@endfor
        </div>
    @endif
    <blockquote class="testimonial-card-quote">
        <div class="testimonial-card-text" id="{{ $quoteId }}" data-testimonial-text tabindex="0" aria-label="Testimonial from {{ $testimonial->displayName() }}"><p>{{ $quote }}</p></div>
        <button class="testimonial-card-more" type="button" data-testimonial-more aria-controls="{{ $quoteId }}" aria-expanded="false" hidden>Read more</button>
    </blockquote>
    @if($sourceUrl && $sourceLabel)
        <a class="testimonial-card-origin" href="{{ $sourceUrl }}" target="_blank" rel="noopener" aria-label="View {{ strtolower($sourceLabel) }} from {{ $testimonial->displayName() }}">{{ $sourceLabel }} <span aria-hidden="true">↗</span></a>
    @endif
</article>
