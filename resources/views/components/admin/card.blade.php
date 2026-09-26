@props(['title' => null, 'subtitle' => null, 'action' => null, 'class' => ''])
<section {{ $attributes->merge(['class' => 'admin-card '.$class]) }}>
    @if($title || $subtitle || $action)
        <header class="admin-card-header">
            <div>@if($title)<h2>{{ $title }}</h2>@endif @if($subtitle)<p>{{ $subtitle }}</p>@endif</div>
            @if($action)<div class="admin-card-action">{{ $action }}</div>@endif
        </header>
    @endif
    {{ $slot }}
</section>
