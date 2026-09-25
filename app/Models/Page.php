<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Page extends Model
{
    use SoftDeletes;

    protected $fillable = ['key', 'slug', 'name', 'page_type', 'status', 'is_visible', 'hero_eyebrow', 'hero_title', 'hero_highlight', 'hero_summary', 'hero_image', 'hero_primary_label', 'hero_primary_url', 'hero_secondary_label', 'hero_secondary_url', 'hero_note', 'hero_aside', 'hero_aside_index', 'hero_overlay', 'hero_alignment', 'support_heading', 'support_copy', 'effective_date', 'seo_title', 'seo_description', 'canonical_url', 'robots', 'og_title', 'og_description', 'og_image', 'published_at'];

    protected $casts = ['is_visible' => 'boolean', 'published_at' => 'datetime', 'effective_date' => 'date'];

    public function sections() { return $this->hasMany(PageSection::class)->orderBy('sort_order'); }
    public function faqs() { return $this->morphMany(Faq::class, 'faqable')->orderBy('sort_order'); }

    public function scopePublished(Builder $query): Builder { return $query->where('status', 'published')->where('is_visible', true)->where(fn (Builder $query) => $query->whereNull('published_at')->orWhere('published_at', '<=', now())); }

    public function section(string $key): ?PageSection { return $this->sections->firstWhere('key', $key); }
}
