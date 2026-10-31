@extends('layouts.public')

@section('service_context', 'development')

@php
    $sections = $page->sections->where('is_enabled', true)->keyBy('key');
    $capabilities = $sections->get('capabilities');
    $products = $sections->get('products');
    $languages = $sections->get('languages');
    $process = $sections->get('process');
    $finalCta = $sections->get('final_cta');
    $selectedWork = $sections->get('selected-work');
    $industriesSection = $sections->get('industries');
    $capabilityCards = collect($capabilities?->payload['cards'] ?? [])->values();
    $heroTitle = $page->hero_title ?: 'Websites and software built around real business operations.';
    $heroSummary = $page->hero_summary ?: 'Professional websites, custom business systems, portals, dashboards and integrations for hospitality, tourism, education and growing organisations.';
@endphp

@section('title', $page->seo_title ?: 'Digital Software — Mwanafunzi Investor')
@section('description', $page->seo_description ?: $heroSummary)
@section('canonical', route('digital-systems'))

@section('content')
    <x-public-hero class="development-hero" :eyebrow="$page->hero_eyebrow ?: 'Mwanafunzi Investor | Digital Software'" :title="$heroTitle" :summary="$heroSummary" :image="$page->hero_image" :focal-point="$page->hero_focal_point" :overlay="$page->hero_overlay" :alignment="$page->hero_alignment" setting="hero_tools_image" :fallback-image="config('public.hero_defaults.modules')">
        <div class="detail-actions">
            @if($selectedWork && $featuredProjects->isNotEmpty())<a class="button button-dark" href="#{{ \App\Support\CmsContent::value($selectedWork, 'anchor_id', 'selected-work') }}">View completed projects <span aria-hidden="true">↓</span></a>@endif
            <a class="text-link text-link-light" href="{{ route('digital-software.contact') }}">Discuss a project <span aria-hidden="true">↗</span></a>
        </div>
        <div class="digital-credibility-row" aria-label="Digital Software capabilities"><span>Tanzania</span><span>Available remotely</span><span>Laravel</span><span>PHP</span><span>Business systems</span></div>
    </x-public-hero>

    @if($selectedWork && $featuredProjects->isNotEmpty())
        <section class="platform-section digital-featured-projects" id="selected-work" aria-labelledby="digital-featured-heading">
            <div class="container">
                <div class="split-heading"><div><p class="eyebrow"><span class="eyebrow-line"></span> {{ \App\Support\CmsContent::value($selectedWork, 'eyebrow', 'Selected work') }}</p><h2 id="digital-featured-heading">{{ $selectedWork->heading }}</h2></div>@if($selectedWork->cta_label && $selectedWork->cta_url)<a class="text-link" href="{{ \App\Support\Seo::resolve($selectedWork->cta_url, '#') }}" target="{{ \App\Support\CmsContent::value($selectedWork, 'cta_target', '_self') }}" @if(\App\Support\CmsContent::value($selectedWork, 'cta_target', '_self') === '_blank') rel="noopener" @endif>{{ $selectedWork->cta_label }} <span aria-hidden="true">↗</span></a>@endif</div>
                @if($selectedWork->body)<p class="body-copy digital-section-intro">{{ $selectedWork->body }}</p>@endif
                <div class="digital-project-grid">
                    @foreach($featuredProjects->take(4) as $project)
                        @php($projectImage = $project->featured_image ? \App\Support\PublicHero::candidate($project->featured_image) : null)
                        <a class="digital-project-card" href="{{ route('digital-software.projects.show', $project) }}">
                            @if($projectImage)<img src="{{ $projectImage['url'] }}" @if($projectImage['srcset']) srcset="{{ $projectImage['srcset'] }}" sizes="(max-width: 760px) 100vw, (max-width: 1100px) 50vw, 620px" @endif alt="{{ $project->title }} project cover" loading="lazy">@endif
                            <div class="digital-project-card-body"><div class="digital-project-card-kicker"><span>{{ $project->industry ?: 'Business systems' }}</span><span>{{ $project->statusLabel() }}</span></div><h3>{{ $project->title }}</h3><p>{{ $project->short_description }}</p>@if($project->technologies)<div class="portfolio-tags">@foreach(array_slice($project->technologies, 0, 4) as $technology)<small>{{ $technology }}</small>@endforeach</div>@endif<div class="digital-project-card-footer"><span>{{ $project->role ?: 'Full-stack development' }}</span><strong>View case study <span aria-hidden="true">→</span></strong></div></div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if($capabilityCards->isNotEmpty())
        <section class="platform-section platform-muted" id="capabilities" aria-labelledby="digital-capabilities-heading">
            <div class="container"><div class="split-heading"><div><p class="eyebrow"><span class="eyebrow-line"></span> Capabilities</p><h2 id="digital-capabilities-heading">{{ $capabilities?->heading ?: 'Capabilities shaped around the work.' }}</h2></div><div class="body-copy rich-copy">{!! \App\Support\RichText::render($capabilities?->body ?: 'Websites, software, portals, integrations and support designed around the people and workflows that use them.') !!}</div></div><div class="digital-capability-grid">@foreach($capabilityCards as $card)<article><span>{{ $card['index'] ?? str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span><h3>{{ $card['title'] ?? '' }}</h3><p>{{ $card['body'] ?? '' }}</p>@if(!empty($card['tags']))<small>{{ $card['tags'] }}</small>@endif</article>@endforeach</div></div>
        </section>
    @endif

    @if($products)
        <section class="platform-section platform-muted digital-products-section" id="{{ \App\Support\CmsContent::value($products, 'anchor_id', 'products') }}"><div class="container"><div class="split-heading"><div><p class="eyebrow"><span class="eyebrow-line"></span> {{ \App\Support\CmsContent::value($products, 'eyebrow', 'What we deliver') }}</p><h2>{{ $products->heading }}</h2></div><div class="body-copy rich-copy">{!! \App\Support\RichText::render($products->body) !!}</div></div><div class="digital-capability-grid">@foreach($products->payload['cards'] ?? [] as $card)<article><span>{{ $card['index'] ?? str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span><h3>{{ $card['title'] ?? '' }}</h3><p>{{ $card['body'] ?? '' }}</p>@if(!empty($card['tags']))<small>{{ $card['tags'] }}</small>@endif</article>@endforeach</div></div></section>
    @endif

    @if($industriesSection && $industries->isNotEmpty())
        <section class="platform-section digital-industries" id="{{ \App\Support\CmsContent::value($industriesSection, 'anchor_id', 'industries') }}" aria-labelledby="digital-industries-heading"><div class="container"><div class="split-heading"><div><p class="eyebrow"><span class="eyebrow-line"></span> {{ \App\Support\CmsContent::value($industriesSection, 'eyebrow', 'Areas of work') }}</p><h2 id="digital-industries-heading">{{ $industriesSection->heading }}</h2></div>@if($industriesSection->body)<p class="body-copy">{{ $industriesSection->body }}</p>@endif</div><div class="digital-industry-list">@foreach($industries as $industry)<span>{{ $industry }}</span>@endforeach</div></div></section>
    @endif

    @if($process)
        <section class="platform-section dev-process"><div class="container"><div class="dev-process-heading"><div><p class="eyebrow eyebrow-light"><span class="eyebrow-line"></span> {{ $process->payload['eyebrow'] ?? 'How we work' }}</p><h2>{{ $process->heading }}</h2></div><div class="rich-copy">{!! \App\Support\RichText::render($process->body) !!}</div></div><div class="dev-process-grid">@foreach($process->payload['steps'] ?? [] as $step)<div class="dev-process-step"><span>{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span><h3>{{ $step['title'] ?? '' }}</h3><p>{{ $step['body'] ?? '' }}</p></div>@endforeach</div></div></section>
    @endif

    @if($languages)
        <section class="platform-section platform-muted software-languages" id="languages"><div class="container"><div class="split-heading"><div><p class="eyebrow"><span class="eyebrow-line"></span> Supporting evidence</p><h2>{{ $languages->heading }}</h2></div><div class="body-copy rich-copy">{!! \App\Support\RichText::render($languages->body) !!}</div></div><div class="service-card-grid">@foreach($languages->payload['cards'] ?? [] as $card)<article class="service-card service-card-muted"><span>{{ $card['index'] ?? str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span><h3>{{ $card['title'] ?? '' }}</h3><p>{{ $card['body'] ?? '' }}</p>@if(!empty($card['tags']))<small>{{ $card['tags'] }}</small>@endif</article>@endforeach</div></div></section>
    @endif

    @if($featuredTestimonials->isNotEmpty())
        <section class="platform-section testimonial-section" id="testimonials" aria-labelledby="development-testimonials-heading"><div class="container"><div class="testimonial-section-heading"><div><p class="eyebrow"><span class="eyebrow-line"></span> Client perspective</p><h2 id="development-testimonials-heading">Approved perspectives connected to the work.</h2></div><p class="testimonial-section-description">Published only when the relevant permission and review state are recorded.</p></div>@include('partials.testimonial-carousel', ['testimonials' => $featuredTestimonials, 'carouselId' => 'development-testimonials'])</div></section>
    @endif

    @if($finalCta)<section class="platform-dark digital-final-cta" aria-labelledby="digital-final-cta-heading"><div class="container"><p class="eyebrow eyebrow-light"><span class="eyebrow-line"></span> {{ \App\Support\CmsContent::value($finalCta, 'eyebrow', 'Have a project in mind?') }}</p><h2 id="digital-final-cta-heading">{{ $finalCta->heading }}</h2><p>{{ $finalCta->body }}</p>@if($finalCta->cta_label && $finalCta->cta_url)<a class="button button-accent" href="{{ \App\Support\Seo::resolve($finalCta->cta_url, '#') }}" target="{{ \App\Support\CmsContent::value($finalCta, 'cta_target', '_self') }}" @if(\App\Support\CmsContent::value($finalCta, 'cta_target', '_self') === '_blank') rel="noopener" @endif>{{ $finalCta->cta_label }} <span aria-hidden="true">↗</span></a>@endif</div></section>@endif
@endsection
