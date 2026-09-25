@extends('layouts.public')
@section('title', $page->seo_title ?: $page->name.' — Mwanafunzi Investor')
@section('description', $page->seo_description ?: $page->hero_summary)
@section('content')
<x-public-hero class="public-page-hero" :eyebrow="$page->hero_eyebrow ?: $page->name" :title="$page->hero_title ?: $page->name" :summary="$page->hero_summary" :image="$page->hero_image" :overlay="$page->hero_overlay" :alignment="$page->hero_alignment" />
@foreach($page->sections->where('is_enabled', true)->sortBy('sort_order') as $section)
    <section class="platform-section cms-public-section cms-section-{{ $section->section_type }}"><div class="container editorial-copy"><p class="eyebrow">{{ $section->section_type }}</p>@if($section->heading)<h2>{{ $section->heading }}</h2>@endif @if($section->body)<div class="rich-copy">{!! \App\Support\RichText::render($section->body) !!}</div>@endif @if($section->cta_label && $section->cta_url)<a class="button button-accent" href="{{ $section->cta_url }}">{{ $section->cta_label }} <span aria-hidden="true">↗</span></a>@endif</div></section>
@endforeach
@endsection
