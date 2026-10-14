@extends('layouts.public')
@section('title', $page?->seo_title ?: 'Terms of Use — Mwanafunzi Investor')
@section('description', $page?->seo_description ?: $page?->hero_summary)
@section('content')
    @include('public.pages.legal-content', ['related' => ['privacy-policy' => 'Privacy policy', 'risk-disclosure' => 'Risk disclosure', 'refund-policy' => 'Refund policy']])
@endsection
