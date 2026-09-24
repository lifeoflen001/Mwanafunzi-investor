<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVersion extends Model
{
    protected $fillable = ['product_id', 'version', 'released_at', 'notes'];
    protected function casts(): array { return ['released_at' => 'date']; }
    public function product() { return $this->belongsTo(Product::class); }
    public function assets() { return $this->hasMany(ProductAsset::class); }
}
