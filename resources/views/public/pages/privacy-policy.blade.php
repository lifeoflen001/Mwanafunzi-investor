@extends('layouts.public')
@section('title', $page?->seo_title ?: 'Privacy Policy — Mwanafunzi Investor')
@section('description', $page?->seo_description ?: 'How Mwanafunzi Investor handles account, contact, order and session information.')
@section('content')
    @include('public.pages.legal-content', ['heading' => 'Privacy policy', 'eyebrow' => 'Policies / Privacy', 'intro' => 'Review how the platform handles information.', 'related' => ['terms' => 'Terms of use', 'risk-disclosure' => 'Risk disclosure', 'refund-policy' => 'Refund policy']])
@endsection
