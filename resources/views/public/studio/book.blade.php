@extends('layouts.public')

@section('title', 'Book a shoot — Creative Studio')
@section('description', 'Send a structured photography or videography enquiry to the Mwanafunzi Investor Creative Studio.')
@section('service_context', 'studio')

@section('content')
    <x-public-hero class="public-page-hero studio-book-hero" compact eyebrow="Creative Studio / Enquiry" title="Plan the right shoot." summary="Tell us what you are making, when you need it and what the finished work should help you do." :image="$studio->hero_image" :overlay="'strong'" setting="hero_about_image" :fallback-image="config('public.hero_defaults.about')">
        <div class="detail-actions"><a class="text-link text-link-light" href="{{ route('creative-studio') }}">Back to Creative Studio <span aria-hidden="true">↗</span></a></div>
    </x-public-hero>

    <section class="platform-section contact-section studio-book-section">
        <div class="container">
            <div class="contact-intro">
                <x-section-label label="Studio enquiry" />
                <div>
                    <h2>Start with the brief.</h2>
                    <p>This is an enquiry, not a booking confirmation. We will review the details, ask any useful follow-up questions and confirm availability before anything is scheduled.</p>
                </div>
            </div>

            <form class="contact-form studio-book-form" method="post" action="{{ route('creative-studio.book.store') }}" enctype="multipart/form-data">
                @csrf
                @include('partials.form-feedback')
                @include('components.recaptcha', ['action' => 'studio_booking'])
                <label class="honeypot" aria-hidden="true">Website<input tabindex="-1" autocomplete="off" name="website"></label>
                @if($selectedProject)<input type="hidden" name="project_id" value="{{ $selectedProject->id }}"><p class="studio-book-context"><span>Project context</span><strong>{{ $selectedProject->title }}</strong></p>@endif
                <div class="form-row">
                    <label for="studio-name">Name<input id="studio-name" name="name" value="{{ old('name', auth()->user()?->name) }}" required autocomplete="name"></label>
                    <label for="studio-email">Email<input id="studio-email" type="email" name="email" value="{{ old('email', auth()->user()?->email) }}" required autocomplete="email"></label>
                </div>
                <div class="form-row">
                    <label for="studio-phone">Phone / WhatsApp<input id="studio-phone" name="phone" value="{{ old('phone', auth()->user()?->phone) }}" autocomplete="tel"></label>
                    <label for="studio-service">Requested service<select id="studio-service" name="service_id"><option value="">I am not sure yet</option>@foreach($services as $service)<option value="{{ $service->id }}" @selected((string) old('service_id', $selectedService?->id) === (string) $service->id)>{{ $service->title }}</option>@endforeach</select></label>
                </div>
                <div class="form-row">
                    <label for="studio-date">Preferred date<input id="studio-date" type="date" name="preferred_date" value="{{ old('preferred_date') }}" min="{{ now()->toDateString() }}"></label>
                    <label for="studio-time">Preferred time or window<input id="studio-time" name="preferred_time" value="{{ old('preferred_time') }}" placeholder="For example: morning"></label>
                </div>
                <div class="form-row">
                    <label for="studio-location">Location<input id="studio-location" name="location" value="{{ old('location') }}" placeholder="Town, venue or remote"></label>
                    <label for="studio-duration">Estimated duration<input id="studio-duration" name="duration" value="{{ old('duration') }}" placeholder="For example: half day"></label>
                </div>
                <label for="studio-budget">Budget range <span>(optional)</span><input id="studio-budget" name="budget_range" value="{{ old('budget_range') }}" placeholder="Share only if you already have a range"></label>
                <label for="studio-description">What are you making?<textarea id="studio-description" name="description" rows="6" required placeholder="Describe the people, product, event or story, and what you need delivered.">{{ old('description') }}</textarea></label>
                <label for="studio-requirements">Special requirements <span>(optional)</span><textarea id="studio-requirements" name="special_requirements" rows="4" placeholder="Access, timing, format or privacy requirements.">{{ old('special_requirements') }}</textarea></label>
                <label for="studio-references">Reference files <span>(optional, up to 5 files)</span><input id="studio-references" type="file" name="reference_files[]" multiple accept="image/jpeg,image/png,image/webp,application/pdf,video/mp4,video/quicktime"><small>Keep each file under 16 MB. Reference files remain private to the studio team.</small></label>
                <label class="consent"><input type="checkbox" name="consent" value="1" required> <span>I agree that Mwanafunzi Investor may use the information in this form to respond to this studio enquiry. <a href="{{ route('legal', 'privacy-policy') }}">Read the Privacy Policy</a>.</span></label>
                <button class="button button-dark contact-submit" type="submit">Send studio enquiry <span aria-hidden="true">↗</span></button>
                <p class="contact-response-note">Submitting this form does not reserve a date or confirm a price.</p>
            </form>
        </div>
    </section>
@endsection
