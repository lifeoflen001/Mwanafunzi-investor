@extends('layouts.public')
@section('title', $page?->seo_title ?: \App\Models\SiteSetting::getValue('default_seo_title'))
@section('description', $page?->seo_description ?: $page?->hero_summary)
@section('content')
    @include('public.pages.legal-content', ['related' => ['terms' => 'Terms of use', 'risk-disclosure' => 'Risk disclosure', 'refund-policy' => 'Refund policy']])
@endsection
