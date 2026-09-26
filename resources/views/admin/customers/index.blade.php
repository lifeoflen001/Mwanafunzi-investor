@extends('layouts.admin')
@section('title', 'Customers')
@section('portal-heading', 'Customers')
@section('content')
<x-admin.page-header eyebrow="Students / Customers" title="Customer directory" description="Review account state, learning access, purchases and downloads without exposing credentials." />
<x-admin.filter-toolbar method="get" action="{{ route('admin.customers') }}">
    <label class="sr-only" for="customer-search">Search customers</label><input id="customer-search" name="q" value="{{ $term }}" placeholder="Search name, email or phone">
    <select name="status" aria-label="Filter customers by status"><option value="">All statuses</option><option value="active" @selected(request('status') === 'active')>Active</option><option value="suspended" @selected(request('status') === 'suspended')>Suspended</option></select>
    <button class="button button-secondary button-small" type="submit">Filter</button>@if($term || request('status'))<a class="admin-back-link" href="{{ route('admin.customers') }}">Clear</a>@endif
</x-admin.filter-toolbar>
<x-admin.card class="admin-table-card"><div class="admin-table"><div class="admin-table-head"><span>Customer</span><span>Status</span><span>Orders</span><span>Courses</span><span>Access</span><span></span></div>
    @forelse($customers as $customer)
        <div class="admin-table-row"><span><strong>{{ $customer->name }}</strong><small>{{ $customer->email }}@if($customer->phone) · {{ $customer->phone }}@endif</small></span><x-admin.status-badge :status="$customer->status ?: 'active'" /><span>{{ $customer->orders_count }}</span><span>{{ $customer->enrollments_count }}</span><span>{{ $customer->entitlements_count }}</span><x-admin.action-menu><a href="{{ route('admin.customers.show', $customer) }}">View customer</a></x-admin.action-menu></div>
    @empty
        <div class="empty-state compact"><p>No customers match this filter.</p></div>
    @endforelse
</div></x-admin.card>
{{ $customers->links() }}
@endsection
