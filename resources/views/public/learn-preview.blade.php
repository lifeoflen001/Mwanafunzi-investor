@extends('layouts.public')
@section('title', 'Preview: '.$topic->title)
@section('description', $topic->short_description)
@section('content')
<section class="page-hero"><div class="container narrow"><span class="status-badge">Private preview · {{ $topic->status ?: ($topic->is_published ? 'published' : 'draft') }}</span><p class="eyebrow"><span class="eyebrow-line"></span> Learning topic</p><h1>{{ $topic->title }}</h1><p class="lede">{{ $topic->short_description }}</p></div></section>
<section class="platform-section"><div class="container editorial-copy"><p class="eyebrow">Preview content</p><div class="rich-copy">{!! \App\Support\RichText::render($topic->full_description ?: $topic->short_description) !!}</div></div></section>
@endsection
