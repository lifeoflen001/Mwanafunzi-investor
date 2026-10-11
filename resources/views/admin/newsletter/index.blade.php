@extends('layouts.admin')
@section('title', 'Newsletter')
@section('portal-heading', 'Newsletter')
@section('content')
<x-admin.page-header eyebrow="Communication / Newsletter" title="Newsletter subscribers" description="Manage explicit email subscriptions, confirmations and unsubscribe requests." action-url="{{ route('admin.settings.newsletter') }}" action-label="Newsletter settings" action-class="button button-secondary" />
<div class="admin-stats admin-operational-stats">
    @foreach([['Total subscribers', $stats['total']], ['Active subscribers', $stats['active']], ['Pending confirmation', $stats['pending']], ['Unsubscribed', $stats['unsubscribed']], ['New this month', $stats['new_this_month']]] as $stat)<div class="admin-stat-card"><span>{{ $stat[0] }}</span><strong>{{ number_format($stat[1]) }}</strong><small>Live database count</small></div>@endforeach
</div>
<div class="admin-dashboard-grid">
    <section class="admin-form-card"><div class="admin-section-heading"><div><p class="eyebrow">Add subscriber</p><h2>Manual subscription</h2></div></div><form class="admin-form" method="post" action="{{ route('admin.newsletter.store') }}"><div class="form-row"><label>Email address<input type="email" name="email" required autocomplete="email"></label><label>Source<select name="source" required>@foreach($sources as $source)<option value="{{ $source }}">{{ ucfirst(str_replace('-', ' ', $source)) }}</option>@endforeach</select></label></div>@csrf<div class="form-actions"><button class="button button-primary" type="submit">Add subscriber <span aria-hidden="true">↗</span></button></div></form></section>
    <section class="admin-form-card"><div class="admin-section-heading"><div><p class="eyebrow">Export</p><h2>Consent-safe CSV</h2></div></div><p class="field-help">Exports only subscription lifecycle fields. Spreadsheet formula prefixes are neutralised.</p><a class="button button-secondary" href="{{ route('admin.newsletter.export', request()->only(['status', 'source'])) }}">Export CSV <span aria-hidden="true">↗</span></a></section>
</div>
<x-admin.filter-toolbar method="get" action="{{ route('admin.newsletter') }}"><label class="admin-search-field"><span class="sr-only">Search subscribers</span><input type="search" name="search" value="{{ request('search') }}" placeholder="Search email"></label><label><span class="sr-only">Status</span><select name="status"><option value="">All statuses</option>@foreach($statuses as $status)<option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>@endforeach</select></label><label><span class="sr-only">Source</span><select name="source"><option value="">All sources</option>@foreach($sources as $source)<option value="{{ $source }}" @selected(request('source') === $source)>{{ ucfirst(str_replace('-', ' ', $source)) }}</option>@endforeach</select></label><label><span class="sr-only">Date</span><input type="date" name="date" value="{{ request('date') }}"></label><button class="button button-secondary button-small" type="submit">Filter</button>@if(request()->hasAny(['search','status','source','date']))<a class="button button-ghost button-small" href="{{ route('admin.newsletter') }}">Reset</a>@endif</x-admin.filter-toolbar>
<div class="admin-table newsletter-table"><div class="admin-table-head"><span>Email</span><span>Status</span><span>Source</span><span>Subscribed</span><span>Confirmed</span><span>User</span><span>Actions</span></div>
    @forelse($subscribers as $subscriber)
        <div class="admin-table-row"><div><a href="{{ route('admin.newsletter.show', $subscriber) }}"><strong>{{ $subscriber->email }}</strong></a><small>{{ $subscriber->last_confirmation_sent_at?->diffForHumans() ?: 'No confirmation sent' }}</small></div><x-admin.status-badge :status="$subscriber->status" /><span>{{ ucfirst(str_replace('-', ' ', $subscriber->source)) }}</span><time datetime="{{ $subscriber->created_at->toIso8601String() }}">{{ $subscriber->created_at->format('M j, Y') }}</time><span>{{ $subscriber->confirmed_at?->format('M j, Y') ?: '—' }}</span><span>{{ $subscriber->user?->name ?: 'Guest' }}</span><div class="admin-actions"><x-admin.action-menu><a href="{{ route('admin.newsletter.show', $subscriber) }}">View</a>
            @if($subscriber->status === 'pending')<form method="post" action="{{ route('admin.newsletter.resend', $subscriber) }}">@csrf<button type="submit">Resend confirmation</button></form>
            @endif
            @if($subscriber->status !== 'unsubscribed')<form method="post" action="{{ route('admin.newsletter.unsubscribe.admin', $subscriber) }}">@csrf<button type="submit">Unsubscribe</button></form>
            @else<form method="post" action="{{ route('admin.newsletter.restore', $subscriber) }}">@csrf<button type="submit">Restore</button></form>
            @endif
            <form method="post" action="{{ route('admin.newsletter.destroy', $subscriber) }}">@csrf @method('delete')<button type="submit">Delete</button></form></x-admin.action-menu></div></div>
    @empty
        <x-admin.empty-state title="No subscribers found." description="New footer subscriptions will appear here." />
    @endforelse
</div>
{{ $subscribers->links() }}
@endsection
