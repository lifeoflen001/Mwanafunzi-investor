@extends('layouts.public')

@php($pageSections = $page?->sections?->where('is_enabled', true)->keyBy('key') ?? collect())
@php($divisionsSection = $pageSections->get('divisions'))
@php($finalCtaSection = $pageSections->get('final-cta'))
@section('title', $page?->seo_title ?: 'Services — Mwanafunzi Investor')
@section('description', $page?->seo_description ?: 'Digital software, websites and operational tools shaped around real business needs.')
@section('canonical', $page?->canonical_url ?: route('services'))

@section('content')
<x-public-hero class="public-page-hero services-hero" :eyebrow="$page?->hero_eyebrow ?: 'Mwanafunzi Investor / Services'" :title="$page?->hero_title ?: 'Services built around real business needs'" :title-html="$page?->hero_highlight ? e($page->hero_title ?: 'Services').'<br><em>'.e($page->hero_highlight).'</em>' : null" :summary="$page?->hero_summary ?: 'Digital software, websites and operational tools shaped around the way your business actually works.'" :image="$page?->hero_image" :overlay="$page?->hero_overlay ?: 'strong'" setting="hero_tools_image" :fallback-image="config('public.hero_defaults.modules')">
    <a class="button button-accent" href="{{ \App\Support\Seo::resolve($page?->hero_primary_url, route('contact', ['module' => 'development'])) }}">{{ $page?->hero_primary_label ?: 'Start a project' }} <span aria-hidden="true">↗</span></a>
</x-public-hero>

@if($divisionsSection && $businessUnits->isNotEmpty())
<section class="platform-section portfolio-divisions-section" id="divisions">
    <div class="container">
        <div class="split-heading">
            <div>
                <p class="eyebrow"><span class="eyebrow-line"></span> {{ \App\Support\CmsContent::value($divisionsSection, 'eyebrow', 'The Mwanafunzi ecosystem') }}</p>
                <h2>{{ $divisionsSection->heading }}</h2>
            </div>
            @if($divisionsSection->body)<p class="body-copy">{{ $divisionsSection->body }}</p>@endif
        </div>
        <div class="portfolio-division-grid">@foreach($businessUnits as $businessUnit)<a class="portfolio-division-card" href="{{ $serviceRoutes[$businessUnit->slug] ?? ($businessUnit->route_name ? route($businessUnit->route_name) : route('contact')) }}"><span>{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                <h3>{{ $businessUnit->name }}</h3>
                <p>{{ $businessUnit->description }}</p><strong>Explore {{ $businessUnit->name }} <span aria-hidden="true">↗</span></strong>
            </a>@endforeach</div>
    </div>
</section>
@endif

@if($pageSections->get('process'))
@php($process = $pageSections->get('process'))
<section class="platform-dark portfolio-process-section">
    <div class="container">
        <div class="split-heading">
            <div>
                <p class="eyebrow eyebrow-light"><span class="eyebrow-line"></span>{{ $process->payload['eyebrow'] ?? 'How I work' }}</p>
                <h2>{{ $process->heading }}</h2>
            </div>
            <div class="rich-copy">{!! \App\Support\RichText::render($process->body) !!}</div>
        </div>@if(!empty($process->payload['steps']))<div class="portfolio-process-grid">@foreach($process->payload['steps'] as $step)<div><span>{{ $step['index'] ?? str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                <h3>{{ $step['title'] ?? '' }}</h3>
                <p>{{ $step['body'] ?? '' }}</p>
            </div>@endforeach</div>@endif
    </div>
</section>
@endif

@if($finalCtaSection)
<section class="platform-section portfolio-cta">
    <div class="container two-column">
        <div>
            <p class="eyebrow"><span class="eyebrow-line"></span> {{ \App\Support\CmsContent::value($finalCtaSection, 'eyebrow', 'Have something specific in mind?') }}</p>
            <h2>{{ $finalCtaSection->heading }}</h2>
        </div>
        <div>
            @if($finalCtaSection->body)<p class="body-copy">{{ $finalCtaSection->body }}</p>@endif
            @if($finalCtaSection->cta_label && $finalCtaSection->cta_url)<a class="button button-dark" href="{{ \App\Support\Seo::resolve($finalCtaSection->cta_url, route('contact', ['module' => 'development'])) }}">{{ $finalCtaSection->cta_label }} <span aria-hidden="true">↗</span></a>@endif
        </div>
    </div>
</section>
@endif
@endsection
