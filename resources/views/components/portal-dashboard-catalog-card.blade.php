@props(['item', 'kind'])
@php
    $isCourse = $kind === 'course';
    $title = $isCourse ? $item->title : $item->name;
    $href = route($isCourse ? 'courses.show' : 'tools.show', $item);
    $image = \App\Support\PublicHero::resolve([$isCourse ? $item->featured_image : $item->thumbnail, config('public.hero_defaults.'.($isCourse ? 'courses' : 'tools'))]);
    $price = $isCourse ? $item->price : ($item->sale_price ?? $item->price);
    $priceLabel = $price !== null ? app(\App\Services\MoneyFormatter::class)->format($price, $item->currency ?: 'TZS') : ($isCourse && $item->status === 'published' ? 'Details on course page' : 'Coming soon');
    $meta = $isCourse ? ($item->level ?: 'Financial education') : ($item->product_type ?: 'Digital tool');
    $availability = $isCourse ? $item->status : $item->availability;
@endphp
<a class="student-dashboard-catalog-card" href="{{ $href }}">
    <div class="student-dashboard-catalog-media">
        @if($image)
            <img src="{{ $image['url'] }}" @if($image['srcset']) srcset="{{ $image['srcset'] }}" sizes="(max-width: 760px) 100vw, 25vw" @endif alt="" loading="lazy">
        @else
            <span aria-hidden="true">{{ $isCourse ? '▤' : '↓' }}</span>
        @endif
    </div>
    <div class="student-dashboard-catalog-copy">
        <div class="student-card-meta"><span>{{ $meta }}</span><span>{{ str($availability)->replace('_', ' ')->title() }}</span></div>
        <h3>{{ $title }}</h3>
        <p>{{ $isCourse ? ($item->short_description ?: 'A structured path for deliberate learners.') : ($item->short_description ?: 'A focused tool for deliberate practice.') }}</p>
        <div class="student-dashboard-catalog-footer"><strong>{{ $priceLabel }}</strong><span>View {{ $isCourse ? 'course' : 'tool' }} <span aria-hidden="true">↗</span></span></div>
    </div>
</a>
