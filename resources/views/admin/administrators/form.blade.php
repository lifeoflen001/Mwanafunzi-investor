@extends('layouts.admin')
@section('title', $isCreate ? 'New administrator' : 'Edit administrator')
@section('portal-heading', 'Administrators')
@section('content')
<x-admin.page-header eyebrow="Access control" title="{{ $isCreate ? 'New administrator' : 'Edit administrator' }}" description="Administrator access is protected by server-side authorization and audit logging." />
<a class="admin-back-link" href="{{ route('admin.administrators') }}">← Back to administrators</a>
<div class="admin-form-card"><form class="admin-form" method="post" action="{{ $isCreate ? route('admin.administrators.store') : route('admin.administrators.update', $administrator) }}" data-unsaved-warning>
    @csrf @if(!$isCreate) @method('put') @endif
    <label>Name<input name="name" value="{{ old('name', $administrator->name) }}" autocomplete="name" required></label>
    <label>Email<input type="email" name="email" value="{{ old('email', $administrator->email) }}" autocomplete="email" required></label>
    <div class="form-row"><label>Password<input type="password" name="password" autocomplete="new-password" {{ $isCreate ? 'required' : '' }} minlength="8">@if(!$isCreate)<span>Leave blank to keep the current password.</span>@endif</label><label>Confirm password<input type="password" name="password_confirmation" autocomplete="new-password" {{ $isCreate ? 'required' : '' }} minlength="8"></label></div>
    <label>Status<select name="status" required><option value="active" @selected(old('status', $administrator->status ?: 'active') === 'active')>Active — can sign in</option><option value="suspended" @selected(old('status', $administrator->status) === 'suspended')>Suspended — cannot sign in</option></select></label>
    <div class="form-section"><p class="field-help">Every administrator has the same protected CMS access. The system will not allow the final active administrator to be suspended.</p></div>
    <div class="form-actions"><button class="button button-dark" type="submit">{{ $isCreate ? 'Create administrator' : 'Save administrator' }} <span aria-hidden="true">↗</span></button></div>
</form></div>
@endsection
