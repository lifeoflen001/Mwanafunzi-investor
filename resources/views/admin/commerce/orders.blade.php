@extends('layouts.admin')
@section('title', 'Commerce orders')
@section('content')
<x-admin.page-header eyebrow="Commerce / Orders" title="Orders" description="Search order records, payment state, and customer history." />
<x-admin.filter-toolbar method="get">
    <input name="q" value="{{ request('q') }}" placeholder="Search order or customer" aria-label="Search orders">
    <select name="status" aria-label="Filter orders by status"><option value="">All statuses</option>@foreach(['pending_payment','processing','paid','completed','cancelled','refunded','partially_refunded'] as $status)<option value="{{ $status }}" @selected(request('status') === $status)>{{ str_replace('_', ' ', $status) }}</option>@endforeach</select>
    <button class="button button-secondary button-small" type="submit">Filter</button>
</x-admin.filter-toolbar>
<x-admin.card class="admin-table-card">
<div class="admin-table"><div class="admin-table-head"><span>Order</span><span>Customer</span><span>Total</span><span>Status</span><span></span></div>
@forelse($orders as $order)
<div class="admin-table-row"><a href="{{ route('admin.commerce.orders.show', $order) }}"><strong>{{ $order->order_number }}</strong><small>{{ $order->items_count }} item(s)</small></a><span>{{ $order->customer_name }}<small>{{ $order->customer_email }}</small></span><strong>{{ app(\App\Services\MoneyFormatter::class)->format($order->total, $order->currency) }}</strong><span><x-admin.status-badge :status="$order->status->value" /><small>{{ str_replace('_', ' ', $order->payment_status->value) }}</small></span><x-admin.action-menu><a href="{{ route('admin.commerce.orders.show', $order) }}">View order</a></x-admin.action-menu></div>
@empty<x-admin.empty-state title="No orders yet." description="Orders will appear here after checkout activity." />@endforelse
</div></x-admin.card>
{{ $orders->links() }}
@endsection
