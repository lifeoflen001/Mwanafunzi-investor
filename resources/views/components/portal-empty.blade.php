@props(['icon' => '○', 'title', 'description', 'href' => null, 'action' => null])
<div class="student-empty">
    <span class="student-empty-icon" aria-hidden="true">{{ $icon }}</span>
    <div>
        <h3>{{ $title }}</h3>
        <p>{{ $description }}</p>
        @if($href && $action)<a class="student-text-link" href="{{ $href }}">{{ $action }} <span aria-hidden="true">↗</span></a>@endif
    </div>
</div>
