<?php

namespace App\Models;

use App\Enums\RefundStatus;
use Illuminate\Database\Eloquent\Model;

class Refund extends Model
{
    protected $fillable = ['order_id', 'payment_id', 'requested_by', 'amount', 'currency', 'status', 'provider_reference', 'reason', 'processed_at', 'metadata'];
    protected function casts(): array { return ['amount' => 'decimal:2', 'status' => RefundStatus::class, 'processed_at' => 'datetime', 'metadata' => 'array']; }
    public function order() { return $this->belongsTo(Order::class); }
    public function payment() { return $this->belongsTo(Payment::class); }
    public function requester() { return $this->belongsTo(User::class, 'requested_by'); }
}
