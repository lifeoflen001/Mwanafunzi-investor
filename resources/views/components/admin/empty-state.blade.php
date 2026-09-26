@props(['title' => 'Nothing here yet.', 'description' => null, 'actionUrl' => null, 'actionLabel' => null])
<div class="admin-empty-state">
    <span class="admin-empty-icon" aria-hidden="true">○</span>
    <div><h3>{{ $title }}</h3>@if($description)<p>{{ $description }}</p>@endif</div>
    @if($actionUrl && $actionLabel)<a class="button button-secondary button-small" href="{{ $actionUrl }}">{{ $actionLabel }} <span aria-hidden="true">+</span></a>@endif
</div>
