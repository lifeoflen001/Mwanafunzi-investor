@extends('layouts.admin')
@section('title', 'Customers')
@section('portal-heading', 'Customers')
@section('content')
<div class="admin-heading"><div><p class="eyebrow">Students / Customers</p><h1>Customer directory</h1><p>Review account state, learning access, purchases and downloads without exposing credentials.</p></div></div>
<form class="admin-filter-row" method="get" action="{{ route('admin.customers') }}">
    <label class="sr-only" for="customer-search">Search customers</label><input id="customer-search" name="q" value="{{ $term }}" placeholder="Search name, email or phone">
    <select name="status" aria-label="Filter customers by status"><option value="">All statuses</option><option value="active" @selected(request('status') === 'active')>Active</option><option value="suspended" @selected(request('status') === 'suspended')>Suspended</option></select>
    <button class="button button-light" type="submit">Filter</button>@if($term || request('status'))<a class="back-link" href="{{ route('admin.customers') }}">Clear</a>@endif
</form>
<div class="admin-table"><div class="admin-table-head"><span>Customer</span><span>Status</span><span>Orders</span><span>Courses</span><span>Access</span><span></span></div>
    @forelse($customers as $customer)
        <div class="admin-table-row"><span><strong>{{ $customer->name }}</strong><small>{{ $customer->email }}@if($customer->phone) · {{ $customer->phone }}@endif</small></span><span class="status-badge">{{ $customer->status ?: 'active' }}</span><span>{{ $customer->orders_count }}</span><span>{{ $customer->enrollments_count }}</span><span>{{ $customer->entitlements_count }}</span><span class="admin-actions"><a href="{{ route('admin.customers.show', $customer) }}">View <span aria-hidden="true">↗</span></a></span></div>
    @empty
        <div class="empty-state compact"><p>No customers match this filter.</p></div>
    @endforelse
</div>
{{ $customers->links() }}
@endsection
