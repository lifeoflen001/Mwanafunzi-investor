@extends('layouts.public')
@section('title', $page?->seo_title ?: \App\Models\SiteSetting::getValue('default_seo_title'))
@section('description', $page?->seo_description ?: $page?->hero_summary)
@php
    $cmsPhilosophy = $page?->sections?->where('is_enabled', true)->firstWhere('key', 'philosophy');
    $cmsMissionVision = $page?->sections?->where('is_enabled', true)->firstWhere('key', 'mission-vision');
    $cmsApproach = $page?->sections?->where('is_enabled', true)->firstWhere('key', 'approach');
    $storyPayload = $cmsPhilosophy?->payload ?: [];
    $storyImage = $cmsPhilosophy?->image ? \App\Support\PublicHero::candidate($cmsPhilosophy->image) : null;
    $storyImagePosition = \App\Support\HeroFocalPoint::normalise($storyPayload['image_focal_point'] ?? 'center center');
    $missionPayload = $cmsMissionVision?->payload ?: [];
    $heroTitleHtml = $page?->hero_title && $page?->hero_highlight ? e($page->hero_title).'<br><em>'.e($page->hero_highlight).'</em>' : null;
@endphp
@section('content')
<x-public-hero class="public-page-hero" :eyebrow="$page?->hero_eyebrow" :title="$page?->hero_title" :title-html="$heroTitleHtml" :summary="$page?->hero_summary" :image="$page?->hero_image" :focal-point="$page?->hero_focal_point" :overlay="$page?->hero_overlay" :alignment="$page?->hero_alignment" setting="hero_about_image" :fallback-image="config('public.hero_defaults.about')">
    @if($page?->hero_primary_label && $page?->hero_primary_url)
        <div class="detail-actions"><a class="button button-accent" href="{{ $page->hero_primary_url }}">{{ $page->hero_primary_label }} <span aria-hidden="true">↗</span></a>@if($page->hero_secondary_label && $page->hero_secondary_url)<a class="text-link text-link-light" href="{{ $page->hero_secondary_url }}">{{ $page->hero_secondary_label }} <span aria-hidden="true">↗</span></a>@endif</div>
    @endif
</x-public-hero>
@if($cmsPhilosophy)
<section class="about-story-section">
    <div class="container about-story-grid">
        @if($storyImage)
            <figure class="about-story-media" style="--about-story-image-position:{{ $storyImagePosition }}"><img src="{{ $storyImage['url'] }}" @if($storyImage['srcset']) srcset="{{ $storyImage['srcset'] }}" sizes="(max-width: 760px) 100vw, 48vw" @endif alt="{{ $storyPayload['image_alt'] ?? '' }}" loading="lazy"></figure>
        @else
            <div class="about-story-media is-empty" aria-hidden="true"></div>
        @endif
        <div class="about-story-content">
            <p class="eyebrow"><span class="eyebrow-line"></span> {{ $storyPayload['eyebrow'] ?? '' }}</p>
            @if($cmsPhilosophy->heading)<h2>{{ $cmsPhilosophy->heading }}</h2>@endif
            <div class="about-story-subsections">
                @if(!empty($storyPayload['who_heading']) || !empty($storyPayload['who_body']))<article><h3>{{ $storyPayload['who_heading'] ?? 'Who we are' }}</h3><div class="rich-copy">{!! \App\Support\RichText::render($storyPayload['who_body'] ?? '') !!}</div></article>@endif
                @if(!empty($storyPayload['story_heading']) || !empty($storyPayload['story_body']))<article><h3>{{ $storyPayload['story_heading'] ?? 'Our story' }}</h3><div class="rich-copy">{!! \App\Support\RichText::render($storyPayload['story_body'] ?? '') !!}</div></article>@endif
            </div>
            @if(!empty($storyPayload['quote']))<blockquote class="about-story-quote">@if(!empty($storyPayload['quote_label']))<span>{{ $storyPayload['quote_label'] }}</span>@endif<p>{{ $storyPayload['quote'] }}</p></blockquote>@endif
        </div>
    </div>
</section>
@endif
@if($cmsMissionVision && (($missionPayload['mission_enabled'] ?? true) || ($missionPayload['vision_enabled'] ?? true)))
<section class="about-mission-vision"><div class="container"><div class="about-mission-vision-grid">
    @if($missionPayload['mission_enabled'] ?? true)<article><p class="eyebrow">{{ $missionPayload['mission_label'] ?? 'Our mission' }}</p>@if(!empty($missionPayload['mission_heading']))<h3>{{ $missionPayload['mission_heading'] }}</h3>@endif<div class="rich-copy">{!! \App\Support\RichText::render($missionPayload['mission_body'] ?? '') !!}</div></article>@endif
    @if($missionPayload['vision_enabled'] ?? true)<article><p class="eyebrow">{{ $missionPayload['vision_label'] ?? 'Our vision' }}</p>@if(!empty($missionPayload['vision_heading']))<h3>{{ $missionPayload['vision_heading'] }}</h3>@endif<div class="rich-copy">{!! \App\Support\RichText::render($missionPayload['vision_body'] ?? '') !!}</div></article>@endif
</div></div></section>
@endif
@if($cmsApproach)
<section class="platform-dark"><div class="container two-column"><div><p class="eyebrow eyebrow-light"><span class="eyebrow-line"></span> {{ $cmsApproach->payload['eyebrow'] ?? '' }}</p><h2>{{ $cmsApproach->heading }}</h2></div><div><div class="rich-copy">{!! \App\Support\RichText::render($cmsApproach->body) !!}</div>@if($cmsApproach->cta_label && $cmsApproach->cta_url)<a class="button button-accent" href="{{ $cmsApproach->cta_url }}">{{ $cmsApproach->cta_label }} <span aria-hidden="true">↗</span></a>@endif</div></div></section>
@endif
@if(!empty($teamMembers))
<section class="team-section">
    <div class="container">
        <div class="team-section-heading">
            <div>
                <p class="eyebrow"><span class="eyebrow-line"></span> The people behind the work</p>
                <h2>A small team with a serious point of view.</h2>
            </div>
            <p>Good work is built by people who care about the details, stay curious and keep the process honest.</p>
        </div>
        <div class="team-grid">
            @foreach($teamMembers as $member)
                <a class="team-card" href="{{ route('team.show', $member->slug) }}">
                    @php($memberImage = $member->portrait ? \App\Support\PublicHero::candidate($member->portrait) : null)
                    <div class="team-card-media">@if($memberImage)<img src="{{ $memberImage['url'] }}" @if($memberImage['srcset']) srcset="{{ $memberImage['srcset'] }}" sizes="(max-width: 760px) 90vw, 42vw" @endif @if($memberImage['width']) width="{{ $memberImage['width'] }}" height="{{ $memberImage['height'] }}" @endif alt="{{ $member->name }}" loading="lazy" style="object-position:{{ $member->portrait_focal_point ?: 'center 25%' }}">@endif</div>
                    <div class="team-card-body"><div><span class="eyebrow">{{ $member->role ?: ($member->department ?: 'Mwanafunzi Investor team') }}</span><h3>{{ $member->name }}</h3>@if($member->short_intro)<p>{{ $member->short_intro }}</p>@endif</div><span class="team-card-arrow" aria-hidden="true">↗</span></div>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif
@endsection
