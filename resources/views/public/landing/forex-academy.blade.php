@extends('layouts.public')

@php
    $homePhilosophy = $homePage?->section('philosophy');
    $homeFramework = $homePage?->section('framework');
    $homeFinalCta = $homePage?->section('final_cta');
@endphp

@section('title', $page?->seo_title ?: 'Forex Academy — Mwanafunzi Investor')
@section('description', $page?->seo_description ?: 'Structured forex education for probability, risk planning, mechanical analysis and disciplined execution.')
@section('canonical', route('forex-academy'))
@section('og_title', $page?->og_title ?: 'Forex Academy — Mwanafunzi Investor')
@section('og_description', $page?->og_description ?: 'Learn to understand markets, build rules and protect your capital without the hype.')

@section('content')
    <div class="service-landing service-landing-academy">
        <x-public-hero class="academy-hero" :eyebrow="$page?->hero_eyebrow ?: 'Mwanafunzi Investor / Forex Academy'" :title="$page?->hero_title ?: 'Become a'" :title-html="$page?->hero_highlight ? e($page->hero_title ?: 'Become a').'<br><em>'.e($page->hero_highlight).'</em>' : 'Become a<br><em>Student of Money.</em>'" :summary="$page?->hero_summary ?: 'Learn systematic trading, probability, risk planning and disciplined execution without the hype.'" setting="hero_learn_image" :fallback-image="config('public.hero_defaults.learn')" :overlay="$page?->hero_overlay ?: 'strong'">
            <div class="hero-buttons"><a class="button button-accent" href="#learning-pillars">Explore the academy <span aria-hidden="true">↓</span></a><a class="text-link text-link-light" href="{{ route('contact', ['module' => 'forex']) }}">Ask a question <span aria-hidden="true">↗</span></a></div>
        </x-public-hero>

        <section class="academy-intro section"><div class="container two-column"><div><p class="eyebrow"><span class="eyebrow-line"></span>The learning position</p><h2>Build a process that can hold up under uncertainty.</h2></div><div class="body-copy rich-copy">{!! \App\Support\RichText::render($homePhilosophy?->body ?: 'The work is to understand markets, make risk visible, write rules and review decisions honestly. This is education for the long game—not a promise of easy outcomes.') !!}</div></div></section>

        <section class="academy-pillars section" id="learning-pillars"><div class="container"><div class="split-heading"><div><p class="eyebrow"><span class="eyebrow-line"></span>What the academy teaches</p><h2>From market language to <em>repeatable decisions.</em></h2></div><p class="body-copy">Practical learning paths for people who want context before complexity and process before prediction.</p></div><div class="academy-pillar-grid">@foreach($topics->take(8) as $topic)<a class="academy-pillar" href="{{ route('learn.show', $topic) }}"><span>{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span><h3>{{ $topic->title }}</h3><p>{{ $topic->short_description }}</p><b aria-hidden="true">↗</b></a>@endforeach</div></div></section>

        <section class="academy-courses section"><div class="container"><div class="split-heading"><div><p class="eyebrow"><span class="eyebrow-line"></span>Courses and paths</p><h2>Start where your process <em>needs work.</em></h2></div><a class="text-link" href="{{ route('courses') }}">View all courses <span aria-hidden="true">→</span></a></div><div class="academy-course-list">@forelse($courses->take(4) as $course)<a href="{{ route('courses.show', $course) }}"><span>{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span><div><h3>{{ $course->title }}</h3><p>{{ $course->short_description }}</p></div><small>{{ $course->level ?: 'Self-paced' }} ↗</small></a>@empty<div class="empty-state"><p>The first courses are being prepared.</p></div>@endforelse</div></div></section>

        @if($products->isNotEmpty())<section class="academy-tools section"><div class="container two-column"><div><p class="eyebrow eyebrow-light"><span class="eyebrow-line"></span>Tools for deliberate practice</p><h2>Make the process <em>visible.</em></h2></div><div><div class="body-copy rich-copy">Simple tools for journaling, planning and reviewing the work around a trade.</div><div class="academy-tool-links">@foreach($products->take(3) as $product)<a href="{{ route('tools.show', $product) }}"><span>{{ $product->name }}</span><b aria-hidden="true">↗</b></a>@endforeach</div></div></div></section>@endif

        <section class="academy-risk section"><div class="container two-column"><div><p class="eyebrow"><span class="eyebrow-line"></span>Risk first. Process always.</p><h2>No promises. Just <em>better questions.</em></h2></div><div class="body-copy">Trading involves risk. The academy focuses on probability, position sizing, trade management, journaling and the discipline to keep learning—never fabricated results or guaranteed outcomes.</div></div></section>

        @if($homeFramework)<section class="platform-dark academy-framework"><div class="container"><div class="framework-header"><p class="eyebrow eyebrow-light"><span class="eyebrow-line"></span>{{ $homeFramework->payload['eyebrow'] ?? 'Student of Money principles' }}</p><h2>{{ $homeFramework->heading }}</h2><p>{{ $homeFramework->body }}</p></div><div class="framework-steps">@foreach(($homeFramework->payload['steps'] ?? []) as $step)<div class="framework-step"><span>{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span><strong>{{ $step['title'] ?? '' }}</strong><p>{{ $step['body'] ?? '' }}</p></div>@endforeach</div></div></section>@endif

        <section class="academy-final final-cta"><div class="container final-cta-inner"><p class="eyebrow eyebrow-light"><span class="eyebrow-line"></span>Start with a stronger foundation</p><h2>{{ $homeFinalCta?->heading ?: 'Become trustworthy with the decisions in front of you.' }}</h2><p>{{ $homeFinalCta?->body ?: 'The next useful step is usually a careful conversation.' }}</p><div class="hero-buttons"><a class="button button-accent" href="{{ route('courses') }}">Start Learning <span aria-hidden="true">↗</span></a><a class="text-link text-link-light" href="{{ route('contact', ['module' => 'forex']) }}">Contact the desk <span aria-hidden="true">→</span></a></div></div></section>
    </div>
@endsection
