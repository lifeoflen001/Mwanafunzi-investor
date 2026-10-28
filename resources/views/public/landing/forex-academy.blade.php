@extends('layouts.public')

@php
    $intro = $page?->section('academy-intro');
    $pillars = $page?->section('academy-pillars');
    $academyCourses = $page?->section('academy-courses');
    $toolsSection = $page?->section('academy-tools');
    $risk = $page?->section('academy-risk');
    $framework = $page?->section('academy-framework');
    $finalCta = $page?->section('academy-final');
@endphp

@section('title', $page?->seo_title ?: 'Financial Academy — Mwanafunzi Investor')
@section('description', $page?->seo_description ?: 'Structured forex education for probability, risk planning, mechanical analysis and disciplined execution.')
@section('canonical', route('forex-academy'))
@section('og_title', $page?->og_title ?: 'Financial Academy — Mwanafunzi Investor')
@section('og_description', $page?->og_description ?: 'Learn to understand markets, build rules and protect your capital without the hype.')

@section('content')
    <div class="service-landing service-landing-academy">
        <x-public-hero class="academy-hero" :eyebrow="$page?->hero_eyebrow ?: 'Mwanafunzi Investor / Financial Academy'" :title="$page?->hero_title ?: 'Become a'" :title-html="$page?->hero_highlight ? e($page->hero_title ?: 'Become a').'<br> <em>'.e($page->hero_highlight).'</em>' : 'Become a<br> <em>Student of Money.</em>'" :summary="$page?->hero_summary ?: 'Learn systematic trading, probability, risk planning and disciplined execution without the hype.'" setting="hero_learn_image" :fallback-image="config('public.hero_defaults.learn')" :overlay="$page?->hero_overlay ?: 'strong'">
            <div class="hero-buttons">@if($pillars)<a class="button button-accent" href="#{{ \App\Support\CmsContent::value($pillars, 'anchor_id', 'learning-pillars') }}">Explore the academy <span aria-hidden="true">↓</span></a>@endif<a class="text-link text-link-light" href="{{ route('contact', ['module' => 'forex']) }}">Ask a question <span aria-hidden="true">↗</span></a></div>
        </x-public-hero>

        @if($intro && $intro->is_enabled)<section class="academy-intro section"><div class="container two-column"><div><p class="eyebrow"><span class="eyebrow-line"></span>{{ \App\Support\CmsContent::value($intro, 'eyebrow', 'The learning position') }}</p><h2>{{ $intro->heading }}</h2></div><div class="body-copy rich-copy">{!! \App\Support\RichText::render($intro->body) !!}</div></div></section>@endif

        @if($pillars && $pillars->is_enabled)<section class="academy-pillars section" id="{{ \App\Support\CmsContent::value($pillars, 'anchor_id', 'learning-pillars') }}"><div class="container"><div class="split-heading"><div><p class="eyebrow"><span class="eyebrow-line"></span>{{ \App\Support\CmsContent::value($pillars, 'eyebrow', 'What the academy teaches') }}</p><h2>{{ $pillars->heading }}</h2></div>@if($pillars->body)<p class="body-copy">{{ $pillars->body }}</p>@endif</div><div class="academy-pillar-grid">@foreach($topics->take(8) as $topic)<a class="academy-pillar" href="{{ route('learn.show', $topic) }}"><span>{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span><h3>{{ $topic->title }}</h3><p>{{ $topic->short_description }}</p><b aria-hidden="true">↗</b></a>@endforeach</div></div></section>@endif

        @if($academyCourses && $academyCourses->is_enabled)<section class="academy-courses section"><div class="container"><div class="split-heading"><div><p class="eyebrow"><span class="eyebrow-line"></span>{{ \App\Support\CmsContent::value($academyCourses, 'eyebrow', 'Courses and paths') }}</p><h2>{{ $academyCourses->heading }}</h2></div>@if($academyCourses->cta_label && $academyCourses->cta_url)<a class="text-link" href="{{ \App\Support\Seo::resolve($academyCourses->cta_url, route('courses')) }}">{{ $academyCourses->cta_label }} <span aria-hidden="true">→</span></a>@endif</div><div class="academy-course-list">@forelse($courses->take(4) as $course)<a href="{{ route('courses.show', $course) }}"><span>{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span><div><h3>{{ $course->title }}</h3><p>{{ $course->short_description }}</p></div><small>{{ $course->level ?: 'Self-paced' }} ↗</small></a>@empty<div class="empty-state"><p>{{ \App\Support\CmsContent::value($academyCourses, 'empty_body', 'The first courses are being prepared.') }}</p></div>@endforelse</div></div></section>@endif

        @if($toolsSection && $toolsSection->is_enabled && $products->isNotEmpty())
            <section class="academy-tools section">
                <div class="container two-column">
                    <div>
                        <p class="eyebrow eyebrow-light"><span class="eyebrow-line"></span>{{ \App\Support\CmsContent::value($toolsSection, 'eyebrow', 'Tools for deliberate practice') }}</p>
                        <h2>{{ $toolsSection->heading }}</h2>
                    </div>
                    <div>
                        <div class="body-copy rich-copy">{!! \App\Support\RichText::render($toolsSection?->body ?: 'Simple tools for journaling, planning and reviewing the work around a trade.') !!}</div>
                        <div class="academy-tool-links">@foreach($products->take(3) as $product)<a href="{{ route('tools.show', $product) }}"><span>{{ $product->name }}</span><b aria-hidden="true">↗</b></a>@endforeach</div>
                    </div>
                </div>
            </section>
        @endif

        @if($risk && $risk->is_enabled)<section class="academy-risk section"><div class="container two-column"><div><p class="eyebrow"><span class="eyebrow-line"></span>{{ \App\Support\CmsContent::value($risk, 'eyebrow', 'Risk first. Process always.') }}</p><h2>{{ $risk->heading }}</h2></div><div class="body-copy rich-copy">{!! \App\Support\RichText::render($risk->body) !!}</div></div></section>@endif

        @if($framework && $framework->is_enabled)<section class="platform-dark academy-framework"><div class="container"><div class="framework-header"><p class="eyebrow eyebrow-light"><span class="eyebrow-line"></span>{{ \App\Support\CmsContent::value($framework, 'eyebrow', 'Student of Money principles') }}</p><h2>{{ $framework->heading }}</h2><p>{{ $framework->body }}</p></div><div class="framework-steps">@foreach(($framework->payload['steps'] ?? []) as $step)<div class="framework-step"><span>{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span><strong>{{ $step['title'] ?? '' }}</strong><p>{{ $step['body'] ?? '' }}</p></div>@endforeach</div></div></section>@endif

        @if($finalCta && $finalCta->is_enabled)<section class="academy-final final-cta"><div class="container final-cta-inner"><p class="eyebrow eyebrow-light"><span class="eyebrow-line"></span>{{ \App\Support\CmsContent::value($finalCta, 'eyebrow', 'Start with a stronger foundation') }}</p><h2>{{ $finalCta->heading }}</h2><p>{{ $finalCta->body }}</p><div class="hero-buttons">@if($finalCta->cta_label && $finalCta->cta_url)<a class="button button-accent" href="{{ \App\Support\Seo::resolve($finalCta->cta_url, '#') }}">{{ $finalCta->cta_label }} <span aria-hidden="true">↗</span></a>@endif @if(\App\Support\CmsContent::value($finalCta, 'secondary_cta_label') && \App\Support\CmsContent::value($finalCta, 'secondary_cta_url'))<a class="text-link text-link-light" href="{{ \App\Support\Seo::resolve(\App\Support\CmsContent::value($finalCta, 'secondary_cta_url'), '#') }}">{{ \App\Support\CmsContent::value($finalCta, 'secondary_cta_label') }} <span aria-hidden="true">→</span></a>@endif</div></div></section>@endif
    </div>
@endsection
