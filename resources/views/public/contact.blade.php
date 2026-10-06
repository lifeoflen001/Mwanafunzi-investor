@php
    $contactEmail = \App\Models\SiteSetting::getValue('contact_email', 'mwanafunziinvestor@outlook.com');
    $contactPhone = \App\Models\SiteSetting::getValue('contact_phone', '+255 787 172 686');
    $contactWhatsApp = \App\Models\SiteSetting::getValue('contact_whatsapp') ?: $contactPhone;
    $contactLocation = \App\Models\SiteSetting::getValue('contact_location', 'Tanzania');
    $intro = $page?->section('intro');
    $contactTopics = $intro?->payload['list'] ?? [];
@endphp
@extends('layouts.public')
@section('title', $page?->seo_title ?: 'Contact — Mwanafunzi Investor')
@section('description', $page?->seo_description ?: ($intro?->body ?: 'Contact the Mwanafunzi Investor desk for general questions, course enquiries, product support or partnerships.'))
@section('content')
<x-public-hero class="public-page-hero" :eyebrow="$intro?->payload['eyebrow'] ?? 'The desk'" :title="($intro?->heading ?: 'Markets will always be uncertain. Your process does not have to be.')" :summary="($intro?->body ?: 'For general questions, course enquiries, product support or partnership ideas, send a note and we will respond as soon as we can.')" setting="hero_contact_image" :fallback-image="config('public.hero_defaults.contact')" />
<section class="platform-section contact-section">
    <div class="container">
        <div class="contact-intro">
            <x-section-label label="Start a conversation" />
            <div>
                <h2>Let’s talk.</h2>
                <p>Tell us what you are learning, building or creating. The Mwanafunzi desk is here for thoughtful questions, project enquiries and useful next steps.</p>
            </div>
        </div>

        <div class="contact-grid">
            <div class="contact-details">
                <x-section-label label="Contact details" />
                <dl>
                    <div class="contact-detail">
                        <dt><span class="contact-detail-icon" aria-hidden="true">✉</span>Email</dt>
                        <dd><a href="mailto:{{ $contactEmail }}">{{ $contactEmail }}</a></dd>
                    </div>
                    <div class="contact-detail">
                        <dt><span class="contact-detail-icon" aria-hidden="true">↗</span>Phone / WhatsApp</dt>
                        <dd><a href="tel:{{ preg_replace('/\D+/', '', $contactPhone) }}">{{ $contactPhone }}</a><span class="contact-separator">·</span><a href="https://wa.me/{{ preg_replace('/\D+/', '', $contactWhatsApp) }}" target="_blank" rel="noopener">WhatsApp <span aria-hidden="true">↗</span></a></dd>
                    </div>
                    <div class="contact-detail">
                        <dt><span class="contact-detail-icon" aria-hidden="true">⌖</span>Location</dt>
                        <dd>{{ $contactLocation }}</dd>
                    </div>
                </dl>
                @if($contactTopics)
                    <div class="contact-note">
                        <x-section-label label="You can contact us about" />
                        <ul class="contact-topic-list">@foreach($contactTopics as $topic)<li>{{ $topic }}</li>@endforeach</ul>
                    </div>
                @endif
            </div>

            <form class="contact-form" method="post" action="{{ route('contact.submit') }}">
                @csrf
                @if(session('success'))<div class="form-success" role="status">{{ session('success') }}</div>@endif
                @if($errors->any())<div class="form-errors" role="alert">Please check the highlighted fields and try again.</div>@endif
                <p class="contact-form-intro">A few details help us send your enquiry to the right part of the platform.</p>
                <label class="honeypot" aria-hidden="true">Website<input tabindex="-1" autocomplete="off" name="website"></label>
                <div class="form-row">
                    <label for="name">Name<input id="name" name="name" value="{{ old('name') }}" required autocomplete="name">@error('name')<small>{{ $message }}</small>@enderror</label>
                    <label for="email">Email<input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email">@error('email')<small>{{ $message }}</small>@enderror</label>
                </div>
                <div class="form-row">
                    <label for="phone">Phone (optional)<input id="phone" name="phone" value="{{ old('phone') }}" autocomplete="tel"></label>
                    <label for="business_unit_id">I am enquiring about<select id="business_unit_id" name="business_unit_id"><option value="">Mwanafunzi platform</option>@foreach($businessUnits as $businessUnit)<option value="{{ $businessUnit->id }}" @selected((string) old('business_unit_id', $selectedBusinessUnit?->id) === (string) $businessUnit->id)>{{ $businessUnit->name }}</option>@endforeach</select></label>
                </div>
                <label for="category">What can we help with?<select id="category" name="category" required><option value="">Select a reason</option><option value="general">General question</option><option value="course">Course question</option><option value="product">Digital product support</option><option value="partnership">Partnership</option><option value="support">Technical support</option><option value="other">Other</option></select></label>
                <label for="message">Message<textarea id="message" name="message" rows="5" required>{{ old('message') }}</textarea>@error('message')<small>{{ $message }}</small>@enderror</label>
                <label class="consent"><input type="checkbox" name="consent" value="1" required> <span>I understand this is an educational platform and not personalised financial advice. <a href="{{ route('legal', 'risk-disclosure') }}">Read the risk disclosure.</a></span></label>
                <button class="button button-dark contact-submit" type="submit">Send message <span aria-hidden="true">↗</span></button>
                <p class="contact-response-note">We usually reply with the next useful step, not a sales script.</p>
            </form>
        </div>
    </div>
</section>
@endsection
