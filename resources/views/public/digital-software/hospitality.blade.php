@extends('layouts.public')

@section('service_context', 'development')
@section('title', 'Hospitality & Tourism digital solutions — Mwanafunzi Investor')
@section('description', 'Digital solutions for hotels, lodges, safari camps and tour operators: websites, enquiry journeys, operational systems and integrations.')
@section('canonical', route('digital-software.hospitality'))

@section('content')
    <x-public-hero class="public-page-hero hospitality-hero" eyebrow="Digital Software / Hospitality & Tourism" title="Digital solutions for hotels, lodges and tour operators." summary="Professional websites and operational systems shaped around guest journeys, direct enquiries and the work behind the desk." :image="$page?->hero_image" overlay="strong" setting="hero_tools_image" :fallback-image="config('public.hero_defaults.modules')">
        <div class="detail-actions"><a class="button button-dark" href="#hospitality-work">Explore hospitality work <span aria-hidden="true">↓</span></a><a class="text-link text-link-light" href="{{ route('digital-software.contact') }}">Discuss a project <span aria-hidden="true">↗</span></a></div>
    </x-public-hero>
    <section class="platform-dark" id="hospitality-work" aria-labelledby="hospitality-work-heading"><div class="container"><div class="split-heading"><div><p class="eyebrow eyebrow-light"><span class="eyebrow-line"></span> Selected work</p><h2 id="hospitality-work-heading">Hospitality and tourism projects.</h2></div><p class="body-copy">Published projects are shown only when their industry and public status have been documented in the CMS.</p></div><div class="digital-project-grid">@forelse($projects as $project)@php($projectImage = $project->featured_image ? \App\Support\PublicHero::candidate($project->featured_image) : null)<a class="digital-project-card digital-project-card-dark" href="{{ route('digital-software.projects.show', $project) }}">@if($projectImage)<img src="{{ $projectImage['url'] }}" alt="{{ $project->title }} project cover" loading="lazy">@endif<div class="digital-project-card-body"><div class="digital-project-card-kicker"><span>{{ $project->industry }}</span><span>{{ $project->statusLabel() }}</span></div><h3>{{ $project->title }}</h3><p>{{ $project->short_description }}</p><strong>View case study <span aria-hidden="true">→</span></strong></div></a>@empty<div class="service-empty-note">Hospitality case studies will appear here as the relevant project records are documented and published.</div>@endforelse</div>{{ $projects->links() }}</div></section>
@endsection
