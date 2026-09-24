<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductAsset extends Model
{
    protected $fillable = ['product_id', 'product_version_id', 'name', 'path', 'disk', 'mime_type', 'extension', 'size', 'checksum', 'download_policy', 'sort_order'];
    protected function casts(): array { return ['size' => 'integer']; }
    public function product() { return $this->belongsTo(Product::class); }
    public function version() { return $this->belongsTo(ProductVersion::class, 'product_version_id'); }
    public function downloads() { return $this->hasMany(DownloadLog::class); }
}
