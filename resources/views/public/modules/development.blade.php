@extends('layouts.public')

@section('service_context', 'development')

@php
    $sections = $page->sections->where('is_enabled', true)->keyBy('key');
    $capabilities = $sections->get('capabilities');
    $products = $sections->get('products');
    $languages = $sections->get('languages');
    $testimonials = $sections->get('testimonials');
    $process = $sections->get('process');
    $cta = $sections->get('final_cta');
    $heroTitleHtml = $page->hero_highlight ? e($page->hero_title ?: $module->name).'<br><em>'.e($page->hero_highlight).'</em>' : null;
    $cards = $capabilities?->payload['cards'] ?? [];
    $steps = $process?->payload['steps'] ?? [];
@endphp

@section('title', $page->seo_title ?: $module->name.' — Mwanafunzi Investor')
@section('description', $page->seo_description ?: $module->description)

@section('content')
    <x-public-hero class="development-hero" :eyebrow="$page->hero_eyebrow ?: 'Mwanafunzi Investor / Digital Software'" :title="$page->hero_title ?: $module->name" :title-html="$heroTitleHtml" :summary="$page->hero_summary ?: $module->description" :image="$page->hero_image" :focal-point="$page->hero_focal_point" :overlay="$page->hero_overlay" :alignment="$page->hero_alignment" setting="hero_tools_image" :fallback-image="config('public.hero_defaults.modules')">
        <div class="detail-actions">
            <a class="button button-dark" href="{{ $page->hero_primary_url ?: route('contact', ['module' => $module->slug]) }}">{{ $page->hero_primary_label ?: 'Discuss a project' }} <span aria-hidden="true">↗</span></a>
            <a class="text-link text-link-light" href="{{ $page->hero_secondary_url ?: '#capabilities' }}">{{ $page->hero_secondary_label ?: 'See what we build' }} <span aria-hidden="true">↓</span></a>
        </div>
    </x-public-hero>

    @if($capabilities)
        <section class="platform-section platform-muted" id="capabilities">
            <div class="container">
                <div class="split-heading">
                    <div>
                        <p class="eyebrow"><span class="eyebrow-line"></span> {{ $capabilities->payload['eyebrow'] ?? 'What we build' }}</p>
                        <h2>{{ $capabilities->heading }}</h2>
                    </div>
                    <div class="body-copy rich-copy">{!! \App\Support\RichText::render($capabilities->body) !!}</div>
                </div>
                <div class="dev-service-grid">
                    @foreach($cards as $card)
                        <article class="dev-service-card">
                            <span class="dev-service-index">{{ $card['index'] ?? str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                            <h3>{{ $card['title'] ?? '' }}</h3>
                            <p>{{ $card['body'] ?? '' }}</p>
                            @if(!empty($card['tags']))<span class="dev-service-tags">{{ $card['tags'] }}</span>@endif
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if($products)
        <section class="platform-section software-products" id="products">
            <div class="container">
                <div class="split-heading"><div><p class="eyebrow"><span class="eyebrow-line"></span> {{ $products->payload['eyebrow'] ?? 'What we deliver' }}</p><h2>{{ $products->heading }}</h2></div><div class="body-copy rich-copy">{!! \App\Support\RichText::render($products->body) !!}</div></div>
                <div class="service-card-grid">@foreach($products->payload['cards'] ?? [] as $card)<article class="service-card"><span>{{ $card['index'] ?? str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span><h3>{{ $card['title'] ?? '' }}</h3><p>{{ $card['body'] ?? '' }}</p>@if(!empty($card['tags']))<small>{{ $card['tags'] }}</small>@endif</article>@endforeach</div>
            </div>
        </section>
    @endif

    @if($languages)
        <section class="platform-muted platform-section software-languages" id="languages">
            <div class="container">
                <div class="split-heading"><div><p class="eyebrow"><span class="eyebrow-line"></span> {{ $languages->payload['eyebrow'] ?? 'Languages and technologies' }}</p><h2>{{ $languages->heading }}</h2></div><div class="body-copy rich-copy">{!! \App\Support\RichText::render($languages->body) !!}</div></div>
                <div class="service-card-grid">@foreach($languages->payload['cards'] ?? [] as $card)<article class="service-card service-card-muted"><span>{{ $card['index'] ?? str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span><h3>{{ $card['title'] ?? '' }}</h3><p>{{ $card['body'] ?? '' }}</p>@if(!empty($card['tags']))<small>{{ $card['tags'] }}</small>@endif</article>@endforeach</div>
            </div>
        </section>
    @endif

    @if($featuredProjects->isNotEmpty())
        <section class="platform-section service-projects" id="projects">
            <div class="container"><div class="split-heading"><div><p class="eyebrow"><span class="eyebrow-line"></span> Selected work</p><h2>Projects built for real work.</h2></div><a class="text-link" href="{{ route('digital-software.projects') }}">View all software projects <span aria-hidden="true">↗</span></a></div><div class="service-project-grid">@foreach($featuredProjects->take(3) as $project)@php($projectImage = $project->featured_image ? \App\Support\PublicHero::candidate($project->featured_image) : null)<a class="service-project-card" href="{{ route('digital-software.projects') }}">@if($projectImage)<img src="{{ $projectImage['url'] }}" alt="{{ $project->title }}" loading="lazy">@endif<div><span>{{ $project->project_type ?: 'Case study' }}</span><h3>{{ $project->title }}</h3><p>{{ $project->short_description }}</p></div></a>@endforeach</div></div>
        </section>
    @endif

    @if($testimonials)
        <section class="platform-dark platform-section service-testimonials" id="testimonials">
            <div class="container"><div class="split-heading"><div><p class="eyebrow eyebrow-light"><span class="eyebrow-line"></span> {{ $testimonials->payload['eyebrow'] ?? 'Client perspective' }}</p><h2>{{ $testimonials->heading }}</h2></div><div class="rich-copy">{!! \App\Support\RichText::render($testimonials->body) !!}</div></div>@if($featuredTestimonials->isNotEmpty())<div class="service-testimonial-grid">@foreach($featuredTestimonials->take(3) as $testimonial)<blockquote>“{{ $testimonial->testimonial }}”<footer><strong>{{ $testimonial->displayName() }}</strong>@if($testimonial->client_role){{ $testimonial->client_role }}@endif</footer></blockquote>@endforeach</div>@else<div class="service-empty-note">Published client perspectives will appear here as they are approved in the admin desk.</div>@endif<a class="text-link text-link-light" href="{{ route('digital-software.testimonials') }}">Explore software testimonials <span aria-hidden="true">↗</span></a></div>
        </section>
    @endif

    @if($process)
        <section class="platform-dark dev-process">
            <div class="container">
                <div class="dev-process-heading">
                    <div>
                        <p class="eyebrow eyebrow-light"><span class="eyebrow-line"></span> {{ $process->payload['eyebrow'] ?? 'How we work' }}</p>
                        <h2>{{ $process->heading }}</h2>
                    </div>
                    <div class="rich-copy">{!! \App\Support\RichText::render($process->body) !!}</div>
                </div>
                <div class="dev-process-grid">
                    @foreach($steps as $step)
                        <div class="dev-process-step"><span>{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span><h3>{{ $step['title'] ?? '' }}</h3><p>{{ $step['body'] ?? '' }}</p></div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if($cta)
        <section class="platform-section dev-cta">
            <div class="container two-column">
                <div>
                    <p class="eyebrow"><span class="eyebrow-line"></span> {{ $cta->payload['eyebrow'] ?? 'Have a project in mind?' }}</p>
                    <h2>{{ $cta->heading }}</h2>
                </div>
                <div>
                    <div class="rich-copy">{!! \App\Support\RichText::render($cta->body) !!}</div>
                    @if($cta->cta_label && $cta->cta_url)<a class="button button-dark" href="{{ $cta->cta_url }}">{{ $cta->cta_label }} <span aria-hidden="true">↗</span></a>@endif
                </div>
            </div>
        </section>
    @endif
@endsection
