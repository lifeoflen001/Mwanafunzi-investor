@extends('layouts.public')
@section('title', $page->seo_title ?: $page->name.' — Mwanafunzi Investor')
@section('description', $page->seo_description ?: $page->hero_summary ?: '')
@section('content')
@php
    $pageTitleHtml = $page->hero_highlight ? e($page->hero_title ?: $page->name).'<br> <em>'.e($page->hero_highlight).'</em>' : null;
@endphp
<x-public-hero class="public-page-hero" :eyebrow="$page->hero_eyebrow ?: $page->name" :title="$page->hero_title ?: $page->name" :title-html="$pageTitleHtml" :summary="$page->hero_summary" :image="$page->hero_image" :overlay="$page->hero_overlay" :alignment="$page->hero_alignment" :breadcrumbs="[['label' => 'Home', 'url' => route('home')], ['label' => $page->name]]" />
@foreach($page->sections->where('is_enabled', true)->sortBy('sort_order') as $section)
    @php($sectionImage = $section->image ? \App\Support\PublicHero::candidate($section->image) : null)
    @php($sectionPayload = $section->payload ?: [])
    <section @if(!empty($sectionPayload['anchor_id'])) id="{{ $sectionPayload['anchor_id'] }}" @endif class="platform-section cms-public-section cms-section-{{ $section->section_type }}"><div class="container editorial-copy">@if(!empty($sectionPayload['eyebrow']))<p class="eyebrow">{{ $sectionPayload['eyebrow'] }}</p>@endif @if($sectionImage)<figure class="cms-section-figure"><img class="cms-section-image" src="{{ $sectionImage['url'] }}" alt="{{ $sectionPayload['image_alt'] ?? '' }}" loading="lazy">@if(!empty($sectionPayload['image_caption']))<figcaption>{{ $sectionPayload['image_caption'] }}</figcaption>@endif</figure>@endif @if($section->heading)<h2>{{ $section->heading }}</h2>@endif @if($section->body)<div class="rich-copy">{!! \App\Support\RichText::render($section->body) !!}</div>@endif @if($section->cta_label && $section->cta_url)<a class="button button-accent" href="{{ \App\Support\Seo::resolve($section->cta_url, '#') }}" target="{{ $sectionPayload['cta_target'] ?? '_self' }}" @if(($sectionPayload['cta_target'] ?? '_self') === '_blank') rel="noopener" @endif>{{ $section->cta_label }} <span aria-hidden="true">↗</span></a>@endif</div></section>
@endforeach
@if($page->faqs?->isNotEmpty())
    <section class="platform-section cms-faq-section"><div class="container editorial-copy"><p class="eyebrow">Questions</p><h2>Frequently asked questions</h2>@foreach($page->faqs as $faq)<details><summary>{{ $faq->question }} <span>+</span></summary><p>{{ $faq->answer }}</p></details>@endforeach</div></section>
@endif
@endsection
