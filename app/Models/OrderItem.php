<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = ['order_id', 'purchasable_type', 'purchasable_id', 'title', 'unit_price', 'quantity', 'line_total', 'currency', 'snapshot'];
    protected function casts(): array { return ['unit_price' => 'decimal:2', 'line_total' => 'decimal:2', 'snapshot' => 'array']; }
    public function order() { return $this->belongsTo(Order::class); }
    public function purchasable() { return $this->morphTo(); }
}
