@extends('layouts.account')
@section('title', 'My courses — Mwanafunzi Investor')
@section('portal-heading', 'My courses')
@section('content')
<section class="student-page-heading"><div><p class="student-eyebrow">Workspace / My courses</p><h1>Your learning library.</h1><p>Open the courses you have access to and continue at your own pace.</p></div><a class="student-button student-button-primary" href="{{ route('courses') }}">Browse courses <span aria-hidden="true">↗</span></a></section>
<section class="student-library-grid">
@forelse($enrollments as $enrollment)
    @php
        $completedIds = $enrollment->lessonCompletions->pluck('course_lesson_id');
        $nextLesson = $enrollment->course->modules->flatMap(fn ($module) => $module->lessons->where('is_published', true))->first(fn ($lesson) => !$completedIds->contains($lesson->id));
    @endphp
    <a class="student-library-card" href="{{ $nextLesson ? route('account.courses.lessons.show', [$enrollment, $nextLesson]) : route('account.courses.show', $enrollment) }}">
        <div class="student-library-media">@if($enrollment->course->featured_image)<img src="{{ asset($enrollment->course->featured_image) }}" alt="">@else<span aria-hidden="true">▤</span>@endif</div>
        <div class="student-library-copy"><div class="student-card-meta"><x-portal-status :status="$enrollment->status" /><span>{{ $enrollment->course->level ?: 'Course' }}</span></div><h2>{{ $enrollment->course->title }}</h2><p>{{ $enrollment->course->short_description ?: 'Open the course workspace to view the curriculum.' }}</p><div class="student-card-progress"><span><small>Progress</small><strong>{{ $enrollment->progressPercent() }}%</strong></span><span class="student-progress-track"><span style="width:{{ $enrollment->progressPercent() }}%"></span></span></div><span class="student-text-link">{{ $nextLesson ? 'Continue learning' : ($enrollment->progressPercent() >= 100 ? 'Review course' : 'Open course') }} <span aria-hidden="true">↗</span></span></div>
    </a>
@empty
    <div class="student-wide-empty"><x-portal-empty class="student-empty-hero" icon="▤" title="Your learning journey starts here." description="Browse the learning library to find a course that fits the process you want to build. Your real enrollments and progress will appear in this workspace." :href="route('courses')" action="Browse courses" /></div>
@endforelse
</section>
<div class="student-pagination">{{ $enrollments->links() }}</div>
@endsection
