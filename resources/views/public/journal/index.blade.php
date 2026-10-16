@extends('layouts.public')
@php
    $intro = $page?->section('intro');
    $introPayload = $intro?->payload ?? [];
    $pageTitle = $category?->name ?: ($tag ? 'Tag: '.$tag->name : ($author ? 'Notes by '.$author->name : 'News, notes & market education'));
    $taxonomy = $category ?: $tag;
    $taxonomyCanonical = $taxonomy?->canonical_url ?: ($category ? route('blog.category', $category) : ($tag ? route('blog.tag', $tag) : route('blog')));
    $hasFilters = $search !== '' || $category || $tag || $author || $sort !== 'newest';
    $publishedArticleCount = $articles->total();
    $heroBreadcrumbs = [['label' => 'Home', 'url' => route('home')], ['label' => 'Blog', 'url' => route('blog')]];
    if ($category) $heroBreadcrumbs[] = ['label' => $category->name, 'url' => route('blog.category', $category)];
    elseif ($tag) $heroBreadcrumbs[] = ['label' => 'Tag: '.$tag->name, 'url' => route('blog.tag', $tag)];
    elseif ($author) $heroBreadcrumbs[] = ['label' => 'Notes by '.$author->name, 'url' => route('blog.author', $author)];
@endphp
@section('title', $taxonomy?->seo_title ?: ($page?->seo_title ?: $pageTitle.' — Mwanafunzi Investor'))
@section('description', $taxonomy?->seo_description ?: ($page?->seo_description ?: ($intro?->body ?: 'Daily updates, market education, trading reviews and practical notes from Mwanafunzi Investor.')))
@section('canonical', $taxonomyCanonical)
@section('robots', ($hasDiscoveryQuery ? 'noindex,follow' : ($taxonomy?->robots ?: 'index,follow')))
@section('og_title', $taxonomy?->og_title ?: ($page?->seo_title ?: $pageTitle.' — Mwanafunzi Investor'))
@section('og_description', $taxonomy?->og_description ?: ($page?->seo_description ?: ($intro?->body ?: 'Daily updates, market education, trading reviews and practical notes from Mwanafunzi Investor.')))
@if($taxonomy?->og_image) @section('og_image', \App\Support\PublicHero::candidate($taxonomy->og_image)['url'] ?? asset('storage/'.$taxonomy->og_image)) @endif
@section('structured_data')<script type="application/ld+json">{!! json_encode(\App\Support\Seo::breadcrumbSchema($heroBreadcrumbs), JSON_UNESCAPED_SLASHES) !!}</script>@endsection
@section('content')
    <x-public-hero class="public-page-hero" :eyebrow="$introPayload['eyebrow'] ?? 'Blog / News & updates'" :title="$pageTitle" :summary="$intro?->body ?? 'A living record of market education, platform updates and ideas worth returning to. No invented headlines. No made-up results.'" setting="hero_journal_image" :fallback-image="config('public.hero_defaults.journal')" :breadcrumbs="$heroBreadcrumbs" />

    <section class="platform-section editorial-index-section">
        <div class="container">
            <form class="journal-filters editorial-filters" method="get" action="{{ route('blog') }}" aria-label="Filter blog articles">
                <label>Search
                    <input type="search" name="search" value="{{ $search }}" maxlength="120" placeholder="Search title, topic or tag" autocomplete="off">
                </label>
                <label>Category
                    <select name="category">
                        <option value="">All categories</option>
                        @foreach($categories as $item)
                            <option value="{{ $item->slug }}" @selected($category?->slug === $item->slug || request('category') === $item->slug)>{{ $item->name }} ({{ $item->published_articles_count }})</option>
                        @endforeach
                    </select>
                </label>
                <label>Tag
                    <select name="tag">
                        <option value="">All tags</option>
                        @foreach($tags as $item)
                            <option value="{{ $item->slug }}" @selected($tag?->slug === $item->slug || request('tag') === $item->slug)>{{ $item->name }}</option>
                        @endforeach
                    </select>
                </label>
                <label>Sort
                    <select name="sort">
                        <option value="newest" @selected($sort === 'newest')>Newest first</option>
                        <option value="oldest" @selected($sort === 'oldest')>Oldest first</option>
                        <option value="updated" @selected($sort === 'updated')>Recently updated</option>
                    </select>
                </label>
                <input type="hidden" name="view" value="{{ $view }}" data-journal-view-input>
                <div class="journal-filter-actions">
                    <button class="button button-dark" type="submit">Search blog <span aria-hidden="true">↗</span></button>
                    @if($hasFilters)<a class="journal-clear-link" href="{{ route('blog') }}">Clear filters</a>@endif
                </div>
            </form>

            <div class="editorial-subsection-heading">
                <div><p class="eyebrow">{{ $hasFilters ? 'Results' : 'Latest updates' }}</p><h2>{{ $search !== '' ? 'What we found.' : 'The latest notes.' }}</h2><span class="editorial-result-count">{{ $publishedArticleCount }} {{ \Illuminate\Support\Str::plural('article', $publishedArticleCount) }}</span></div>
                <div class="editorial-view-switcher" role="group" aria-label="Choose blog view">
                    <span class="editorial-view-label">View</span>
                    <button class="editorial-view-button" type="button" data-journal-view="grid" aria-pressed="{{ $view === 'grid' ? 'true' : 'false' }}" aria-label="Grid view" title="Grid view"><svg aria-hidden="true" viewBox="0 0 16 16"><rect x="1" y="1" width="5" height="5"/><rect x="10" y="1" width="5" height="5"/><rect x="1" y="10" width="5" height="5"/><rect x="10" y="10" width="5" height="5"/></svg><span class="sr-only">Grid</span></button>
                    <button class="editorial-view-button" type="button" data-journal-view="list" aria-pressed="{{ $view === 'list' ? 'true' : 'false' }}" aria-label="List view" title="List view"><svg aria-hidden="true" viewBox="0 0 16 16"><path d="M1 3h2M6 3h9M1 8h2M6 8h9M1 13h2M6 13h9"/></svg><span class="sr-only">List</span></button>
                </div>
            </div>

            @if($hasFilters)
                <div class="editorial-filter-context"><span class="eyebrow">Current view</span><strong>{{ $search !== '' ? 'Search results for “'.$search.'”' : $pageTitle }}</strong><span>{{ $articles->total() }} {{ \Illuminate\Support\Str::plural('article', $articles->total()) }}</span><a href="{{ route('blog') }}">Clear view <span aria-hidden="true">×</span></a></div>
            @endif

            <div class="article-grid editorial-article-grid{{ $view === 'list' ? ' is-list-view' : '' }}">
                @forelse($articles as $article)
                    @php
                        $articleSource = \App\Support\PublicHero::candidate((string) $article->featured_image);
                        $articleIsVideo = (bool) ($articleSource['is_video'] ?? false);
                        $cardImage = \App\Support\PublicHero::resolveImage([$article->featured_image, $article->og_image, config('public.hero_defaults.journal')]);
                    @endphp
                    <article class="article-card editorial-article-card">
                        <a class="article-card-media" href="{{ route('blog.show', $article) }}" aria-label="Read article: {{ $article->title }}">
                            @if($cardImage)<img src="{{ $cardImage['url'] }}" @if($cardImage['srcset']) srcset="{{ $cardImage['srcset'] }}" sizes="(max-width: 760px) 100vw, (max-width: 1050px) 50vw, 33vw" @endif alt="{{ $article->title }}" loading="lazy">@else<span class="editorial-media-placeholder">Article note</span>@endif
                            @if($articleIsVideo)<span class="editorial-media-badge">Video</span>@endif
                        </a>
                        <div class="article-meta"><span>{{ $article->displayCategory() }}</span><span>{{ $article->published_at?->format('M j, Y') }}</span></div>
                        <h2><a href="{{ route('blog.show', $article) }}">{{ $article->title }}</a></h2>
                        @if($article->publicExcerpt())<p>{{ $article->publicExcerpt() }}</p>@endif
                        <div class="article-card-byline"><span>{{ $article->displayAuthor() }}</span><span>{{ $article->readingTimeMinutes() }} min read</span></div>
                        <div class="article-card-footer"><span>Published note</span><a class="text-link" href="{{ route('blog.show', $article) }}">Read <span aria-hidden="true">→</span></a></div>
                    </article>
                @empty
                    <div class="empty-state editorial-empty-state"><span class="empty-index">J / 00</span><h2>{{ $search !== '' || $category || $tag ? 'Nothing matched these filters.' : 'The next entry is being studied.' }}</h2><p>{{ $search !== '' || $category || $tag ? 'Try a broader phrase or clear the filters to browse the latest notes.' : ($introPayload['empty_body'] ?? 'When a new note is published, it will appear here from the connected blog source.') }}</p><a class="button button-dark" href="{{ route('blog') }}">{{ $search !== '' || $category || $tag ? 'Clear filters' : 'Refresh notes' }} <span aria-hidden="true">↗</span></a></div>
                @endforelse
            </div>

            @if($articles->hasPages())
                <nav class="editorial-pagination" aria-label="Blog pagination">
                    @if($articles->onFirstPage())<span class="editorial-pagination-disabled">Previous</span>@else<a href="{{ $articles->previousPageUrl() }}" rel="prev">Previous</a>@endif
                    <div class="editorial-pagination-pages">@foreach($articles->getUrlRange(max(1, $articles->currentPage() - 2), min($articles->lastPage(), $articles->currentPage() + 2)) as $pageNumber => $url)<a href="{{ $url }}" @if($pageNumber === $articles->currentPage()) aria-current="page" class="is-current" @endif>{{ $pageNumber }}</a>@endforeach</div>
                    @if($articles->hasMorePages())<a href="{{ $articles->nextPageUrl() }}" rel="next">Next</a>@else<span class="editorial-pagination-disabled">Next</span>@endif
                </nav>
            @endif
        </div>
    </section>
@endsection
