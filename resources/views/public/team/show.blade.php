@extends('layouts.public')
@php
    $memberImage = $member->portrait ? (str_starts_with($member->portrait, 'http') ? $member->portrait : asset(ltrim($member->portrait, '/'))) : null;
    $memberTitle = $member->seo_title ?: $member->name.' — Mwanafunzi Investor';
    $memberDescription = $member->seo_description ?: ($member->short_intro ?: $member->name.' at Mwanafunzi Investor.');
    $schema = [
        '@context' => 'https://schema.org', '@type' => 'Person', 'name' => $member->name,
        'url' => $member->canonical_url ?: route('team.show', $member->slug), 'image' => $memberImage,
        'jobTitle' => $member->role ?: null, 'worksFor' => ['@type' => 'Organization', 'name' => 'Mwanafunzi Investor', 'url' => url('/')],
        'sameAs' => collect($member->socialLinks())->pluck('url')->reject(fn ($url) => str_starts_with($url, 'mailto:'))->values()->all(),
        'email' => $member->public_email ?: null,
    ];
@endphp
@section('title', $memberTitle)
@section('description', $memberDescription)
@section('canonical', $member->canonical_url ?: route('team.show', $member->slug))
@section('og_title', $memberTitle)
@section('og_description', $memberDescription)
@if($member->og_image)
    @section('og_image', str_starts_with($member->og_image, 'http') ? $member->og_image : asset('storage/'.ltrim($member->og_image, '/')))
@endif
@section('structured_data')<script type="application/ld+json">{!! json_encode(array_filter($schema, fn ($value) => $value !== null && $value !== []), JSON_UNESCAPED_SLASHES) !!}</script>@endsection
@section('content')
<main class="team-member-page">
    <section class="team-member-hero">
        <div class="container">
            <a class="team-member-back text-link" href="{{ route('about') }}"><span aria-hidden="true">←</span> Meet the team</a>
            <div class="team-member-grid">
                <div class="team-member-media">@if($memberImage)<img src="{{ $memberImage }}" alt="{{ $member->name }}" style="object-position:{{ $member->portrait_focal_point ?: 'center 25%' }}">@else<div class="team-member-placeholder" aria-hidden="true">{{ collect(explode(' ', $member->name))->map(fn ($part) => mb_substr($part, 0, 1))->take(2)->implode('') }}</div>@endif</div>
                <div class="team-member-copy">
                    <p class="eyebrow"><span class="eyebrow-line"></span> {{ $member->department ?: 'Mwanafunzi Investor team' }}</p>
                    <h1>{{ $member->name }}</h1>
                    <p class="team-member-role">{{ $member->role ?: 'Team member' }}</p>
                    @if($member->short_intro)<p class="team-member-intro">{{ $member->short_intro }}</p>@endif
                    @if($member->socialLinks())
                        <nav class="team-social-links" aria-label="{{ $member->name }} contact and social links"><span class="team-social-label">Contact</span>@foreach($member->socialLinks() as $social)<a class="team-social-link" href="{{ $social['url'] }}" @if(!str_starts_with($social['url'], 'mailto:')) target="_blank" rel="noopener" @endif aria-label="{{ $social['label'] }}" title="{{ $social['label'] }}"><span aria-hidden="true">{{ $social['short'] }}</span></a>@endforeach</nav>
                    @endif
                    @if($member->focus || $member->expertise)
                        <div class="team-member-meta">
                            @if($member->focus)<div><span>Focus</span><strong>{{ $member->focus }}</strong></div>@endif
                            @if($member->expertise)<div><span>Expertise / responsibilities</span><ul>@foreach($member->expertise as $item)<li>{{ $item }}</li>@endforeach</ul></div>@endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
    @if($member->bio)
        <section class="team-member-bio"><div class="container two-column"><p class="eyebrow"><span class="eyebrow-line"></span> About {{ $member->name }}</p><div class="rich-copy">{!! \App\Support\RichText::render($member->bio) !!}</div></div></section>
    @endif
</main>
@endsection
