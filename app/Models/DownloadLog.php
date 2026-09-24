<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DownloadLog extends Model
{
    protected $fillable = ['entitlement_id', 'user_id', 'product_id', 'product_asset_id', 'order_id', 'downloaded_at', 'ip_address', 'user_agent'];
    protected function casts(): array { return ['downloaded_at' => 'datetime']; }
    public function entitlement() { return $this->belongsTo(Entitlement::class); }
    public function asset() { return $this->belongsTo(ProductAsset::class, 'product_asset_id'); }
}
