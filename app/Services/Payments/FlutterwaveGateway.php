<?php

namespace App\Services\Payments;

use App\Contracts\PaymentGatewayInterface;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class FlutterwaveGateway implements PaymentGatewayInterface
{
    public function name(): string { return 'flutterwave'; }

    public function initializePayment(Payment $payment, Order $order, string $redirectUrl): array
    {
        $this->assertConfigured();
        $response = Http::withToken(config('services.flutterwave.secret_key'))->acceptJson()->post(rtrim(config('services.flutterwave.base_url'), '/').'/payments', [
            'tx_ref' => $payment->internal_reference,
            'amount' => $payment->amount,
            'currency' => $payment->currency,
            'redirect_url' => $redirectUrl,
            'customer' => ['email' => $order->customer_email, 'name' => $order->customer_name, 'phonenumber' => $order->phone],
            'customizations' => ['title' => config('app.name', 'Mwanafunzi Investor')],
        ]);
        if ($response->failed() || ! $response->json('data.link')) throw new RuntimeException('The payment provider could not initialize this payment.');
        return ['status' => 'success', 'checkout_url' => $response->json('data.link'), 'reference' => $payment->internal_reference, 'provider_response' => ['status' => $response->json('status')]];
    }

    public function verifyPayment(Payment $payment, ?string $providerTransactionId = null, array $context = []): array
    {
        $this->assertConfigured();
        if (! $providerTransactionId) throw new RuntimeException('A provider transaction ID is required for verification.');
        $response = Http::withToken(config('services.flutterwave.secret_key'))->acceptJson()->get(rtrim(config('services.flutterwave.base_url'), '/').'/transactions/'.urlencode($providerTransactionId).'/verify');
        if ($response->failed() || ! $response->json('data')) throw new RuntimeException('The payment provider could not verify this payment.');
        $data = $response->json('data');
        return ['status' => $data['status'] ?? 'unknown', 'id' => (string) ($data['id'] ?? $providerTransactionId), 'tx_ref' => $data['tx_ref'] ?? null, 'amount' => $data['amount'] ?? null, 'currency' => $data['currency'] ?? null, 'provider_reference' => $data['flw_ref'] ?? null, 'metadata' => ['payment_type' => $data['payment_type'] ?? null]];
    }

    public function retrieveTransaction(string $providerTransactionId): array { return $this->verifyPayment(new Payment(['currency' => config('commerce.currency')]), $providerTransactionId); }
    public function normalizeStatus(string $status): string { return match (strtolower($status)) { 'successful', 'paid', 'completed' => 'paid', 'cancelled', 'canceled' => 'cancelled', 'failed', 'error' => 'failed', default => 'pending' }; }
    public function validateWebhook(array $headers, string $rawBody): bool
    {
        $secret = config('services.flutterwave.secret_hash');
        return $secret && isset($headers['verif-hash']) && hash_equals($secret, (string) $headers['verif-hash']);
    }
    public function refundPayment(Payment $payment, string $amount, string $reason): array { throw new RuntimeException('Flutterwave automated refunds are not enabled in this phase.'); }
    private function assertConfigured(): void { if (! config('services.flutterwave.secret_key')) throw new RuntimeException('Flutterwave sandbox credentials are not configured.'); }
}
