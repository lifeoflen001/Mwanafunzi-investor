@extends('layouts.admin')
@section('title', 'Business modules')
@section('portal-heading', 'Business modules')
@section('content')
<div class="admin-heading"><div><p class="eyebrow">Platform / Modules</p><h1>Business modules</h1><p>Manage the public module identity and availability. Public routes and application behavior remain controlled by code.</p></div></div>

<div class="admin-subresource-list admin-business-unit-list">
    @forelse($units as $unit)
        @php($page = $pages->get($unit->slug))
        <section class="admin-card admin-business-unit-card">
            <div class="admin-subresource-header"><div><strong>{{ $unit->name }}</strong><small>{{ $unit->slug }} · {{ $unit->route_name ?: 'No public route' }}</small></div><div class="admin-actions">@if($unit->is_active)<em class="status-badge status-badge-success">Active</em>@else<em class="status-badge">Hidden</em>@endif @if($page)<a class="text-link" href="{{ route('admin.pages.edit', $page) }}">Edit page <span aria-hidden="true">↗</span></a>@endif</div></div>
            <form class="admin-form admin-inline-form" method="post" action="{{ route('admin.business-units.update', $unit) }}">
                @csrf @method('put')
                <div class="form-row"><label>Module name<input name="name" value="{{ old('name', $unit->name) }}" required></label><label>Slug <span>(code-controlled)</span><input value="{{ $unit->slug }}" readonly></label></div>
                <label>Tagline<input name="tagline" value="{{ old('tagline', $unit->tagline) }}"></label>
                <label>Description<textarea name="description" rows="3">{{ old('description', $unit->description) }}</textarea></label>
                <div class="form-row"><label>Accent colour<span class="settings-token-control"><input type="color" name="accent_color" value="{{ old('accent_color', $unit->accent_color ?: '#c56c38') }}"></span></label><label>Public route <span>(code-controlled)</span><input value="{{ $unit->route_name ?: 'Not registered' }}" readonly></label></div>
                <div class="form-row"><label>Display order<input type="number" name="sort_order" min="0" value="{{ old('sort_order', $unit->sort_order) }}" required></label><label class="checkbox-field"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $unit->is_active))> Visible and accepting enquiries</label></div>
                <button class="button button-dark" type="submit">Save module <span aria-hidden="true">↗</span></button>
            </form>
        </section>
    @empty
        <div class="empty-state"><p>No business modules are registered.</p></div>
    @endforelse
</div>
@endsection
