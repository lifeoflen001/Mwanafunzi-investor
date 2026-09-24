<?php

namespace App\Models;

use App\Enums\EntitlementStatus;
use Illuminate\Database\Eloquent\Model;

class Entitlement extends Model
{
    protected $fillable = ['user_id', 'order_id', 'order_item_id', 'product_id', 'course_id', 'customer_email', 'type', 'status', 'product_version_id', 'starts_at', 'ends_at', 'revoked_at', 'metadata'];
    protected function casts(): array { return ['status' => EntitlementStatus::class, 'starts_at' => 'datetime', 'ends_at' => 'datetime', 'revoked_at' => 'datetime', 'metadata' => 'array']; }
    public function user() { return $this->belongsTo(User::class); }
    public function order() { return $this->belongsTo(Order::class); }
    public function orderItem() { return $this->belongsTo(OrderItem::class); }
    public function product() { return $this->belongsTo(Product::class); }
    public function course() { return $this->belongsTo(Course::class); }
    public function version() { return $this->belongsTo(ProductVersion::class, 'product_version_id'); }
    public function downloads() { return $this->hasMany(DownloadLog::class); }
    public function isActive(): bool { return ($this->status?->value ?? $this->status) === EntitlementStatus::Active->value && (! $this->ends_at || $this->ends_at->isFuture()); }
}
