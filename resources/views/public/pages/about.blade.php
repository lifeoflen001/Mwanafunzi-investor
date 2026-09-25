@extends('layouts.public')
@section('title', $page?->seo_title ?: 'About — Mwanafunzi Investor')
@section('description', $page?->seo_description ?: ($page?->hero_summary ?: 'Why Mwanafunzi Investor exists and how a Student of Money approaches markets, systems and risk.'))
@php
    $cmsPhilosophy = $page?->sections?->where('is_enabled', true)->firstWhere('key', 'philosophy');
    $cmsApproach = $page?->sections?->where('is_enabled', true)->firstWhere('key', 'approach');
    $heroTitleHtml = $page?->hero_highlight ? e($page->hero_title ?: 'About Mwanafunzi Investor').'<br><em>'.e($page->hero_highlight).'</em>' : null;
@endphp
@section('content')
<x-public-hero class="public-page-hero" :eyebrow="$page?->hero_eyebrow ?: 'About Mwanafunzi Investor'" :title="$page?->hero_title ?: 'Become trustworthy with the decisions in front of you.'" :title-html="$heroTitleHtml" :summary="$page?->hero_summary ?: 'Mwanafunzi means student. The name is a reminder that the work is continuous: learn, practise, review and improve.'" :image="$page?->hero_image" :overlay="$page?->hero_overlay" :alignment="$page?->hero_alignment" setting="hero_about_image" :fallback-image="config('public.hero_defaults.about')">
    @if($page?->hero_primary_label && $page?->hero_primary_url)
        <div class="detail-actions"><a class="button button-accent" href="{{ $page->hero_primary_url }}">{{ $page->hero_primary_label }} <span aria-hidden="true">↗</span></a>@if($page->hero_secondary_label && $page->hero_secondary_url)<a class="text-link text-link-light" href="{{ $page->hero_secondary_url }}">{{ $page->hero_secondary_label }} <span aria-hidden="true">↗</span></a>@endif</div>
    @endif
</x-public-hero>
@if($cmsPhilosophy)
<section class="platform-section"><div class="container editorial-copy"><p class="eyebrow">{{ $cmsPhilosophy->payload['eyebrow'] ?? 'Why this exists' }}</p>@if($cmsPhilosophy->heading)<h2>{{ $cmsPhilosophy->heading }}</h2>@endif @if($cmsPhilosophy->body)<div class="rich-copy">{!! \App\Support\RichText::render($cmsPhilosophy->body) !!}</div>@endif @if(!empty($cmsPhilosophy->payload['pull']))<div class="editorial-pull">{{ $cmsPhilosophy->payload['pull'] }}</div>@endif</div></section>
@else
<section class="platform-section"><div class="container editorial-copy"><p class="eyebrow">Why this exists</p><h2>A different kind of market education.</h2><p>Mwanafunzi Investor exists for people who want to understand markets without the hype. The approach is grounded in systematic trading, probability, risk-first thinking and the humility to keep learning.</p><p>This is a real journey philosophy: no invented results, fictional credentials or promises of easy outcomes. The work is to build a process that can hold up when certainty is impossible.</p><div class="editorial-pull">“The point is not perfection — it is becoming trustworthy with the decisions in front of you.”</div></div></section>
@endif
@if($cmsApproach)
<section class="platform-dark"><div class="container two-column"><div><p class="eyebrow eyebrow-light"><span class="eyebrow-line"></span> {{ $cmsApproach->payload['eyebrow'] ?? 'The approach' }}</p><h2>{{ $cmsApproach->heading }}</h2></div><div><div class="rich-copy">{!! \App\Support\RichText::render($cmsApproach->body) !!}</div>@if($cmsApproach->cta_label && $cmsApproach->cta_url)<a class="button button-accent" href="{{ $cmsApproach->cta_url }}">{{ $cmsApproach->cta_label }} <span aria-hidden="true">↗</span></a>@endif</div></div></section>
@else
<section class="platform-dark"><div class="container two-column"><div><p class="eyebrow eyebrow-light"><span class="eyebrow-line"></span> The approach</p><h2>Risk first. Process always.</h2></div><div><p>We study the relationships behind price, turn ideas into rules, respect sample size and make the cost of being wrong visible before acting. That is education for the long game.</p><a class="button button-accent" href="{{ route('student-of-money') }}">Read Student of Money <span aria-hidden="true">↗</span></a></div></div></section>
@endif
@endsection
