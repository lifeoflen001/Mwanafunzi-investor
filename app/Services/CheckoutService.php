<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Course;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use App\Support\Money;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class CheckoutService
{
    public function createForProduct(User $user, Product $product, array $customer): Order
    {
        if (! $product->isPurchasable()) throw new RuntimeException('This product is not available for purchase.');
        return $this->create($user, $product, $customer);
    }

    public function createForCourse(User $user, Course $course, array $customer): Order
    {
        if (! $course->isPublishedForPurchase() || ! $course->enrollment_available) throw new RuntimeException('This course is not open for enrollment.');
        return $this->create($user, $course, $customer);
    }

    public function initialize(Order $order, string $redirectUrl): array
    {
        $payment = Payment::create(['order_id' => $order->id, 'provider' => config('commerce.provider'), 'internal_reference' => 'MI-PAY-'.str()->upper(Str::random(18)), 'amount' => $order->total, 'currency' => $order->currency, 'status' => PaymentStatus::Pending]);
        \App\Models\PaymentEvent::create(['payment_id' => $payment->id, 'event_key' => 'created-'.$payment->internal_reference, 'event_type' => 'payment_created', 'provider' => $payment->provider, 'payload' => ['amount' => $payment->amount, 'currency' => $payment->currency], 'status' => 'processed', 'processed_at' => now()]);
        $order->update(['payment_provider' => $payment->provider, 'internal_payment_reference' => $payment->internal_reference]);
        if (Money::fromDecimal($order->total, $order->currency)->isZero()) {
            app(PaymentService::class)->completeFreeOrder($payment);
            return ['checkout_url' => route('payments.success', $order), 'payment' => $payment];
        }
        $result = app(\App\Services\Payments\PaymentGatewayManager::class)->gateway($payment->provider)->initializePayment($payment, $order, $redirectUrl);
        $payment->update(['status' => PaymentStatus::Processing]);
        $order->update(['status' => OrderStatus::Processing]);
        \App\Models\PaymentEvent::create(['payment_id' => $payment->id, 'event_key' => 'initialized-'.$payment->internal_reference, 'event_type' => 'payment_initialized', 'provider' => $payment->provider, 'payload' => ['reference' => $payment->internal_reference], 'status' => 'processed', 'processed_at' => now()]);
        return ['checkout_url' => $result['checkout_url'], 'payment' => $payment, 'provider' => $result];
    }

    private function create(User $user, Product|Course $purchasable, array $customer): Order
    {
        $currency = strtoupper($purchasable->currency ?: config('commerce.currency'));
        $unit = Money::fromDecimal($purchasable->effectivePrice(), $currency);
        $tax = new Money(0, $currency);
        if (config('commerce.tax.enabled') && ! config('commerce.tax.prices_include')) $tax = $this->tax($unit, (string) config('commerce.tax.rate', '0'));
        $total = $unit->add($tax);
        return DB::transaction(function () use ($user, $purchasable, $customer, $currency, $unit, $tax, $total) {
            $order = Order::create(['order_number' => $this->orderNumber(), 'user_id' => $user->id, 'customer_email' => $customer['email'], 'customer_name' => $customer['name'], 'phone' => $customer['phone'] ?? $user->phone, 'country' => $customer['country'] ?? $user->country, 'status' => OrderStatus::PendingPayment, 'payment_status' => $unit->isZero() ? PaymentStatus::Paid : PaymentStatus::Pending, 'subtotal' => $unit->toDecimal(), 'tax' => $tax->toDecimal(), 'total' => $total->toDecimal(), 'currency' => $currency, 'tax_label' => config('commerce.tax.label'), 'tax_rate' => config('commerce.tax.rate', '0'), 'prices_include_tax' => config('commerce.tax.prices_include'), 'terms_accepted_at' => now(), 'billing_details' => ['email' => $customer['email'], 'name' => $customer['name'], 'phone' => $customer['phone'] ?? null, 'country' => $customer['country'] ?? null]]);
            $order->items()->create(['purchasable_type' => $purchasable::class, 'purchasable_id' => $purchasable->id, 'title' => $purchasable instanceof Product ? $purchasable->name : $purchasable->title, 'unit_price' => $unit->toDecimal(), 'quantity' => 1, 'line_total' => $unit->toDecimal(), 'currency' => $currency, 'snapshot' => ['name' => $purchasable instanceof Product ? $purchasable->name : $purchasable->title, 'slug' => $purchasable->slug, 'version' => $purchasable instanceof Product ? $purchasable->version : null, 'description' => $purchasable->short_description]]);
            return $order->load('items');
        });
    }

    private function orderNumber(): string { do { $number = 'MI-'.now()->format('Y').'-'.str()->upper(Str::random(6)); } while (Order::where('order_number', $number)->exists()); return $number; }
    private function tax(Money $amount, string $rate): Money { $rate = preg_replace('/[^0-9.]/', '', $rate) ?: '0'; [$whole, $fraction] = array_pad(explode('.', $rate, 2), 2, '0'); $rateUnits = ((int) $whole * 10000) + (int) str_pad(substr($fraction, 0, 4), 4, '0'); return new Money(intdiv($amount->minor * $rateUnits, 1000000), $amount->currency); }
}
