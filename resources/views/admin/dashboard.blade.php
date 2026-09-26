@extends('layouts.admin')
@section('title', 'Dashboard')
@section('portal-heading', 'Dashboard')
@section('content')
@php
    $statDescriptions = [
        'published pages' => 'Live public routes',
        'published courses' => 'Published learning',
        'products' => 'Tools and downloads',
        'articles' => 'Journal records',
        'media assets' => 'Managed files',
        'students' => 'Customer accounts',
        'administrators' => 'Authorised operators',
        'orders' => 'Commerce records',
        'unread messages' => 'Need attention',
    ];
@endphp

<x-admin.page-header eyebrow="Content management" title="Dashboard" description="Operational overview of content, customers and platform activity." action-url="{{ route('home') }}" action-label="View public site" action-class="button button-secondary" />

<section class="admin-stats" aria-label="Platform summary">
    @foreach($counts as $label => $count)
        <div><span>{{ ucfirst($label) }}</span><strong>{{ $count }}</strong><small>{{ $statDescriptions[$label] ?? 'Current total' }}</small></div>
    @endforeach
    <div><span>Media storage</span><strong>{{ number_format($mediaStorage / 1048576, 1) }}<small> MB</small></strong><small>Uploaded asset footprint</small></div>
</section>

<div class="admin-dashboard-grid">
    <div class="admin-dashboard-stack">
        <x-admin.card title="Recent content" subtitle="Latest records added to the platform.">
            <x-slot:action><a class="admin-card-link" href="{{ route('admin.articles') }}">View journal →</a></x-slot:action>
            <div class="admin-dashboard-list">
                @forelse($recentContent as $item)
                    <div class="admin-dashboard-list-item"><div><strong>{{ $item['title'] }}</strong><small>{{ $item['type'] }}</small></div><time datetime="{{ $item['date']->toIso8601String() }}">{{ $item['date']->format('M j, Y') }}</time></div>
                @empty
                    <x-admin.empty-state title="No content created yet." description="New courses and journal records will appear here." />
                @endforelse
            </div>
        </x-admin.card>

        <x-admin.card title="Recent enquiries" subtitle="Messages that may need a response.">
            <x-slot:action><a class="admin-card-link" href="{{ route('admin.messages') }}">Open inbox →</a></x-slot:action>
            <div class="admin-dashboard-list">
                @forelse($recentMessages as $message)
                    <div class="admin-dashboard-list-item"><div><a href="{{ route('admin.messages.show', $message) }}"><strong>{{ $message->name }}</strong></a><small>{{ \Illuminate\Support\Str::limit($message->message, 76) }}</small></div><div><x-admin.status-badge :status="$message->status" /><time datetime="{{ $message->created_at->toIso8601String() }}">{{ $message->created_at->format('M j') }}</time></div></div>
                @empty
                    <x-admin.empty-state title="Inbox is clear." description="New contact enquiries will appear here." />
                @endforelse
            </div>
        </x-admin.card>
    </div>

    <div class="admin-dashboard-stack">
        <x-admin.card title="Quick actions" subtitle="Common publishing and operations tasks.">
            <div class="admin-quick-grid">
                <a href="{{ route('admin.pages') }}"><div><strong>Pages</strong><small>Manage public sections</small></div><span aria-hidden="true">↗</span></a>
                <a href="{{ route('admin.courses.create') }}"><div><strong>Course</strong><small>Start a learning product</small></div><span aria-hidden="true">+</span></a>
                <a href="{{ route('admin.articles.create') }}"><div><strong>Journal</strong><small>Write a field note</small></div><span aria-hidden="true">+</span></a>
                <a href="{{ route('admin.media') }}"><div><strong>Media</strong><small>Upload an asset</small></div><span aria-hidden="true">+</span></a>
            </div>
        </x-admin.card>

        <x-admin.card title="Content operations" subtitle="Jump directly to the areas you manage most.">
            <div class="admin-operation-links">
                <a href="{{ route('admin.pages') }}"><span>Pages and policies</span><b>{{ $counts['published pages'] ?? 0 }}</b></a>
                <a href="{{ route('admin.commerce.orders') }}"><span>Orders and payments</span><b>{{ $counts['orders'] ?? 0 }}</b></a>
                <a href="{{ route('admin.customers') }}"><span>Students and customers</span><b>{{ $counts['students'] ?? 0 }}</b></a>
                <a href="{{ route('admin.audit') }}"><span>Activity log</span><b>→</b></a>
            </div>
        </x-admin.card>
    </div>
</div>
@endsection
