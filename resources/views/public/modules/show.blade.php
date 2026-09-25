@extends('layouts.public')

@section('title', $module->name . ' — Mwanafunzi Investor')
@section('description', $module->description)

@section('content')
    <section class="page-hero module-hero" style="--module-accent: {{ $module->accent_color ?: 'var(--copper)' }}">
        <div class="container narrow">
            <p class="eyebrow"><span class="eyebrow-line"></span> Mwanafunzi Investor / {{ $module->name }}</p>
            <h1>{{ $module->tagline }}</h1>
            <p class="lede">{{ $module->description }}</p>
        </div>
    </section>

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
