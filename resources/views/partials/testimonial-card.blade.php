@props(['testimonial', 'dark' => false])
@php
    $clientImage = $testimonial->client_photo ? \App\Support\PublicHero::candidate($testimonial->client_photo) : null;
    $companyLogo = ! $clientImage && $testimonial->company_logo ? \App\Support\PublicHero::candidate($testimonial->company_logo) : null;
    $relatedProject = $testimonial->project;
    $projectIsPublic = $relatedProject && $relatedProject->status === 'published' && (! $relatedProject->published_at || $relatedProject->published_at->isPast());
    $quote = trim((string) $testimonial->testimonial);
    $hasReadMore = mb_strlen($quote) > 240;
    $sourceHost = $testimonial->source_url ? parse_url($testimonial->source_url, PHP_URL_HOST) : null;
@endphp
<article class="testimonial-card{{ $dark ? ' testimonial-card-dark' : '' }}">
    <div class="testimonial-card-identity">
        @if($clientImage || $companyLogo)
            <div class="testimonial-card-media">
                @if($clientImage)
                    <img src="{{ $clientImage['url'] }}" alt="{{ $testimonial->anonymous_display ? 'Client portrait' : $testimonial->displayName() }}" loading="lazy" @if($clientImage['width'] && $clientImage['height']) width="{{ $clientImage['width'] }}" height="{{ $clientImage['height'] }}" @endif>
                @else
                    <img src="{{ $companyLogo['url'] }}" alt="{{ $testimonial->company ? $testimonial->company.' logo' : 'Client logo' }}" loading="lazy" @if($companyLogo['width'] && $companyLogo['height']) width="{{ $companyLogo['width'] }}" height="{{ $companyLogo['height'] }}" @endif>
                @endif
            </div>
        @else
            <div class="testimonial-card-avatar" aria-hidden="true">{{ strtoupper(substr($testimonial->displayName(), 0, 1)) }}</div>
        @endif
        <cite>
            <strong>{{ $testimonial->displayName() }}</strong>
            @if($testimonial->client_role)<span>{{ $testimonial->client_role }}</span>@endif
            @if($testimonial->company && ! $testimonial->hide_company && ! $testimonial->anonymous_display)<span>{{ $testimonial->company }}</span>@endif
        </cite>
    </div>
    @if($testimonial->rating)
        <div class="testimonial-card-rating" aria-label="Rating: {{ $testimonial->rating }} out of 5">
            @for($star = 1; $star <= 5; $star++)<span aria-hidden="true" class="{{ $star <= $testimonial->rating ? 'is-filled' : '' }}">★</span>@endfor
        </div>
    @endif
    <blockquote class="testimonial-card-quote">
        <p>{{ $hasReadMore ? \Illuminate\Support\Str::limit($quote, 240) : $quote }}</p>
        @if($hasReadMore)
            <details class="testimonial-card-more">
                <summary>Read more</summary>
                <p>{{ $quote }}</p>
            </details>
        @endif
        <footer>
            <span class="testimonial-card-source">{{ $sourceHost ? 'Source: '.$sourceHost : 'Client perspective' }}</span>
            @if($projectIsPublic)
                <a class="testimonial-card-project" href="{{ route('projects.show', $relatedProject) }}">{{ $relatedProject->title }} <span aria-hidden="true">↗</span></a>
            @endif
            @if($testimonial->source_url)
                <a class="testimonial-card-project" href="{{ $testimonial->source_url }}" target="_blank" rel="noopener">View source <span aria-hidden="true">↗</span></a>
            @endif
        </footer>
    </blockquote>
</article>
