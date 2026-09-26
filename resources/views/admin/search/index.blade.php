@extends('layouts.admin')
@section('title', 'Search')
@section('portal-heading', 'Search')
@section('content')
    <x-admin.page-header eyebrow="Admin desk / Search" title="Search the desk" description="Search real pages, courses, products, journal entries, customers, orders and enquiries." />
    @if($term === '')
        <x-admin.empty-state title="Search the desk" description="Enter a search term to find content and operational records." />
    @elseif($results->isEmpty())
        <x-admin.empty-state title="No records matched" description="No records matched “{{ $term }}”." />
    @else
        <div class="admin-subresource-list">@foreach($results as $result)<a class="admin-subresource-header" href="{{ $result['url'] }}"><div><strong>{{ $result['type'] }} · {{ $result['title'] }}</strong><small>{{ $result['meta'] ?: 'No status' }}</small></div><span aria-hidden="true">↗</span></a>@endforeach</div>
    @endif
@endsection
