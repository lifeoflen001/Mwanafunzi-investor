@props(['status'])
@php
    $key = strtolower(str_replace([' ', '-'], '_', (string) $status));
    $tone = match (true) {
        in_array($key, ['published', 'paid', 'completed', 'active', 'resolved', 'verified', 'deal_won'], true) => 'success',
        in_array($key, ['pending', 'pending_payment', 'processing', 'in_progress', 'waitlist', 'coming_soon'], true) => 'warning',
        in_array($key, ['failed', 'spam', 'cancelled', 'refunded', 'archived', 'suspended'], true) => 'danger',
        default => 'neutral',
    };
@endphp
<span {{ $attributes->merge(['class' => 'admin-badge admin-badge-'.$tone]) }}>{{ str_replace('_', ' ', ucfirst($key)) }}</span>
