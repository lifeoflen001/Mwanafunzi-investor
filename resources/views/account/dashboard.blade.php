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

@if(!$hasActivity)
<section class="student-onboarding-state student-panel">
    <div class="student-onboarding-copy"><p class="student-eyebrow">Your next step</p><h2>Start with one deliberate action.</h2><p>Your workspace is ready. Browse the learning library, explore the tools, or finish your profile so the account is ready when you are.</p></div>
    <div class="student-onboarding-actions">
        <a class="student-button student-button-primary" href="{{ route('courses') }}">Browse courses <span aria-hidden="true">↗</span></a>
        <a class="student-button student-button-secondary" href="{{ route('tools') }}">Explore tools <span aria-hidden="true">↗</span></a>
        <a class="student-text-link" href="{{ route('account.profile') }}">Complete profile <span aria-hidden="true">→</span></a>
    </div>
</section>
@endif

@if($hasActivity)
<div class="student-metric-grid">
    <x-portal-metric label="Courses" :value="$courseCount" description="Your learning access" tone="teal" icon="▤" />
    <x-portal-metric label="In progress" :value="$inProgressCount" description="Courses underway" tone="gold" icon="↗" />
    <x-portal-metric label="Completed" :value="$completedCourseCount" description="Finished courses" tone="blue" icon="✓" />
    <x-portal-metric label="Account status" :value="auth()->user()->hasVerifiedEmail() ? 'Ready' : 'Action'" :description="auth()->user()->hasVerifiedEmail() ? 'Workspace active' : 'Verify your email'" tone="copper" icon="○" />
</div>

<div class="student-dashboard-grid">
    <x-portal-card class="student-panel student-panel-wide">
        <div class="student-panel-heading"><div><p class="student-eyebrow">Course access</p><h2>Continue learning</h2></div><a class="student-text-link" href="{{ route('account.courses') }}">View all <span aria-hidden="true">→</span></a></div>
        @forelse($enrollments as $enrollment)
            @php($nextLesson = $enrollment->course->modules->flatMap(fn ($module) => $module->lessons->where('is_published', true))->first(fn ($lesson) => !$enrollment->lessonCompletions->contains('course_lesson_id', $lesson->id)))
            <a class="student-list-row" href="{{ $nextLesson ? route('account.courses.lessons.show', [$enrollment, $nextLesson]) : route('account.courses.show', $enrollment) }}"><span class="student-list-icon student-list-icon-teal" aria-hidden="true">▤</span><span><strong>{{ $enrollment->course->title }}</strong><small>{{ $enrollment->progressPercent() }}% complete · {{ $nextLesson ? 'Resume the next lesson' : ($enrollment->progressPercent() >= 100 ? 'Course complete — review the curriculum' : ($enrollment->course->short_description ?: 'Open your course workspace and curriculum.')) }}</small></span><span class="student-list-trailing"><x-portal-status :status="$enrollment->status" /><b aria-hidden="true">↗</b></span></a>
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
@else
<div class="student-dashboard-grid student-dashboard-empty-grid">
    <x-portal-card class="student-panel">
        <div class="student-panel-heading"><div><p class="student-eyebrow">Learning overview</p><h2>Courses</h2></div><a class="student-text-link" href="{{ route('courses') }}">Browse <span aria-hidden="true">↗</span></a></div>
        <x-portal-empty icon="▤" title="No course access yet." description="Browse the Financial Academy when you are ready to start learning." :href="route('courses')" action="Browse courses" />
    </x-portal-card>
    <x-portal-card class="student-panel">
        <div class="student-panel-heading"><div><p class="student-eyebrow">Digital tools</p><h2>Your library</h2></div><a class="student-text-link" href="{{ route('tools') }}">Explore <span aria-hidden="true">↗</span></a></div>
        <x-portal-empty icon="↓" title="No tools yet." description="Purchased tools and secure downloads will appear here." :href="route('tools')" action="Explore tools" />
    </x-portal-card>
    <x-portal-card class="student-panel">
        <div class="student-panel-heading"><div><p class="student-eyebrow">Recent purchases</p><h2>Orders &amp; receipts</h2></div><a class="student-text-link" href="{{ route('account.orders') }}">View orders <span aria-hidden="true">↗</span></a></div>
        <x-portal-empty icon="▣" title="No purchases yet." description="Confirmed orders and receipts will be listed here." :href="route('tools')" action="Explore products" />
    </x-portal-card>
    <x-portal-card class="student-panel student-panel-dark">
        <div class="student-panel-heading"><div><p class="student-eyebrow">Recommended next action</p><h2>Keep your account ready.</h2></div></div>
        <div class="student-quick-links"><a href="{{ auth()->user()->hasVerifiedEmail() ? route('account.profile') : route('verification.notice') }}"><span class="student-quick-icon">{{ auth()->user()->hasVerifiedEmail() ? '○' : '!' }}</span><span><strong>{{ auth()->user()->hasVerifiedEmail() ? 'Complete your profile' : 'Verify your email' }}</strong><small>{{ auth()->user()->hasVerifiedEmail() ? 'Keep your account details current' : 'Unlock protected account actions' }}</small></span><b aria-hidden="true">→</b></a><a href="{{ route('contact') }}"><span class="student-quick-icon">↗</span><span><strong>Contact the desk</strong><small>Get help when you need it</small></span><b aria-hidden="true">→</b></a></div>
    </x-portal-card>
</div>
@endif
@endsection
