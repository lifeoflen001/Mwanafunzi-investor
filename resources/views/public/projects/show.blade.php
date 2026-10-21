@extends('layouts.public')

@php
    $heroImage = $project->featured_image ? \App\Support\PublicHero::candidate($project->featured_image) : null;
    $projectCanonical = \App\Support\Seo::resolve($project->canonical_url, route('projects.show', $project));
    $isStudioProject = $isStudioProject ?? false;
    $studioService = $studioService ?? null;
@endphp

@section('title', $project->seo_title ?: $project->title.' — Mwanafunzi Investor')
@section('description', $project->seo_description ?: $project->short_description)
@section('canonical', $projectCanonical)
@section('og_title', $project->og_title ?: $project->seo_title ?: $project->title)
@section('og_description', $project->og_description ?: $project->seo_description ?: $project->short_description)
@section('robots', $project->robots ?: 'index,follow')
@if($isStudioProject) @section('service_context', 'studio') @endif
@if($heroImage) @section('og_image', $heroImage['url']) @endif
@section('structured_data')<script type="application/ld+json">{!! json_encode(['@context' => 'https://schema.org', '@type' => $isStudioProject ? 'VisualArtwork' : 'CreativeWork', 'name' => $project->title, 'description' => $project->short_description, 'url' => $projectCanonical, 'image' => $heroImage['url'] ?? null, 'dateCreated' => ($project->event_date ?: $project->project_date)?->toDateString()], JSON_UNESCAPED_SLASHES) !!}</script>@endsection

@section('content')
    @if($isStudioProject)
        @php
            $featuredMedia = $project->gallery->firstWhere('path', $project->featured_image);
            $galleryItems = $project->gallery->reject(fn ($media) => $media->path === $project->featured_image)->values();
            $studioCategory = $project->creative_category ?: $project->project_type ?: 'Creative Studio';
            $studioMediaType = $project->media_type ?: 'photography';
            $featuredIsVideo = $featuredMedia && str_starts_with((string) $featuredMedia->mime_type, 'video/');
            $enquiryParameters = array_filter(['project' => $project->slug, 'service' => $studioService?->slug]);
            $enquiryUrl = route('creative-studio.book', $enquiryParameters);
            $galleryData = $galleryItems->map(function ($media) {
                $candidate = \App\Support\PublicHero::candidate($media->path) ?: [];
                $variants = collect($media->variants ?: [])->sortBy('width')->values();
                $fullVariant = $variants->last();
                $posterVariant = $variants->firstWhere('kind', 'poster');
                return [
                    'type' => str_starts_with((string) $media->mime_type, 'video/') ? 'video' : 'image',
                    'url' => $candidate['url'] ?? asset('storage/'.$media->path),
                    'srcset' => $candidate['srcset'] ?? '',
                    'fullUrl' => $fullVariant ? asset('storage/'.$fullVariant['path']) : ($candidate['url'] ?? asset('storage/'.$media->path)),
                    'poster' => $posterVariant ? asset('storage/'.$posterVariant['path']) : null,
                    'width' => $media->width,
                    'height' => $media->height,
                    'mime' => $media->mime_type,
                    'alt' => $media->pivot->alt_text ?: $media->alt_text ?: $project->title,
                    'caption' => $media->pivot->caption ?: $media->caption,
                ];
            })->values();
        @endphp

        <section class="studio-project-intro">
            <div class="container studio-project-intro-grid">
                <div>
                    <nav class="studio-project-breadcrumbs" aria-label="Breadcrumb">
                        <a href="{{ route('creative-studio') }}">Creative Studio</a>
                        <span aria-hidden="true">→</span>
                        <a href="{{ route('creative-studio.projects') }}">Projects</a>
                        <span aria-hidden="true">→</span>
                        <span aria-current="page">{{ $project->title }}</span>
                    </nav>
                    <p class="eyebrow"><span class="eyebrow-line"></span>{{ $studioCategory }}</p>
                    <h1>{{ $project->title }}</h1>
                    @if($project->short_description)<p class="studio-project-lede">{{ $project->short_description }}</p>@endif
                    <a class="button button-accent" href="{{ $enquiryUrl }}">Enquire about {{ strtolower($studioCategory) }} <span aria-hidden="true">↗</span></a>
                </div>
                <div class="studio-project-intro-note" aria-label="Project format">
                    <span>Format</span>
                    <strong>{{ ucfirst($studioMediaType) }}</strong>
                </div>
            </div>
        </section>

        @if($heroImage)<section class="studio-project-featured"><div class="container"><figure>@if($featuredIsVideo)<video controls preload="metadata" width="{{ $featuredMedia->width ?: '' }}" height="{{ $featuredMedia->height ?: '' }}" aria-label="{{ $featuredMedia->pivot->alt_text ?: $featuredMedia->alt_text ?: $project->title }}"><source src="{{ $featuredMedia->url }}" type="{{ $featuredMedia->mime_type }}"></video>@else<img src="{{ $heroImage['url'] }}" @if($heroImage['srcset']) srcset="{{ $heroImage['srcset'] }}" sizes="(max-width: 760px) 100vw, 1280px" @endif width="{{ $heroImage['width'] ?? '' }}" height="{{ $heroImage['height'] ?? '' }}" alt="{{ $featuredMedia?->pivot?->alt_text ?: $featuredMedia?->alt_text ?: $project->title }}" style="object-position: {{ $project->featured_focal_point ?: 'center center' }}" fetchpriority="high">@endif @if($featuredMedia?->pivot?->caption)<figcaption>{{ $featuredMedia->pivot->caption }}</figcaption>@endif</figure></div></section>@endif

        @php($verifiedDetails = collect([['label' => 'Photography type', 'value' => $project->photography_type], ['label' => 'Services provided', 'value' => $project->services_provided ? collect($project->services_provided)->join(', ') : null], ['label' => 'Location', 'value' => $project->location], ['label' => 'Event date', 'value' => $project->event_date?->format('F j, Y')]])->filter(fn ($detail) => filled($detail['value'])))
        @if($verifiedDetails->isNotEmpty())<section class="studio-project-details" aria-labelledby="studio-project-details-heading"><div class="container"><div class="studio-project-details-inner"><p class="eyebrow"><span class="eyebrow-line"></span> Project details</p><h2 id="studio-project-details-heading">The brief, kept clear.</h2><dl>@foreach($verifiedDetails as $detail)<div><dt>{{ $detail['label'] }}</dt><dd>{{ $detail['value'] }}</dd></div>@endforeach</dl></div></div></section>@endif

        @if($galleryItems->isNotEmpty())<section class="studio-project-gallery" aria-labelledby="studio-project-gallery-heading"><div class="container"><div class="studio-project-section-heading"><div><p class="eyebrow"><span class="eyebrow-line"></span> Selected frames</p><h2 id="studio-project-gallery-heading">A collection of moments.</h2></div><p>{{ $galleryItems->count() === 1 ? '1 additional frame.' : $galleryItems->count().' additional frames.' }}</p></div><div class="studio-gallery-grid">@foreach($galleryItems as $index => $media)@php($candidate = \App\Support\PublicHero::candidate($media->path) ?: [])@if(str_starts_with((string) $media->mime_type, 'video/'))<figure class="studio-gallery-item studio-gallery-item-video"><video controls preload="metadata" width="{{ $media->width ?: '' }}" height="{{ $media->height ?: '' }}" aria-label="{{ $media->pivot->alt_text ?: $media->alt_text ?: $project->title }}" @if($media->variants && collect($media->variants)->firstWhere('kind', 'poster')) poster="{{ asset('storage/'.collect($media->variants)->firstWhere('kind', 'poster')['path']) }}" @endif><source src="{{ $media->url }}" type="{{ $media->mime_type }}"></video>@if($media->pivot->caption)<figcaption>{{ $media->pivot->caption }}</figcaption>@endif</figure>@else<figure class="studio-gallery-item"><button type="button" class="studio-gallery-trigger" data-gallery-trigger data-gallery-index="{{ $index }}" aria-label="Open image {{ $index + 1 }} of {{ $galleryItems->count() }}"><img src="{{ $candidate['url'] ?? $media->url }}" @if($candidate['srcset'] ?? null) srcset="{{ $candidate['srcset'] }}" sizes="(max-width: 760px) 100vw, (max-width: 1100px) 50vw, 620px" @endif width="{{ $media->width ?: '' }}" height="{{ $media->height ?: '' }}" alt="{{ $media->pivot->alt_text ?: $media->alt_text ?: $project->title }}" loading="lazy"><span class="studio-gallery-open" aria-hidden="true">View</span></button>@if($media->pivot->caption)<figcaption>{{ $media->pivot->caption }}</figcaption>@endif</figure>@endif @endforeach</div></div></section><div class="studio-gallery-viewer" data-gallery-viewer hidden role="dialog" aria-modal="true" aria-labelledby="studio-gallery-viewer-title"><script type="application/json" data-gallery-items-json>@json($galleryData)</script><div class="studio-gallery-viewer-inner"><div class="sr-only" id="studio-gallery-viewer-title">Project gallery viewer</div><button type="button" class="studio-gallery-viewer-close" data-gallery-close aria-label="Close gallery">×</button><button type="button" class="studio-gallery-viewer-control studio-gallery-viewer-prev" data-gallery-prev aria-label="Previous image">←</button><div class="studio-gallery-viewer-stage" data-gallery-stage><p class="studio-gallery-viewer-count" data-gallery-count aria-live="polite"></p><div data-gallery-media></div><p class="studio-gallery-viewer-caption" data-gallery-caption></p></div><button type="button" class="studio-gallery-viewer-control studio-gallery-viewer-next" data-gallery-next aria-label="Next image">→</button></div></div>@endif

        <div class="studio-project-back"><div class="container"><a class="text-link" href="{{ route('creative-studio.projects') }}">Back to creative projects <span aria-hidden="true">→</span></a></div></div>
    @else
        <x-public-hero class="public-page-hero project-detail-hero" :eyebrow="$project->project_type ?: 'Case study'" :title="$project->title" :summary="$project->short_description" :image="$project->featured_image" :overlay="'strong'" setting="hero_tools_image" :fallback-image="config('public.hero_defaults.modules')" :breadcrumbs="[['label' => 'Projects', 'url' => route('projects')], ['label' => $project->title]]"><div class="project-meta-row">@if($project->industry)<span><small>Industry</small>{{ $project->industry }}</span>@endif @if($project->platform)<span><small>Platform</small>{{ $project->platform }}</span>@endif @if($project->timeline)<span><small>Timeline</small>{{ $project->timeline }}</span>@endif</div></x-public-hero>
        <section class="platform-section"><div class="container project-detail-layout"><article class="project-case-study">@if($project->full_introduction)<div class="project-intro rich-copy">{!! \App\Support\RichText::render($project->full_introduction) !!}</div>@endif @foreach([['challenge', 'The challenge'], ['role', 'My role'], ['approach', 'The approach'], ['solution', 'The solution']] as [$field, $heading]) @if($project->{$field})<section><p class="eyebrow"><span class="eyebrow-line"></span> {{ $heading }}</p><div class="rich-copy">{!! \App\Support\RichText::render($project->{$field}) !!}</div></section>@endif @endforeach @if($project->features)<section><p class="eyebrow"><span class="eyebrow-line"></span> Key features</p><div class="project-feature-list">@foreach($project->features as $feature)<div><h3>{{ $feature['title'] ?? '' }}</h3><p>{{ $feature['description'] ?? '' }}</p></div>@endforeach</div></section>@endif @if($project->results)<section><p class="eyebrow"><span class="eyebrow-line"></span> Result / impact</p><div class="project-results">@foreach($project->results as $result)<div><strong>{{ $result['value'] ?? '' }}</strong><span>{{ $result['label'] ?? '' }}</span>@if(!empty($result['description']))<p>{{ $result['description'] }}</p>@endif</div>@endforeach</div></section>@endif</article><aside class="project-detail-aside"><div><span>Role</span><strong>{{ $project->role ? 'Defined in case study' : 'Project delivery' }}</strong></div>@if($project->services->isNotEmpty())<div><span>Services</span><strong>{{ $project->services->pluck('title')->join(', ') }}</strong></div>@endif @if($project->technologies)<div><span>Technology</span><div class="portfolio-tags">@foreach($project->technologies as $technology)<small>{{ $technology }}</small>@endforeach</div></div>@endif @if($project->live_url)<a class="text-link" href="{{ $project->live_url }}" target="_blank" rel="noopener">Visit project <span aria-hidden="true">↗</span></a>@endif</aside></div></section>
        @if($project->gallery->isNotEmpty())<section class="platform-section platform-muted project-gallery-section"><div class="container"><p class="eyebrow"><span class="eyebrow-line"></span> Project gallery</p><div class="project-gallery">@foreach($project->gallery as $media)<figure>@if(str_starts_with((string) $media->mime_type, 'video/'))<video controls preload="metadata" width="{{ $media->width ?: '' }}" height="{{ $media->height ?: '' }}" aria-label="{{ $media->alt_text ?: $project->title }}"><source src="{{ $media->url }}" type="{{ $media->mime_type }}"></video>@else<img src="{{ $media->url }}" width="{{ $media->width ?: '' }}" height="{{ $media->height ?: '' }}" alt="{{ $media->pivot->alt_text ?: $media->alt_text ?: $project->title }}" loading="lazy">@endif @if($media->pivot->caption)<figcaption>{{ $media->pivot->caption }}</figcaption>@endif</figure>@endforeach</div></div></section>@endif
        @if($project->testimonials->isNotEmpty())<section class="platform-section testimonial-section project-testimonials" aria-labelledby="project-testimonials-heading"><div class="container"><div class="testimonial-section-heading"><div><p class="eyebrow"><span class="eyebrow-line"></span> Client perspective</p><h2 id="project-testimonials-heading">What the work felt like from the other side.</h2></div><p class="testimonial-section-description">An approved perspective connected to this project.</p></div>@include('partials.testimonial-carousel', ['testimonials' => $project->testimonials, 'carouselId' => 'project-testimonials'])</div></section>@endif
        @if($relatedProjects->isNotEmpty())<section class="platform-section"><div class="container"><div class="split-heading"><div><p class="eyebrow"><span class="eyebrow-line"></span> Keep exploring</p><h2>Related projects.</h2></div></div><div class="portfolio-project-grid">@foreach($relatedProjects as $related)<a class="portfolio-project-card" href="{{ route('projects.show', $related) }}">@if($related->featured_image)<img src="{{ \App\Support\PublicHero::candidate($related->featured_image)['url'] ?? asset(config('public.hero_defaults.default')) }}" alt="{{ $related->title }}" loading="lazy">@endif<div><span>{{ $related->project_type ?: 'Case study' }}</span><h3>{{ $related->title }}</h3><p>{{ $related->short_description }}</p></div></a>@endforeach</div></div></section>@endif
    @endif
@endsection
