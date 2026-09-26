@extends('layouts.admin')
@section('title', 'Social links')
@section('portal-heading', 'Social links')
@section('content')
<x-admin.page-header eyebrow="CMS / Settings" title="Social links" description="Keep outbound brand links centralised and optional. Visible links appear in the public footer." />
<form class="admin-form admin-inline-form" method="post" action="{{ route('admin.social-links.store') }}">
    @csrf
    <div class="form-row"><label>Label<input name="label" placeholder="Instagram" required></label><label>URL<input type="url" name="url" placeholder="https://" required></label></div>
    <div class="form-row"><label>Sort order<input type="number" name="sort_order" value="0" min="0" required></label><label class="consent"><input type="checkbox" name="is_visible" value="1" checked> <span>Visible publicly</span></label></div>
    <button class="button button-dark" type="submit">Add link <span aria-hidden="true">+</span></button>
</form>
<div class="admin-table social-link-table">
    <div class="admin-table-head"><span>Link</span><span>Destination</span><span>Order</span><span>Actions</span></div>
    @forelse($links as $link)
        <div class="admin-table-row">
            <form class="admin-inline-form" method="post" action="{{ route('admin.social-links.update', $link) }}">
                @csrf @method('put')
                <input name="label" value="{{ $link->label }}" required>
                <input type="url" name="url" value="{{ $link->url }}" required>
                <input type="number" min="0" name="sort_order" value="{{ $link->sort_order }}" required>
                <label class="checkbox-field"><input type="checkbox" name="is_visible" value="1" @checked($link->is_visible)> Visible</label>
                <button class="text-button" type="submit">Save</button>
            </form>
            <form method="post" action="{{ route('admin.social-links.destroy', $link) }}">
                @csrf @method('delete')
                <button class="text-button danger" type="submit">Remove</button>
            </form>
        </div>
    @empty
        <div class="empty-state"><p>No social links yet.</p></div>
    @endforelse
</div>
@endsection
