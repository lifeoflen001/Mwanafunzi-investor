@props(['item'])
<a class="{{ $item->route_name && request()->routeIs($item->route_name) ? 'active' : '' }}" href="{{ $item->href() }}" target="{{ $item->target }}" @if($item->route_name && request()->routeIs($item->route_name)) aria-current="page" @endif>{{ $item->label }}@if($item->children->isNotEmpty()) <span aria-hidden="true">+</span>@endif</a>
@if($item->children->isNotEmpty())
    <div class="nav-children">@foreach($item->children->where('is_visible', true)->sortBy('sort_order') as $child)<x-navigation-links :item="$child" />@endforeach</div>
@endif
