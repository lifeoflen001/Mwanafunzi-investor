@extends('layouts.account')
@section('title', 'Saved articles — Mwanafunzi Investor')
@section('portal-heading', 'Saved articles')
@section('content')
<section class="student-page-heading"><div><p class="student-eyebrow">Journal / Reading list</p><h1>Saved articles.</h1><p>Keep the ideas worth returning to close at hand.</p></div></section>
<div class="student-saved-grid">
    @forelse($savedArticles as $saved)
        @php($article = $saved->article)
        <x-portal-card class="student-panel student-saved-card">
            <div class="student-card-meta"><span>{{ $article->category?->name ?: 'Journal' }}</span><span>{{ $saved->created_at?->format('M j, Y') }}</span></div>
            <h2><a href="{{ route('blog.show', $article) }}">{{ $article->title }}</a></h2>
            <p>{{ $article->excerpt ?: str($article->body)->stripTags()->limit(145) }}</p>
            <a class="student-text-link" href="{{ route('blog.show', $article) }}">Read article <span aria-hidden="true">→</span></a>
        </x-portal-card>
    @empty
        <x-portal-card class="student-panel student-wide-empty"><x-portal-empty icon="♡" title="Your reading list is empty." description="Save a published journal article to return to it later." :href="route('blog')" action="Browse the journal" /></x-portal-card>
    @endforelse
</div>
<div class="student-pagination">{{ $savedArticles->links() }}</div>
@endsection
