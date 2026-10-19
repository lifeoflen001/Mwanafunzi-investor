@extends('layouts.admin')

@section('title', 'Studio enquiry · '.$inquiry->reference)
@section('portal-heading', 'Studio enquiries')

@section('content')
<x-admin.page-header eyebrow="Creative Studio / Enquiry" title="{{ $inquiry->reference }}" description="{{ $inquiry->name }} · Received {{ $inquiry->created_at?->format('M j, Y H:i') }}" />
<a class="admin-back-link" href="{{ route('admin.studio.inquiries') }}">← Back to studio enquiries</a>
<div class="admin-grid-two">
    <section class="admin-card admin-subresource"><header class="admin-card-header"><div><p class="admin-eyebrow">Brief</p><h2>{{ $inquiry->service?->title ?: 'Creative Studio enquiry' }}</h2></div><x-admin.status-badge :status="$inquiry->status" /></header>
        <dl class="admin-definition-list"><div><dt>Project</dt><dd>{{ $inquiry->project?->title ?: 'Not specified' }}</dd></div><div><dt>Preferred date</dt><dd>{{ $inquiry->preferred_date?->format('F j, Y') ?: 'Not provided' }}{{ $inquiry->preferred_time ? ' · '.$inquiry->preferred_time : '' }}</dd></div><div><dt>Location</dt><dd>{{ $inquiry->location ?: 'Not provided' }}</dd></div><div><dt>Duration</dt><dd>{{ $inquiry->duration ?: 'Not provided' }}</dd></div><div><dt>Budget range</dt><dd>{{ $inquiry->budget_range ?: 'Not provided' }}</dd></div></dl>
        <h3>Project description</h3><p class="body-copy">{!! nl2br(e($inquiry->description)) !!}</p>
        @if($inquiry->special_requirements)<h3>Special requirements</h3><p class="body-copy">{!! nl2br(e($inquiry->special_requirements)) !!}</p>@endif
        @if($inquiry->reference_files)<h3>Private reference files</h3><ul class="admin-simple-list">@foreach($inquiry->reference_files as $index => $file)<li><a href="{{ route('admin.studio.inquiries.references.download', [$inquiry, $index]) }}">{{ $file['name'] ?? 'Reference file' }}</a><small>{{ isset($file['size']) ? number_format($file['size'] / 1048576, 2).' MB' : '' }}</small></li>@endforeach</ul>@endif
    </section>
    <section class="admin-card admin-subresource"><header class="admin-card-header"><div><p class="admin-eyebrow">Client</p><h2>Contact details</h2></div></header>
        <dl class="admin-definition-list"><div><dt>Name</dt><dd>{{ $inquiry->name }}</dd></div><div><dt>Email</dt><dd><a href="mailto:{{ $inquiry->email }}">{{ $inquiry->email }}</a></dd></div><div><dt>Phone</dt><dd>{{ $inquiry->phone ?: 'Not provided' }}</dd></div><div><dt>Portal account</dt><dd>{{ $inquiry->user ? 'Linked client account' : 'Guest enquiry' }}</dd></div><div><dt>Received</dt><dd>{{ $inquiry->created_at?->format('F j, Y H:i') }}</dd></div></dl>
        <form class="admin-form" method="post" action="{{ route('admin.studio.inquiries.update', $inquiry) }}">@csrf @method('patch')<label for="studio-inquiry-status-edit">Workflow status<select id="studio-inquiry-status-edit" name="status">@foreach($statuses as $status)<option value="{{ $status }}" @selected($inquiry->status === $status)>{{ str_replace('_', ' ', str($status)->title()) }}</option>@endforeach</select></label><label for="studio-inquiry-notes">Internal notes<textarea id="studio-inquiry-notes" name="admin_notes" rows="7">{{ old('admin_notes', $inquiry->admin_notes) }}</textarea></label><button class="button button-primary" type="submit">Save enquiry</button></form>
    </section>
</div>
@endsection
