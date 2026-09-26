@extends('layouts.admin')
@section('title', 'Courses')
@section('portal-heading', 'Courses')
@section('content')
<x-admin.page-header eyebrow="Content / Learning" title="Courses" description="Manage learning products, curriculum and publishing status." action-url="{{ route('admin.courses.create') }}" action-label="New course" />
<x-admin.filter-toolbar method="get" action="{{ route('admin.courses') }}">
    <label class="admin-search-field"><span class="sr-only">Search courses</span><input type="search" name="q" value="{{ request('q') }}" placeholder="Search courses"></label>
    <label><span class="sr-only">Course status</span><select name="status"><option value="">All statuses</option><option value="published" @selected(request('status') === 'published')>Published</option><option value="draft" @selected(request('status') === 'draft')>Draft</option><option value="coming_soon" @selected(request('status') === 'coming_soon')>Coming soon</option></select></label>
    <button class="button button-secondary button-small" type="submit">Filter</button>
    @if(request('q') || request('status'))<a class="button button-ghost button-small" href="{{ route('admin.courses') }}">Reset</a>@endif
</x-admin.filter-toolbar>
<div class="admin-table admin-course-table"><div class="admin-table-head"><span>Course</span><span>Level</span><span>Status</span><span>Updated</span><span>Actions</span></div>
    @forelse($courses as $course)
        <div class="admin-table-row"><div><strong>{{ $course->title }}</strong><small>{{ $course->slug }} · {{ $course->modules_count ?? $course->modules?->count() ?? 0 }} modules</small></div><span>{{ $course->level ?: 'Not set' }}</span><x-admin.status-badge :status="$course->trashed() ? 'archived' : $course->status" /><span>{{ $course->updated_at?->format('M j, Y') }}</span><div class="admin-actions"><x-admin.action-menu><a href="{{ route('admin.courses.edit', $course) }}">Edit</a>@if($course->trashed())<form method="post" action="{{ route('admin.courses.restore', $course->id) }}">@csrf<button type="submit">Restore</button></form>@else<form method="post" action="{{ route('admin.courses.destroy', $course) }}">@csrf @method('delete')<button type="submit">Archive</button></form>@endif</x-admin.action-menu></div></div>
    @empty
        <x-admin.empty-state title="No courses found." description="Create a course to start building the learning catalogue." action-url="{{ route('admin.courses.create') }}" action-label="New course" />
    @endforelse
</div>
{{ $courses->withQueryString()->links() }}
@endsection
