@extends('layouts.admin')
@section('title', 'Comment moderation')
@section('portal-heading', 'Comments')
@section('content')
<x-admin.page-header eyebrow="Content / Discussion" title="Comment moderation" description="Review reader discussion before it reaches the public Journal." />
<div class="admin-stat-grid comment-status-grid">@foreach($counts as $status => $count)<x-admin.stat-card :label="ucfirst($status)" :value="$count" />@endforeach</div>
<x-admin.filter-toolbar method="get" action="{{ route('admin.comments') }}">
    <label class="admin-search-field"><span class="sr-only">Search comments</span><input type="search" name="q" value="{{ request('q') }}" placeholder="Search comment, email or article"></label>
    <label><span class="sr-only">Comment status</span><select name="status"><option value="">All statuses</option>@foreach([...\App\Models\Comment::STATUSES, 'reported'] as $status)<option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>@endforeach</select></label>
    <button class="button button-secondary button-small" type="submit">Filter</button>
</x-admin.filter-toolbar>
<div class="admin-table admin-comment-table"><div class="admin-table-head"><span>Comment</span><span>Article</span><span>Author</span><span>Status</span><span>Reports</span><span>Actions</span></div>
@forelse($comments as $comment)
    <div class="admin-table-row"><div><strong>{{ \Illuminate\Support\Str::limit($comment->body, 90) }}</strong><small>{{ $comment->parent_id ? 'Reply' : 'Comment' }} · {{ $comment->created_at->format('M j, Y H:i') }}</small></div><span>{{ \Illuminate\Support\Str::limit($comment->article?->title ?: 'Deleted article', 45) }}</span><span>{{ $comment->user?->name ?: $comment->name ?: 'Reader' }}<small>{{ $comment->user?->email ?: $comment->email }}</small></span><x-admin.status-badge :status="$comment->status" /><span>{{ $comment->reports_count }}</span><div class="admin-actions"><x-admin.action-menu>
        <a href="{{ route('admin.comments.show', $comment) }}">View context</a>
        @if($comment->status !== 'approved')
            <form method="post" action="{{ route('admin.comments.update', $comment) }}">@csrf @method('patch')<input type="hidden" name="status" value="approved"><button type="submit">Approve</button></form>
        @endif
        @if(in_array($comment->status, ['hidden', 'spam', 'rejected'], true))
            <form method="post" action="{{ route('admin.comments.update', $comment) }}">@csrf @method('patch')<input type="hidden" name="status" value="pending"><button type="submit">Restore</button></form>
        @else
            <form method="post" action="{{ route('admin.comments.update', $comment) }}">@csrf @method('patch')<input type="hidden" name="status" value="hidden"><button type="submit">Hide</button></form>
            <form method="post" action="{{ route('admin.comments.update', $comment) }}">@csrf @method('patch')<input type="hidden" name="status" value="spam"><button type="submit">Mark spam</button></form>
        @endif
        <form method="post" action="{{ route('admin.comments.destroy', $comment) }}" data-confirm="Delete this comment permanently?">@csrf @method('delete')<button type="submit">Delete</button></form>
    </x-admin.action-menu></div></div>
@empty
    <x-admin.empty-state title="No comments found." description="Approved and pending discussions will appear here as readers participate." />
@endforelse
</div>
{{ $comments->withQueryString()->links() }}
@endsection
