@extends('layouts.public')

@section('title', $module->name . ' — Mwanafunzi Investor')
@section('description', $module->description)

@section('content')
    <section class="page-hero studio-hero" style="--module-accent: {{ $module->accent_color ?: 'var(--copper)' }}">
        <div class="container">
            <p class="eyebrow eyebrow-light"><span class="eyebrow-line"></span> Mwanafunzi Investor / Creative Studio</p>
            <div class="studio-hero-layout">
                <div>
                    <h1>Make the moment <em>stay.</em></h1>
                    <p class="lede">Photography and video for people, brands and organisations with something worth seeing, remembering and sharing.</p>
                    <div class="detail-actions">
                        <a class="button button-accent" href="{{ route('contact', ['module' => $module->slug]) }}">Plan a shoot <span aria-hidden="true">↗</span></a>
                        <a class="text-link text-link-light" href="#services">Explore the studio <span aria-hidden="true">↓</span></a>
                    </div>
                </div>
                <div class="studio-visual" aria-label="Abstract studio light and camera frame" role="img">
                    <span class="studio-visual-label">Light / Frame / Story</span>
                    <strong>03 / 03</strong>
                    <i aria-hidden="true"></i>
                </div>
            </div>
        </div>
    </section>

    <section class="platform-section studio-services" id="services">
        <div class="container">
            <div class="split-heading">
                <div>
                    <p class="eyebrow"><span class="eyebrow-line"></span> What we make</p>
                    <h2>Images with a reason to <em>exist.</em></h2>
                </div>
                <p class="body-copy">The right frame is more than a record of what happened. It gives a person, product or place a clearer way to be understood.</p>
            </div>

            <div class="studio-service-grid">
                <article class="studio-service-card"><span>01</span><h3>Brand and campaign</h3><p>Visual direction and image-making for brands that need a consistent, recognisable point of view.</p></article>
                <article class="studio-service-card"><span>02</span><h3>Portraits and people</h3><p>Thoughtful portraits for founders, teams, creatives and people who want to be seen as themselves.</p></article>
                <article class="studio-service-card"><span>03</span><h3>Events and documentary</h3><p>Observational coverage that preserves the energy, details and people that make an event matter.</p></article>
                <article class="studio-service-card"><span>04</span><h3>Short-form video</h3><p>Focused social and campaign films built around a clear message, strong pacing and useful delivery formats.</p></article>
                <article class="studio-service-card"><span>05</span><h3>Interviews and stories</h3><p>Human-led video for organisations that need to explain their work through real voices and real context.</p></article>
                <article class="studio-service-card"><span>06</span><h3>Post-production</h3><p>Editing, colour, sound and final exports that help the finished work feel considered wherever it is used.</p></article>
            </div>
        </div>
    </section>

    <section class="platform-muted platform-section studio-approach">
        <div class="container two-column">
            <div>
                <p class="eyebrow"><span class="eyebrow-line"></span> The approach</p>
                <h2>Calm on set.<br><em>Care in the frame.</em></h2>
            </div>
            <div class="studio-approach-copy">
                <p>Good visual work starts before the camera comes out. We take time to understand the people, mood and purpose behind the brief so the final images feel natural and useful.</p>
                <ul class="check-list">
                    <li>Clear creative direction before production</li>
                    <li>A considered plan for people, place and light</li>
                    <li>Practical formats for web, social and print</li>
                    <li>Organised delivery of the final selected work</li>
                </ul>
            </div>
        </div>
    </section>

    <section class="platform-dark studio-process">
        <div class="container">
            <div class="studio-process-heading">
                <div>
                    <p class="eyebrow eyebrow-light"><span class="eyebrow-line"></span> From brief to delivery</p>
                    <h2>Make room for the <em>real.</em></h2>
                </div>
                <p>We keep production structured enough to feel dependable and open enough to let the honest moment happen.</p>
            </div>
            <div class="studio-process-grid">
                <div><span>01</span><h3>Brief</h3><p>We clarify the story, audience, mood and practical requirements.</p></div>
                <div><span>02</span><h3>Prepare</h3><p>We plan the location, schedule, people, shot list and production details.</p></div>
                <div><span>03</span><h3>Capture</h3><p>We create an environment where the useful, natural frame can appear.</p></div>
                <div><span>04</span><h3>Deliver</h3><p>We refine, organise and export the work for the places it needs to go.</p></div>
            </div>
        </div>
    </section>

    <section class="platform-section studio-cta">
        <div class="container two-column">
            <div>
                <p class="eyebrow"><span class="eyebrow-line"></span> Ready when you are</p>
                <h2>There is a story<br><em>in the room.</em></h2>
            </div>
            <div>
                <p>Share the idea, the occasion or the feeling you need to capture. We will help you shape the right kind of shoot.</p>
                <a class="button button-dark" href="{{ route('contact', ['module' => $module->slug]) }}">Start a conversation <span aria-hidden="true">↗</span></a>
            </div>
        </div>
    </section>
@endsection
