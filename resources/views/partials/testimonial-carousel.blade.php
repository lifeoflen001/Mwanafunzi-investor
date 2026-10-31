@props(['testimonials', 'dark' => false, 'carouselId' => 'testimonials'])
@php
    $testimonialItems = $testimonials instanceof \Illuminate\Pagination\LengthAwarePaginator ? $testimonials->getCollection() : collect($testimonials);
    $testimonialCount = $testimonialItems->count();
@endphp
@if($testimonialItems->isNotEmpty())
    <div class="testimonial-grid testimonial-grid-count-{{ min($testimonialCount, 3) }}{{ $dark ? ' testimonial-grid-dark' : '' }}" id="{{ $carouselId }}" data-testimonial-grid data-testimonial-count="{{ $testimonialCount }}" aria-label="Client testimonials">
        @foreach($testimonialItems as $testimonial)
            @include('partials.testimonial-card', ['testimonial' => $testimonial, 'dark' => $dark, 'featured' => $testimonialCount === 1])
        @endforeach
    </div>
@endif
