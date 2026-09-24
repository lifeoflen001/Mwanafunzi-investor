@extends('layouts.public')
@section('title', 'Payment not completed — Mwanafunzi Investor')
@section('content')<section class="page-hero"><div class="container narrow"><p class="eyebrow">Payment not completed</p><h1>Your order is still recoverable.</h1><p class="lede">Order {{ $order->order_number }} was not confirmed. No successful payment is shown here.</p><a class="button button-dark" href="{{ route('account.orders.show', $order) }}">View order <span aria-hidden="true">↗</span></a></div></section>@endsection
