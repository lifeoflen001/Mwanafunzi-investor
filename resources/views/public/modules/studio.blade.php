@extends('layouts.public')

@section('service_context', 'studio')

@php
    $sections = $page->sections->where('is_enabled', true)->keyBy('key');
    $services = $sections->get('services');
    $formats = $sections->get('formats');
    $testimonials = $sections->get('testimonials');
    $approach = $sections->get('approach');
    $process = $sections->get('process');
    $cta = $sections->get('final_cta');
    $heroTitleHtml = $page->hero_highlight ? e($page->hero_title ?: $module->name).'<br><em>'.e($page->hero_highlight).'</em>' : null;
    $cards = $services?->payload['cards'] ?? [];
    $steps = $process?->payload['steps'] ?? [];
@endphp

@section('title', $page->seo_title ?: $module->name.' — Mwanafunzi Investor')
@section('description', $page->seo_description ?: $module->description)

@section('content')
    <x-public-hero class="studio-hero" :eyebrow="$page->hero_eyebrow ?: 'Mwanafunzi Investor / Creative Studio'" :title="$page->hero_title ?: $module->name" :title-html="$heroTitleHtml" :summary="$page->hero_summary ?: $module->description" :image="$page->hero_image" :focal-point="$page->hero_focal_point" :overlay="$page->hero_overlay" :alignment="$page->hero_alignment" setting="hero_about_image" :fallback-image="config('public.hero_defaults.about')">
        <div class="detail-actions">
            <a class="button button-accent" href="{{ $page->hero_primary_url ?: route('contact', ['module' => $module->slug]) }}">{{ $page->hero_primary_label ?: 'Plan a shoot' }} <span aria-hidden="true">↗</span></a>
            <a class="text-link text-link-light" href="{{ $page->hero_secondary_url ?: '#services' }}">{{ $page->hero_secondary_label ?: 'Explore the studio' }} <span aria-hidden="true">↓</span></a>
        </div>
    </x-public-hero>

    @if($services)
        <section class="platform-section studio-services" id="services">
            <div class="container">
                <div class="split-heading">
                    <div>
                        <p class="eyebrow"><span class="eyebrow-line"></span> {{ $services->payload['eyebrow'] ?? 'What we make' }}</p>
                        <h2>{{ $services->heading }}</h2>
                    </div>
                    <div class="body-copy rich-copy">{!! \App\Support\RichText::render($services->body) !!}</div>
                </div>
                <div class="studio-service-grid">
                    @foreach($cards as $card)
                        <article class="studio-service-card"><span>{{ $card['index'] ?? str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span><h3>{{ $card['title'] ?? '' }}</h3><p>{{ $card['body'] ?? '' }}</p></article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if($formats)
        <section class="platform-muted platform-section studio-formats" id="formats">
            <div class="container"><div class="split-heading"><div><p class="eyebrow"><span class="eyebrow-line"></span> {{ $formats->payload['eyebrow'] ?? 'Formats' }}</p><h2>{{ $formats->heading }}</h2></div><div class="body-copy rich-copy">{!! \App\Support\RichText::render($formats->body) !!}</div></div><div class="service-card-grid">@foreach($formats->payload['cards'] ?? [] as $card)<article class="service-card service-card-muted"><span>{{ $card['index'] ?? str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span><h3>{{ $card['title'] ?? '' }}</h3><p>{{ $card['body'] ?? '' }}</p>@if(!empty($card['tags']))<small>{{ $card['tags'] }}</small>@endif</article>@endforeach</div></div>
        </section>
    @endif

    @if($featuredProjects->isNotEmpty())
        <section class="platform-section studio-projects" id="projects">
            <div class="container"><div class="split-heading"><div><p class="eyebrow"><span class="eyebrow-line"></span> Selected work</p><h2>Stories worth keeping.</h2></div><a class="text-link" href="{{ route('creative-studio.projects') }}">View studio projects <span aria-hidden="true">↗</span></a></div><div class="service-project-grid">@foreach($featuredProjects->take(3) as $project)@php($projectImage = $project->featured_image ? \App\Support\PublicHero::candidate($project->featured_image) : null)<a class="service-project-card" href="{{ route('creative-studio.projects') }}">@if($projectImage)<img src="{{ $projectImage['url'] }}" alt="{{ $project->title }}" loading="lazy">@endif<div><span>{{ $project->project_type ?: 'Creative project' }}</span><h3>{{ $project->title }}</h3><p>{{ $project->short_description }}</p></div></a>@endforeach</div></div>
        </section>
    @endif

    @if($testimonials)
        <section class="platform-dark platform-section service-testimonials" id="testimonials">
            <div class="container"><div class="split-heading"><div><p class="eyebrow eyebrow-light"><span class="eyebrow-line"></span> {{ $testimonials->payload['eyebrow'] ?? 'Client perspective' }}</p><h2>{{ $testimonials->heading }}</h2></div><div class="rich-copy">{!! \App\Support\RichText::render($testimonials->body) !!}</div></div>@if($featuredTestimonials->isNotEmpty())<div class="service-testimonial-grid">@foreach($featuredTestimonials->take(3) as $testimonial)<blockquote>“{{ $testimonial->testimonial }}”<footer><strong>{{ $testimonial->displayName() }}</strong>@if($testimonial->client_role){{ $testimonial->client_role }}@endif</footer></blockquote>@endforeach</div>@else<div class="service-empty-note">Published client perspectives will appear here as they are approved in the admin desk.</div>@endif<a class="text-link text-link-light" href="{{ route('creative-studio.testimonials') }}">Explore studio testimonials <span aria-hidden="true">↗</span></a></div>
        </section>
    @endif

    @if($approach)
        <section class="platform-muted platform-section studio-approach">
            <div class="container two-column">
                <div>
                    <p class="eyebrow"><span class="eyebrow-line"></span> {{ $approach->payload['eyebrow'] ?? 'The approach' }}</p>
                    <h2>{{ $approach->heading }}</h2>
                </div>
                <div class="studio-approach-copy">
                    <div class="rich-copy">{!! \App\Support\RichText::render($approach->body) !!}</div>
                    @if(!empty($approach->payload['list']))<ul class="check-list">@foreach($approach->payload['list'] as $item)<li>{{ $item }}</li>@endforeach</ul>@endif
                </div>
            </div>
        </section>
    @endif

    @if($process)
        <section class="platform-dark studio-process">
            <div class="container">
                <div class="studio-process-heading">
                    <div>
                        <p class="eyebrow eyebrow-light"><span class="eyebrow-line"></span> {{ $process->payload['eyebrow'] ?? 'From brief to delivery' }}</p>
                        <h2>{{ $process->heading }}</h2>
                    </div>
                    <div class="rich-copy">{!! \App\Support\RichText::render($process->body) !!}</div>
                </div>
                <div class="studio-process-grid">
                    @foreach($steps as $step)<div><span>{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span><h3>{{ $step['title'] ?? '' }}</h3><p>{{ $step['body'] ?? '' }}</p></div>@endforeach
                </div>
            </div>
        </section>
    @endif

    @if($cta)
        <section class="platform-section studio-cta">
            <div class="container two-column">
                <div>
                    <p class="eyebrow"><span class="eyebrow-line"></span> {{ $cta->payload['eyebrow'] ?? 'Ready when you are' }}</p>
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
