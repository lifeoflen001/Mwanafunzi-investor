@php
    $updated = $updated ?? null;
    $sections = $sections ?? [];
    $related = $related ?? [];
    $supportHeading = $supportHeading ?? 'Questions about this policy?';
    $supportCopy = $supportCopy ?? 'Contact the Mwanafunzi Investor desk and tell us which part needs clarification.';
    $eyebrow = $page?->hero_eyebrow ?: ($eyebrow ?? 'Policies and legal information');
    $heading = $page?->hero_title ?: $heading;
    $cmsSections = $page?->sections?->where('is_enabled', true)->sortBy('sort_order') ?? collect();
    if ($cmsSections->isNotEmpty()) {
        $sections = $cmsSections->map(function ($section) {
            $payload = $section->payload ?: [];
            return [
                'id' => \Illuminate\Support\Str::slug($section->key),
                'title' => $section->heading ?: \Illuminate\Support\Str::headline($section->key),
                'paragraphs' => preg_split('/\n\s*\n/', trim((string) $section->body), -1, PREG_SPLIT_NO_EMPTY),
                'list' => $payload['list'] ?? [],
                'callout' => $payload['callout'] ?? null,
            ];
        })->values()->all();
    }
    $intro = $page?->hero_summary ?: $intro;
    $supportHeading = $page?->support_heading ?: $supportHeading;
    $supportCopy = $page?->support_copy ?: $supportCopy;
    $updated = $page?->effective_date?->format('F j, Y') ?: ($updated ?? $page?->updated_at?->format('F j, Y'));
@endphp

<x-public-hero class="legal-hero" compact :eyebrow="$eyebrow ?? 'Policies and legal information'" :title="$heading" :summary="$intro" setting="hero_legal_image" :fallback-image="config('public.hero_defaults.legal')" />

<section class="platform-section legal-page">
    <div class="container legal-layout">
        <aside class="legal-toc" aria-label="On this page">
            <strong>On this page</strong>
            <ol>
                @foreach($sections as $section)
                    <li><a href="#{{ $section['id'] }}">{{ $section['title'] }}</a></li>
                @endforeach
                @if($page?->faqs?->isNotEmpty())<li><a href="#faq">Questions</a></li>@endif
                <li><a href="#support">Contact</a></li>
            </ol>
        </aside>
        <article class="legal-body">
            @if($updated)<p class="legal-updated">Last updated: {{ $updated }}</p>@endif
            <p class="legal-intro">{{ $intro }}</p>
            @foreach($sections as $section)
                <section class="legal-section" id="{{ $section['id'] }}">
                    <h2>{{ $section['title'] }}</h2>
                    @foreach($section['paragraphs'] ?? [] as $paragraph)<p>{{ $paragraph }}</p>@endforeach
                    @if(!empty($section['list']))<ul>@foreach($section['list'] as $item)<li>{{ $item }}</li>@endforeach</ul>@endif
                    @if(!empty($section['callout']))<p class="legal-callout">{{ $section['callout'] }}</p>@endif
                </section>
            @endforeach
            <section class="legal-support" id="support">
                <p class="eyebrow">Policy support</p>
                <h2>{{ $supportHeading }}</h2>
                <p>{{ $supportCopy }}</p>
                <a class="button button-accent" href="{{ route('contact') }}">Contact us <span aria-hidden="true">↗</span></a>
            </section>
            @if($page?->faqs?->isNotEmpty())
                <section class="legal-section legal-faqs" id="faq">
                    <h2>Questions</h2>
                    @foreach($page->faqs as $faq)<details><summary>{{ $faq->question }} <span>+</span></summary><p>{{ $faq->answer }}</p></details>@endforeach
                </section>
            @endif
            @if($related)<div class="legal-related"><strong>Related policies</strong><div class="legal-related-links">@foreach($related as $slug => $label)<a href="{{ route('legal', $slug) }}">{{ $label }} <span aria-hidden="true">↗</span></a>@endforeach</div></div>@endif
        </article>
    </div>
</section>
