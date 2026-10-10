@php
    $serviceNavigation = [
        'forex' => [
            'label' => 'Financial Academy',
            'links' => [
                ['label' => 'Home', 'url' => route('forex-academy')],
                ['label' => 'Learn', 'url' => route('learn')],
                ['label' => 'Courses', 'url' => route('courses')],
                ['label' => 'Tools', 'url' => route('tools')],
            ],
        ],
        'development' => [
            'label' => 'Digital Software',
            'links' => [
                ['label' => 'Home', 'url' => route('digital-systems')],
                ['label' => 'Products', 'url' => route('digital-software.products')],
                ['label' => 'Projects', 'url' => route('digital-software.projects')],
                ['label' => 'Testimonials', 'url' => route('digital-software.testimonials')],
            ],
        ],
        'studio' => [
            'label' => 'Creative Studio',
            'links' => [
                ['label' => 'Home', 'url' => route('creative-studio')],
                ['label' => 'Services', 'url' => route('creative-studio.services')],
                ['label' => 'Projects', 'url' => route('creative-studio.projects')],
                ['label' => 'Testimonials', 'url' => route('creative-studio.testimonials')],
            ],
        ],
    ];
    $currentServiceNavigation = $serviceNavigation[$serviceContext] ?? null;
@endphp

@if($currentServiceNavigation)
    <span class="service-nav-label">{{ $currentServiceNavigation['label'] }}</span>
    @foreach($currentServiceNavigation['links'] as $link)
        <a class="{{ request()->url() === $link['url'] ? 'active' : '' }}" href="{{ $link['url'] }}">{{ $link['label'] }}</a>
    @endforeach
@endif
