@extends('layouts.admin')
@section('title', 'Commerce entitlements')
@section('content')
<x-admin.page-header eyebrow="Commerce / Access" title="Entitlements" description="Review access records for products and courses." />
<x-admin.card class="admin-table-card"><div class="admin-table"><div class="admin-table-head"><span>Customer</span><span>Resource</span><span>Order</span><span>Status</span></div>@forelse($entitlements as $entitlement)<div class="admin-table-row"><span>{{ $entitlement->user?->email ?: $entitlement->customer_email }}</span><span>{{ $entitlement->product?->name ?: $entitlement->course?->title }}</span><span>{{ $entitlement->order?->order_number ?: '—' }}</span><x-admin.status-badge :status="$entitlement->status->value" /></div>@empty<x-admin.empty-state title="No entitlements yet." />@endforelse</div></x-admin.card>
{{ $entitlements->links() }}
@endsection
