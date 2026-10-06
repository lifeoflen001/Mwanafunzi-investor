@props(['label', 'value', 'description', 'tone' => 'copper', 'icon' => '•'])
<article class="student-metric student-metric-{{ $tone }}">
    <span class="student-metric-icon" aria-hidden="true">{{ $icon }}</span>
    <p>{{ $label }}</p>
    <strong>{{ $value }}</strong>
    <small>{{ $description }}</small>
</article>
