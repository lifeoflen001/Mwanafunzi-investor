<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentEvent;
use App\Support\Money;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\CommerceMail;
use RuntimeException;

class PaymentService
{
    public function completeFreeOrder(Payment $payment): void
    {
        $this->complete($payment, ['status' => 'successful', 'id' => 'free-'.$payment->internal_reference, 'tx_ref' => $payment->internal_reference, 'amount' => '0.00', 'currency' => $payment->currency]);
    }

    public function verifyAndComplete(Payment $payment, array $providerData, string $eventKey = null): Order
    {
        return $this->complete($payment, $providerData, $eventKey);
    }

    public function complete(Payment $payment, array $providerData, ?string $eventKey = null): Order
    {
        $status = app(\App\Services\Payments\PaymentGatewayManager::class)->gateway($payment->provider)->normalizeStatus((string) ($providerData['status'] ?? 'unknown'));
        if ($status !== PaymentStatus::Paid->value) throw new RuntimeException('Payment has not been confirmed.');
        if (($providerData['tx_ref'] ?? null) !== $payment->internal_reference) throw new RuntimeException('Payment reference mismatch.');
        if (strtoupper((string) ($providerData['currency'] ?? '')) !== strtoupper($payment->currency)) throw new RuntimeException('Payment currency mismatch.');
        if (Money::fromDecimal((string) ($providerData['amount'] ?? '0'), $payment->currency)->minor < Money::fromDecimal((string) $payment->amount, $payment->currency)->minor) throw new RuntimeException('Payment amount mismatch.');
        return DB::transaction(function () use ($payment, $providerData, $eventKey) {
            $payment = Payment::query()->lockForUpdate()->with('order.items')->findOrFail($payment->id);
            if ($eventKey && PaymentEvent::where('event_key', $eventKey)->where('status', 'processed')->exists()) return $payment->order;
            if ($payment->status !== PaymentStatus::Paid) {
                $payment->update(['status' => PaymentStatus::Paid, 'provider_transaction_id' => (string) ($providerData['id'] ?? null), 'provider_reference' => $providerData['provider_reference'] ?? null, 'paid_at' => now(), 'verified_at' => now(), 'metadata' => $providerData['metadata'] ?? null]);
                $payment->order->update(['status' => OrderStatus::Completed, 'payment_status' => PaymentStatus::Paid, 'transaction_reference' => $providerData['provider_reference'] ?? ($providerData['id'] ?? null), 'paid_at' => now(), 'completed_at' => now()]);
                $entitlements = app(EntitlementService::class);
                foreach ($payment->order->items as $item) {
                    $entitlement = $entitlements->grantForItem($item, $payment->order->user);
                    if ($entitlement?->course_id) app(EnrollmentService::class)->activateFromEntitlement($entitlement);
                }
            }
            if ($eventKey) PaymentEvent::updateOrCreate(['event_key' => $eventKey], ['payment_id' => $payment->id, 'event_type' => 'payment_confirmed', 'provider' => $payment->provider, 'payload' => $providerData, 'status' => 'processed', 'processed_at' => now()]);
            DB::afterCommit(function () use ($payment) { Mail::to($payment->order->customer_email)->queue(new CommerceMail('Payment confirmed — '.$payment->order->order_number, 'Payment confirmed', 'Your payment has been verified and your Mwanafunzi Investor access is ready.', route('account.orders.show', $payment->order), 'View your order')); });
            return $payment->order->fresh(['items', 'entitlements']);
        });
    }

    public function fail(Payment $payment, string $reason): void
    {
        $payment->update(['status' => PaymentStatus::Failed, 'failed_at' => now(), 'metadata' => ['reason' => $reason]]);
        $payment->order()->update(['status' => OrderStatus::PendingPayment, 'payment_status' => PaymentStatus::Failed, 'failure_reason' => $reason]);
    }
}
