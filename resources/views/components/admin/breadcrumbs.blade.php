@props(['items' => []])
<nav class="admin-breadcrumbs" aria-label="Breadcrumb">
    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
    @foreach($items as $item)
        <span aria-hidden="true">/</span>
        @if(!empty($item['url']))<a href="{{ $item['url'] }}">{{ $item['label'] }}</a>@else<span>{{ $item['label'] }}</span>@endif
    @endforeach
</nav>
