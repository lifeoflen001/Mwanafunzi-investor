@props(['testimonials', 'dark' => false, 'carouselId' => 'testimonials'])
@php
    $testimonialItems = $testimonials instanceof \Illuminate\Pagination\LengthAwarePaginator ? $testimonials->getCollection() : collect($testimonials);
    $testimonialCount = $testimonialItems->count();
@endphp
@if($testimonialItems->isNotEmpty())
    <div class="testimonial-carousel{{ $dark ? ' testimonial-carousel-dark' : '' }}" id="{{ $carouselId }}" data-testimonial-carousel data-carousel-count="{{ $testimonialCount }}">
        <div class="testimonial-carousel-viewport" data-carousel-viewport tabindex="0" role="region" aria-label="Client testimonials">
            <div class="testimonial-carousel-track" data-carousel-track>
                @foreach($testimonialItems as $testimonial)
                    @php($quoteId = $carouselId.'-quote-'.$loop->index)
                    <div class="testimonial-carousel-slide" data-carousel-slide role="group" aria-label="{{ $loop->iteration }} of {{ $testimonialCount }}">
                        @include('partials.testimonial-card', ['testimonial' => $testimonial, 'dark' => $dark, 'quoteId' => $quoteId])
                    </div>
                @endforeach
            </div>
        </div>
        @if($testimonialCount > 1)
            <div class="testimonial-carousel-controls">
                <div class="testimonial-carousel-dots" data-carousel-dots aria-label="Choose testimonial"></div>
                <div class="testimonial-carousel-arrows">
                    <button class="testimonial-carousel-arrow" type="button" data-carousel-prev aria-label="Previous testimonials" disabled>←</button>
                    <button class="testimonial-carousel-arrow" type="button" data-carousel-next aria-label="Next testimonials">→</button>
                </div>
            </div>
        @endif
    </div>
@endif
