@props(['item'])
@php($isActive = $item->publicRouteName() && request()->routeIs($item->publicRouteName()))
<a class="{{ $isActive ? 'active' : '' }}" href="{{ $item->href() }}" target="{{ $item->target }}" @if($isActive) aria-current="page" @endif>{{ $item->label }}@if($item->children->isNotEmpty()) <span aria-hidden="true">+</span>@endif</a>
@if($item->children->isNotEmpty())
    <div class="nav-children">@foreach($item->children->where('is_visible', true)->sortBy('sort_order') as $child)<x-navigation-links :item="$child" />@endforeach</div>
@endif
