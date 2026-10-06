@extends('layouts.account')
@section('title', 'Orders & receipts — Mwanafunzi Investor')
@section('portal-heading', 'Orders & receipts')
@section('content')
<section class="student-page-heading"><div><p class="student-eyebrow">Workspace / Orders</p><h1>Purchases and receipts.</h1><p>A clear record of your Mwanafunzi Investor purchases.</p></div><a class="student-button student-button-secondary" href="{{ route('tools') }}">Explore products <span aria-hidden="true">↗</span></a></section>
<x-portal-card class="student-panel student-order-table"><div class="student-table-scroll"><div class="student-table-head"><span>Order reference</span><span>Date</span><span>Items</span><span>Total</span><span>Payment</span><span></span></div>@forelse($orders as $order)<a class="student-table-row" href="{{ route('account.orders.show', $order) }}"><strong>{{ $order->order_number }}</strong><span>{{ $order->created_at->format('M j, Y') }}</span><span>{{ $order->items_count }} item{{ $order->items_count === 1 ? '' : 's' }}</span><span>{{ app(\App\Services\MoneyFormatter::class)->format($order->total, $order->currency) }}</span><span><x-portal-status :status="$order->payment_status" /></span><b aria-hidden="true">↗</b></a>@empty<div class="student-wide-empty"><x-portal-empty icon="▣" title="No orders yet." description="Your purchases and receipts will appear here." :href="route('tools')" action="Explore products" /></div>@endforelse</div></x-portal-card>
<div class="student-pagination">{{ $orders->links() }}</div>
@endsection
