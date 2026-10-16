@php
    $slides = [
        [
            'image' => asset('images/hero-learn.webp'),
            'eyebrow' => 'Financial education',
            'title' => 'Learn with a clearer framework.',
            'copy' => 'Build practical market understanding through lessons designed to help you think in systems.',
            'alt' => 'Editorial learning desk with notes and market diagrams',
        ],
        [
            'image' => asset('images/hero-journal.webp'),
            'eyebrow' => 'Your journal',
            'title' => 'Keep your next decision intentional.',
            'copy' => 'Return to thoughtful conversations, journal notes and resources whenever you are ready.',
            'alt' => 'Market journal workspace with written notes and charts',
        ],
        [
            'image' => asset('images/hero-courses.webp'),
            'eyebrow' => 'Practical learning',
            'title' => 'Make progress at your own pace.',
            'copy' => 'Explore structured learning resources for money, markets, risk and probability.',
            'alt' => 'Course learning materials arranged on a desk',
        ],
        [
            'image' => asset('images/hero-tools.webp'),
            'eyebrow' => 'Tools and resources',
            'title' => 'Use better tools for better thinking.',
            'copy' => 'Keep useful calculators, frameworks and market resources close to your learning journey.',
            'alt' => 'Financial education tools and market resources',
        ],
    ];
@endphp

<section class="client-auth-showcase" data-auth-showcase aria-label="Mwanafunzi Investor highlights">
    <div class="client-auth-showcase-slides">
        @foreach($slides as $slide)
            <article class="client-auth-slide{{ $loop->first ? ' is-active' : '' }}" data-auth-slide-panel="{{ $loop->index }}" aria-hidden="{{ $loop->first ? 'false' : 'true' }}">
                <img src="{{ $slide['image'] }}" alt="{{ $slide['alt'] }}" @if(!$loop->first) loading="lazy" @endif>
                <div class="client-auth-slide-overlay"></div>
                <div class="client-auth-slide-copy">
                    <p class="eyebrow">{{ $slide['eyebrow'] }}</p>
                    <h2>{{ $slide['title'] }}</h2>
                    <p>{{ $slide['copy'] }}</p>
                </div>
            </article>
        @endforeach
    </div>

    <div class="client-auth-showcase-footer">
        <p>Student of money. Probability. Systems. Discipline.</p>
        <div class="client-auth-slide-controls" role="tablist" aria-label="Choose a highlight">
            @foreach($slides as $slide)
                <button type="button" role="tab" class="client-auth-slide-dot{{ $loop->first ? ' is-active' : '' }}" data-auth-slide="{{ $loop->index }}" aria-label="Show {{ $slide['eyebrow'] }} highlight" aria-selected="{{ $loop->first ? 'true' : 'false' }}"></button>
            @endforeach
        </div>
    </div>
</section>
