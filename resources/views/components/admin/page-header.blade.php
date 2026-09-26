@props([
    'eyebrow' => null,
    'title',
    'description' => null,
    'actionUrl' => null,
    'actionLabel' => null,
    'actionClass' => 'button button-primary',
])
<header class="admin-page-header">
    <div class="admin-page-header-copy">
        @if($eyebrow)<p class="admin-eyebrow">{{ $eyebrow }}</p>@endif
        <h1>{{ $title }}</h1>
        @if($description)<p class="admin-page-description">{{ $description }}</p>@endif
    </div>
    @if(($actionUrl && $actionLabel) || isset($actions))
        <div class="admin-page-header-actions">
            @if($actionUrl && $actionLabel)<a class="{{ $actionClass }}" href="{{ $actionUrl }}">{{ $actionLabel }} <span aria-hidden="true">+</span></a>@endif
            @isset($actions){{ $actions }}@endisset
        </div>
    @endif
</header>
