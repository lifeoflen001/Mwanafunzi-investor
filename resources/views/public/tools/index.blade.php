@extends('layouts.public')
@php
    $intro = $page?->section('intro');
    $introPayload = $intro?->payload ?? [];
@endphp
@section('title', $page?->seo_title ?: 'Tools — Mwanafunzi Investor')
@section('description', $page?->seo_description ?: ($intro?->body ?: 'Focused digital tools for deliberate practice, journaling and risk-first trading decisions.'))
@section('content')
<x-public-hero class="public-page-hero" :eyebrow="$introPayload['eyebrow'] ?? 'Tools for deliberate practice'" :title="$intro?->heading ?? 'Make the process visible.'" :summary="$intro?->body ?? 'Simple, focused tools for turning a good intention into a record you can learn from.'" setting="hero_tools_image" :fallback-image="config('public.hero_defaults.tools')" />
<section class="platform-section"><div class="container"><div class="platform-card-grid">@forelse($products as $product)@php($cardImage = \App\Support\PublicHero::resolve([$product->thumbnail, config('public.hero_defaults.tools')]))<a class="platform-card" href="{{ route('tools.show', $product) }}">@if($cardImage)<div class="platform-card-media"><img src="{{ $cardImage['url'] }}" @if($cardImage['srcset']) srcset="{{ $cardImage['srcset'] }}" sizes="(max-width: 760px) 100vw, 50vw" @endif alt="" loading="lazy"></div>@endif<div class="card-top"><span class="eyebrow">{{ $product->product_type }}</span><span class="card-icon">↗</span></div><h2>{{ $product->name }}</h2><p>{{ $product->short_description }}</p><div class="card-footer"><span>{{ str_replace('_', ' ', $product->availability) }}</span><span>{{ $introPayload['card_cta_label'] ?? 'View tool' }} ↗</span></div></a>@empty<div class="empty-state"><span class="empty-index">{{ $introPayload['empty_index'] ?? 'TOOLS' }}</span><h2>{{ $introPayload['empty_heading'] ?? 'The first tools are being prepared.' }}</h2><p>{{ $introPayload['empty_body'] ?? 'Check back soon for deliberate practice tools.' }}</p></div>@endforelse</div>{{ $products->links() }}</div></section>
@endsection
