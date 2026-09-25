@extends('layouts.admin')
@section('title', 'Search')
@section('portal-heading', 'Search')
@section('content')
    <div class="admin-heading"><div><p class="eyebrow">Admin desk / Search</p><h1>Search the desk</h1><p>Search real pages, courses, products, journal entries, orders and enquiries.</p></div></div>
    @if($term === '')
        <div class="empty-state"><p>Enter a search term to find content and operational records.</p></div>
    @elseif($results->isEmpty())
        <div class="empty-state"><p>No records matched “{{ $term }}”.</p></div>
    @else
        <div class="admin-subresource-list">@foreach($results as $result)<a class="admin-subresource-header" href="{{ $result['url'] }}"><div><strong>{{ $result['type'] }} · {{ $result['title'] }}</strong><small>{{ $result['meta'] ?: 'No status' }}</small></div><span aria-hidden="true">↗</span></a>@endforeach</div>
    @endif
@endsection
