@php
    $footerDisclaimer = $disclaimer ?? $riskDisclaimer ?? 'Educational content only. This is not personalised financial advice. Trading involves risk.';
@endphp
<footer class="site-footer">
    <div class="container">
        <div class="footer-top">
            <div class="footer-brand">
                <a class="brand" href="{{ route('home') }}" aria-label="{{ $brandName }} home">
                    <img class="brand-image brand-logo-light" src="{{ asset('images/brand/mwanafunzi-logo-light.png') }}" alt="{{ $brandName }}">
                </a>
                <p class="footer-tagline">{!! nl2br(e($footerCopy)) !!}</p>
                <p class="footer-brand-context">Learn, build and create works with a point of view.</p>
                @if($socialLinks->isNotEmpty())
                    <div class="footer-social" aria-label="Social links">
                        <span>Follow</span>
                        <div class="footer-social-links">
                            @foreach($socialLinks as $social)
                                @php($socialLabel = strtolower($social->label))
                                <a class="footer-social-link" href="{{ $social->url }}" target="_blank" rel="noopener noreferrer" aria-label="{{ $social->label }}" title="{{ $social->label }}">
                                    <span class="footer-social-icon" aria-hidden="true">
                                        @if(str_contains($socialLabel, 'instagram'))
                                            <svg viewBox="0 0 24 24"><rect x="3.5" y="3.5" width="17" height="17" rx="4"></rect><circle cx="12" cy="12" r="4"></circle><circle cx="17.4" cy="6.8" r=".8" class="fill"></circle></svg>
                                        @elseif(str_contains($socialLabel, 'youtube'))
                                            <svg viewBox="0 0 24 24"><rect x="3" y="5.5" width="18" height="13" rx="4"></rect><path d="m10 9 5 3-5 3z" class="fill-stroke"></path></svg>
                                        @elseif(str_contains($socialLabel, 'linkedin'))
                                            <svg viewBox="0 0 24 24"><path d="M6 9v9M6 6.2v.1M10 18v-5a4 4 0 0 1 8 0v5M10 9v9"></path></svg>
                                        @elseif(str_contains($socialLabel, 'facebook'))
                                            <svg viewBox="0 0 24 24"><path d="M14 20v-8h2.7l.4-3H14V7.2c0-.9.3-1.5 1.6-1.5h1.7V3a22 22 0 0 0-2.5-.1C12.3 2.9 10 4.5 10 7.4V9H7.5v3H10v8"></path></svg>
                                        @elseif(str_contains($socialLabel, 'tiktok'))
                                            <svg viewBox="0 0 24 24"><path d="M14 4v10.2a4.3 4.3 0 1 1-3.2-4.1M14 4c.6 2.2 2 3.5 4.2 3.8"></path></svg>
                                        @elseif(str_contains($socialLabel, 'twitter') || trim($social->label) === 'X')
                                            <svg viewBox="0 0 24 24"><path d="m5 4 14 16M19 4 5 20"></path></svg>
                                        @else
                                            <span>{{ mb_strtoupper(mb_substr($social->label, 0, 2)) }}</span>
                                        @endif
                                    </span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <div class="footer-nav">
                @forelse($footerNavigation as $group => $items)
                    <div>
                        <span>{{ ucfirst($group) }}</span>
                        @foreach($items as $item)
                            <a href="{{ $item->href() }}" target="{{ $item->target }}">{{ $item->label }}</a>
                            @foreach($item->children->where('is_visible', true)->sortBy('sort_order') as $child)
                                <a class="footer-sub-link" href="{{ $child->href() }}" target="{{ $child->target }}">{{ $child->label }}</a>
                            @endforeach
                        @endforeach
                    </div>
                @empty
                    <div><span>Explore</span><a href="{{ route('learn') }}">Learn</a><a href="{{ route('courses') }}">Courses</a><a href="{{ route('tools') }}">Tools</a></div>
                    <div><span>Company</span><a href="{{ route('journal') }}">Blog</a><a href="{{ route('about') }}">About us</a><a href="{{ route('contact') }}">Contact us</a></div>
                    <div><span>Legal</span><a href="{{ route('legal', 'privacy-policy') }}">Privacy policy</a><a href="{{ route('legal', 'terms') }}">Terms of services</a><a href="{{ route('legal', 'risk-disclosure') }}">Risk disclosure</a></div>
                @endforelse

                <div class="footer-contact">
                    <span>Contact</span>
                    <a class="footer-contact-link" href="mailto:{{ $email }}" aria-label="Email {{ $email }}"><span>Email us</span><small class="sr-only">{{ $email }}</small></a>
                    <a href="tel:{{ preg_replace('/\D+/', '', $phone) }}">{{ $phone }}</a>
                    @if($whatsapp)
                        <a href="https://wa.me/{{ preg_replace('/\D+/', '', $whatsapp) }}" target="_blank" rel="noopener">WhatsApp {{ $whatsapp }} <span aria-hidden="true">↗</span></a>
                    @endif
                    <p>{{ $location }}</p>
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <span>{{ $footerCopyright }}</span>
            <p class="site-disclaimer">{{ $footerDisclaimer }}</p>
            <strong>{{ $footerBottomStatement }}</strong>
        </div>
    </div>
</footer>
