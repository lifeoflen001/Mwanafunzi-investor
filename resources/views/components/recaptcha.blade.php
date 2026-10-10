@php($recaptcha = app(\App\Services\RecaptchaService::class))
@if($recaptcha->isEnabled($action ?? null) && filled($recaptcha->siteKey()))
    <input type="hidden" name="recaptcha_token" data-recaptcha-token>
    <span class="sr-only" data-recaptcha-status role="status" aria-live="polite"></span>
    <noscript><p class="field-help">JavaScript is required to submit this form securely.</p></noscript>
@endif
