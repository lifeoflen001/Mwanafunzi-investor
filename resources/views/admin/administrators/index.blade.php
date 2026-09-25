@extends('layouts.admin')
@section('title', 'Administrators')
@section('portal-heading', 'Administrators')
@section('content')
<div class="admin-heading"><div><p class="eyebrow">Access control</p><h1>Administrators</h1><p>Control who can access the CMS and keep the last-login trail visible.</p></div><a class="button button-dark" href="{{ route('admin.administrators.create') }}">New administrator <span aria-hidden="true">+</span></a></div>
<form class="admin-filter-row" method="get" action="{{ route('admin.administrators') }}"><label class="sr-only" for="administrator-search">Search administrators</label><input id="administrator-search" name="q" value="{{ $term }}" placeholder="Search name or email"><button class="button button-light" type="submit">Search</button>@if($term)<a class="back-link" href="{{ route('admin.administrators') }}">Clear</a>@endif</form>
<div class="admin-table"><div class="admin-table-head"><span>Administrator</span><span>Status</span><span>Last login</span><span>Actions</span></div>@forelse($administrators as $administrator)<div class="admin-table-row admin-user-row"><div><strong>{{ $administrator->name }}</strong><small>{{ $administrator->email }}</small></div><span class="status-badge">{{ $administrator->status ?: 'active' }}</span><span>{{ $administrator->last_login_at?->format('M j, Y H:i') ?: 'Never' }}</span><div class="admin-actions"><a href="{{ route('admin.administrators.edit', $administrator) }}">Edit</a></div></div>@empty<div class="empty-state"><p>No administrators found.</p></div>@endforelse</div>
{{ $administrators->links() }}
@endsection
