@php
    $payload = $section->payload ?: [];
@endphp
<section class="section products-section" id="tools">
    <div class="container">
        <div class="section-heading split-heading reveal">
            <div><p class="eyebrow"><span class="eyebrow-line"></span> {{ $payload['eyebrow'] ?? '' }}</p><h2>{{ $section->heading }}</h2></div>
            <p class="body-copy">{{ $section->body }}</p>
        </div>
        <div class="product-grid">
            @if($products->isNotEmpty())
                @foreach($products as $product)
                    @php
                        $mockups = ['journal-mockup', 'simulator-mockup', 'risk-mockup', 'calculator-mockup'];
                        $mockup = $mockups[$loop->index] ?? 'simulator-mockup';
                    @endphp
                    <a class="product-card {{ $loop->first ? 'product-featured' : '' }} {{ $loop->iteration === 4 ? 'product-wide' : '' }} reveal" href="{{ route('tools.show', $product) }}"><div class="product-mockup {{ $mockup }}"><div class="mockup-top"><span>{{ strtoupper($product->name) }}</span><span>{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span></div><div class="sim-chart"><i></i><i></i><i></i><i></i><i></i><i></i><i></i></div><div class="risk-ring"><span>PLAN<br><b>FIRST</b></span></div></div><div class="product-copy"><span class="product-category">{{ $product->product_type }}</span><h3>{{ $product->name }}</h3><p>{{ $product->short_description }}</p><div class="product-meta"><span>{{ $payload['card_cta_label'] ?? '' }} <b>↗</b></span><span>{{ str_replace('_', ' ', $product->availability) }}</span></div></div></a>
                @endforeach
            @else
                <div class="journal-empty"><div class="empty-index">{{ $payload['empty_index'] ?? '' }}</div><div><h3>{{ $payload['empty_heading'] ?? '' }}</h3><p>{{ $payload['empty_body'] ?? '' }}</p></div>@if(!empty($payload['empty_cta_label']) && !empty($payload['empty_cta_url']))<a class="text-link" href="{{ $payload['empty_cta_url'] }}">{{ $payload['empty_cta_label'] }} <span aria-hidden="true">↗</span></a>@endif</div>
            @endif
        </div>
    </div>
</section>
