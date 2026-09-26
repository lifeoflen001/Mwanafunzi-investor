@extends('layouts.admin')
@section('title', 'Learning topics')
@section('portal-heading', 'Learning topics')
@section('content')
<x-admin.page-header eyebrow="Content / Learn" title="Learning topics" description="Organise the learning paths that guide students through the platform." action-url="{{ route('admin.topics.create') }}" action-label="New topic" />
<x-admin.filter-toolbar method="get" action="{{ route('admin.topics') }}">
    <label class="admin-search-field"><span class="sr-only">Search topics</span><input type="search" name="q" value="{{ request('q') }}" placeholder="Search topics"></label>
    <button class="button button-secondary button-small" type="submit">Search</button>
    @if(request('q'))<a class="button button-ghost button-small" href="{{ route('admin.topics') }}">Reset</a>@endif
</x-admin.filter-toolbar>
<div class="admin-table"><div class="admin-table-head"><span>Topic</span><span>Level</span><span>Study time</span><span>Visibility</span><span>Actions</span></div>
    @forelse($topics as $topic)
        <div class="admin-table-row"><div><strong>{{ $topic->title }}</strong><small>{{ $topic->slug }}</small></div><span>{{ $topic->skill_level ?: 'Not set' }}</span><span>{{ $topic->study_time ?: 'Self-paced' }}</span><x-admin.status-badge :status="$topic->is_published ? 'published' : 'draft'" /><div class="admin-actions"><a href="{{ route('admin.topics.edit', $topic) }}">Edit</a></div></div>
    @empty
        <x-admin.empty-state title="No learning topics found." description="Create a topic to structure the learning path." action-url="{{ route('admin.topics.create') }}" action-label="New topic" />
    @endforelse
</div>
{{ $topics->withQueryString()->links() }}
@endsection
