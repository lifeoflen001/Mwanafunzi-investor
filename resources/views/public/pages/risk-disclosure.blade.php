@extends('layouts.public')
@section('title', $page?->seo_title ?: 'Risk Disclosure — Mwanafunzi Investor')
@section('description', $page?->seo_description ?: 'A clear explanation of trading risk, educational content and responsible use of Mwanafunzi Investor tools.')
@section('content')
    @include('public.pages.legal-content', ['related' => ['disclaimer' => 'Educational disclaimer', 'terms' => 'Terms of use', 'privacy-policy' => 'Privacy policy']])
@endsection
