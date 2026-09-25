@extends('layouts.admin')

@section('title', 'Customer · '.$customer->name)
@section('portal-heading', 'Customers')

@section('content')
    <div class="admin-heading">
        <div>
            <p class="eyebrow">Students / Customers</p>
            <h1>{{ $customer->name }}</h1>
            <p>{{ $customer->email }} · Joined {{ $customer->created_at?->format('M j, Y') }}</p>
        </div>
        <a class="back-link" href="{{ route('admin.customers') }}">← Back to customers</a>
    </div>

    <div class="admin-stats">
        <div><span>Orders</span><strong>{{ $customer->orders_count }}</strong></div>
        <div><span>Course access</span><strong>{{ $customer->enrollments_count }}</strong></div>
        <div><span>Entitlements</span><strong>{{ $customer->entitlements_count }}</strong></div>
        <div><span>Waitlists</span><strong>{{ $customer->waitlists_count }}</strong></div>
    </div>

    <section class="admin-card admin-subresource">
        <div class="admin-heading">
            <div>
                <p class="eyebrow">Account control</p>
                <h2>Account status</h2>
                <p>Suspending prevents normal customer authentication while retaining historical records.</p>
            </div>
        </div>
        <form class="admin-filter-row" method="post" action="{{ route('admin.customers.status', $customer) }}">
            @csrf @method('patch')
            <select name="status" aria-label="Customer status">
                <option value="active" @selected(($customer->status ?: 'active') === 'active')>Active</option>
                <option value="suspended" @selected($customer->status === 'suspended')>Suspended</option>
            </select>
            <button class="button button-light" type="submit">Save status</button>
        </form>
    </section>

    <div class="admin-grid-two">
        <section class="admin-card admin-subresource">
            <div class="admin-heading"><div><p class="eyebrow">Purchase history</p><h2>Orders</h2></div></div>
            @forelse($customer->orders as $order)
                <a class="admin-subresource-header" href="{{ route('admin.commerce.orders.show', $order) }}">
                    <div><strong>{{ $order->order_number }}</strong><small>{{ $order->created_at?->format('M j, Y') }} · {{ $order->items_count }} items · {{ app(\App\Services\MoneyFormatter::class)->format($order->total, $order->currency) }}</small></div>
                    <span>{{ $order->payment_status->value ?? $order->payment_status }}</span>
                </a>
            @empty
                <div class="empty-state compact"><p>No orders yet.</p></div>
            @endforelse
        </section>

        <section class="admin-card admin-subresource">
            <div class="admin-heading"><div><p class="eyebrow">Learning access</p><h2>Enrollments</h2></div></div>
            @forelse($customer->enrollments as $enrollment)
                <div class="admin-subresource-header">
                    <div><strong>{{ $enrollment->course?->title ?: 'Course removed' }}</strong><small>{{ $enrollment->enrolled_at?->format('M j, Y') ?: 'Date unavailable' }}</small></div>
                    <span>{{ $enrollment->status->value ?? $enrollment->status }}</span>
                </div>
            @empty
                <div class="empty-state compact"><p>No enrollments yet.</p></div>
            @endforelse
        </section>
    </div>

    <section class="admin-card admin-subresource">
        <div class="admin-heading"><div><p class="eyebrow">Owned access</p><h2>Entitlements and waitlists</h2></div></div>
        <div class="admin-table">
            <div class="admin-table-head"><span>Resource</span><span>Type</span><span>Status</span></div>
            @forelse($customer->entitlements as $entitlement)
                <div class="admin-table-row"><span>{{ $entitlement->product?->name ?: $entitlement->course?->title ?: 'Resource removed' }}</span><span>{{ $entitlement->type }}</span><span>{{ $entitlement->status->value ?? $entitlement->status }}</span></div>
            @empty
                <div class="empty-state compact"><p>No entitlements yet.</p></div>
            @endforelse
        </div>
        @if($customer->waitlists->isNotEmpty())
            <h3 class="subresource-heading">Waitlists</h3>
            @foreach($customer->waitlists as $waitlist)
                <div class="admin-subresource-header"><div><strong>{{ $waitlist->course?->title ?: 'Course removed' }}</strong><small>{{ $waitlist->joined_at?->format('M j, Y') }}</small></div><span>{{ $waitlist->status }}</span></div>
            @endforeach
        @endif
    </section>
@endsection
