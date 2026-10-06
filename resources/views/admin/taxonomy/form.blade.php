@extends('layouts.admin')
@section('title', $title)
@section('portal-heading', 'Journal')
@section('content')
<x-admin.page-header eyebrow="CMS / Journal" title="{{ $title }}" description="Keep the editorial taxonomy clear for readers and authors." />
<a class="admin-back-link" href="{{ route('admin.'.$kind) }}">← Back to {{ $kind }}</a>
<div class="admin-form-card"><form class="admin-form" method="post" action="{{ $action }}">@csrf @if($item->exists) @method('put') @endif
    <label>Name<input name="name" value="{{ old('name', $item->name) }}" required maxlength="120"></label>
    <label>Slug<input name="slug" value="{{ old('slug', $item->slug) }}" placeholder="Generated from the name"><small>Use a stable lowercase URL slug. Leave blank to generate it.</small></label>
    <label>Description<textarea name="description" rows="4" maxlength="1000">{{ old('description', $item->description) }}</textarea></label>
    @if($kind === 'categories')
        <div class="form-row"><label>SEO title<input name="seo_title" value="{{ old('seo_title', $item->seo_title) }}" maxlength="190"></label><label>Sort order<input type="number" name="sort_order" min="0" value="{{ old('sort_order', $item->sort_order ?: 0) }}"></label></div>
        <label>SEO description<textarea name="seo_description" rows="3" maxlength="300">{{ old('seo_description', $item->seo_description) }}</textarea></label>
        <label class="consent"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $item->exists ? $item->is_active : true))> <span>Available for new articles and public discovery</span></label>
    @endif
    <div class="form-actions"><button class="button button-dark" type="submit">Save {{ $kind === 'categories' ? 'category' : 'tag' }} <span aria-hidden="true">↗</span></button><a class="button button-light" href="{{ route('admin.'.$kind) }}">Cancel</a></div>
</form></div>
@endsection
