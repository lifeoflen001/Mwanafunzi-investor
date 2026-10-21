@extends('layouts.public')

@section('service_context', $module->slug)
@php
    $sections = $page?->sections->where('is_enabled', true)->keyBy('key') ?? collect();
    $section = $sections->get($collection);
    $isStudio = $module->slug === 'studio';
    $titles = [
        'products' => 'Software products',
        'services' => 'Studio services',
        'projects' => $isStudio ? 'Studio projects' : 'Software projects',
        'testimonials' => $isStudio ? 'Studio testimonials' : 'Software testimonials',
    ];
    $intro = [
        'products' => 'Practical digital products shaped around the way your organisation needs to work.',
        'services' => 'Photography, videography, printing and production support for people, brands and occasions.',
        'projects' => 'A considered selection of work made with real people, real constraints and a clear purpose.',
        'testimonials' => 'A selection of approved client perspectives from the work we have delivered.',
    ];
@endphp

@section('title', $titles[$collection].' — '.$module->name)
@section('description', $intro[$collection])
@section('canonical', url()->current())

@section('content')
    <x-public-hero class="service-collection-hero" compact :eyebrow="$module->name.' / '.ucfirst($collection)" :title="$titles[$collection]" :summary="$intro[$collection]" :image="$page?->hero_image" :overlay="$page?->hero_overlay ?: 'strong'" setting="hero_tools_image" :fallback-image="config('public.hero_defaults.modules')">
        <div class="detail-actions"><a class="button button-accent" href="{{ $isStudio ? route('creative-studio.book') : route('contact', ['module' => $module->slug]) }}">{{ $isStudio ? 'Book a shoot' : 'Start a project' }} <span aria-hidden="true">↗</span></a><a class="text-link text-link-light" href="{{ $isStudio ? route('creative-studio') : route('digital-systems') }}">Back to {{ $module->name }} <span aria-hidden="true">↗</span></a></div>
    </x-public-hero>

    @if($collection === 'products')
        <section class="platform-section"><div class="container"><div class="split-heading"><div><p class="eyebrow"><span class="eyebrow-line"></span> {{ $section?->payload['eyebrow'] ?? ucfirst($collection) }}</p><h2>{{ $section?->heading ?: $titles[$collection] }}</h2></div><div class="body-copy rich-copy">{!! \App\Support\RichText::render($section?->body ?: $intro[$collection]) !!}</div></div><div class="service-card-grid">@forelse($section?->payload['cards'] ?? [] as $card)<article class="service-card"><span>{{ $card['index'] ?? str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span><h3>{{ $card['title'] ?? '' }}</h3><p>{{ $card['body'] ?? '' }}</p>@if(!empty($card['tags']))<small>{{ $card['tags'] }}</small>@endif</article>@empty<div class="service-empty-note">This catalogue is being prepared. Contact us and we will help shape the right brief.</div>@endforelse</div></div></section>
    @elseif($collection === 'services')
        <section class="platform-section"><div class="container"><div class="split-heading"><div><p class="eyebrow"><span class="eyebrow-line"></span> {{ $section?->payload['eyebrow'] ?? 'Services' }}</p><h2>{{ $section?->heading ?: $titles[$collection] }}</h2></div><div class="body-copy rich-copy">{!! \App\Support\RichText::render($section?->body ?: $intro[$collection]) !!}</div></div><div class="service-card-grid">@forelse($services as $service)<a class="service-card" href="{{ $isStudio ? route('creative-studio.services.show', $service) : route('services.show', $service) }}">@if($service->featured_image)<img src="{{ \App\Support\PublicHero::candidate($service->featured_image)['url'] ?? asset(config('public.hero_defaults.default')) }}" alt="{{ $service->title }}" loading="lazy">@endif<span>{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span><h3>{{ $service->title }}</h3><p>{{ $service->short_description }}</p><small>{{ $service->pricingText() }}</small></a>@empty<div class="service-empty-note">No published services have been added yet. The studio team can add real services from the admin desk before they appear here.</div>@endforelse</div></div></section>
    @elseif($collection === 'projects')
        <section class="platform-section"><div class="container"><div class="service-project-grid service-project-grid-wide">@forelse($projects as $project)@php($projectImage = $project->featured_image ? \App\Support\PublicHero::candidate($project->featured_image) : null)<a class="service-project-card" href="{{ route('projects.show', $project) }}">@if($projectImage)<img src="{{ $projectImage['url'] }}" alt="{{ $project->title }}" loading="lazy">@endif<div><span>{{ $project->project_type ?: ($isStudio ? 'Creative project' : 'Software project') }}</span><h3>{{ $project->title }}</h3><p>{{ $project->short_description }}</p>@if($project->services->isNotEmpty())<small>{{ $project->services->pluck('title')->join(' · ') }}</small>@endif</div></a>@empty<div class="service-empty-note">Published projects will appear here as case studies are added to the admin desk.</div>@endforelse</div>{{ $projects->links() }}</div></section>
    @elseif($testimonials->isNotEmpty())
        <section class="platform-section testimonial-section" aria-labelledby="collection-testimonials-heading"><div class="container"><div class="testimonial-section-heading"><div><p class="eyebrow"><span class="eyebrow-line"></span> {{ $section?->payload['eyebrow'] ?? 'Client perspective' }}</p><h2 id="collection-testimonials-heading">{{ $section?->heading ?: $titles[$collection] }}</h2></div><div class="testimonial-section-description rich-copy">{!! \App\Support\RichText::render($section?->body ?: $intro[$collection]) !!}</div></div>@include('partials.testimonial-carousel', ['testimonials' => $testimonials, 'carouselId' => $module->slug.'-testimonials']){{ $testimonials->links() }}</div></section>
    @endif
@endsection
