<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentEvent extends Model
{
    protected $fillable = ['payment_id', 'event_key', 'event_type', 'provider', 'payload', 'status', 'processed_at', 'error_message'];
    protected function casts(): array { return ['payload' => 'array', 'processed_at' => 'datetime']; }
    public function payment() { return $this->belongsTo(Payment::class); }
}
