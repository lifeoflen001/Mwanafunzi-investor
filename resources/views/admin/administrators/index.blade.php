@extends('layouts.admin')
@section('title', 'Administrators')
@section('portal-heading', 'Administrators')
@section('content')
<x-admin.page-header eyebrow="Access control" title="Administrators" description="Control who can access the CMS and keep the last-login trail visible." action-url="{{ route('admin.administrators.create') }}" action-label="New administrator" />
<x-admin.filter-toolbar method="get" action="{{ route('admin.administrators') }}"><input name="q" value="{{ $term }}" placeholder="Search name or email" aria-label="Search administrators"><button class="button button-secondary button-small" type="submit">Search</button>@if($term)<a class="admin-back-link" href="{{ route('admin.administrators') }}">Clear</a>@endif</x-admin.filter-toolbar>
<x-admin.card class="admin-table-card"><div class="admin-table"><div class="admin-table-head"><span>Administrator</span><span>Status</span><span>Last login</span><span></span></div>@forelse($administrators as $administrator)<div class="admin-table-row admin-user-row"><div><strong>{{ $administrator->name }}</strong><small>{{ $administrator->email }}</small></div><x-admin.status-badge :status="$administrator->status ?: 'active'" /><span>{{ $administrator->last_login_at?->format('M j, Y H:i') ?: 'Never' }}</span><x-admin.action-menu><a href="{{ route('admin.administrators.edit', $administrator) }}">Edit administrator</a></x-admin.action-menu></div>@empty<x-admin.empty-state title="No administrators found." />@endforelse</div></x-admin.card>
{{ $administrators->links() }}
@endsection
