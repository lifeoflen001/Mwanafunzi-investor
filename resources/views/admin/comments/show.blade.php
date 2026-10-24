@extends('layouts.admin')
@section('title', 'Comment context')
@section('portal-heading', 'Comments')
@section('content')
<x-admin.page-header eyebrow="Content / Discussion" title="Comment context" description="Review the full conversation before changing its moderation status." />
<a class="admin-back-link" href="{{ route('admin.comments') }}">← Back to comments</a>
<div class="admin-grid-two">
    <x-admin.card title="Comment" subtitle="{{ $comment->article?->title ?: 'Deleted article' }}">
        <div class="admin-detail-copy">
            <div class="admin-person-heading">@if($comment->user)<x-portal-avatar :user="$comment->user" size="small" decorative />@else<span class="admin-person-avatar" aria-hidden="true">{{ $comment->initials() }}</span>@endif<p class="admin-eyebrow">{{ $comment->displayName() }} · {{ $comment->created_at->format('M j, Y H:i') }}</p></div>
            @if($comment->email)<p>{{ $comment->email }}</p>@endif
            @if($comment->parent)<p><strong>Replying to:</strong> {{ \Illuminate\Support\Str::limit($comment->parent->body, 160) }}</p>@endif
            <p>{{ $comment->body }}</p>
        </div>
        <div class="form-actions">
            <form method="post" action="{{ route('admin.comments.update', $comment) }}">@csrf @method('patch')<select name="status" aria-label="Moderation status">@foreach(\App\Models\Comment::STATUSES as $status)<option value="{{ $status }}" @selected($comment->status === $status)>{{ ucfirst($status) }}</option>@endforeach</select><button class="button button-dark" type="submit">Save status</button></form>
            <form method="post" action="{{ route('admin.comments.destroy', $comment) }}" data-confirm="Delete this comment permanently?">@csrf @method('delete')<button class="button button-danger" type="submit">Delete</button></form>
        </div>
    </x-admin.card>
    <x-admin.card title="Conversation" subtitle="{{ $comment->replies->count() }} replies · {{ $comment->reports->count() }} reports · ↑ {{ $comment->up_reactions_count }} · ↓ {{ $comment->down_reactions_count }}">@forelse($comment->replies as $reply)<div class="admin-subresource"><div class="admin-person-heading">@if($reply->user)<x-portal-avatar :user="$reply->user" size="small" decorative />@else<span class="admin-person-avatar" aria-hidden="true">{{ $reply->initials() }}</span>@endif<p class="admin-eyebrow">{{ $reply->displayName() }} · {{ $reply->created_at->format('M j, Y H:i') }}</p></div><p>{{ $reply->body }}</p><x-admin.status-badge :status="$reply->status" /></div>@empty<p class="admin-muted">No replies yet.</p>@endforelse</x-admin.card>
</div>
@if($comment->reports->isNotEmpty())<x-admin.card title="Reports" class="admin-section-spaced"><div class="admin-table"><div class="admin-table-head"><span>Reporter</span><span>Reason</span><span>Details</span><span>Status</span></div>@foreach($comment->reports as $report)<div class="admin-table-row"><span class="admin-person-cell">@if($report->user)<x-portal-avatar :user="$report->user" size="small" decorative />@else<span class="admin-person-avatar" aria-hidden="true">R</span>@endif<span>{{ $report->user?->name ?: 'Reader' }}</span></span><span>{{ ucfirst($report->reason) }}</span><span>{{ $report->details ?: '—' }}</span><x-admin.status-badge :status="$report->status" /></div>@endforeach</div></x-admin.card>@endif
@endsection
