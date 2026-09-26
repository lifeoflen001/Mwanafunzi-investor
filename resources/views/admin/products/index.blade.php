@extends('layouts.admin')
@section('title', 'Products')
@section('portal-heading', 'Products')
@section('content')
<x-admin.page-header eyebrow="Commerce / Tools" title="Products" description="Manage digital tools, availability, media and protected downloads." action-url="{{ route('admin.products.create') }}" action-label="New product" />
<x-admin.filter-toolbar method="get" action="{{ route('admin.products') }}">
    <label class="admin-search-field"><span class="sr-only">Search products</span><input type="search" name="q" value="{{ request('q') }}" placeholder="Search products"></label>
    <label><span class="sr-only">Product availability</span><select name="availability"><option value="">All availability</option><option value="available" @selected(request('availability') === 'available')>Available</option><option value="waitlist" @selected(request('availability') === 'waitlist')>Waitlist</option><option value="coming_soon" @selected(request('availability') === 'coming_soon')>Coming soon</option></select></label>
    <button class="button button-secondary button-small" type="submit">Filter</button>
    @if(request('q') || request('availability'))<a class="button button-ghost button-small" href="{{ route('admin.products') }}">Reset</a>@endif
</x-admin.filter-toolbar>
<div class="admin-table admin-product-table"><div class="admin-table-head"><span>Product</span><span>Type</span><span>Availability</span><span>Version</span><span>Actions</span></div>
    @forelse($products as $product)
        <div class="admin-table-row"><div><strong>{{ $product->name }}</strong><small>{{ $product->slug }}</small></div><span>{{ $product->product_type ?: 'Digital product' }}</span><x-admin.status-badge :status="$product->trashed() ? 'archived' : $product->availability" /><span>{{ $product->version ?: 'Not set' }}</span><div class="admin-actions"><x-admin.action-menu><a href="{{ route('admin.products.edit', $product) }}">Edit</a>@if($product->trashed())<form method="post" action="{{ route('admin.products.restore', $product->id) }}">@csrf<button type="submit">Restore</button></form>@else<form method="post" action="{{ route('admin.products.destroy', $product) }}">@csrf @method('delete')<button type="submit">Archive</button></form>@endif</x-admin.action-menu></div></div>
    @empty
        <x-admin.empty-state title="No products found." description="Create a product to manage tools and downloads." action-url="{{ route('admin.products.create') }}" action-label="New product" />
    @endforelse
</div>
{{ $products->withQueryString()->links() }}
@endsection
