@extends('layouts.admin')
@section('title', 'Enquiry · '.$message->name)
@section('portal-heading', 'Enquiries')
@section('content')
<div class="admin-heading"><div><p class="eyebrow">CMS / Inbox</p><h1>{{ $message->name }}</h1><p>{{ $message->email }} · Received {{ $message->created_at?->format('M j, Y H:i') }}</p></div><a class="back-link" href="{{ route('admin.messages') }}">← Back to enquiries</a></div>
<div class="admin-grid-two">
    <section class="admin-card admin-subresource"><div class="admin-heading"><div><p class="eyebrow">Message</p><h2>{{ str_replace('_', ' ', $message->category) }}</h2></div></div><p class="body-copy">{!! nl2br(e($message->message)) !!}</p></section>
    <section class="admin-card admin-subresource"><div class="admin-heading"><div><p class="eyebrow">Contact details</p><h2>Sender</h2></div></div><dl class="admin-definition-list"><div><dt>Name</dt><dd>{{ $message->name }}</dd></div><div><dt>Email</dt><dd><a href="mailto:{{ $message->email }}">{{ $message->email }}</a></dd></div><div><dt>Phone</dt><dd>{{ $message->phone ?: 'Not provided' }}</dd></div><div><dt>Business module</dt><dd>{{ $message->businessUnit?->name ?: 'General desk' }}</dd></div><div><dt>Received</dt><dd>{{ $message->created_at?->format('M j, Y H:i') }}</dd></div></dl><form class="admin-filter-row" method="post" action="{{ route('admin.messages.update', $message) }}">@csrf @method('patch')<label class="sr-only" for="message-status">Message status</label><select id="message-status" name="status"><option value="new" @selected($message->status === 'new')>New</option><option value="in_progress" @selected($message->status === 'in_progress')>In progress</option><option value="resolved" @selected($message->status === 'resolved')>Resolved</option><option value="spam" @selected($message->status === 'spam')>Spam</option></select><button class="button button-light" type="submit">Save status</button></form></section>
</div>
@endsection
