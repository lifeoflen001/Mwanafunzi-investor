@extends('layouts.admin')
@section('title', 'My profile')
@section('portal-heading', 'My profile')
@section('content')
<x-admin.page-header eyebrow="Admin desk / Account" title="My profile" description="Manage your identity, contact information and account security." />
<div class="admin-profile-layout">
    <aside class="admin-card admin-profile-summary">
        <div class="admin-profile-avatar-wrap">@if($user->avatar_url)<img class="admin-profile-avatar" src="{{ $user->avatar_url }}" alt="Profile photo for {{ $user->name }}">@else<span class="admin-profile-avatar admin-profile-avatar-fallback" aria-hidden="true">{{ $user->initials() }}</span>@endif</div>
        <h2>{{ $user->name }}</h2><p>{{ $user->job_title ?: 'Administrator' }}</p><small>{{ $user->email }}</small>
        <div class="admin-profile-avatar-actions"><form method="post" action="{{ route('admin.profile.avatar.update') }}" enctype="multipart/form-data" class="admin-avatar-upload" data-avatar-form>@csrf<label class="button button-secondary button-small" for="admin-avatar">Choose photo</label><input id="admin-avatar" type="file" name="avatar" accept="image/jpeg,image/png,image/webp,image/avif" hidden required data-avatar-input><button class="button button-ghost button-small" type="submit">Upload cropped photo</button><small data-avatar-crop-status>Choose a photo to crop before uploading.</small></form>@if($user->avatar_path)<form method="post" action="{{ route('admin.profile.avatar.remove') }}">@csrf @method('delete')<button class="button button-ghost button-small" type="submit">Remove photo</button></form>@endif</div>
        <dl class="admin-profile-account"><div><dt>Role</dt><dd>Administrator</dd></div><div><dt>Status</dt><dd><x-admin.status-badge status="active" /></dd></div><div><dt>Member since</dt><dd>{{ $user->created_at?->format('M j, Y') ?: 'Not recorded' }}</dd></div><div><dt>Last login</dt><dd>{{ $user->last_login_at?->diffForHumans() ?: 'Not recorded' }}</dd></div></dl>
    </aside>
    <div class="admin-profile-sections">
        <x-admin.card title="Personal information" subtitle="This identity appears in the admin desk and audit history.">
            <form class="admin-form" method="post" action="{{ route('admin.profile.update') }}" data-unsaved-warning>@csrf @method('put')<div class="form-row"><label>Display name<input name="name" value="{{ old('name', $user->name) }}" autocomplete="name" required></label><label>Phone<input name="phone" value="{{ old('phone', $user->phone) }}" autocomplete="tel"></label></div><div class="form-row"><label>Job title<input name="job_title" value="{{ old('job_title', $user->job_title) }}" placeholder="e.g. Founder and administrator"></label><label>Department / role<input name="department" value="{{ old('department', $user->department) }}" placeholder="e.g. Platform operations"></label></div><label>Email <span>(managed by account security)</span><input value="{{ $user->email }}" disabled autocomplete="email"></label><label>Bio<textarea name="bio" rows="4" maxlength="1000">{{ old('bio', $user->bio) }}</textarea></label><div class="form-actions"><button class="button button-primary" type="submit">Save profile <span aria-hidden="true">↗</span></button></div></form>
        </x-admin.card>
        <x-admin.card title="Password and security" subtitle="Use a unique password of at least eight characters. Your password is never displayed back to you.">
            <form class="admin-form" method="post" action="{{ route('admin.profile.password') }}">@csrf @method('put')<label>Current password<div class="admin-password-field"><input type="password" name="current_password" autocomplete="current-password" required><button type="button" class="admin-password-toggle" aria-label="Show current password" data-password-toggle>Show</button></div></label><div class="form-row"><label>New password<div class="admin-password-field"><input type="password" name="password" autocomplete="new-password" minlength="8" required><button type="button" class="admin-password-toggle" aria-label="Show new password" data-password-toggle>Show</button></div><small>Use at least eight characters and avoid reused passwords.</small></label><label>Confirm new password<div class="admin-password-field"><input type="password" name="password_confirmation" autocomplete="new-password" minlength="8" required><button type="button" class="admin-password-toggle" aria-label="Show password confirmation" data-password-toggle>Show</button></div></label></div><div class="form-actions"><button class="button button-secondary" type="submit">Update password <span aria-hidden="true">↗</span></button></div></form>
        </x-admin.card>
    </div>
</div>
<div class="admin-avatar-crop-modal" data-avatar-crop-modal hidden role="dialog" aria-modal="true" aria-labelledby="avatar-crop-title">
    <div class="admin-avatar-crop-card">
        <div class="admin-avatar-crop-header"><div><p class="admin-eyebrow">Profile photo</p><h2 id="avatar-crop-title">Crop your picture</h2></div><button class="admin-avatar-crop-close" type="button" aria-label="Close crop dialog" data-avatar-crop-cancel>×</button></div>
        <p class="admin-avatar-crop-help">Drag the image to position it, then adjust the zoom before using the crop.</p>
        <div class="admin-avatar-crop-canvas-wrap"><canvas width="360" height="360" data-avatar-crop-canvas></canvas></div>
        <label class="admin-avatar-zoom">Zoom<input type="range" min="1" max="3" step="0.01" value="1" data-avatar-crop-zoom></label>
        <div class="admin-confirm-actions"><button class="button button-secondary" type="button" data-avatar-crop-cancel>Cancel</button><button class="button button-primary" type="button" data-avatar-crop-apply>Use cropped photo</button></div>
    </div>
</div>
@endsection
