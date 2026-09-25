@extends('layouts.public')

@section('title', $module->name . ' — Mwanafunzi Investor')
@section('description', $module->description)

@section('content')
    <section class="page-hero development-hero" style="--module-accent: {{ $module->accent_color ?: 'var(--copper)' }}">
        <div class="container">
            <p class="eyebrow"><span class="eyebrow-line"></span> Mwanafunzi Investor / Digital Systems</p>
            <div class="dev-hero-grid">
                <div>
                    <h1>Digital tools for work that needs to <em>move.</em></h1>
                    <p class="lede">We design and build clear, dependable websites and software for organisations that are ready to work with less friction.</p>
                    <div class="detail-actions">
                        <a class="button button-dark" href="{{ route('contact', ['module' => $module->slug]) }}">Discuss a project <span aria-hidden="true">↗</span></a>
                        <a class="text-link" href="#capabilities">See what we build <span aria-hidden="true">↓</span></a>
                    </div>
                </div>
                <div class="dev-hero-note">
                    <span class="dev-hero-rule"></span>
                    <p>Thoughtful design.<br>Useful technology.<br>Long-term thinking.</p>
                    <strong>02 / 03</strong>
                </div>
            </div>
        </div>
    </section>

    <section class="platform-section platform-muted" id="capabilities">
        <div class="container">
            <div class="split-heading">
                <div>
                    <p class="eyebrow"><span class="eyebrow-line"></span> What we build</p>
                    <h2>Systems that make the next step <em>clearer.</em></h2>
                </div>
                <p class="body-copy">Every project starts with the work behind the brief: what needs to happen, who needs to use it and where the current process gets in the way.</p>
            </div>

            <div class="dev-service-grid">
                <article class="dev-service-card">
                    <span class="dev-service-index">01</span>
                    <h3>Websites that earn attention</h3>
                    <p>Purposeful public-facing websites with strong structure, responsive layouts and content that helps people decide.</p>
                    <span class="dev-service-tags">Strategy · Design · Build</span>
                </article>
                <article class="dev-service-card">
                    <span class="dev-service-index">02</span>
                    <h3>Software that reduces friction</h3>
                    <p>Custom web applications, dashboards and internal tools that turn repeated work into a clearer system.</p>
                    <span class="dev-service-tags">Laravel · Interfaces · Workflows</span>
                </article>
                <article class="dev-service-card">
                    <span class="dev-service-index">03</span>
                    <h3>Commerce and integrations</h3>
                    <p>Practical commerce experiences and integrations that connect customers, payments, content and operations.</p>
                    <span class="dev-service-tags">Commerce · Payments · APIs</span>
                </article>
                <article class="dev-service-card">
                    <span class="dev-service-index">04</span>
                    <h3>Care after launch</h3>
                    <p>Measured improvements, maintenance and support so the system keeps earning its place as the work grows.</p>
                    <span class="dev-service-tags">Support · Iteration · Growth</span>
                </article>
            </div>
        </div>
    </section>

    <section class="platform-dark dev-process">
        <div class="container">
            <div class="dev-process-heading">
                <div>
                    <p class="eyebrow eyebrow-light"><span class="eyebrow-line"></span> How we work</p>
                    <h2>Good work is a <em>process.</em></h2>
                </div>
                <p>Clear decisions early make better products later. We keep the work visible, collaborative and grounded in the actual problem.</p>
            </div>
            <div class="dev-process-grid">
                <div class="dev-process-step"><span>01</span><h3>Understand</h3><p>Map the audience, goals, constraints and the work the system must support.</p></div>
                <div class="dev-process-step"><span>02</span><h3>Shape</h3><p>Turn the brief into a focused structure, useful flows and a visual direction.</p></div>
                <div class="dev-process-step"><span>03</span><h3>Build</h3><p>Develop in visible stages, test the important paths and keep the foundation maintainable.</p></div>
                <div class="dev-process-step"><span>04</span><h3>Improve</h3><p>Launch with care, learn from use and make the next version more useful.</p></div>
            </div>
        </div>
    </section>

    <section class="platform-section dev-cta">
        <div class="container two-column">
            <div>
                <p class="eyebrow"><span class="eyebrow-line"></span> Have a project in mind?</p>
                <h2>Bring the problem.<br><em>We’ll shape the system.</em></h2>
            </div>
            <div>
                <p>Tell us what you are trying to make clearer, faster or more useful. We will help you find the right next step.</p>
                <a class="button button-dark" href="{{ route('contact', ['module' => $module->slug]) }}">Start a conversation <span aria-hidden="true">↗</span></a>
            </div>
        </div>
    </section>
@endsection
