@php
    $brandName = $brandName ?? \App\Models\SiteSetting::getValue('brand_name', 'Mwanafunzi Investor');
    $brandLogoLightUrl = $brandLogoLightUrl ?? \App\Support\BrandAssets::logo('light');
    /* One editable footer contact defaults source. Admin-managed settings still override these values. */
    $footerContactDefaults = config('footer.contact');
    $email = $email ?? \App\Models\SiteSetting::getValue('contact_email', $footerContactDefaults['email']);
    $phone = $phone ?? \App\Models\SiteSetting::getValue('contact_phone', $footerContactDefaults['phone']);
    $whatsapp = $whatsapp ?? \App\Models\SiteSetting::getValue('contact_whatsapp');
    $location = $location ?? \App\Models\SiteSetting::getValue('contact_location', $footerContactDefaults['location']);
    $phoneHref = '+'.ltrim(preg_replace('/\D+/', '', $phone), '+');
    $footerCopy = trim(preg_replace('/\s+/', ' ', str_replace('\\n', ' ', (string) ($footerCopy ?? 'Student of Money. Probability. Systems. Discipline.'))));
    $footerCopyright = $footerCopyright ?? \App\Models\SiteSetting::getValue('footer_copyright', '© '.date('Y').' '.$brandName);
    $footerBottomStatement = $footerBottomStatement ?? \App\Models\SiteSetting::getValue('footer_bottom_statement', 'ALWAYS A MWANAFUNZI.');
    $footerBrandContext = \App\Models\SiteSetting::getValue('footer_brand_context', 'Clear, practical lessons on markets, risk and probability.');
    $footerCompanyLabel = \App\Models\SiteSetting::getValue('footer_company_label', 'Company');
    $footerExploreLabel = \App\Models\SiteSetting::getValue('footer_explore_label', 'Explore');
    $footerLegalLabel = \App\Models\SiteSetting::getValue('footer_legal_label', 'Legal');
    $footerContactLabel = \App\Models\SiteSetting::getValue('footer_contact_label', 'Contact');
    $footerSocialLabel = \App\Models\SiteSetting::getValue('footer_social_label', 'Follow');
    $footerNewsletterEyebrow = \App\Models\SiteSetting::getValue('footer_newsletter_eyebrow', 'Stay connected');
    $footerNavigation = $footerNavigation ?? \App\Support\PublicSiteData::footerNavigation();
    $socialLinks = $socialLinks ?? \App\Support\PublicSiteData::socialLinks();
    $resolvePublicUrl = static fn (string $path): string => \App\Support\Seo::resolve($path, '#') ?: '#';
@endphp
<footer class="site-footer">
    <div class="container">
        @php
            $companyLinks = $footerNavigation->get('company', collect());
            $exploreLinks = $footerNavigation->get('explore', collect());
            $legalLinks = $footerNavigation->get('legal', collect());
            $legalFallbackLinks = [
                ['label' => 'Refund Policy', 'href' => route('legal', 'refund-policy')],
                ['label' => 'Cookie Policy', 'href' => route('cookie-policy')],
                ['label' => 'Disclaimer', 'href' => route('legal', 'disclaimer')],
            ];
        @endphp
        <div class="footer-top">
            <div class="footer-brand">
                <a class="brand" href="{{ route('home') }}" aria-label="{{ $brandName }} home">
                    <img class="brand-image brand-logo-light" src="{{ $brandLogoLightUrl }}" alt="{{ $brandName }}">
                </a>
                <p class="footer-tagline">{{ $footerCopy }}</p>
                <p class="footer-brand-context">{{ $footerBrandContext }}</p>
            </div>

            <div class="footer-links">
                <nav class="footer-group footer-company" aria-label="Company links">
                    <span>{{ $footerCompanyLabel }}</span>
                    @forelse($companyLinks as $item)
                        <a href="{{ $item->href() }}" target="{{ $item->target }}">{{ $item->publicLabel() }}</a>
                    @empty
                        <a href="{{ route('blog') }}">Blog</a><a href="{{ route('about') }}">About</a><a href="{{ route('contact') }}">Contact</a>
                    @endforelse
                    @foreach(['services' => 'Services', 'projects' => 'Projects', 'site-map' => 'Site map'] as $footerRoute => $footerLabel)
                        @unless($companyLinks->contains(fn ($link) => rtrim($link->href(), '/') === rtrim(route($footerRoute), '/')))
                            <a href="{{ route($footerRoute) }}">{{ $footerLabel }}</a>
                        @endunless
                    @endforeach
                </nav>

                <nav class="footer-group footer-explore" aria-label="Explore links">
                    <span>{{ $footerExploreLabel }}</span>
                    @forelse($exploreLinks as $item)
                        <a href="{{ $item->href() }}" target="{{ $item->target }}">{{ $item->publicLabel() }}</a>
                    @empty
                        <a href="{{ route('learn') }}">Learn</a><a href="{{ route('courses') }}">Courses</a><a href="{{ route('tools') }}">Tools</a>
                    @endforelse
                    @foreach(['forex-academy' => 'Financial Academy', 'digital-systems' => 'Digital Software', 'creative-studio' => 'Creative Studio'] as $footerRoute => $footerLabel)
                        @unless($exploreLinks->contains(fn ($link) => rtrim($link->href(), '/') === rtrim(route($footerRoute), '/')))
                            <a href="{{ route($footerRoute) }}">{{ $footerLabel }}</a>
                        @endunless
                    @endforeach
                </nav>

                <nav class="footer-group footer-legal" aria-label="Legal links">
                    <span>{{ $footerLegalLabel }}</span>
                    @foreach($legalLinks as $item)
                        <a href="{{ $item->href() }}" target="{{ $item->target }}">{{ $item->publicLabel() }}</a>
                    @endforeach
                    @foreach($legalFallbackLinks as $item)
                        @unless($legalLinks->contains(fn ($link) => rtrim($link->href(), '/') === rtrim($item['href'], '/')))
                            <a href="{{ $item['href'] }}">{{ $item['label'] }}</a>
                        @endunless
                    @endforeach
                </nav>
                <nav class="footer-group footer-contact" aria-label="Contact details">
                    <span>{{ $footerContactLabel }}</span>
                    <a class="footer-contact-link" href="mailto:{{ $email }}" aria-label="Email {{ $email }}">{{ $email }}</a>
                    <a href="tel:{{ $phoneHref }}">{{ $phone }}</a>
                    @if($whatsapp)
                        <a href="https://wa.me/{{ preg_replace('/\D+/', '', $whatsapp) }}" target="_blank" rel="noopener">WhatsApp {{ $whatsapp }} <span aria-hidden="true">↗</span></a>
                    @endif
                    <p>{{ $location }}</p>
                </nav>
            </div>
        </div>

        @php($newsletterSettings = \App\Support\NewsletterSettings::all())
        @if($newsletterSettings['enabled'] && $newsletterSettings['footer_visible'])
            <section class="footer-newsletter-row footer-contact-newsletter" aria-labelledby="footer-newsletter-heading">
                <div class="footer-newsletter-copy">
                    <p class="eyebrow">{{ $footerNewsletterEyebrow }}</p>
                    <h2 id="footer-newsletter-heading">{{ $newsletterSettings['heading'] }}</h2>
                    <p class="footer-newsletter-description">{{ $newsletterSettings['description'] }}</p>
                </div>
                <form class="footer-newsletter-form" method="post" action="{{ route('newsletter.subscribe') }}" data-newsletter-form>
                    @csrf
                    @include('components.recaptcha', ['action' => 'newsletter'])
                    <label for="footer-newsletter-email">Email address</label>
                    <div class="footer-newsletter-fields">
                        <input id="footer-newsletter-email" type="email" name="email" value="{{ old('email') }}" placeholder="Email address" required autocomplete="email" inputmode="email">
                        <input class="newsletter-honeypot" type="text" name="website" tabindex="-1" autocomplete="off" aria-hidden="true">
                        <button class="button button-accent footer-newsletter-submit" type="submit" aria-label="Subscribe to the newsletter" aria-live="polite" data-newsletter-submit><span class="sr-only" data-newsletter-submit-label>Subscribe</span> <span aria-hidden="true">→</span></button>
                    </div>
                    <p class="footer-newsletter-consent">{{ $newsletterSettings['consent_copy'] }} <a href="{{ $resolvePublicUrl($newsletterSettings['privacy_url']) }}">Privacy policy</a></p>
                    @if(session('newsletter_status') === 'check-inbox')<p class="form-success" role="status" aria-live="polite">{{ $newsletterSettings['pending_message'] }}</p>@elseif(session('newsletter_status') === 'subscribed')<p class="form-success" role="status" aria-live="polite">{{ $newsletterSettings['success_message'] }}</p>@elseif(session('newsletter_status') === 'already')<p class="form-success" role="status" aria-live="polite">You're already subscribed.</p>@elseif(session('newsletter_status') === 'rate-limited')<p class="form-error" role="alert" aria-live="assertive">Please wait a moment before trying again.</p>@endif
                    @if($errors->has('email') && old('email') !== null)<p class="field-error" role="alert">{{ $errors->first('email') }}</p>@endif
                </form>
            </section>
        @endif

        @if($socialLinks->isNotEmpty())
            <nav class="footer-social footer-social-bottom" aria-label="Social links">
                <span>{{ $footerSocialLabel }}</span>
                <div class="footer-social-links">
                    @foreach($socialLinks as $social)
                        @php($socialLabel = strtolower($social->label))
                        <a class="footer-social-link" href="{{ $social->url }}" target="_blank" rel="noopener noreferrer" aria-label="Visit our {{ $social->label }}" title="Visit our {{ $social->label }}">
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
            </nav>
        @endif

        <div class="footer-bottom">
            <span>{{ $footerCopyright }}</span>
            <div class="footer-bottom-links">@if(app(\App\Services\RecaptchaService::class)->isEnabled())<span class="recaptcha-disclosure">This site is protected by reCAPTCHA.</span>@endif<a href="{{ $resolvePublicUrl(\App\Support\CookieConsentSettings::all()['policy_url']) }}" data-cookie-preferences>Cookie Preferences</a><strong>{{ $footerBottomStatement }}</strong></div>
        </div>
    </div>
</footer>
