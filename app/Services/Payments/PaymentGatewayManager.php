<?php

namespace App\Services\Payments;

use App\Contracts\PaymentGatewayInterface;
use RuntimeException;

class PaymentGatewayManager
{
    public function gateway(?string $name = null): PaymentGatewayInterface
    {
        return match ($name ?: config('commerce.provider', 'sandbox')) {
            'sandbox' => app(SandboxGateway::class),
            'flutterwave' => app(FlutterwaveGateway::class),
            default => throw new RuntimeException('Unsupported payment provider.'),
        };
    }
}
