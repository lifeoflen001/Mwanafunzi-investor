<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Enums\RefundStatus;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Refund;
use App\Models\User;
use App\Support\Money;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class RefundService
{
    public function markRefunded(Order $order, User $admin, string $amount, string $reason): Refund
    {
        return DB::transaction(function () use ($order, $admin, $amount, $reason) {
            $payment = $order->payments()->whereIn('status', [PaymentStatus::Paid->value, PaymentStatus::PartiallyRefunded->value])->latest()->lockForUpdate()->firstOrFail();
            $refundAmount = Money::fromDecimal($amount, $order->currency);
            $paidAmount = Money::fromDecimal((string) $payment->amount, $order->currency);
            $refundedAmount = $order->refunds()->whereIn('status', [RefundStatus::Refunded->value, RefundStatus::PartiallyRefunded->value])->get()->reduce(
                fn (Money $total, Refund $refund) => $total->add(Money::fromDecimal((string) $refund->amount, $order->currency)),
                new Money(0, $order->currency)
            );
            $refundableBalance = $paidAmount->subtract($refundedAmount);
            if ($refundAmount->minor <= 0 || $refundAmount->minor > $refundableBalance->minor) throw new RuntimeException('Refund amount is outside the refundable balance.');
            $fully = $refundAmount->minor === $refundableBalance->minor;
            $refund = Refund::create(['order_id' => $order->id, 'payment_id' => $payment->id, 'requested_by' => $admin->id, 'amount' => $refundAmount->toDecimal(), 'currency' => $order->currency, 'status' => $fully ? RefundStatus::Refunded : RefundStatus::PartiallyRefunded, 'reason' => $reason, 'processed_at' => now()]);
            $payment->update(['status' => $fully ? PaymentStatus::Refunded : PaymentStatus::PartiallyRefunded]);
            $order->update(['status' => $fully ? OrderStatus::Refunded : OrderStatus::PartiallyRefunded, 'payment_status' => $fully ? PaymentStatus::Refunded : PaymentStatus::PartiallyRefunded, 'refunded_at' => $fully ? now() : null]);
            if ($fully) { app(EntitlementService::class)->revokeForOrder($order); app(EnrollmentService::class)->revokeForOrder($order); }
            return $refund;
        });
    }
}
