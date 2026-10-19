@extends('layouts.admin')

@section('title', 'Studio enquiries')
@section('portal-heading', 'Studio enquiries')

@section('content')
<x-admin.page-header eyebrow="Creative Studio / Workflow" title="Studio enquiries" description="Review real shoot briefs before moving them into quotation, scheduling or production." />
<x-admin.filter-toolbar method="get" action="{{ route('admin.studio.inquiries') }}">
    <label class="sr-only" for="studio-inquiry-search">Search enquiries</label><input id="studio-inquiry-search" name="q" value="{{ request('q') }}" placeholder="Search reference, sender or email">
    <label class="sr-only" for="studio-inquiry-status">Filter enquiry status</label><select id="studio-inquiry-status" name="status"><option value="">All statuses</option>@foreach(\App\Models\StudioInquiry::STATUSES as $status)<option value="{{ $status }}" @selected(request('status') === $status)>{{ str_replace('_', ' ', str($status)->title()) }}</option>@endforeach</select>
    <button class="button button-secondary button-small" type="submit">Filter</button>@if(request('q') || request('status'))<a class="admin-back-link" href="{{ route('admin.studio.inquiries') }}">Clear</a>@endif
</x-admin.filter-toolbar>
<x-admin.card class="admin-table-card"><div class="admin-table studio-inquiry-table">
    <div class="admin-table-head"><span>Reference / sender</span><span>Service</span><span>Preferred date</span><span>Status</span><span>Received</span><span></span></div>
    @forelse($inquiries as $inquiry)
        <div class="admin-table-row">
            <div><strong><a href="{{ route('admin.studio.inquiries.show', $inquiry) }}">{{ $inquiry->reference }}</a></strong><small>{{ $inquiry->name }} · {{ $inquiry->email }}@if($inquiry->project) · {{ $inquiry->project->title }}@endif</small></div>
            <span>{{ $inquiry->service?->title ?: 'Service to be confirmed' }}</span>
            <span>{{ $inquiry->preferred_date?->format('M j, Y') ?: 'Not set' }}</span>
            <span><x-admin.status-badge :status="$inquiry->status" /></span>
            <span>{{ $inquiry->created_at?->format('M j, Y H:i') }}</span>
            <x-admin.action-menu><a href="{{ route('admin.studio.inquiries.show', $inquiry) }}">View enquiry</a></x-admin.action-menu>
        </div>
    @empty
        <x-admin.empty-state title="No studio enquiries yet." description="Submitted Creative Studio briefs will appear here. No demo enquiries have been added." />
    @endforelse
</div></x-admin.card>
{{ $inquiries->links() }}
@endsection
