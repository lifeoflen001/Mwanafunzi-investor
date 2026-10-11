@extends('layouts.public')
@section('title', $page?->seo_title ?: 'Cookie Policy — Mwanafunzi Investor')
@section('description', $page?->seo_description ?: 'How Mwanafunzi Investor uses cookies and how to manage cookie preferences.')
@section('content')
    @include('public.pages.legal-content', ['related' => ['privacy-policy' => 'Privacy policy', 'terms' => 'Terms of use']])
    <section class="platform-section cookie-policy-registry"><div class="container"><div class="legal-body"><p class="eyebrow">Current registry</p><h2>Cookies and services documented for this site</h2><div class="cookie-registry-table"><table><caption class="sr-only">Cookies and services documented for Mwanafunzi Investor</caption><thead><tr><th scope="col">Name</th><th scope="col">Provider</th><th scope="col">Purpose</th><th scope="col">Category</th><th scope="col">Expiry</th></tr></thead><tbody>@foreach(\App\Models\CookieRegistry::active()->get() as $entry)<tr><td>{{ $entry->name }}</td><td>{{ $entry->provider }}</td><td>{{ $entry->purpose }}</td><td>{{ \Illuminate\Support\Str::headline($entry->category) }}</td><td>{{ $entry->expiry ?: 'Not specified' }}</td></tr>@endforeach</tbody></table></div></div></div></section>
@endsection
