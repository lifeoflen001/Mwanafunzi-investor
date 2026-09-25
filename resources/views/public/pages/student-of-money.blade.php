@extends('layouts.public')

@php
    $intro = $page?->section('intro');
    $framework = $page?->section('framework');
    $heroTitleHtml = $page?->hero_highlight ? e($page->hero_title ?: 'Student of Money').'<br><em>'.e($page->hero_highlight).'</em>' : null;
    $steps = $framework?->payload['steps'] ?? [];
@endphp

@section('title', $page?->seo_title ?: 'Student of Money — Mwanafunzi Investor')
@section('description', $page?->seo_description ?: $page?->hero_summary)

@section('content')
    <x-public-hero class="manifesto-hero" :eyebrow="$page?->hero_eyebrow ?: ($intro?->payload['eyebrow'] ?? 'The signature philosophy')" :title="$page?->hero_title ?: 'Student of Money'" :title-html="$heroTitleHtml" :summary="$page?->hero_summary" :image="$page?->hero_image" :overlay="$page?->hero_overlay ?: 'medium'" :alignment="$page?->hero_alignment ?: 'left'" setting="hero_student_image" :fallback-image="config('public.hero_defaults.student-of-money')" />

    @if($intro)
        <section class="platform-section">
            <div class="container editorial-copy">
                <p class="eyebrow">{{ $intro->payload['eyebrow'] ?? 'The Student of Money' }}</p>
                <h2>{{ $intro->heading }}</h2>
                <div class="rich-copy">{!! \App\Support\RichText::render($intro->body) !!}</div>
            </div>
        </section>
    @endif

    @if($framework)
        <section class="platform-dark framework-platform">
            <div class="container">
                <p class="eyebrow eyebrow-light"><span class="eyebrow-line"></span> {{ $framework->payload['eyebrow'] ?? 'A process you can come back to' }}</p>
                <h2>{{ $framework->heading }}</h2>
                <div class="framework-steps platform-framework">
                    @foreach($steps as $step)
                        <div class="framework-step"><span>{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span><strong>{{ $step['title'] ?? '' }}</strong><p>{{ $step['body'] ?? '' }}</p></div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection
