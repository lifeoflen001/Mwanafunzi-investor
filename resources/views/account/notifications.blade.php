@extends('layouts.account')
@section('title', 'Notifications — Mwanafunzi Investor')
@section('portal-heading', 'Notifications')
@section('content')
<section class="student-page-heading">
    <div><p class="student-eyebrow">Account / Updates</p><h1>Account updates.</h1><p>Keep track of comments, studio enquiries and other activity connected to your workspace.</p></div>
    @if($unreadCount > 0)
        <form method="post" action="{{ route('account.notifications.read-all') }}">@csrf<button class="student-button student-button-secondary" type="submit">Mark all as read</button></form>
    @endif
</section>
<x-portal-card class="student-panel">
    @forelse($notifications as $notification)
        @php($data = $notification->data)
        <div class="student-notification-row {{ $notification->read_at ? '' : 'is-unread' }}">
            <span class="student-notification-marker" aria-hidden="true">{{ $notification->read_at ? '○' : '●' }}</span>
            <div><strong>{{ $data['title'] ?? 'Account update' }}</strong><p>{{ $data['body'] ?? '' }}</p><small>{{ $notification->created_at?->diffForHumans() }}</small></div>
            <form method="post" action="{{ route('account.notifications.read', $notification) }}">@csrf<button class="student-text-button" type="submit">{{ $notification->read_at ? 'Open' : 'Mark read' }} <span aria-hidden="true">↗</span></button></form>
        </div>
    @empty
        <x-portal-empty icon="○" title="No account updates yet." description="Important activity from your workspace will appear here." />
    @endforelse
</x-portal-card>
<div class="student-pagination">{{ $notifications->links() }}</div>
@endsection
