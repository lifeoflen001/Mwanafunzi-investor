@props(['inquiry'])

@php
    $serviceTitle = $inquiry->service?->title ?: 'Creative Studio enquiry';
    $detailUrl = route('account.studio.inquiries.show', $inquiry);
@endphp

<article class="student-service-card">
    <div class="student-service-card-head">
        <span class="student-list-icon student-list-icon-copper" aria-hidden="true">◫</span>
        <x-portal-status :status="$inquiry->statusLabel()" />
    </div>
    <p class="student-eyebrow">Creative Studio</p>
    <h3><a href="{{ $detailUrl }}">{{ $serviceTitle }}</a></h3>
    <p class="student-service-card-meta">{{ $inquiry->reference }} · Submitted {{ $inquiry->created_at?->format('M j, Y') }}</p>
    <a class="student-text-link" href="{{ $detailUrl }}">View enquiry <span aria-hidden="true">↗</span></a>
</article>
