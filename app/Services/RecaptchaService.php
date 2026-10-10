<?php

namespace App\Services;

use App\Models\SiteSetting;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class RecaptchaService
{
    public function isEnabled(?string $form = null): bool
    {
        if (app()->environment(['local', 'testing']) && ! config('recaptcha.enforce_in_testing', false)) return false;
        if (! $this->booleanSetting('security.recaptcha.enabled', (bool) config('recaptcha.enabled'))) return false;
        if ($form === null) return true;

        return (bool) ($this->formSettings()[$form] ?? true);
    }

    public function siteKey(): ?string
    {
        return $this->stringSetting('security.recaptcha.site_key', config('recaptcha.site_key'));
    }

    public function secretKey(): ?string
    {
        return $this->stringSetting('security.recaptcha.secret_key', config('recaptcha.secret_key'));
    }

    public function hasSecret(): bool
    {
        return filled($this->secretKey());
    }

    public function minScore(): float
    {
        return max(0, min(1, (float) $this->stringSetting('security.recaptcha.min_score', config('recaptcha.min_score', 0.5))));
    }

    public function hostname(): ?string
    {
        return $this->stringSetting('security.recaptcha.hostname', config('recaptcha.hostname'));
    }

    public function formSettings(): array
    {
        $configured = SiteSetting::getValue('security.recaptcha.forms', config('recaptcha.forms', []));

        return is_array($configured) ? array_replace(config('recaptcha.forms', []), $configured) : config('recaptcha.forms', []);
    }

    public function verify(?string $token, string $action, ?Request $request = null): bool
    {
        if (! $this->isEnabled($action)) return true;
        if (! filled($token) || ! $this->hasSecret()) return false;

        try {
            /** @var PendingRequest $client */
            $client = Http::asForm()
                ->connectTimeout((int) config('security.outbound_connect_timeout', 10))
                ->timeout((int) config('recaptcha.timeout', 5));
            $response = $client->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => $this->secretKey(),
                'response' => $token,
                'remoteip' => $request?->ip(),
            ]);
            if (! $response->successful() || ! $response->json('success', false)) return false;

            if (config('recaptcha.version', 'v3') === 'v3') {
                $responseAction = (string) $response->json('action', '');
                $score = (float) $response->json('score', 0);
                if ($responseAction !== $action || $score < $this->minScore()) return false;
            }

            $hostname = $this->hostname();
            return ! filled($hostname) || hash_equals($hostname, (string) $response->json('hostname', ''));
        } catch (Throwable $exception) {
            Log::warning('reCAPTCHA verification failed safely.', [
                'action' => $action,
                'exception' => $exception::class,
            ]);

            return false;
        }
    }

    public function source(string $key): string
    {
        return SiteSetting::getValue('security.recaptcha.'.$key) !== null ? 'encrypted site settings' : 'environment';
    }

    private function booleanSetting(string $key, bool $default): bool
    {
        return filter_var(SiteSetting::getValue($key, $default), FILTER_VALIDATE_BOOLEAN);
    }

    private function stringSetting(string $key, mixed $default): ?string
    {
        $value = SiteSetting::getValue($key, $default);

        return filled($value) ? (string) $value : null;
    }
}
