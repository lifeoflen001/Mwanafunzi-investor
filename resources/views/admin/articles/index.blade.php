@extends('layouts.admin')
@section('title', 'Journal articles')
@section('portal-heading', 'Journal')
@section('content')
<x-admin.page-header eyebrow="Content / Journal" title="Journal articles" description="Write, publish and archive field notes from one editorial queue." action-url="{{ route('admin.articles.create') }}" action-label="New article" />
<x-admin.filter-toolbar method="get" action="{{ route('admin.articles') }}">
    <label class="admin-search-field"><span class="sr-only">Search articles</span><input type="search" name="q" value="{{ request('q') }}" placeholder="Search title or slug"></label>
    <label><span class="sr-only">Article status</span><select name="status"><option value="">All statuses</option><option value="draft" @selected(request('status') === 'draft')>Draft</option><option value="published" @selected(request('status') === 'published')>Published</option></select></label>
    <button class="button button-secondary button-small" type="submit">Filter</button>
    @if(request('q') || request('status'))<a class="button button-ghost button-small" href="{{ route('admin.articles') }}">Reset</a>@endif
</x-admin.filter-toolbar>
<div class="admin-table admin-article-table"><div class="admin-table-head"><span>Article</span><span>Category</span><span>Status</span><span>Published</span><span>Actions</span></div>
    @forelse($articles as $article)
        <div class="admin-table-row"><div><strong>{{ $article->title }}</strong><small>{{ $article->slug }}</small></div><span>{{ $article->category?->name ?: 'Uncategorised' }}</span><x-admin.status-badge :status="$article->trashed() ? 'archived' : $article->status" /><span>{{ $article->published_at?->format('M j, Y') ?: 'Not published' }}</span><div class="admin-actions"><x-admin.action-menu><a href="{{ route('admin.articles.edit', $article) }}">Edit</a>@if($article->trashed())<form method="post" action="{{ route('admin.articles.restore', $article->id) }}">@csrf<button type="submit">Restore</button></form>@else<form method="post" action="{{ route('admin.articles.destroy', $article) }}">@csrf @method('delete')<button type="submit">Archive</button></form>@endif</x-admin.action-menu></div></div>
    @empty
        <x-admin.empty-state title="No articles found." description="Start a journal note when you are ready to publish." action-url="{{ route('admin.articles.create') }}" action-label="New article" />
    @endforelse
</div>
{{ $articles->withQueryString()->links() }}
@endsection
