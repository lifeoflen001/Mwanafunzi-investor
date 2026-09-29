@extends('layouts.admin')
@section('title', 'Search')
@section('portal-heading', 'Search')
@section('content')
    <x-admin.page-header eyebrow="Admin desk / Search" title="Search the desk" description="Search real pages, courses, products, services, projects, testimonials, customers, orders, media and enquiries." />
    <form class="admin-search-results-form" method="get" action="{{ route('admin.search') }}" role="search">
        <label class="sr-only" for="admin-results-search">Search the desk</label>
        <span aria-hidden="true">⌕</span>
        <input id="admin-results-search" name="q" value="{{ $term }}" placeholder="Search admin records..." autofocus>
        <button class="button button-primary button-small" type="submit">Search</button>
        @if($term)<a class="button button-ghost button-small" href="{{ route('admin.search') }}">Clear</a>@endif
    </form>
    @if($term !== '')
        <nav class="admin-search-tabs" aria-label="Search result types">
            @foreach(['all' => 'All', 'Page' => 'Pages', 'Product' => 'Products', 'Service' => 'Services', 'Project' => 'Projects', 'Testimonial' => 'Testimonials', 'Customer' => 'Customers', 'Order' => 'Orders', 'Enquiry' => 'Enquiries', 'Course' => 'Courses', 'Article' => 'Journal', 'Topic' => 'Topics', 'Media' => 'Media'] as $key => $label)
                <a class="{{ $type === $key ? 'is-active' : '' }}" href="{{ route('admin.search', ['q' => $term, 'type' => $key]) }}">{{ $label }}</a>
            @endforeach
        </nav>
        <div class="admin-search-summary"><strong>{{ $results->count() }}</strong> {{ $results->count() === 1 ? 'result' : 'results' }} for “{{ $term }}”</div>
        @if($results->isEmpty())
            <x-admin.empty-state title="No results found" description="Try another keyword or check the spelling." />
        @else
            <div class="admin-search-results">@foreach($results as $result)<a class="admin-search-result" href="{{ $result['url'] }}"><span class="admin-search-result-icon" aria-hidden="true">{{ match($result['type']) { 'Page' => '▣', 'Product' => '◈', 'Service' => '◈', 'Project' => '▧', 'Testimonial' => '“', 'Customer' => '◎', 'Order' => '#', 'Enquiry' => '✉', 'Media' => '▧', default => '◌' } }}</span><span><strong>{{ $result['title'] }}</strong><small>{{ $result['type'] }} · {{ $result['meta'] ?: 'No status' }}</small></span><span class="admin-search-result-arrow" aria-hidden="true">↗</span></a>@endforeach</div>
        @endif
    @else
        <x-admin.empty-state title="Search the desk" description="Enter a keyword to find content and operational records." />
    @endif
@endsection
