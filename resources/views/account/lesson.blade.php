@extends('layouts.account')
@section('title', $lesson->title.' — '.$enrollment->course->title)
@section('portal-heading', 'Lesson')
@php
    $course = $enrollment->course;
    $completedCount = $enrollment->completedLessonCount();
    $totalCount = $enrollment->totalPublishedLessons();
@endphp
@section('content')
<section class="student-detail-heading">
    <a class="student-back-link" href="{{ route('account.courses.show', $enrollment) }}">← {{ $course->title }}</a>
    <div class="student-detail-heading-grid">
        <div><p class="student-eyebrow">Lesson / {{ $course->title }}</p><h1>{{ $lesson->title }}</h1><p>{{ $completedCount }} of {{ $totalCount }} lessons complete.</p></div>
        @if($isComplete)<x-portal-status status="completed" label="Completed" />@endif
    </div>
</section>

<div class="student-lesson-layout">
    <article class="student-panel student-lesson-content">
        @if($lesson->content)
            {!! \App\Support\RichText::render($lesson->content) !!}
        @else
            <div class="student-empty"><span class="student-empty-icon" aria-hidden="true">○</span><div><h2>Lesson content is being prepared.</h2><p>This lesson is visible in the curriculum, but its learning material has not been published yet.</p></div></div>
        @endif
        <div class="student-lesson-actions">
            @if($previousLesson)<a class="student-button student-button-secondary" href="{{ route('account.courses.lessons.show', [$enrollment, $previousLesson]) }}">← Previous</a>@endif
            @if(!$isComplete && $lesson->content)
                <form method="post" action="{{ route('account.courses.lessons.complete', [$enrollment, $lesson]) }}">@csrf<button class="student-button student-button-primary" type="submit">Mark lesson complete <span aria-hidden="true">✓</span></button></form>
            @elseif($isComplete)
                <span class="student-lesson-complete-note">Lesson complete <span aria-hidden="true">✓</span></span>
            @endif
            @if($nextLesson)<a class="student-button student-button-dark" href="{{ route('account.courses.lessons.show', [$enrollment, $nextLesson]) }}">Next lesson →</a>@endif
        </div>
    </article>

    <aside class="student-lesson-aside">
        <x-portal-card class="student-panel student-panel-soft">
            <p class="student-eyebrow">Course progress</p>
            <strong class="student-progress-value">{{ $enrollment->progressPercent() }}%</strong>
            <div class="student-progress-track" role="progressbar" aria-label="Course progress" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $enrollment->progressPercent() }}"><span style="width:{{ $enrollment->progressPercent() }}%"></span></div>
            <a class="student-text-link" href="{{ route('account.courses.show', $enrollment) }}">View curriculum <span aria-hidden="true">↗</span></a>
        </x-portal-card>
    </aside>
</div>
@endsection
