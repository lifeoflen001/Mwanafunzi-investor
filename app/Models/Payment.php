<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = ['order_id', 'provider', 'internal_reference', 'provider_transaction_id', 'provider_reference', 'amount', 'currency', 'status', 'metadata', 'paid_at', 'failed_at', 'verified_at'];
    protected function casts(): array { return ['amount' => 'decimal:2', 'metadata' => 'array', 'paid_at' => 'datetime', 'failed_at' => 'datetime', 'verified_at' => 'datetime', 'status' => PaymentStatus::class]; }
    public function order() { return $this->belongsTo(Order::class); }
    public function events() { return $this->hasMany(PaymentEvent::class); }
    public function refunds() { return $this->hasMany(Refund::class); }
}
