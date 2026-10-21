@extends('layouts.public')
@php
    $ogImage = \App\Support\PublicHero::resolveImage([$article->og_image, $article->featured_image, config('public.hero_defaults.journal')]);
    $articleContent = \App\Support\ArticleContent::prepare($article->content);
    $heroBreadcrumbs = [['label' => 'Home', 'url' => route('home')], ['label' => 'Blog', 'url' => route('blog')]];
    if ($article->category) $heroBreadcrumbs[] = ['label' => $article->category->name, 'url' => route('blog.category', $article->category)];
@endphp
@section('title', ($article->seo_title ?: $article->title) . ' — Mwanafunzi Investor')
@section('description', $article->seo_description ?: ($article->publicExcerpt() ?: 'News, notes and market education from Mwanafunzi Investor.'))
@section('canonical', $article->canonicalUrl())
@section('robots', $article->robots ?: 'index,follow')
@section('og_title', $article->og_title ?: ($article->seo_title ?: $article->title))
@section('og_description', $article->og_description ?: ($article->seo_description ?: ($article->publicExcerpt() ?: 'News, notes and market education from Mwanafunzi Investor.')))
@section('og_type', 'article')
@if($ogImage)
@section('og_image'){{ $ogImage['url'] }}@endsection
@endif
@section('article_meta')
<meta property="article:published_time" content="{{ $article->published_at?->toIso8601String() }}">
@if($article->hasMeaningfulUpdate())<meta property="article:modified_time" content="{{ $article->updated_at->toIso8601String() }}">@endif
@if($article->authorMember)<meta property="article:author" content="{{ $article->authorMember->name }}">@endif
@endsection
@php($schemaContext = chr(64).'context')
@php($articleSchema = [$schemaContext => 'https://schema.org', '@type' => 'BlogPosting', 'headline' => $article->title, 'description' => $article->publicExcerpt(), 'datePublished' => $article->published_at?->toIso8601String(), 'image' => $ogImage['url'] ?? null, 'author' => $article->authorMember ? ['@type' => 'Person', 'name' => $article->authorMember->name, 'url' => route('team.show', $article->authorMember)] : ['@type' => 'Organization', 'name' => 'Mwanafunzi Investor'], 'mainEntityOfPage' => $article->canonicalUrl(), 'publisher' => ['@type' => 'Organization', 'name' => 'Mwanafunzi Investor']])
@if($article->hasMeaningfulUpdate()) @php($articleSchema['dateModified'] = $article->updated_at->toIso8601String()) @endif
@section('structured_data')<script type="application/ld+json">{!! json_encode($articleSchema, JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}</script>@endsection
@section('content')
<article class="editorial-article-detail">
    <x-public-hero class="detail-hero" compact :eyebrow="$article->displayCategory().' · '.($article->published_at?->format('M j, Y') ?: 'Unscheduled')" :title="$article->title" :summary="$article->publicExcerpt()" :image="$article->featured_image" :focal-point="$article->hero_focal_point" setting="hero_journal_image" :fallback-image="config('public.hero_defaults.journal')" :metadata="array_filter([$article->displayAuthor(), $article->readingTimeMinutes().' min read'])" :breadcrumbs="$heroBreadcrumbs">
        @if($article->authorMember && $article->authorMember->portrait)
            <div class="detail-hero-author"><img src="{{ \App\Support\PublicHero::candidate($article->authorMember->portrait)['url'] ?? asset('storage/'.$article->authorMember->portrait) }}" alt="" width="40" height="40"><span>Written by {{ $article->authorMember->name }}</span></div>
        @endif
    </x-public-hero>

    <section class="platform-section article-reading-section">
        <div class="container editorial-reading-layout">
            <div class="editorial-reading-main">
                <div class="editorial-reading-toolbar"><span>{{ $article->displayCategory() }}</span><span>Published {{ $article->published_at?->format('M j, Y') }}@if($article->hasMeaningfulUpdate()) · Updated {{ $article->updated_at->format('M j, Y') }}@endif</span></div>
                @if($articleContent['toc'])
                    <details class="article-toc" open><summary>In this note</summary><ol>@foreach($articleContent['toc'] as $heading)<li class="article-toc-level-{{ $heading['level'] }}"><a href="#{{ $heading['id'] }}">{{ $heading['label'] }}</a></li>@endforeach</ol></details>
                @endif
                <div class="rich-copy article-body">{!! $articleContent['html'] !!}</div>

                @if($article->learningTopics->isNotEmpty())
                    <section class="article-further-learning" aria-labelledby="further-learning-heading"><p class="eyebrow">Further learning</p><h2 id="further-learning-heading">Keep building the context.</h2><div class="article-learning-links">@foreach($article->learningTopics as $topic)<a class="article-learning-link" href="{{ route('learn.show', $topic) }}"><span><strong>{{ $topic->title }}</strong><small>{{ $topic->short_description }}</small></span><span aria-hidden="true">↗</span></a>@endforeach</div></section>
                @endif

                @if($article->socialLinks->isNotEmpty())
                    <section class="related-social-updates" aria-labelledby="social-updates-heading"><p class="eyebrow">Related social content</p><h2 id="social-updates-heading">Continue the update.</h2><div class="related-social-grid">@foreach($article->socialLinks as $social)<a class="related-social-card" href="{{ $social->url }}" target="_blank" rel="noopener noreferrer" aria-label="{{ $social->label ?: 'Open this '.$social->platformLabel().' update' }}"><span class="related-social-icon" aria-hidden="true">↗</span><span><strong>{{ $social->label ?: 'Open this '.$social->platformLabel().' update' }}</strong><small>{{ $social->platformLabel() }}</small></span></a>@endforeach</div></section>
                @endif

                <div class="article-disclaimer">Educational content only. This is not personalised financial advice. <a href="{{ route('legal', 'risk-disclosure') }}">Read the risk disclosure.</a></div>

                @if($previousArticle || $nextArticle)
                    <nav class="article-neighbour-nav" aria-label="Article navigation">@if($previousArticle)<a href="{{ route('blog.show', $previousArticle) }}"><span>Previous note</span><strong>{{ $previousArticle->title }}</strong></a>@else<span></span>@endif @if($nextArticle)<a class="article-neighbour-next" href="{{ route('blog.show', $nextArticle) }}"><span>Next note</span><strong>{{ $nextArticle->title }}</strong></a>@endif</nav>
                @endif
            </div>

            <aside class="editorial-reading-aside">
                <div class="detail-card article-engagement-card">
                    <p class="eyebrow">Useful note?</p>
                    @auth
                        <form method="post" action="{{ route('blog.reaction', $article) }}" data-reaction-form>@csrf<input type="hidden" name="type" value="like"><button class="article-like-button {{ $userHasLiked ? 'is-active' : '' }}" type="submit" aria-label="{{ $userHasLiked ? 'Remove like' : 'Like this article' }}" aria-pressed="{{ $userHasLiked ? 'true' : 'false' }}"><span aria-hidden="true">{{ $userHasLiked ? '♥' : '♡' }}</span> <span data-reaction-count>{{ $article->likes_count }}</span> <span>Like</span></button></form>
                    @else
                        <a class="article-like-button" href="{{ route('login') }}" aria-label="Sign in to like this article">♡ {{ $article->likes_count }} <span>Sign in to like</span></a>
                    @endauth
                    <div class="article-share" data-share-url="{{ $article->canonicalUrl() }}"><span class="eyebrow">Share</span><div class="article-share-links"><button type="button" data-copy-link aria-label="Copy article link">Copy link</button><a href="https://wa.me/?text={{ urlencode($article->title.' '.$article->canonicalUrl()) }}" target="_blank" rel="noopener noreferrer" aria-label="Share on WhatsApp">WhatsApp</a><a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode($article->canonicalUrl()) }}" target="_blank" rel="noopener noreferrer" aria-label="Share on LinkedIn">LinkedIn</a><a href="https://twitter.com/intent/tweet?text={{ urlencode($article->title) }}&url={{ urlencode($article->canonicalUrl()) }}" target="_blank" rel="noopener noreferrer" aria-label="Share on X">X</a></div><span class="share-feedback" data-share-feedback role="status" aria-live="polite"></span></div>
                </div>
                @if($article->authorMember)
                    <div class="detail-card article-author-card">@if($article->authorMember->portrait)<img src="{{ \App\Support\PublicHero::candidate($article->authorMember->portrait)['url'] ?? asset('storage/'.$article->authorMember->portrait) }}" alt="{{ $article->authorMember->name }}" loading="lazy">@endif<p class="eyebrow">Written by</p><h2>{{ $article->authorMember->name }}</h2><p>{{ $article->authorMember->role }}</p>@if($article->authorMember->short_intro)<p>{{ $article->authorMember->short_intro }}</p>@endif<a class="text-link" href="{{ route('team.show', $article->authorMember) }}">View profile <span aria-hidden="true">↗</span></a></div>
                @endif
            </aside>
        </div>
    </section>
</article>

<section class="platform-section platform-muted editorial-comments" id="comments"><div class="container editorial-comments-inner"><div class="editorial-subsection-heading"><div><p class="eyebrow">Discussion</p><h2>Join the discussion.</h2></div><span>{{ $article->comments_count }} approved comments</span></div>@if(session('success'))<div class="comment-notice" role="status">{{ session('success') }}</div>
@endif
@if($errors->any())<div class="comment-notice comment-notice-error" role="alert">{{ $errors->first() }}</div>
@endif
<div class="comment-list">@include('public.journal.partials.comment-list')</div>
@if(!empty($preview))
    <div class="comment-signin"><p>Discussion is disabled in a private preview.</p></div>
@elseif(auth()->check())
    <div class="comment-form-card"><form method="post" action="{{ route('blog.comments.store', $article) }}">@csrf<label for="comment-body">Comment</label><textarea id="comment-body" name="body" rows="4" required maxlength="3000" placeholder="Share a considered response."></textarea><input class="comment-honeypot" type="text" name="website" tabindex="-1" autocomplete="off"><x-recaptcha action="comments" /><button class="button button-dark" type="submit">Post comment <span aria-hidden="true">↗</span></button></form></div>
@else
    <div class="comment-signin"><p>Sign in to add a comment or reply.</p><a class="button button-dark" href="{{ route('login', ['redirect' => request()->getRequestUri().'#comments']) }}">Sign in to comment <span aria-hidden="true">↗</span></a><x-google-sign-in :redirect="request()->getRequestUri().'#comments'" /></div>
@endif
</div></section>

@if($relatedArticles->isNotEmpty())
    <section class="platform-section article-related-section"><div class="container"><div class="editorial-subsection-heading"><div><p class="eyebrow">Keep reading</p><h2>Related notes.</h2></div></div><div class="article-grid article-related-grid">@foreach($relatedArticles as $related)<article class="article-card article-related-card">@php($relatedImage = \App\Support\PublicHero::resolve([$related->featured_image, config('public.hero_defaults.journal')]))@if($relatedImage)<a class="article-card-media" href="{{ route('blog.show', $related) }}">@if($relatedImage['is_video'] ?? false)<video src="{{ $relatedImage['url'] }}" preload="metadata" muted playsinline aria-label="{{ $related->title }}"></video>@else<img src="{{ $relatedImage['url'] }}" alt="{{ $related->title }}" loading="lazy">@endif</a>@endif<div class="article-meta"><span>{{ $related->displayCategory() }}</span><span>{{ $related->published_at?->format('M j, Y') }}</span></div><h2><a href="{{ route('blog.show', $related) }}">{{ $related->title }}</a></h2>@if($related->publicExcerpt())<p>{{ $related->publicExcerpt() }}</p>@endif<div class="article-card-footer"><span>{{ $related->readingTimeMinutes() }} min read</span><a class="text-link" href="{{ route('blog.show', $related) }}">Read note <span aria-hidden="true">→</span></a></div></article>@endforeach</div></div></section>
@endif
@endsection
