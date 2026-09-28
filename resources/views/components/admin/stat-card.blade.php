@props([
    'label',
    'value',
    'description' => null,
    'tone' => 'orange',
])
<article {{ $attributes->merge(['class' => 'admin-stat-card admin-stat-card-'.$tone]) }}>
    <span class="admin-stat-card-label">{{ $label }}</span>
    <strong class="admin-stat-card-value">{{ $value }}</strong>
    @if($description)<small class="admin-stat-card-description">{{ $description }}</small>@endif
</article>
