@props(['testimonials', 'dark' => false, 'carouselId' => 'testimonials'])
@php($testimonialItems = $testimonials instanceof \Illuminate\Pagination\LengthAwarePaginator ? $testimonials->getCollection() : collect($testimonials))
@if($testimonialItems->isNotEmpty())
    <div class="testimonial-carousel{{ $dark ? ' testimonial-carousel-dark' : '' }}" id="{{ $carouselId }}" data-testimonial-carousel data-carousel-count="{{ $testimonialItems->count() }}">
        <div class="testimonial-carousel-viewport" data-carousel-viewport tabindex="0" role="region" aria-roledescription="carousel" aria-label="Client testimonials">
            <div class="testimonial-carousel-track" data-carousel-track>
                @foreach($testimonialItems as $testimonial)
                    <div class="testimonial-carousel-slide" data-carousel-slide role="group" aria-roledescription="slide" aria-label="{{ $loop->iteration }} of {{ $testimonialItems->count() }}">
                        @include('partials.testimonial-card', ['testimonial' => $testimonial, 'dark' => $dark])
                    </div>
                @endforeach
            </div>
        </div>
        @if($testimonialItems->count() > 1)
            <div class="testimonial-carousel-controls">
                <div class="testimonial-carousel-dots" data-carousel-dots aria-label="Choose testimonial"></div>
                <div class="testimonial-carousel-arrows">
                    <button class="testimonial-carousel-arrow" type="button" data-carousel-prev aria-label="Previous testimonial" disabled>←</button>
                    <button class="testimonial-carousel-arrow" type="button" data-carousel-next aria-label="Next testimonial">→</button>
                </div>
            </div>
        @endif
    </div>
@endif
