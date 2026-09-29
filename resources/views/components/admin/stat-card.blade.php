@props([
    'label',
    'value',
    'description' => null,
    'tone' => 'orange',
    'url' => null,
])
@php($tag = $url ? 'a' : 'article')
<{{ $tag }} {{ $attributes->merge(['class' => 'admin-stat-card admin-stat-card-'.$tone.($url ? ' admin-stat-card-link' : '')]) }} @if($url)href="{{ $url }}"@endif>
    <span class="admin-stat-card-label">{{ $label }}</span>
    <strong class="admin-stat-card-value">{{ $value }}</strong>
    @if($description)<small class="admin-stat-card-description">{{ $description }}</small>@endif
</{{ $tag }}>
