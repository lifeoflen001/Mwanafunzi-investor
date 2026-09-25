@extends('layouts.public')
@section('title', $page?->seo_title ?: \App\Models\SiteSetting::getValue('default_seo_title'))
@section('description', $page?->seo_description ?: $page?->hero_summary)
@php
    $cmsPhilosophy = $page?->sections?->where('is_enabled', true)->firstWhere('key', 'philosophy');
    $cmsApproach = $page?->sections?->where('is_enabled', true)->firstWhere('key', 'approach');
    $heroTitleHtml = $page?->hero_title && $page?->hero_highlight ? e($page->hero_title).'<br><em>'.e($page->hero_highlight).'</em>' : null;
@endphp
@section('content')
<x-public-hero class="public-page-hero" :eyebrow="$page?->hero_eyebrow" :title="$page?->hero_title" :title-html="$heroTitleHtml" :summary="$page?->hero_summary" :image="$page?->hero_image" :focal-point="$page?->hero_focal_point" :overlay="$page?->hero_overlay" :alignment="$page?->hero_alignment" setting="hero_about_image" :fallback-image="config('public.hero_defaults.about')">
    @if($page?->hero_primary_label && $page?->hero_primary_url)
        <div class="detail-actions"><a class="button button-accent" href="{{ $page->hero_primary_url }}">{{ $page->hero_primary_label }} <span aria-hidden="true">↗</span></a>@if($page->hero_secondary_label && $page->hero_secondary_url)<a class="text-link text-link-light" href="{{ $page->hero_secondary_url }}">{{ $page->hero_secondary_label }} <span aria-hidden="true">↗</span></a>@endif</div>
    @endif
</x-public-hero>
@if($cmsPhilosophy)
<section class="platform-section"><div class="container editorial-copy"><p class="eyebrow">{{ $cmsPhilosophy->payload['eyebrow'] ?? '' }}</p>@if($cmsPhilosophy->heading)<h2>{{ $cmsPhilosophy->heading }}</h2>@endif @if($cmsPhilosophy->body)<div class="rich-copy">{!! \App\Support\RichText::render($cmsPhilosophy->body) !!}</div>@endif @if(!empty($cmsPhilosophy->payload['pull']))<div class="editorial-pull">{{ $cmsPhilosophy->payload['pull'] }}</div>@endif</div></section>
@endif
@if($cmsApproach)
<section class="platform-dark"><div class="container two-column"><div><p class="eyebrow eyebrow-light"><span class="eyebrow-line"></span> {{ $cmsApproach->payload['eyebrow'] ?? '' }}</p><h2>{{ $cmsApproach->heading }}</h2></div><div><div class="rich-copy">{!! \App\Support\RichText::render($cmsApproach->body) !!}</div>@if($cmsApproach->cta_label && $cmsApproach->cta_url)<a class="button button-accent" href="{{ $cmsApproach->cta_url }}">{{ $cmsApproach->cta_label }} <span aria-hidden="true">↗</span></a>@endif</div></div></section>
@endif
@endsection
