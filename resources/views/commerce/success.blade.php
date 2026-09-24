@extends('layouts.public')
@section('title', 'Payment confirmed — Mwanafunzi Investor')
@section('content')<section class="page-hero"><div class="container narrow"><p class="eyebrow">Payment confirmed</p><h1>Thank you. Your access is ready.</h1><p class="lede">Order {{ $order->order_number }} · {{ app(\App\Services\MoneyFormatter::class)->format($order->total, $order->currency) }}</p><a class="button button-dark" href="{{ route('account.dashboard') }}">Open your account <span aria-hidden="true">↗</span></a></div></section>@endsection
