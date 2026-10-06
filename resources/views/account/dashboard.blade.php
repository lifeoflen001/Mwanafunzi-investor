@extends('layouts.account')
@section('title', 'Overview — Mwanafunzi Investor')
@section('portal-heading', 'Overview')
@section('content')
<section class="student-page-heading">
    <div><p class="student-eyebrow">Student portal / Overview</p><h1>Welcome back, {{ auth()->user()->name }}.</h1><p>Continue learning, access your tools and manage your purchases from one calm workspace.</p></div>
    <div class="student-heading-actions"><a class="student-button student-button-secondary" href="{{ route('account.profile') }}">Edit profile <span aria-hidden="true">↗</span></a><a class="student-button student-button-primary" href="{{ route('home') }}" target="_blank" rel="noopener">Explore platform <span aria-hidden="true">↗</span></a></div>
</section>

@unless(auth()->user()->hasVerifiedEmail())
<section class="student-alert student-alert-warning" role="status"><span class="student-alert-icon" aria-hidden="true">!</span><div><strong>Email verification required</strong><p>Verify your email to complete purchases and access protected learning.</p></div><a href="{{ route('verification.notice') }}">Verify email <span aria-hidden="true">↗</span></a></section>
@else
<section class="student-alert student-alert-success" role="status"><span class="student-alert-icon" aria-hidden="true">✓</span><div><strong>Your account is ready.</strong><p>Your verified account can access purchases, courses and downloads.</p></div><a href="{{ route('account.security') }}">Review security <span aria-hidden="true">↗</span></a></section>
@endunless

<div class="student-metric-grid">
    <x-portal-metric label="Active courses" :value="$courseCount" description="Your learning access" tone="teal" icon="▤" />
    <x-portal-metric label="Available tools" :value="$downloadCount" description="Your digital library" tone="gold" icon="↓" />
    <x-portal-metric label="Orders" :value="$orderCount" description="Purchases and receipts" tone="blue" icon="▣" />
    <x-portal-metric label="Account status" :value="auth()->user()->hasVerifiedEmail() ? 'Ready' : 'Action'" :description="auth()->user()->hasVerifiedEmail() ? 'Workspace active' : 'Verify your email'" tone="copper" icon="○" />
</div>

<div class="student-dashboard-grid">
    <x-portal-card class="student-panel student-panel-wide">
        <div class="student-panel-heading"><div><p class="student-eyebrow">Course access</p><h2>Continue learning</h2></div><a class="student-text-link" href="{{ route('account.courses') }}">View all <span aria-hidden="true">→</span></a></div>
        @forelse($enrollments as $enrollment)
            <a class="student-list-row" href="{{ route('account.courses.show', $enrollment) }}"><span class="student-list-icon student-list-icon-teal" aria-hidden="true">▤</span><span><strong>{{ $enrollment->course->title }}</strong><small>{{ $enrollment->course->short_description ?: 'Open your course workspace and curriculum.' }}</small></span><span class="student-list-trailing"><x-portal-status :status="$enrollment->status" /><b aria-hidden="true">↗</b></span></a>
        @empty
            <x-portal-empty icon="▤" title="No active courses yet." description="When you enroll, your learning will appear here." :href="route('courses')" action="Browse courses" />
        @endforelse
    </x-portal-card>

    <x-portal-card class="student-panel">
        <div class="student-panel-heading"><div><p class="student-eyebrow">Purchase history</p><h2>Recent orders</h2></div><a class="student-text-link" href="{{ route('account.orders') }}">View all <span aria-hidden="true">→</span></a></div>
        @forelse($orders as $order)
            <a class="student-list-row" href="{{ route('account.orders.show', $order) }}"><span class="student-list-icon student-list-icon-gold" aria-hidden="true">▣</span><span><strong>{{ $order->order_number }}</strong><small>{{ $order->created_at->format('M j, Y') }} · {{ $order->items_count }} item{{ $order->items_count === 1 ? '' : 's' }}</small></span><span class="student-list-trailing"><strong>{{ app(\App\Services\MoneyFormatter::class)->format($order->total, $order->currency) }}</strong><b aria-hidden="true">↗</b></span></a>
        @empty
            <x-portal-empty icon="▣" title="No orders yet." description="Your purchases and receipts will appear here." :href="route('tools')" action="Explore tools" />
        @endforelse
    </x-portal-card>

    <x-portal-card class="student-panel">
        <div class="student-panel-heading"><div><p class="student-eyebrow">Tool access</p><h2>Your downloads</h2></div><a class="student-text-link" href="{{ route('account.downloads') }}">View all <span aria-hidden="true">→</span></a></div>
        @forelse($downloads->take(4) as $entitlement)
            <a class="student-list-row" href="{{ route('account.downloads') }}"><span class="student-list-icon student-list-icon-blue" aria-hidden="true">↓</span><span><strong>{{ $entitlement->product->name }}</strong><small>{{ $entitlement->product->assets->count() }} downloadable asset{{ $entitlement->product->assets->count() === 1 ? '' : 's' }}</small></span><span class="student-list-trailing"><x-portal-status :status="$entitlement->status" /><b aria-hidden="true">↗</b></span></a>
        @empty
            <x-portal-empty icon="↓" title="Your tool library is empty." description="Purchased and free tools will appear here." :href="route('tools')" action="Explore tools" />
        @endforelse
    </x-portal-card>

    <x-portal-card class="student-panel student-panel-dark">
        <div class="student-panel-heading"><div><p class="student-eyebrow">Account controls</p><h2>Keep your workspace in order.</h2></div></div>
        <div class="student-quick-links"><a href="{{ route('account.profile') }}"><span class="student-quick-icon">○</span><span><strong>Profile details</strong><small>Update your personal information</small></span><b aria-hidden="true">→</b></a><a href="{{ route('account.security') }}"><span class="student-quick-icon">◇</span><span><strong>Security</strong><small>Protect your account access</small></span><b aria-hidden="true">→</b></a><a href="{{ route('contact') }}"><span class="student-quick-icon">↗</span><span><strong>Contact the desk</strong><small>Get help with the platform</small></span><b aria-hidden="true">→</b></a></div>
    </x-portal-card>
</div>
@endsection
