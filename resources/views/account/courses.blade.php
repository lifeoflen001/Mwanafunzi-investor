@extends('layouts.account')
@section('title', 'My courses — Mwanafunzi Investor')
@section('portal-heading', 'My courses')
@section('content')
<section class="student-page-heading"><div><p class="student-eyebrow">Workspace / My courses</p><h1>Your learning library.</h1><p>Open the courses you have access to and continue at your own pace.</p></div><a class="student-button student-button-primary" href="{{ route('courses') }}">Browse courses <span aria-hidden="true">↗</span></a></section>
<section class="student-library-grid">
@forelse($enrollments as $enrollment)
    <a class="student-library-card" href="{{ route('account.courses.show', $enrollment) }}">
        <div class="student-library-media">@if($enrollment->course->featured_image)<img src="{{ asset($enrollment->course->featured_image) }}" alt="">@else<span aria-hidden="true">▤</span>@endif</div>
        <div class="student-library-copy"><div class="student-card-meta"><x-portal-status :status="$enrollment->status" /><span>{{ $enrollment->course->level ?: 'Course' }}</span></div><h2>{{ $enrollment->course->title }}</h2><p>{{ $enrollment->course->short_description ?: 'Open the course workspace to view the curriculum.' }}</p><span class="student-text-link">Open course <span aria-hidden="true">↗</span></span></div>
    </a>
@empty
    <div class="student-wide-empty"><x-portal-empty icon="▤" title="No courses yet." description="When you enroll, your learning will appear here." :href="route('courses')" action="Explore courses" /></div>
@endforelse
</section>
<div class="student-pagination">{{ $enrollments->links() }}</div>
@endsection
