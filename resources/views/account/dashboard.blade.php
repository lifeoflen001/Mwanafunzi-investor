@extends('layouts.account')
@section('title', 'Overview — Mwanafunzi Investor')
@section('portal-heading', 'Overview')
@section('content')
<section class="student-dashboard-hero">
    <div>
        <p class="student-eyebrow">Student portal / Overview</p>
        <h1>{{ $isNewCustomer ? 'Welcome, '.$user->name.'.' : 'Welcome back, '.$user->name.'.' }}</h1>
        <p>{{ $isNewCustomer ? 'Build your process with structured financial education and focused tools for deliberate practice.' : 'Pick up where you left off, review your access and keep the next useful action visible.' }}</p>
    </div>
    <div class="student-dashboard-hero-actions">
        @if($featuredEnrollment && $featuredNextLesson)
            <a class="student-button student-button-primary" href="{{ route('account.courses.lessons.show', [$featuredEnrollment, $featuredNextLesson]) }}">Continue learning <span aria-hidden="true">↗</span></a>
        @elseif($isNewCustomer)
            <a class="student-button student-button-primary" href="{{ route('courses') }}">Explore courses <span aria-hidden="true">↗</span></a>
        @else
            <a class="student-button student-button-primary" href="{{ route('account.courses') }}">Open my courses <span aria-hidden="true">↗</span></a>
        @endif
        <a class="student-button student-button-secondary" href="{{ $isNewCustomer ? route('tools') : route('account.downloads') }}">{{ $isNewCustomer ? 'Explore tools' : 'Open my tools' }} <span aria-hidden="true">↗</span></a>
    </div>
</section>

@foreach($alerts as $alert)
    <section class="student-alert student-alert-{{ $alert['tone'] }}" role="status">
        <span class="student-alert-icon" aria-hidden="true">{{ $alert['tone'] === 'info' ? 'i' : '!' }}</span>
        <div><strong>{{ $alert['title'] }}</strong><p>{{ $alert['body'] }}</p></div>
        <a href="{{ $alert['href'] }}">{{ $alert['action'] }} <span aria-hidden="true">↗</span></a>
    </section>
@endforeach

@if($isNewCustomer)
    @php($completedChecklist = collect($checklist)->where('complete', true)->count())
    <div class="student-dashboard-new-grid">
        <x-portal-card class="student-panel student-dashboard-checklist">
            <div class="student-panel-heading">
                <div><p class="student-eyebrow">Getting started</p><h2>Build a ready workspace.</h2></div>
                <strong class="student-dashboard-count">{{ $completedChecklist }}/{{ count($checklist) }}</strong>
            </div>
            <p class="student-dashboard-section-copy">A short path from account setup to your first useful learning action.</p>
            <ol class="student-checklist">
                @foreach($checklist as $item)
                    <li class="{{ $item['complete'] ? 'is-complete' : '' }}">
                        <span class="student-checklist-marker" aria-hidden="true">{{ $item['complete'] ? '✓' : str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                        <span><strong>{{ $item['label'] }}</strong><small>{{ $item['description'] }}</small></span>
                        <a href="{{ $item['href'] }}">{{ $item['action'] }} <span aria-hidden="true">↗</span></a>
                    </li>
                @endforeach
            </ol>
        </x-portal-card>

        <x-portal-card class="student-panel student-panel-soft student-dashboard-readiness">
            <p class="student-eyebrow">Account status</p>
            <h2>{{ $user->hasVerifiedEmail() ? 'Your account is ready to explore.' : 'One important step remains.' }}</h2>
            <p>{{ $user->hasVerifiedEmail() ? 'Your verified account can move through the public learning and tools catalogue.' : 'Email verification protects purchases and unlocks protected learning actions.' }}</p>
            <dl class="student-definition-list">
                <div><dt>Email</dt><dd><x-portal-status :status="$user->hasVerifiedEmail() ? 'verified' : 'pending'" :label="$user->hasVerifiedEmail() ? 'Verified' : 'Required'" /></dd></div>
                <div><dt>Profile</dt><dd>{{ $profileComplete ? 'Complete' : 'Needs details' }}</dd></div>
                <div><dt>Access</dt><dd>Nothing purchased yet</dd></div>
            </dl>
            @unless($user->hasVerifiedEmail())<a class="student-button student-button-secondary" href="{{ route('verification.notice') }}">Verify email <span aria-hidden="true">↗</span></a>@endunless
        </x-portal-card>
    </div>

    <section class="student-dashboard-section">
        <div class="student-dashboard-section-heading"><div><p class="student-eyebrow">Explore learning</p><h2>Start with the right course.</h2></div><a class="student-text-link" href="{{ route('courses') }}">View all courses <span aria-hidden="true">→</span></a></div>
        @if($catalogCourses->isNotEmpty())
            <div class="student-dashboard-catalog-grid">@foreach($catalogCourses as $course)<x-portal-dashboard-catalog-card :item="$course" kind="course" />@endforeach</div>
        @else
            <x-portal-card class="student-panel"><x-portal-empty icon="▤" title="Courses are being prepared." description="Published Financial Academy courses will appear here as they are made available." :href="route('courses')" action="Visit courses" /></x-portal-card>
        @endif
    </section>

    <section class="student-dashboard-section">
        <div class="student-dashboard-section-heading"><div><p class="student-eyebrow">Explore digital tools</p><h2>Make the process visible.</h2></div><a class="student-text-link" href="{{ route('tools') }}">View all tools <span aria-hidden="true">→</span></a></div>
        @if($catalogTools->isNotEmpty())
            <div class="student-dashboard-catalog-grid">@foreach($catalogTools as $tool)<x-portal-dashboard-catalog-card :item="$tool" kind="tool" />@endforeach</div>
        @else
            <x-portal-card class="student-panel"><x-portal-empty icon="↓" title="Tools are being prepared." description="Focused tools for journaling, simulation and risk planning will appear here." :href="route('tools')" action="Visit tools" /></x-portal-card>
        @endif
    </section>
@else
    <div class="student-metric-grid student-dashboard-stats">
        <x-portal-metric label="Enrolled courses" :value="$stats['enrolled']" description="Your active learning access" tone="teal" icon="▤" />
        <x-portal-metric label="In progress" :value="$stats['in_progress']" description="Courses still underway" tone="gold" icon="↗" />
        <x-portal-metric label="Completed courses" :value="$stats['completed']" description="Finished learning paths" tone="blue" icon="✓" />
        <x-portal-metric label="Completed lessons" :value="$stats['lessons']" description="Recorded in your workspace" tone="copper" icon="○" />
    </div>

    <div class="student-dashboard-returning-grid">
        <x-portal-card class="student-panel student-dashboard-featured-panel">
            <div class="student-panel-heading"><div><p class="student-eyebrow">Continue learning</p><h2>{{ $featuredEnrollment ? 'Your next useful lesson.' : 'Your learning access.' }}</h2></div><a class="student-text-link" href="{{ route('account.courses') }}">View all <span aria-hidden="true">→</span></a></div>
            @if($featuredEnrollment)
                <x-portal-dashboard-course :enrollment="$featuredEnrollment" :next-lesson="$featuredNextLesson" />
            @else
                <x-portal-empty icon="▤" title="No active course access yet." description="Your course access will appear here when a confirmed enrollment is available." :href="route('courses')" action="Explore courses" />
            @endif
        </x-portal-card>
        <x-portal-card class="student-panel student-panel-dark student-dashboard-next-panel">
            <div class="student-panel-heading"><div><p class="student-eyebrow">Recommended next action</p><h2>{{ $featuredNextLesson ? 'Resume the next lesson.' : 'Review your workspace.' }}</h2></div></div>
            <p class="student-dashboard-dark-copy">{{ $featuredNextLesson ? 'Your next unfinished lesson is ready. Keep the process moving one deliberate step at a time.' : 'Your available records, tools and account controls are collected here.' }}</p>
            <div class="student-quick-links">
                @if($featuredEnrollment && $featuredNextLesson)<a href="{{ route('account.courses.lessons.show', [$featuredEnrollment, $featuredNextLesson]) }}"><span class="student-quick-icon">↗</span><span><strong>Resume learning</strong><small>{{ $featuredNextLesson->title }}</small></span><b aria-hidden="true">→</b></a>@endif
                <a href="{{ route('account.profile') }}"><span class="student-quick-icon">○</span><span><strong>Profile details</strong><small>{{ $profileComplete ? 'Review your personal information' : 'Complete your account details' }}</small></span><b aria-hidden="true">→</b></a>
                <a href="{{ route('account.security') }}"><span class="student-quick-icon">◇</span><span><strong>Security</strong><small>Protect your account access</small></span><b aria-hidden="true">→</b></a>
            </div>
        </x-portal-card>
    </div>

    <section class="student-dashboard-section">
        <div class="student-dashboard-section-heading"><div><p class="student-eyebrow">My courses</p><h2>Learning access at a glance.</h2></div><a class="student-text-link" href="{{ route('account.courses') }}">View all courses <span aria-hidden="true">→</span></a></div>
        <div class="student-dashboard-course-grid">
            @foreach($enrollments->take(4) as $enrollment)
                @php($nextLesson = $enrollment->course->modules->flatMap(fn ($module) => $module->lessons->where('is_published', true))->first(fn ($lesson) => ! $enrollment->lessonCompletions->contains('course_lesson_id', $lesson->id)))
                <x-portal-dashboard-course :enrollment="$enrollment" :next-lesson="$nextLesson" />
            @endforeach
        </div>
    </section>

    <div class="student-dashboard-two-column">
        <section class="student-dashboard-section student-dashboard-section-compact">
            <div class="student-dashboard-section-heading"><div><p class="student-eyebrow">My digital tools</p><h2>Tools you can access.</h2></div><a class="student-text-link" href="{{ route('account.downloads') }}">Open library <span aria-hidden="true">→</span></a></div>
            <x-portal-card class="student-panel">
                @forelse($tools->take(4) as $entitlement)
                    <a class="student-list-row" href="{{ route('account.downloads') }}"><span class="student-list-icon student-list-icon-blue" aria-hidden="true">↓</span><span><strong>{{ $entitlement->product->name }}</strong><small>{{ $entitlement->product->assets->count() }} downloadable asset{{ $entitlement->product->assets->count() === 1 ? '' : 's' }} · {{ $entitlement->version?->version ?: $entitlement->product->version ?: 'Current' }}</small></span><span class="student-list-trailing"><x-portal-status :status="$entitlement->status" /><b aria-hidden="true">↗</b></span></a>
                @empty
                    <x-portal-empty icon="↓" title="No active tools yet." description="Confirmed product access will appear in your library." :href="route('tools')" action="Explore tools" />
                @endforelse
            </x-portal-card>
        </section>

        <section class="student-dashboard-section student-dashboard-section-compact">
            <div class="student-dashboard-section-heading"><div><p class="student-eyebrow">Recent purchases</p><h2>Orders and payment state.</h2></div><a class="student-text-link" href="{{ route('account.orders') }}">View all orders <span aria-hidden="true">→</span></a></div>
            <x-portal-card class="student-panel">
                @forelse($orders->take(4) as $order)
                    @php($itemNames = $order->items->pluck('title')->take(2)->join(', '))
                    <a class="student-list-row" href="{{ route('account.orders.show', $order) }}"><span class="student-list-icon student-list-icon-gold" aria-hidden="true">▣</span><span><strong>{{ $order->order_number }}</strong><small>{{ $itemNames ?: 'Order items' }} · {{ $order->created_at->format('M j, Y') }}</small></span><span class="student-list-trailing"><span><strong>{{ app(\App\Services\MoneyFormatter::class)->format($order->total, $order->currency) }}</strong><x-portal-status :status="$order->payment_status" /></span><b aria-hidden="true">↗</b></span></a>
                @empty
                    <x-portal-empty icon="▣" title="No purchase history yet." description="Confirmed orders and their payment state will appear here." :href="route('tools')" action="Explore products" />
                @endforelse
            </x-portal-card>
        </section>
    </div>
@endif
@endsection
