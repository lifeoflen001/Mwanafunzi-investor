@extends('layouts.public')

@section('title', $module->name . ' — Mwanafunzi Investor')
@section('description', $module->description)

@section('content')
    <x-public-hero class="module-hero" :eyebrow="'Mwanafunzi Investor / '.$module->name" :title="$module->tagline" :summary="$module->description" setting="hero_tools_image" :fallback-image="config('public.hero_defaults.modules')" />

    <section class="platform-section module-placeholder">
        <div class="container two-column">
            <div>
                <p class="eyebrow"><span class="eyebrow-line"></span> Module in progress</p>
                <h2>{{ $module->name }} has its own place in the system.</h2>
            </div>
            <div>
                <p>This module is now registered as part of the Mwanafunzi Investor platform. Its services, projects and media will be added here next without disturbing the Forex Academy experience.</p>
                <a class="button button-dark" href="{{ route('contact') }}">Start a conversation <span aria-hidden="true">↗</span></a>
            </div>
        </div>
    </section>
@endsection
