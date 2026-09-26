@extends('layouts.admin')

@section('title', 'Customer · '.$customer->name)
@section('portal-heading', 'Customers')

@section('content')
    <x-admin.page-header eyebrow="Students / Customers" title="{{ $customer->name }}" description="{{ $customer->email }} · Joined {{ $customer->created_at?->format('M j, Y') }}" />
    <a class="admin-back-link" href="{{ route('admin.customers') }}">← Back to customers</a>

    <div class="admin-stats">
        <div><span>Orders</span><strong>{{ $customer->orders_count }}</strong></div>
        <div><span>Course access</span><strong>{{ $customer->enrollments_count }}</strong></div>
        <div><span>Entitlements</span><strong>{{ $customer->entitlements_count }}</strong></div>
        <div><span>Downloads</span><strong>{{ $downloadCount }}</strong></div>
    </div>

    <section class="admin-card admin-subresource">
        <header class="admin-card-header"><div><p class="admin-eyebrow">Profile</p><h2>Account details</h2></div></header>
        <dl class="admin-definition-list">
            <div><dt>Email</dt><dd><a href="mailto:{{ $customer->email }}">{{ $customer->email }}</a></dd></div>
            <div><dt>Phone</dt><dd>{{ $customer->phone ?: 'Not provided' }}</dd></div>
            <div><dt>Country</dt><dd>{{ $customer->country ?: 'Not provided' }}</dd></div>
            <div><dt>Email verified</dt><dd>{{ $customer->email_verified_at?->format('M j, Y') ?: 'Not verified' }}</dd></div>
            <div><dt>Last sign in</dt><dd>{{ $customer->last_login_at?->format('M j, Y H:i') ?: 'Not recorded' }}</dd></div>
        </dl>
    </section>

    <section class="admin-card admin-subresource">
        <header class="admin-card-header">
            <div>
                <p class="eyebrow">Account control</p>
                <h2>Account status</h2>
                <p>Suspending prevents normal customer authentication while retaining historical records.</p>
            </div>
        </header>
        <form class="admin-filter-toolbar" method="post" action="{{ route('admin.customers.status', $customer) }}">
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
            <header class="admin-card-header"><div><p class="admin-eyebrow">Purchase history</p><h2>Orders</h2></div></header>
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
            <header class="admin-card-header"><div><p class="admin-eyebrow">Learning access</p><h2>Enrollments</h2></div></header>
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
        <header class="admin-card-header"><div><p class="admin-eyebrow">Owned access</p><h2>Entitlements and waitlists</h2></div></header>
        <div class="admin-table">
            <div class="admin-table-head"><span>Resource</span><span>Type</span><span>Status</span></div>
            @forelse($customer->entitlements as $entitlement)
                <div class="admin-table-row"><span>{{ $entitlement->product?->name ?: $entitlement->course?->title ?: 'Resource removed' }}</span><span>{{ $entitlement->type }}</span><span>{{ $entitlement->status->value ?? $entitlement->status }} · {{ $entitlement->downloads->count() }} downloads</span></div>
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
