@extends('layouts.admin')
@section('title', 'Dashboard')
@section('portal-heading', 'Dashboard')
@section('content')
<div class="admin-dashboard-page">
    <x-admin.page-header eyebrow="Operations" title="Dashboard" description="See what is happening, what needs attention and what to do next." action-url="{{ route('home') }}" action-label="View public site ↗" action-class="button button-secondary" />

    <section class="admin-dashboard-section" aria-labelledby="operational-overview-title">
        <div class="admin-dashboard-section-heading">
            <div><p class="admin-eyebrow">Today at a glance</p><h2 id="operational-overview-title">Operational overview</h2></div>
        </div>
        <div class="admin-stats admin-operational-stats">
            @foreach($primaryKpis as $kpi)
                <x-admin.stat-card :label="$kpi['label']" :value="$kpi['value']" :description="$kpi['description']" :url="$kpi['url']" />
            @endforeach
        </div>
    </section>

    <section class="admin-dashboard-section" aria-labelledby="editorial-overview-title">
        <div class="admin-dashboard-section-heading"><div><p class="admin-eyebrow">Journal / Blog</p><h2 id="editorial-overview-title">Editorial overview</h2></div><a class="admin-card-link" href="{{ route('admin.journal') }}">Open all posts →</a></div>
        <div class="admin-stats admin-operational-stats">@foreach($editorialKpis as $kpi)<x-admin.stat-card :label="$kpi['label']" :value="$kpi['value']" :description="$kpi['description']" :url="$kpi['url']" />@endforeach</div>
    </section>

    <section class="admin-card admin-attention-panel" aria-labelledby="needs-attention-title">
        <header class="admin-card-header">
            <div><p class="admin-eyebrow">Action queue</p><h2 id="needs-attention-title">Needs attention</h2><p>Open work gathered from live enquiries, commerce and publishing records.</p></div>
            @if($attentionItems->isNotEmpty())<span class="admin-attention-summary">{{ $attentionItems->sum('count') }} item(s)</span>@endif
        </header>
        @if($attentionItems->isNotEmpty())
            <div class="admin-attention-list">
                @foreach($attentionItems as $item)
                    <a href="{{ $item['url'] }}" class="admin-attention-item"><span class="admin-attention-count">{{ $item['count'] }}</span><span><strong>{{ $item['label'] }}</strong><small>{{ $item['action'] }} <span aria-hidden="true">↗</span></small></span></a>
                @endforeach
            </div>
        @else
            <x-admin.empty-state title="No open work needs attention." description="The live action queues are clear." />
        @endif
    </section>

    <div class="admin-dashboard-grid">
        <div class="admin-dashboard-stack">
            <x-admin.card title="Recent activity" subtitle="Real changes and incoming records from the operating desk.">
                <x-slot:action><a class="admin-card-link" href="{{ route('admin.audit') }}">Open activity log →</a></x-slot:action>
                <div class="admin-dashboard-list admin-activity-list">
                    @forelse($recentActivity as $activity)
                        <div class="admin-dashboard-list-item admin-activity-item"><span class="admin-activity-marker" aria-hidden="true"></span><div><small>{{ $activity['label'] }}</small>@if($activity['url'])<a href="{{ $activity['url'] }}"><strong>{{ $activity['title'] }}</strong></a>@else<strong>{{ $activity['title'] }}</strong>@endif</div><time datetime="{{ $activity['date']->toIso8601String() }}">{{ $activity['date']->diffForHumans() }}</time></div>
                    @empty
                        <x-admin.empty-state title="No recent activity yet." description="New records and audited changes will appear here." />
                    @endforelse
                </div>
            </x-admin.card>

            <x-admin.card title="Recent enquiries" subtitle="Context for the conversations waiting on the desk.">
                <x-slot:action><a class="admin-card-link" href="{{ route('admin.messages') }}">Open inbox →</a></x-slot:action>
                <div class="admin-dashboard-list admin-enquiry-list">
                    @forelse($recentEnquiries as $enquiry)
                        @php($message = $enquiry['message'])
                        <article class="admin-enquiry-item"><div class="admin-enquiry-main"><a href="{{ route('admin.messages.show', $message) }}"><strong>{{ $message->name }}</strong></a><p>{{ \Illuminate\Support\Str::limit($message->message, 92) }}</p><div class="admin-enquiry-meta"><span>Category: {{ $message->category ?: 'General' }}</span><span>Module: {{ $message->businessUnit?->name ?: 'General desk' }}</span>@if($enquiry['waiting'])<span>Waiting: {{ $enquiry['waiting'] }}</span>@endif</div></div><div class="admin-enquiry-side"><x-admin.status-badge :status="$message->status" /><time datetime="{{ $message->created_at->toIso8601String() }}">{{ $message->created_at->format('M j, Y H:i') }}</time><a href="{{ route('admin.messages.show', $message) }}">Open enquiry <span aria-hidden="true">↗</span></a></div></article>
                    @empty
                        <x-admin.empty-state title="No enquiries yet." description="New contact conversations will appear here." />
                    @endforelse
                </div>
            </x-admin.card>
        </div>

        <div class="admin-dashboard-stack">
            <x-admin.card title="Quick actions" subtitle="High-frequency tasks for the operating desk.">
                <div class="admin-quick-grid admin-dashboard-quick-grid">
                    <a href="{{ route('admin.pages.create') }}"><div><strong>New page</strong><small>Publish a public page</small></div><span aria-hidden="true">+</span></a>
                    <a href="{{ route('admin.courses.create') }}"><div><strong>New course</strong><small>Build learning content</small></div><span aria-hidden="true">+</span></a>
                    <a href="{{ route('admin.products.create') }}"><div><strong>New product</strong><small>Create a digital tool</small></div><span aria-hidden="true">+</span></a>
                    <a href="{{ route('admin.media') }}"><div><strong>Upload media</strong><small>Manage visual assets</small></div><span aria-hidden="true">+</span></a>
                </div>
            </x-admin.card>

            <x-admin.card title="Platform overview" subtitle="Secondary system counts and storage footprint.">
                <div class="admin-platform-overview">
                    @foreach($platformOverview as $item)
                        <a href="{{ $item['url'] }}"><span>{{ $item['label'] }}</span><strong>{{ $item['value'] }}</strong></a>
                    @endforeach
                </div>
            </x-admin.card>
        </div>
    </div>
</div>
@endsection
