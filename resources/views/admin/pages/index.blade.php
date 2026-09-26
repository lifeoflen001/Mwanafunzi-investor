@extends('layouts.admin')
@section('title', 'Pages')
@section('portal-heading', 'Pages')
@section('content')
<x-admin.page-header eyebrow="{{ $pageIndexEyebrow ?? 'Website / Pages' }}" title="{{ $pageIndexTitle ?? 'Pages' }}" description="{{ $pageIndexDescription ?? 'Manage page identity, hero content, visibility and SEO without editing templates.' }}" action-url="{{ route('admin.pages.create', !empty($pageIndexCreateType) ? ['page_type' => $pageIndexCreateType] : []) }}" action-label="{{ $pageIndexCreateLabel ?? 'Add page' }}" />
<x-admin.card class="admin-table-card"><div class="admin-table"><div class="admin-table-head"><span>Page</span><span>Type</span><span>Status</span><span>Sections</span><span>Updated</span><span></span></div>@forelse($pages as $page)<div class="admin-table-row"><span><strong>{{ $page->name }}</strong><small>{{ $page->slug ?: 'Homepage registry key' }}</small></span><span>{{ $page->page_type }}</span><x-admin.status-badge :status="$page->status" /><span>{{ $page->sections_count }}</span><span>{{ $page->updated_at?->format('M j, Y') }}</span><x-admin.action-menu><a href="{{ route('admin.pages.edit', $page) }}">Edit page</a></x-admin.action-menu></div>@empty<x-admin.empty-state title="No pages registered." />@endforelse</div></x-admin.card>
{{ $pages->links() }}
@endsection
