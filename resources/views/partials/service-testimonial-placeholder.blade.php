@php($dark = $dark ?? false)
<div class="service-testimonial-placeholder{{ $dark ? ' is-dark' : '' }}" role="status">
    <p class="eyebrow{{ $dark ? ' eyebrow-light' : '' }}"><span class="eyebrow-line"></span> Testimonials / Coming soon</p>
    <h3>Client perspectives are being prepared.</h3>
    <p>We are collecting permission-based feedback from {{ $serviceName }} engagements. Approved stories will appear here with the work, outcome and client context.</p>
    <div class="service-placeholder-points">
        <span><strong>01</strong> Clear brief</span>
        <span><strong>02</strong> Useful outcome</span>
        <span><strong>03</strong> Client perspective</span>
    </div>
    <a class="{{ $dark ? 'text-link text-link-light' : 'text-link' }}" href="{{ $contactUrl }}">Start a conversation <span aria-hidden="true">↗</span></a>
</div>
