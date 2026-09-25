@extends('layouts.public')

@php
    $sections = $page->sections->where('is_enabled', true)->keyBy('key');
    $capabilities = $sections->get('capabilities');
    $process = $sections->get('process');
    $cta = $sections->get('final_cta');
    $heroTitleHtml = $page->hero_highlight ? e($page->hero_title ?: $module->name).'<br><em>'.e($page->hero_highlight).'</em>' : null;
    $cards = $capabilities?->payload['cards'] ?? [];
    $steps = $process?->payload['steps'] ?? [];
@endphp

@section('title', $page->seo_title ?: $module->name.' — Mwanafunzi Investor')
@section('description', $page->seo_description ?: $module->description)

@section('content')
    <x-public-hero class="development-hero" :eyebrow="$page->hero_eyebrow ?: 'Mwanafunzi Investor / Digital Systems'" :title="$page->hero_title ?: $module->name" :title-html="$heroTitleHtml" :summary="$page->hero_summary ?: $module->description" :image="$page->hero_image" :focal-point="$page->hero_focal_point" :overlay="$page->hero_overlay" :alignment="$page->hero_alignment" setting="hero_tools_image" :fallback-image="config('public.hero_defaults.modules')">
        <div class="detail-actions">
            <a class="button button-dark" href="{{ $page->hero_primary_url ?: route('contact', ['module' => $module->slug]) }}">{{ $page->hero_primary_label ?: 'Discuss a project' }} <span aria-hidden="true">↗</span></a>
            <a class="text-link text-link-light" href="{{ $page->hero_secondary_url ?: '#capabilities' }}">{{ $page->hero_secondary_label ?: 'See what we build' }} <span aria-hidden="true">↓</span></a>
        </div>
    </x-public-hero>

    @if($capabilities)
        <section class="platform-section platform-muted" id="capabilities">
            <div class="container">
                <div class="split-heading">
                    <div>
                        <p class="eyebrow"><span class="eyebrow-line"></span> {{ $capabilities->payload['eyebrow'] ?? 'What we build' }}</p>
                        <h2>{{ $capabilities->heading }}</h2>
                    </div>
                    <div class="body-copy rich-copy">{!! \App\Support\RichText::render($capabilities->body) !!}</div>
                </div>
                <div class="dev-service-grid">
                    @foreach($cards as $card)
                        <article class="dev-service-card">
                            <span class="dev-service-index">{{ $card['index'] ?? str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                            <h3>{{ $card['title'] ?? '' }}</h3>
                            <p>{{ $card['body'] ?? '' }}</p>
                            @if(!empty($card['tags']))<span class="dev-service-tags">{{ $card['tags'] }}</span>@endif
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if($process)
        <section class="platform-dark dev-process">
            <div class="container">
                <div class="dev-process-heading">
                    <div>
                        <p class="eyebrow eyebrow-light"><span class="eyebrow-line"></span> {{ $process->payload['eyebrow'] ?? 'How we work' }}</p>
                        <h2>{{ $process->heading }}</h2>
                    </div>
                    <div class="rich-copy">{!! \App\Support\RichText::render($process->body) !!}</div>
                </div>
                <div class="dev-process-grid">
                    @foreach($steps as $step)
                        <div class="dev-process-step"><span>{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span><h3>{{ $step['title'] ?? '' }}</h3><p>{{ $step['body'] ?? '' }}</p></div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if($cta)
        <section class="platform-section dev-cta">
            <div class="container two-column">
                <div>
                    <p class="eyebrow"><span class="eyebrow-line"></span> {{ $cta->payload['eyebrow'] ?? 'Have a project in mind?' }}</p>
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
