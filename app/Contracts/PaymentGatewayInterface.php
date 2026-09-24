<?php

namespace App\Contracts;

use App\Models\Order;
use App\Models\Payment;

interface PaymentGatewayInterface
{
    public function name(): string;
    public function initializePayment(Payment $payment, Order $order, string $redirectUrl): array;
    public function verifyPayment(Payment $payment, ?string $providerTransactionId = null, array $context = []): array;
    public function retrieveTransaction(string $providerTransactionId): array;
    public function normalizeStatus(string $status): string;
    public function validateWebhook(array $headers, string $rawBody): bool;
    public function refundPayment(Payment $payment, string $amount, string $reason): array;
}
