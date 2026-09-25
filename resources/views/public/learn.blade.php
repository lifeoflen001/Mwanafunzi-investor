@extends('layouts.public')
@php
    $intro = $page?->section('intro');
    $introPayload = $intro?->payload ?? [];
    $principle = $page?->section('principle');
@endphp
@section('title', $page?->seo_title ?: 'Learn — Mwanafunzi Investor')
@section('description', $page?->seo_description ?: ($intro?->body ?: 'Structured learning paths for forex foundations, mechanical analysis, risk planning and running trade management.'))
@section('content')
<x-public-hero class="public-page-hero" :eyebrow="$introPayload['eyebrow'] ?? 'Start with a stronger foundation'" :title="$intro?->heading ?? 'Learn to build a process that can hold up under uncertainty.'" :summary="$intro?->body ?? 'Four practical entry points for replacing noise with a calm, structured way of thinking about markets.'" setting="hero_learn_image" :fallback-image="config('public.hero_defaults.learn')" />
<section class="platform-section"><div class="container"><div class="learning-path">@forelse($topics as $topic)<article class="learning-step"><div class="step-marker">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</div><div><p class="eyebrow">{{ $topic->skill_level }} <span class="eyebrow-dot"></span> {{ $topic->study_time }}</p><h2><a href="{{ route('courses') }}">{{ $topic->title }}</a></h2><p>{{ $topic->short_description }}</p><a class="text-link" href="{{ route('courses') }}">{{ $introPayload['card_cta_label'] ?? 'Explore the path' }} <span aria-hidden="true">→</span></a></div></article>@if(!$loop->last)<div class="path-arrow" aria-hidden="true">↓</div>@endif @empty<div class="empty-state"><span class="empty-index">{{ $introPayload['empty_index'] ?? 'LEARNING PATHS' }}</span><h2>{{ $introPayload['empty_heading'] ?? 'The first lessons are being prepared.' }}</h2><p>{{ $introPayload['empty_body'] ?? 'Check back soon for the first published learning path.' }}</p></div>@endforelse</div></div></section>
@if($principle)<section class="platform-dark"><div class="container two-column"><div><p class="eyebrow eyebrow-light"><span class="eyebrow-line"></span> {{ $principle->payload['eyebrow'] ?? 'The principle' }}</p><h2>{{ $principle->heading }}</h2></div><div><div class="rich-copy">{!! \App\Support\RichText::render($principle->body) !!}</div>@if($principle->cta_label && $principle->cta_url)<a class="button button-accent" href="{{ $principle->cta_url }}">{{ $principle->cta_label }} <span aria-hidden="true">↗</span></a>@endif</div></div></section>@endif
@endsection
