@extends('layouts.public')

@php
    $sections = $page->sections->where('is_enabled', true)->keyBy('key');
    $services = $sections->get('services');
    $approach = $sections->get('approach');
    $process = $sections->get('process');
    $cta = $sections->get('final_cta');
    $heroTitleHtml = $page->hero_highlight ? e($page->hero_title ?: $module->name).'<br><em>'.e($page->hero_highlight).'</em>' : null;
    $cards = $services?->payload['cards'] ?? [];
    $steps = $process?->payload['steps'] ?? [];
@endphp

@section('title', $page->seo_title ?: $module->name.' — Mwanafunzi Investor')
@section('description', $page->seo_description ?: $module->description)

@section('content')
    <x-public-hero class="studio-hero" :eyebrow="$page->hero_eyebrow ?: 'Mwanafunzi Investor / Creative Studio'" :title="$page->hero_title ?: $module->name" :title-html="$heroTitleHtml" :summary="$page->hero_summary ?: $module->description" :image="$page->hero_image" :overlay="$page->hero_overlay" :alignment="$page->hero_alignment" setting="hero_about_image" :fallback-image="config('public.hero_defaults.about')">
        <div class="detail-actions">
            <a class="button button-accent" href="{{ $page->hero_primary_url ?: route('contact', ['module' => $module->slug]) }}">{{ $page->hero_primary_label ?: 'Plan a shoot' }} <span aria-hidden="true">↗</span></a>
            <a class="text-link text-link-light" href="{{ $page->hero_secondary_url ?: '#services' }}">{{ $page->hero_secondary_label ?: 'Explore the studio' }} <span aria-hidden="true">↓</span></a>
        </div>
    </x-public-hero>

    @if($services)
        <section class="platform-section studio-services" id="services">
            <div class="container">
                <div class="split-heading">
                    <div>
                        <p class="eyebrow"><span class="eyebrow-line"></span> {{ $services->payload['eyebrow'] ?? 'What we make' }}</p>
                        <h2>{{ $services->heading }}</h2>
                    </div>
                    <div class="body-copy rich-copy">{!! \App\Support\RichText::render($services->body) !!}</div>
                </div>
                <div class="studio-service-grid">
                    @foreach($cards as $card)
                        <article class="studio-service-card"><span>{{ $card['index'] ?? str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span><h3>{{ $card['title'] ?? '' }}</h3><p>{{ $card['body'] ?? '' }}</p></article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if($approach)
        <section class="platform-muted platform-section studio-approach">
            <div class="container two-column">
                <div>
                    <p class="eyebrow"><span class="eyebrow-line"></span> {{ $approach->payload['eyebrow'] ?? 'The approach' }}</p>
                    <h2>{{ $approach->heading }}</h2>
                </div>
                <div class="studio-approach-copy">
                    <div class="rich-copy">{!! \App\Support\RichText::render($approach->body) !!}</div>
                    @if(!empty($approach->payload['list']))<ul class="check-list">@foreach($approach->payload['list'] as $item)<li>{{ $item }}</li>@endforeach</ul>@endif
                </div>
            </div>
        </section>
    @endif

    @if($process)
        <section class="platform-dark studio-process">
            <div class="container">
                <div class="studio-process-heading">
                    <div>
                        <p class="eyebrow eyebrow-light"><span class="eyebrow-line"></span> {{ $process->payload['eyebrow'] ?? 'From brief to delivery' }}</p>
                        <h2>{{ $process->heading }}</h2>
                    </div>
                    <div class="rich-copy">{!! \App\Support\RichText::render($process->body) !!}</div>
                </div>
                <div class="studio-process-grid">
                    @foreach($steps as $step)<div><span>{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span><h3>{{ $step['title'] ?? '' }}</h3><p>{{ $step['body'] ?? '' }}</p></div>@endforeach
                </div>
            </div>
        </section>
    @endif

    @if($cta)
        <section class="platform-section studio-cta">
            <div class="container two-column">
                <div>
                    <p class="eyebrow"><span class="eyebrow-line"></span> {{ $cta->payload['eyebrow'] ?? 'Ready when you are' }}</p>
                    <h2>{{ $cta->heading }}</h2>
                </div>
                <div>
                    <div class="rich-copy">{!! \App\Support\RichText::render($cta->body) !!}</div>
                    @if($cta->cta_label && $cta->cta_url)<a class="button button-dark" href="{{ $cta->cta_url }}">{{ $cta->cta_label }} <span aria-hidden="true">↗</span></a>@endif
                </div>
            </div>
        </section>
    @endif
@endsection
