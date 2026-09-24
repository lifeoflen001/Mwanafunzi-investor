<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = ['order_number', 'user_id', 'customer_email', 'customer_name', 'phone', 'country', 'status', 'payment_status', 'payment_provider', 'internal_payment_reference', 'transaction_reference', 'subtotal', 'discount', 'tax', 'total', 'currency', 'tax_label', 'tax_rate', 'prices_include_tax', 'terms_accepted_at', 'billing_details', 'failure_reason', 'paid_at', 'completed_at', 'cancelled_at', 'refunded_at'];

    protected function casts(): array
    {
        return ['subtotal' => 'decimal:2', 'discount' => 'decimal:2', 'tax' => 'decimal:2', 'total' => 'decimal:2', 'tax_rate' => 'decimal:4', 'prices_include_tax' => 'boolean', 'billing_details' => 'array', 'terms_accepted_at' => 'datetime', 'paid_at' => 'datetime', 'completed_at' => 'datetime', 'cancelled_at' => 'datetime', 'refunded_at' => 'datetime', 'status' => OrderStatus::class, 'payment_status' => PaymentStatus::class];
    }

    public function user() { return $this->belongsTo(User::class); }
    public function items() { return $this->hasMany(OrderItem::class); }
    public function payments() { return $this->hasMany(Payment::class); }
    public function entitlements() { return $this->hasMany(Entitlement::class); }
    public function refunds() { return $this->hasMany(Refund::class); }
    public function latestPayment() { return $this->hasOne(Payment::class)->latestOfMany(); }
    public function isPaid(): bool { return in_array($this->payment_status?->value ?? $this->payment_status, [PaymentStatus::Paid->value, PaymentStatus::PartiallyRefunded->value, PaymentStatus::Refunded->value], true); }
}
