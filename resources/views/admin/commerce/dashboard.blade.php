@extends('layouts.admin')
@section('title', 'Commerce dashboard')
@section('content')
<x-admin.page-header eyebrow="Commerce / Overview" title="Commerce operations" description="Confirmed payment totals only. Pending and failed attempts are not revenue." />
<div class="admin-stats">
    <x-admin.stat-card label="Orders today" :value="$ordersToday" description="New records today" />
    <x-admin.stat-card label="Paid orders" :value="$paidOrders" description="Confirmed payments" tone="ink" />
    <x-admin.stat-card label="Pending payments" :value="$pendingPayments" description="Need verification" tone="gold" />
    <x-admin.stat-card label="Confirmed revenue" :value="app(\App\Services\MoneyFormatter::class)->format($revenue, config('commerce.currency'))" description="Confirmed only" />
    <x-admin.stat-card label="Active enrollments" :value="$enrollments" description="Current course access" tone="ink" />
    <x-admin.stat-card label="Waitlist" :value="$waitlists" description="Student interest" tone="gold" />
</div>
<div class="admin-quick-grid">
    <x-admin.card title="Orders" subtitle="Review historical order and payment records."><a class="button button-secondary button-small" href="{{ route('admin.commerce.orders') }}">Open orders <span aria-hidden="true">↗</span></a></x-admin.card>
    <x-admin.card title="Payments" subtitle="Inspect provider and verification state."><a class="button button-secondary button-small" href="{{ route('admin.commerce.payments') }}">Open payments <span aria-hidden="true">↗</span></a></x-admin.card>
    <x-admin.card title="Entitlements" subtitle="Review active and revoked access."><a class="button button-secondary button-small" href="{{ route('admin.commerce.entitlements') }}">Open entitlements <span aria-hidden="true">↗</span></a></x-admin.card>
    <x-admin.card title="Enrollments" subtitle="Review course access."><a class="button button-secondary button-small" href="{{ route('admin.commerce.enrollments') }}">Open enrollments <span aria-hidden="true">↗</span></a></x-admin.card>
    <x-admin.card title="Waitlists" subtitle="Export real course interest records."><a class="button button-secondary button-small" href="{{ route('admin.commerce.waitlists') }}">Open waitlists <span aria-hidden="true">↗</span></a></x-admin.card>
</div>
@endsection
