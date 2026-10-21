<?php

namespace App\Services\Payments;

use App\Contracts\PaymentGatewayInterface;
use RuntimeException;

class PaymentGatewayManager
{
    public function gateway(?string $name = null): PaymentGatewayInterface
    {
        $provider = $name ?: config('commerce.provider', 'sandbox');

        if ($provider === 'sandbox' && ! config('commerce.sandbox_enabled')) {
            throw new RuntimeException('Sandbox payments are disabled outside local or testing environments.');
        }

        return match ($provider) {
            'sandbox' => app(SandboxGateway::class),
            'flutterwave' => app(FlutterwaveGateway::class),
            default => throw new RuntimeException('Unsupported payment provider.'),
        };
    }
}
