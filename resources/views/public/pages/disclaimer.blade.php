@extends('layouts.public')
@section('title', $page?->seo_title ?: 'Disclaimer — Mwanafunzi Investor')
@section('description', $page?->seo_description ?: 'The educational scope and limits of Mwanafunzi Investor content, tools and external services.')
@section('content')
    @include('public.pages.legal-content', ['heading' => 'Educational disclaimer', 'eyebrow' => 'Policies / Disclaimer', 'intro' => 'Review the educational scope and limits of this content.', 'related' => ['risk-disclosure' => 'Risk disclosure', 'terms' => 'Terms of use', 'privacy-policy' => 'Privacy policy']])
@endsection
