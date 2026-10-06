@extends('layouts.admin')
@section('title', 'Journal articles')
@section('portal-heading', 'Journal')
@section('content')
<x-admin.page-header eyebrow="Content / Journal" title="Journal articles" description="Write, publish and archive field notes from one editorial queue.">
    <x-slot:actions><a class="button button-secondary" href="{{ route('admin.journal.daily-updates.create') }}">Daily update <span aria-hidden="true">+</span></a></x-slot:actions>
</x-admin.page-header>
<x-admin.filter-toolbar method="get" action="{{ route('admin.articles') }}">
    <label class="admin-search-field"><span class="sr-only">Search articles</span><input type="search" name="q" value="{{ request('q') }}" placeholder="Search title, slug or excerpt"></label>
    <label><span class="sr-only">Status</span><select name="status"><option value="">All statuses</option>@foreach(['draft' => 'Draft', 'scheduled' => 'Scheduled', 'published' => 'Published', 'archived' => 'Archived'] as $value => $label)<option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>@endforeach</select></label>
    <label><span class="sr-only">Content type</span><select name="content_type"><option value="">All types</option>@foreach($contentTypes as $value => $label)<option value="{{ $value }}" @selected(request('content_type') === $value)>{{ $label }}</option>@endforeach</select></label>
    <label><span class="sr-only">Category</span><select name="category"><option value="">All categories</option>@foreach($categories as $category)<option value="{{ $category->id }}" @selected((string) request('category') === (string) $category->id)>{{ $category->name }}</option>@endforeach</select></label>
    <label><span class="sr-only">Author</span><select name="author"><option value="">All authors</option>@foreach($teamMembers as $member)<option value="{{ $member->id }}" @selected((string) request('author') === (string) $member->id)>{{ $member->name }}</option>@endforeach</select></label>
    <label><span class="sr-only">Published from</span><input type="date" name="date_from" value="{{ request('date_from') }}" aria-label="From date"></label><label><span class="sr-only">Published to</span><input type="date" name="date_to" value="{{ request('date_to') }}" aria-label="To date"></label>
    <button class="button button-secondary button-small" type="submit">Filter</button>@if(request()->hasAny(['q', 'status', 'content_type', 'category', 'author', 'date_from', 'date_to']))<a class="button button-ghost button-small" href="{{ route('admin.articles') }}">Reset</a>@endif
</x-admin.filter-toolbar>
<div class="admin-table admin-article-table"><div class="admin-table-head"><span>Post</span><span>Type</span><span>Category</span><span>Author</span><span>Status</span><span>Engagement</span><span>Published / Updated</span><span>Actions</span></div>
    @forelse($articles as $article)
        @php($image = $article->featured_image ? (\App\Support\PublicHero::candidate($article->featured_image)['url'] ?? asset('storage/'.$article->featured_image)) : null)
        <div class="admin-table-row"><div class="admin-article-post">@if($image)<img src="{{ $image }}" alt="" loading="lazy">@endif<div><strong>{{ $article->title }}</strong><small>{{ $article->slug }}@if($article->is_featured) · Featured @endif</small></div></div><span>{{ $contentTypes[$article->content_type] ?? ucfirst(str_replace('_', ' ', $article->content_type ?: 'article')) }}</span><span>{{ $article->category?->name ?: 'Uncategorised' }}</span><span>{{ $article->authorMember?->name ?: $article->author ?: 'Mwanafunzi Investor' }}</span><x-admin.status-badge :status="$article->trashed() ? 'archived' : $article->status" /><span>{{ $article->likes_count }} likes · {{ $article->comments_count }} comments</span><span>{{ $article->published_at?->format('M j, Y') ?: '—' }}<small>Updated {{ $article->updated_at?->format('M j, Y') }}</small></span><div class="admin-actions"><x-admin.action-menu><a href="{{ route('journal.show', $article) }}" target="_blank" rel="noopener">View</a>@if(!$article->trashed())<a href="{{ route('admin.articles.edit', $article) }}">Edit</a>@if($article->status !== 'draft')<a href="{{ \Illuminate\Support\Facades\URL::temporarySignedRoute('admin.preview', now()->addMinutes(30), ['type' => 'article', 'id' => $article->id]) }}" target="_blank" rel="noopener">Preview</a>@endif<form method="post" action="{{ route('admin.articles.destroy', $article) }}" data-confirm="Archive this article?">@csrf @method('delete')<button type="submit">Archive</button></form>@else<form method="post" action="{{ route('admin.articles.restore', $article->id) }}">@csrf<button type="submit">Restore</button></form>@endif</x-admin.action-menu></div></div>
    @empty
        <x-admin.empty-state title="No articles found." description="Start a journal note when you are ready to publish." action-url="{{ route('admin.articles.create') }}" action-label="New article" />
    @endforelse
</div>
{{ $articles->withQueryString()->links() }}
@endsection
