<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'slug', 'product_type', 'thumbnail', 'short_description', 'detailed_description', 'version', 'price', 'sale_price', 'currency', 'availability', 'download_format', 'system_requirements', 'demo_url', 'purchase_url', 'documentation_url', 'is_featured', 'seo_title', 'seo_description', 'og_image', 'sort_order'];
    protected function casts(): array { return ['price' => 'decimal:2', 'sale_price' => 'decimal:2', 'is_featured' => 'boolean']; }
    public function features() { return $this->hasMany(ProductFeature::class)->orderBy('sort_order'); }
    public function images() { return $this->hasMany(ProductImage::class)->orderBy('sort_order'); }
    public function versions() { return $this->hasMany(ProductVersion::class)->latest('released_at'); }
    public function faqs() { return $this->morphMany(Faq::class, 'faqable')->orderBy('sort_order'); }
    public function assets() { return $this->hasMany(ProductAsset::class)->orderBy('sort_order'); }
    public function entitlements() { return $this->hasMany(Entitlement::class); }
    public function isPurchasable(): bool { return in_array($this->availability, ['available'], true); }
    public function effectivePrice(): string { return (string) ($this->sale_price !== null ? $this->sale_price : ($this->price ?? '0.00')); }
    public function scopeAvailable($query) { return $query->whereIn('availability', ['available', 'coming_soon', 'waitlist']); }
}
