@extends('layouts.public')

@php($pageSections = $page?->sections?->where('is_enabled', true)->keyBy('key') ?? collect())
@section('title', $page?->seo_title ?: 'Services — Mwanafunzi Investor')
@section('description', $page?->seo_description ?: 'Digital software, websites and operational tools shaped around real business needs.')
@section('canonical', $page?->canonical_url ?: route('services'))

@section('content')
    <x-public-hero class="public-page-hero services-hero" :eyebrow="$page?->hero_eyebrow ?: 'Mwanafunzi Investor / Services'" :title="$page?->hero_title ?: 'Services built around real business needs'" :title-html="$page?->hero_highlight ? e($page->hero_title ?: 'Services').'<br><em>'.e($page->hero_highlight).'</em>' : null" :summary="$page?->hero_summary ?: 'Digital software, websites and operational tools shaped around the way your business actually works.'" :image="$page?->hero_image" :overlay="$page?->hero_overlay ?: 'strong'" setting="hero_tools_image" :fallback-image="config('public.hero_defaults.modules')">
        <a class="button button-accent" href="{{ $page?->hero_primary_url ?: route('contact', ['module' => 'development']) }}">{{ $page?->hero_primary_label ?: 'Start a project' }} <span aria-hidden="true">↗</span></a>
    </x-public-hero>

    @if($businessUnits->isNotEmpty())
        <section class="platform-section portfolio-divisions-section" id="divisions"><div class="container"><div class="split-heading"><div><p class="eyebrow"><span class="eyebrow-line"></span> The Mwanafunzi ecosystem</p><h2>Choose the part of the work you need.</h2></div><p class="body-copy">Three connected divisions, each with its own route into the work.</p></div><div class="portfolio-division-grid">@foreach($businessUnits as $businessUnit)<a class="portfolio-division-card" href="{{ $serviceRoutes[$businessUnit->slug] ?? ($businessUnit->route_name ? route($businessUnit->route_name) : route('contact')) }}"><span>{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span><h3>{{ $businessUnit->name }}</h3><p>{{ $businessUnit->description }}</p><strong>Explore {{ $businessUnit->name }} <span aria-hidden="true">↗</span></strong></a>@endforeach</div></div></section>
    @endif

    @if($services->isNotEmpty())
        <section class="platform-section portfolio-services-section" id="services"><div class="container"><div class="split-heading"><div><p class="eyebrow"><span class="eyebrow-line"></span> What I offer</p><h2>Practical systems, carefully built.</h2></div><p class="body-copy">Choose the kind of support that matches the problem in front of you. Pricing and delivery guidance is shown only when it has been configured.</p></div><div class="portfolio-service-grid">@foreach($services as $service)<article class="portfolio-service-card"><div class="portfolio-service-card-head"><span class="portfolio-service-icon">{{ $service->icon ?: str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span><span>{{ $service->pricingText() }}</span></div><h3>{{ $service->title }}</h3><p>{{ $service->short_description }}</p>@if($service->delivery_estimate)<small>Typical delivery · {{ $service->delivery_estimate }}</small>@endif<a class="text-link" href="{{ route('services.show', $service) }}">View service <span aria-hidden="true">→</span></a></article>@endforeach</div></div></section>
    @else
        <section class="platform-section"><div class="container editorial-copy"><p class="eyebrow"><span class="eyebrow-line"></span> Services</p><h2>Services are being prepared.</h2><p class="body-copy">The service catalogue will appear here as it is added to the desk. If you already have a specific project in mind, start a conversation.</p><a class="button button-dark" href="{{ route('contact', ['module' => 'development']) }}">Start a project <span aria-hidden="true">↗</span></a></div></section>
    @endif

    @if($pageSections->get('process'))
        @php($process = $pageSections->get('process'))
        <section class="platform-dark portfolio-process-section"><div class="container"><div class="split-heading"><div><p class="eyebrow eyebrow-light"><span class="eyebrow-line"></span>{{ $process->payload['eyebrow'] ?? 'How I work' }}</p><h2>{{ $process->heading }}</h2></div><div class="rich-copy">{!! \App\Support\RichText::render($process->body) !!}</div></div>@if(!empty($process->payload['steps']))<div class="portfolio-process-grid">@foreach($process->payload['steps'] as $step)<div><span>{{ $step['index'] ?? str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span><h3>{{ $step['title'] ?? '' }}</h3><p>{{ $step['body'] ?? '' }}</p></div>@endforeach</div>@endif</div></section>
    @endif

    @if($featuredProjects->isNotEmpty())
        <section class="platform-section portfolio-projects-tease"><div class="container"><div class="split-heading"><div><p class="eyebrow"><span class="eyebrow-line"></span> Selected work</p><h2>Systems built for the work behind the work.</h2></div><a class="text-link" href="{{ route('projects') }}">View all projects <span aria-hidden="true">→</span></a></div><div class="portfolio-project-grid">@foreach($featuredProjects as $project)@php($projectImage = $project->featured_image ? \App\Support\PublicHero::candidate($project->featured_image) : null)<a class="portfolio-project-card" href="{{ route('projects.show', $project) }}">@if($projectImage)<img src="{{ $projectImage['url'] }}" @if($projectImage['srcset']) srcset="{{ $projectImage['srcset'] }}" sizes="(max-width: 760px) 100vw, 50vw" @endif alt="{{ $project->title }}" loading="lazy">@endif<div><span>{{ $project->project_type ?: 'Case study' }}</span><h3>{{ $project->title }}</h3><p>{{ $project->short_description }}</p></div></a>@endforeach</div></div></section>
    @endif

    <section class="platform-section portfolio-cta"><div class="container two-column"><div><p class="eyebrow"><span class="eyebrow-line"></span> Have something specific in mind?</p><h2>Tell me what you are trying to build.</h2></div><div><p class="body-copy">A short conversation is the best place to understand the problem, the people involved and what a useful outcome looks like.</p><a class="button button-dark" href="{{ route('contact', ['module' => 'development']) }}">Discuss your project <span aria-hidden="true">↗</span></a></div></div></section>
@endsection
