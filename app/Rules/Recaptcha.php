<?php

namespace App\Rules;

use App\Services\RecaptchaService;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class Recaptcha implements ValidationRule
{
    public function __construct(private readonly string $action) {}

    public static function rules(string $action): array
    {
        return app(RecaptchaService::class)->isEnabled($action)
            ? ['required', 'string', 'max:4096', new self($action)]
            : ['nullable', 'string', 'max:4096'];
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! app(RecaptchaService::class)->verify(is_string($value) ? $value : null, $this->action, request())) {
            $fail('We could not verify this request. Please try again.');
        }
    }
}
