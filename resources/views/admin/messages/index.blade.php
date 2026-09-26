@extends('layouts.admin')
@section('title', 'Contact messages')
@section('portal-heading', 'Enquiries')
@section('content')
<x-admin.page-header eyebrow="CMS / Inbox" title="Contact messages" description="Keep each enquiry moving without turning the CMS into a CRM." />
<x-admin.filter-toolbar method="get" action="{{ route('admin.messages') }}">
    <label class="sr-only" for="message-search">Search enquiries</label><input id="message-search" name="q" value="{{ $term }}" placeholder="Search sender, email or message">
    <label class="sr-only" for="message-status-filter">Filter enquiry status</label><select id="message-status-filter" name="status"><option value="">All statuses</option><option value="new" @selected(request('status') === 'new')>New</option><option value="in_progress" @selected(request('status') === 'in_progress')>In progress</option><option value="resolved" @selected(request('status') === 'resolved')>Resolved</option><option value="spam" @selected(request('status') === 'spam')>Spam</option></select>
    <button class="button button-secondary button-small" type="submit">Filter</button>@if($term || request('status'))<a class="admin-back-link" href="{{ route('admin.messages') }}">Clear</a>@endif
</x-admin.filter-toolbar>
<x-admin.card class="admin-table-card"><div class="admin-table admin-message-table">
    <div class="admin-table-head"><span>Sender</span><span>Email / phone</span><span>Category</span><span>Module</span><span>Received</span><span>Status</span></div>
    @forelse($messages as $message)
        <div class="admin-table-row">
            <div><strong><a href="{{ route('admin.messages.show', $message) }}">{{ $message->name }}</a> @if(!$message->read_at)<span class="unread-dot" title="Unread">●</span>@endif</strong><small>{{ \Illuminate\Support\Str::limit($message->message, 90) }}</small></div>
            <div><a href="mailto:{{ $message->email }}">{{ $message->email }}</a><small>{{ $message->phone ?: 'No phone provided' }}</small></div>
            <span class="message-category">{{ str_replace('_', ' ', $message->category) }}</span>
            <span>{{ $message->businessUnit?->name ?: 'General desk' }}</span>
            <span>{{ $message->created_at->format('M j, Y H:i') }}@if(!$message->read_at)<form method="post" action="{{ route('admin.messages.read', $message) }}">@csrf<button class="text-button" type="submit">Mark read</button></form>@endif</span>
            <form class="message-status-form" method="post" action="{{ route('admin.messages.update', $message) }}">@csrf @method('patch')<label class="sr-only" for="status-{{ $message->id }}">Message status</label><select id="status-{{ $message->id }}" name="status"><option value="new" @selected($message->status === 'new')>New</option><option value="in_progress" @selected($message->status === 'in_progress')>In progress</option><option value="resolved" @selected($message->status === 'resolved')>Resolved</option><option value="spam" @selected($message->status === 'spam')>Spam</option></select><button class="button button-secondary button-small" type="submit">Update</button></form>
        </div>
    @empty
        <div class="empty-state"><p>No contact messages match this filter.</p></div>
    @endforelse
</div></x-admin.card>
{{ $messages->links() }}
@endsection
