@extends('layouts.admin')
@section('title', 'Commerce dashboard')
@section('content')
<x-admin.page-header eyebrow="Commerce / Overview" title="Commerce operations" description="Confirmed payment totals only. Pending and failed attempts are not revenue." />
<div class="admin-stats">
    <div><span>Orders today</span><strong>{{ $ordersToday }}</strong></div>
    <div><span>Paid orders</span><strong>{{ $paidOrders }}</strong></div>
    <div><span>Pending payments</span><strong>{{ $pendingPayments }}</strong></div>
    <div><span>Confirmed revenue</span><strong>{{ app(\App\Services\MoneyFormatter::class)->format($revenue, config('commerce.currency')) }}</strong></div>
    <div><span>Active enrollments</span><strong>{{ $enrollments }}</strong></div>
    <div><span>Waitlist</span><strong>{{ $waitlists }}</strong></div>
</div>
<div class="admin-quick-grid">
    <x-admin.card title="Orders" subtitle="Review historical order and payment records."><a class="button button-secondary button-small" href="{{ route('admin.commerce.orders') }}">Open orders <span aria-hidden="true">↗</span></a></x-admin.card>
    <x-admin.card title="Payments" subtitle="Inspect provider and verification state."><a class="button button-secondary button-small" href="{{ route('admin.commerce.payments') }}">Open payments <span aria-hidden="true">↗</span></a></x-admin.card>
    <x-admin.card title="Entitlements" subtitle="Review active and revoked access."><a class="button button-secondary button-small" href="{{ route('admin.commerce.entitlements') }}">Open entitlements <span aria-hidden="true">↗</span></a></x-admin.card>
    <x-admin.card title="Enrollments" subtitle="Review course access."><a class="button button-secondary button-small" href="{{ route('admin.commerce.enrollments') }}">Open enrollments <span aria-hidden="true">↗</span></a></x-admin.card>
    <x-admin.card title="Waitlists" subtitle="Export real course interest records."><a class="button button-secondary button-small" href="{{ route('admin.commerce.waitlists') }}">Open waitlists <span aria-hidden="true">↗</span></a></x-admin.card>
</div>
@endsection
