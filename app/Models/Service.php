<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use SoftDeletes;

    protected $fillable = ['title', 'slug', 'short_description', 'detailed_description', 'icon', 'featured_image', 'starting_price', 'pricing_label', 'currency', 'pricing_type', 'delivery_estimate', 'included_features', 'cta_label', 'cta_url', 'sort_order', 'is_featured', 'is_active', 'seo_title', 'seo_description', 'canonical_url', 'robots', 'og_image'];

    protected function casts(): array
    {
        return ['starting_price' => 'decimal:2', 'included_features' => 'array', 'is_featured' => 'boolean', 'is_active' => 'boolean'];
    }

    public function projects() { return $this->belongsToMany(Project::class)->orderBy('display_order'); }

    public function scopeActive(Builder $query): Builder { return $query->where('is_active', true); }

    public function pricingText(): string
    {
        if ($this->pricing_label) return $this->pricing_label;
        if ($this->pricing_type === 'custom_quote' || $this->starting_price === null) return 'Custom quote';
        $amount = number_format((float) $this->starting_price, 0);
        $prefix = $this->pricing_type === 'starting_from' ? 'Starting from ' : '';
        return trim($prefix.($this->currency ? $this->currency.' ' : '').$amount);
    }
}
