@extends('layouts.public')
@section('title', 'Preview: '.$topic->title)
@section('description', $topic->short_description)
@section('content')
<x-public-hero class="public-page-hero" compact eyebrow="Learning topic" :title="$topic->title" :summary="$topic->short_description" :image="$topic->image" setting="hero_learn_image" :fallback-image="config('public.hero_defaults.learn')" :metadata="['Private preview · '.($topic->status ?: ($topic->is_published ? 'published' : 'draft'))]" />
<section class="platform-section"><div class="container editorial-copy"><p class="eyebrow">Preview content</p><div class="rich-copy">{!! \App\Support\RichText::render($topic->full_description ?: $topic->short_description) !!}</div></div></section>
@endsection
