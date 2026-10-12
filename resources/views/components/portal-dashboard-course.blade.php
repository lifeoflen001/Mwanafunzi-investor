@props(['enrollment', 'nextLesson' => null])
@php
    $course = $enrollment->course;
    $progress = $enrollment->progressPercent();
    $href = $nextLesson ? route('account.courses.lessons.show', [$enrollment, $nextLesson]) : route('account.courses.show', $enrollment);
    $image = \App\Support\PublicHero::resolve([$course->featured_image, config('public.hero_defaults.courses')]);
@endphp
<article class="student-dashboard-course">
    <a class="student-dashboard-course-media" href="{{ $href }}" aria-label="Open {{ $course->title }}">
        @if($image)
            <img src="{{ $image['url'] }}" @if($image['srcset']) srcset="{{ $image['srcset'] }}" sizes="(max-width: 760px) 100vw, 34vw" @endif alt="" loading="lazy">
        @else
            <span aria-hidden="true">▤</span>
        @endif
    </a>
    <div class="student-dashboard-course-copy">
        <div class="student-card-meta"><x-portal-status :status="$enrollment->status" /><span>{{ $course->level ?: 'Course' }}</span></div>
        <h3><a href="{{ $href }}">{{ $course->title }}</a></h3>
        <p>{{ $course->short_description ?: 'Open your course workspace and curriculum.' }}</p>
        <div class="student-card-progress">
            <span><small>{{ $progress > 0 ? 'Progress' : 'Ready to start' }}</small><strong>{{ $progress }}%</strong></span>
            <span class="student-progress-track" role="progressbar" aria-label="{{ $course->title }} progress" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $progress }}"><span style="width:{{ $progress }}%"></span></span>
        </div>
        <a class="student-text-link" href="{{ $href }}">{{ $nextLesson ? 'Continue learning' : ($progress >= 100 ? 'Review course' : 'Start learning') }} <span aria-hidden="true">↗</span></a>
    </div>
</article>
