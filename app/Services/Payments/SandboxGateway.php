<?php

namespace App\Services\Payments;

use App\Contracts\PaymentGatewayInterface;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Str;

class SandboxGateway implements PaymentGatewayInterface
{
    public function name(): string { return 'sandbox'; }

    public function initializePayment(Payment $payment, Order $order, string $redirectUrl): array
    {
        return ['status' => 'success', 'checkout_url' => route('payments.sandbox', $payment), 'reference' => $payment->internal_reference, 'mode' => 'sandbox'];
    }

    public function verifyPayment(Payment $payment, ?string $providerTransactionId = null, array $context = []): array
    {
        $success = ($context['status'] ?? 'successful') === 'successful';
        return ['status' => $success ? 'successful' : 'failed', 'id' => $providerTransactionId ?: 'sandbox-'.Str::lower(Str::random(12)), 'tx_ref' => $payment->internal_reference, 'amount' => $payment->amount, 'currency' => $payment->currency];
    }

    public function retrieveTransaction(string $providerTransactionId): array { return ['status' => 'successful', 'id' => $providerTransactionId]; }
    public function normalizeStatus(string $status): string { return match (strtolower($status)) { 'successful', 'paid', 'completed' => 'paid', 'cancelled', 'canceled' => 'cancelled', 'failed', 'error' => 'failed', default => 'pending' }; }
    public function validateWebhook(array $headers, string $rawBody): bool { return hash_equals((string) config('services.flutterwave.secret_hash', 'sandbox-secret'), (string) ($headers['verif-hash'] ?? '')); }
    public function refundPayment(Payment $payment, string $amount, string $reason): array { return ['status' => 'pending', 'amount' => $amount, 'reason' => $reason]; }
}
