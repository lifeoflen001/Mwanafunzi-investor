@props([
    'title',
    'description' => null,
])
<fieldset {{ $attributes->merge(['class' => 'admin-form-section']) }}>
    <legend>{{ $title }}</legend>
    @if($description)<p class="admin-form-section-description">{{ $description }}</p>@endif
    {{ $slot }}
</fieldset>
