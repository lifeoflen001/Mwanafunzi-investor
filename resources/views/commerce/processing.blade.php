@extends('layouts.public')
@section('title', 'Payment processing — Mwanafunzi Investor')
@section('content')<section class="page-hero"><div class="container narrow"><p class="eyebrow">Payment processing</p><h1>We are confirming your payment.</h1><p class="lede">Order {{ $order->order_number }} is still being verified. You can safely leave this page; confirmed access will appear in your account.</p><a class="button button-light" href="{{ route('account.orders.show', $order) }}">View order status <span aria-hidden="true">↗</span></a></div></section>@endsection
