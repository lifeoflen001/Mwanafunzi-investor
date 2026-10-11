@props([
    'variant' => 'text',
    'width' => null,
    'height' => null,
])

@php
    $inlineStyle = collect([
        $width ? "--skeleton-width: {$width}" : null,
        $height ? "--skeleton-height: {$height}" : null,
    ])->filter()->implode('; ');
@endphp

<span
    {{ $attributes->class(['ui-skeleton', "ui-skeleton-{$variant}"]) }}
    @if($inlineStyle) style="{{ $inlineStyle }}" @endif
    aria-hidden="true"
></span>
