@php
    $digitalEmail = \App\Models\SiteSetting::getValue('contact_email', config('footer.contact.email'));
    $digitalPhone = \App\Models\SiteSetting::getValue('contact_phone', config('footer.contact.phone'));
    $digitalLocation = \App\Models\SiteSetting::getValue('contact_location', config('footer.contact.location'));
    $digitalPhoneHref = '+'.ltrim(preg_replace('/\D+/', '', $digitalPhone), '+');
@endphp
<footer class="site-footer digital-software-footer">
    <div class="container">
        <div class="footer-top">
            <div class="footer-brand"><a class="brand" href="{{ route('digital-systems') }}" aria-label="Digital Software home"><img class="brand-image brand-logo-light" src="{{ asset('images/brand/mwanafunzi-logo-light.webp') }}" alt="Mwanafunzi Investor"></a><p class="footer-tagline">Mwanafunzi Investor<br><strong>Digital Software</strong></p><p class="footer-brand-context">Websites, software systems and technical solutions built around real business operations.</p></div>
            <div class="footer-links">
                <nav class="footer-group" aria-label="Digital Software links"><span>Digital Software</span><a href="{{ route('digital-systems') }}">Home</a><a href="{{ route('digital-software.projects') }}">Projects</a><a href="{{ route('digital-software.products') }}">Products</a><a href="{{ route('digital-software.contact') }}">Contact</a></nav>
                <nav class="footer-group" aria-label="Services links"><span>Services</span><a href="{{ route('digital-software.contact', ['type' => 'new-website']) }}">Websites</a><a href="{{ route('digital-software.contact', ['type' => 'custom-software']) }}">Custom software</a><a href="{{ route('digital-software.contact', ['type' => 'management-system']) }}">Business systems</a><a href="{{ route('digital-software.contact', ['type' => 'integration']) }}">Integrations</a><a href="{{ route('digital-software.contact', ['type' => 'maintenance']) }}">Maintenance</a></nav>
                <nav class="footer-group footer-contact" aria-label="Digital Software contact"><span>Contact</span><a href="mailto:{{ $digitalEmail }}">{{ $digitalEmail }}</a><a href="tel:{{ $digitalPhoneHref }}">{{ $digitalPhone }}</a><p>{{ $digitalLocation }} · Available remotely</p><a href="{{ route('home') }}">Explore Mwanafunzi Investor <span aria-hidden="true">↗</span></a></nav>
            </div>
        </div>
        <div class="footer-bottom"><span>© {{ date('Y') }} Mwanafunzi Investor</span><div class="footer-bottom-links"><a href="{{ route('cookie-policy') }}">Cookie Policy</a><a href="{{ route('legal', 'privacy-policy') }}">Privacy</a><a href="{{ route('home') }}">Main platform</a></div></div>
    </div>
</footer>
