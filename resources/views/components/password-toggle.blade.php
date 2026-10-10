@props(['label' => 'Show password'])

<button
    {{ $attributes->merge(['type' => 'button', 'class' => 'password-toggle']) }}
    data-password-toggle
    data-password-label="{{ $label }}"
    aria-label="{{ $label }}"
    aria-pressed="false"
>
    <svg class="password-toggle-icon password-toggle-eye" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
        <path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
        <circle cx="12" cy="12" r="2.7" fill="none" stroke="currentColor" stroke-width="1.8"/>
    </svg>
    <svg class="password-toggle-icon password-toggle-eye-off" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
        <path d="m3 3 18 18M10.6 6.2A10.7 10.7 0 0 1 12 6c6 0 9.5 6 9.5 6a18.3 18.3 0 0 1-3 3.8M6.2 6.8C3.8 8.4 2.5 12 2.5 12s3.5 6 9.5 6c1.2 0 2.3-.2 3.2-.6M9.9 9.9a2.7 2.7 0 0 0 3.8 3.8" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
</button>
