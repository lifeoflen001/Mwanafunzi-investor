@extends('layouts.public')
@section('title', 'Site map — Mwanafunzi Investor')
@section('description', 'Browse the public pages, learning resources, services and policies available from Mwanafunzi Investor.')
@section('content')
<section class="platform-section page-hero"><div class="container"><p class="eyebrow"><span class="eyebrow-line"></span> Site map</p><h1>Find your way around.</h1><p class="body-copy">A clear index of the public areas of Mwanafunzi Investor.</p></div></section>
<section class="platform-section"><div class="container site-map-grid">@foreach($groups as $heading => $links)<div class="site-map-group"><p class="eyebrow">{{ $heading }}</p><ul>@foreach($links as $link)<li><a class="text-link" href="{{ $link['url'] }}">{{ $link['label'] }} <span aria-hidden="true">↗</span></a></li>@endforeach</ul></div>@endforeach</div></section>
@endsection
