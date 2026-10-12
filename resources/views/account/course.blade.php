@extends('layouts.account')
@section('title', $enrollment->course->title.' — Mwanafunzi Investor')
@section('portal-heading', 'Course workspace')
@php
    $course = $enrollment->course;
    $completedIds = $enrollment->lessonCompletions->pluck('course_lesson_id');
    $publishedLessonCount = $enrollment->totalPublishedLessons();
    $completedLessonCount = $enrollment->completedLessonCount();
@endphp
@section('content')
<section class="student-detail-heading">
    <a class="student-back-link" href="{{ route('account.courses') }}">← My courses</a>
    <div class="student-detail-heading-grid">
        <div>
            <p class="student-eyebrow">Course workspace / {{ str($enrollment->status->value)->replace('_', ' ')->title() }}</p>
            <h1>{{ $course->title }}</h1>
            <p>{{ $course->short_description }}</p>
        </div>
        <x-portal-status :status="$enrollment->status" />
    </div>
</section>

<div class="student-course-layout">
    <main>
        <x-portal-card class="student-panel student-course-progress-panel">
            <div class="student-panel-heading">
                <div><p class="student-eyebrow">Your progress</p><h2>Keep moving at your pace.</h2></div>
                <strong class="student-progress-value">{{ $enrollment->progressPercent() }}%</strong>
            </div>
            <div class="student-progress-track" role="progressbar" aria-label="Course progress" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $enrollment->progressPercent() }}"><span style="width:{{ $enrollment->progressPercent() }}%"></span></div>
            <p class="student-progress-copy">{{ $completedLessonCount }} of {{ $publishedLessonCount }} published lesson{{ $publishedLessonCount === 1 ? '' : 's' }} complete.</p>
        </x-portal-card>

        <x-portal-card class="student-panel">
            <div class="student-panel-heading"><div><p class="student-eyebrow">About this course</p><h2>Build the next useful skill.</h2></div></div>
            <div class="student-rich-copy">{!! \App\Support\RichText::render($course->full_description ?: $course->short_description) !!}</div>
        </x-portal-card>

        <x-portal-card class="student-panel">
            <div class="student-panel-heading"><div><p class="student-eyebrow">Curriculum</p><h2>Modules and lessons</h2></div></div>
            <div class="student-module-list">
                @forelse($course->modules as $module)
                    @php($publishedLessons = $module->lessons->where('is_published', true))
                    <details {{ $loop->first ? 'open' : '' }}>
                        <summary><span><b>{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</b>{{ $module->title }}</span><span aria-hidden="true">+</span></summary>
                        <div class="student-module-body">
                            @if($module->description)<p>{{ $module->description }}</p>@endif
                            @forelse($publishedLessons as $lesson)
                                @php($isComplete = $completedIds->contains($lesson->id))
                                <a class="student-lesson-row {{ $isComplete ? 'is-complete' : '' }}" href="{{ route('account.courses.lessons.show', [$enrollment, $lesson]) }}">
                                    <span aria-hidden="true">{{ $isComplete ? '✓' : '○' }}</span>
                                    <span>{{ $lesson->title }}</span>
                                    <small>{{ $isComplete ? 'Complete' : 'Open lesson' }} <span aria-hidden="true">↗</span></small>
                                </a>
                            @empty
                                <p class="student-muted">Lessons are being prepared.</p>
                            @endforelse
                        </div>
                    </details>
                @empty
                    <p class="student-muted">The curriculum is being prepared.</p>
                @endforelse
            </div>
        </x-portal-card>
    </main>

    <aside class="student-course-aside">
        <x-portal-card class="student-panel student-panel-soft">
            <p class="student-eyebrow">Access</p>
            <h2>{{ str($enrollment->status->value)->replace('_', ' ')->title() }}</h2>
            <p>Enrolled {{ $enrollment->enrolled_at?->format('M j, Y') ?: 'recently' }}.</p>
            <a class="student-button student-button-secondary" href="{{ route('contact') }}">Need help? <span aria-hidden="true">↗</span></a>
        </x-portal-card>
        <x-portal-card class="student-panel student-panel-soft">
            <p class="student-eyebrow">Course details</p>
            <dl class="student-definition-list">
                <div><dt>Level</dt><dd>{{ $course->level ?: 'Not specified' }}</dd></div>
                <div><dt>Duration</dt><dd>{{ $course->duration ?: 'Self-paced' }}</dd></div>
                <div><dt>Instructor</dt><dd>{{ $course->instructor ?: 'Mwanafunzi Investor' }}</dd></div>
            </dl>
        </x-portal-card>
    </aside>
</div>
@endsection
