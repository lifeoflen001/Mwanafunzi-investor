@props(['label', 'light' => false])

<p {{ $attributes->class(['eyebrow', 'eyebrow-light' => $light]) }}>
    <span class="eyebrow-line" aria-hidden="true"></span>
    {{ $label }}
</p>
