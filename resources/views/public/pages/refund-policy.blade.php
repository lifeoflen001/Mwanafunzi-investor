@extends('layouts.public')
@section('title', $page?->seo_title ?: 'Refund Policy — Mwanafunzi Investor')
@section('description', $page?->seo_description ?: 'How Mwanafunzi Investor handles product, course, duplicate-payment and refund questions.')
@section('content')
    @include('public.pages.legal-content', ['related' => ['terms' => 'Terms of use', 'privacy-policy' => 'Privacy policy', 'risk-disclosure' => 'Risk disclosure']])
@endsection
