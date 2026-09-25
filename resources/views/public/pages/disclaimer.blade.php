@extends('layouts.public')
@section('title', $page?->seo_title ?: \App\Models\SiteSetting::getValue('default_seo_title'))
@section('description', $page?->seo_description ?: $page?->hero_summary)
@section('content')
    @include('public.pages.legal-content', ['related' => ['risk-disclosure' => 'Risk disclosure', 'terms' => 'Terms of use', 'privacy-policy' => 'Privacy policy']])
@endsection
