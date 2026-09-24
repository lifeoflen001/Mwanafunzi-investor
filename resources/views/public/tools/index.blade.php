@extends('layouts.public')
@section('title', 'Tools — Mwanafunzi Investor')
@section('description', 'Focused digital tools for deliberate practice, journaling and risk-first trading decisions.')
@section('content')
<section class="page-hero"><div class="container narrow"><p class="eyebrow"><span class="eyebrow-line"></span> Tools for deliberate practice</p><h1>Make the process visible.</h1><p class="lede">Simple, focused tools for turning a good intention into a record you can learn from.</p></div></section>
<section class="platform-section"><div class="container"><div class="platform-card-grid">@forelse($products as $product)<a class="platform-card" href="{{ route('tools.show', $product) }}"><div class="card-top"><span class="eyebrow">{{ $product->product_type }}</span><span class="card-icon">↗</span></div><h2>{{ $product->name }}</h2><p>{{ $product->short_description }}</p><div class="card-footer"><span>{{ str_replace('_', ' ', $product->availability) }}</span><span>View tool ↗</span></div></a>@empty<div class="empty-state"><span class="empty-index">TOOLS</span><h2>The first tools are being prepared.</h2><p>Check back soon for deliberate practice tools.</p></div>@endforelse</div>{{ $products->links() }}</div></section>
@endsection
