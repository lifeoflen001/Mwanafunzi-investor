@extends('layouts.public')
@section('title', $page?->seo_title ?: 'Terms — Mwanafunzi Investor')
@section('description', $page?->seo_description ?: 'Terms for using the Mwanafunzi Investor website, courses, tools and account areas.')
@section('content')
    @include('public.pages.legal-content', ['heading' => 'Terms of use', 'eyebrow' => 'Policies / Terms', 'intro' => 'Review the terms that govern use of the platform.', 'related' => ['privacy-policy' => 'Privacy policy', 'risk-disclosure' => 'Risk disclosure', 'refund-policy' => 'Refund policy']])
@endsection
