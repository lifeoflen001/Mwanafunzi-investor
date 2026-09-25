@extends('layouts.admin')
@section('title', 'Pages')
@section('portal-heading', 'Pages')
@section('content')
<div class="admin-heading"><div><p class="eyebrow">{{ $pageIndexEyebrow ?? 'Website / Pages' }}</p><h1>{{ $pageIndexTitle ?? 'Pages' }}</h1><p>{{ $pageIndexDescription ?? 'Manage page identity, hero content, visibility and SEO without editing templates.' }}</p></div><a class="button button-dark" href="{{ route('admin.pages.create', !empty($pageIndexCreateType) ? ['page_type' => $pageIndexCreateType] : []) }}">{{ $pageIndexCreateLabel ?? 'Add page' }} <span aria-hidden="true">↗</span></a></div>
<div class="admin-table"><div class="admin-table-head"><span>Page</span><span>Type</span><span>Status</span><span>Sections</span><span>Updated</span><span></span></div>@forelse($pages as $page)<div class="admin-table-row"><span><strong>{{ $page->name }}</strong><small>{{ $page->slug ?: 'Homepage registry key' }}</small></span><span>{{ $page->page_type }}</span><span><em class="status-badge">{{ $page->status }}</em></span><span>{{ $page->sections_count }}</span><span>{{ $page->updated_at?->format('M j, Y') }}</span><span class="admin-actions"><a class="text-link" href="{{ route('admin.pages.edit', $page) }}">Edit <span aria-hidden="true">↗</span></a></span></div>@empty<div class="empty-state compact"><p>No pages registered.</p></div>@endforelse</div>
{{ $pages->links() }}
@endsection
