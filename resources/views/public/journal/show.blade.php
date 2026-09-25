@extends('layouts.public')
@php($ogImage = $article->og_image ? \App\Support\PublicHero::candidate($article->og_image) : null)
@section('title', ($article->seo_title ?: $article->title) . ' — Mwanafunzi Investor')
@section('description', $article->seo_description ?: $article->excerpt)
@section('canonical', $article->canonical_url ?: route('journal.show', $article))
@section('robots', $article->robots ?: 'index,follow')
@section('og_title', $article->og_title ?: ($article->seo_title ?: $article->title))
@section('og_description', $article->og_description ?: ($article->seo_description ?: $article->excerpt))
@section('og_type', 'article')
@if($ogImage) @section('og_image', $ogImage['url']) @endif
@section('structured_data')<script type="application/ld+json">{!! json_encode(['@context' => 'https://schema.org', '@type' => 'Article', 'headline' => $article->title, 'description' => $article->excerpt, 'datePublished' => $article->published_at?->toIso8601String(), 'image' => $ogImage['url'] ?? null, 'author' => ['@type' => 'Organization', 'name' => $article->author ?: \App\Models\SiteSetting::getValue('brand_name', 'Mwanafunzi Investor')], 'mainEntityOfPage' => route('journal.show', $article)], JSON_UNESCAPED_SLASHES) !!}</script>@endsection
@section('content')
<article><x-public-hero class="detail-hero" compact :eyebrow="($article->category?->name ?: 'Field note').' · '.($article->published_at?->format('M j, Y') ?: 'Unscheduled')" :title="$article->title" :summary="$article->excerpt" :image="$article->featured_image" :focal-point="$article->hero_focal_point" setting="hero_journal_image" :fallback-image="config('public.hero_defaults.journal')" :metadata="array_filter([$article->author ?: 'Mwanafunzi Investor', $article->reading_time ? $article->reading_time.' min read' : null])" :breadcrumbs="[['label' => 'Journal', 'url' => route('journal')]]"> </x-public-hero><section class="platform-section"><div class="container article-body"><div class="rich-copy">{!! \App\Support\RichText::render($article->content) !!}</div><aside class="detail-card"><span class="eyebrow">Keep learning</span><p>Explore the learning paths, courses and tools that give this idea somewhere to live.</p><a class="text-link" href="{{ route('learn') }}">Visit the learning hub <span aria-hidden="true">→</span></a></aside></div></section></article>
@if($relatedArticles->isNotEmpty())<section class="platform-section platform-muted"><div class="container"><p class="eyebrow">Related notes</p><div class="article-grid">@foreach($relatedArticles as $related)<article class="article-card"><h2><a href="{{ route('journal.show', $related) }}">{{ $related->title }}</a></h2><p>{{ $related->excerpt }}</p></article>@endforeach</div></div></section>@endif
@endsection
