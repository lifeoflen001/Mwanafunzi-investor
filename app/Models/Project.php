<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use SoftDeletes;

    protected $fillable = ['title', 'slug', 'client_name', 'industry', 'project_type', 'short_description', 'full_introduction', 'challenge', 'role', 'approach', 'solution', 'timeline', 'platform', 'status', 'project_date', 'live_url', 'repository_url', 'featured_image', 'technologies', 'features', 'results', 'display_order', 'is_featured', 'published_at', 'seo_title', 'seo_description', 'canonical_url', 'robots', 'og_title', 'og_description', 'og_image'];

    protected function casts(): array { return ['technologies' => 'array', 'features' => 'array', 'results' => 'array', 'is_featured' => 'boolean', 'published_at' => 'datetime', 'project_date' => 'date']; }

    public function services() { return $this->belongsToMany(Service::class)->orderBy('sort_order'); }
    public function gallery() { return $this->belongsToMany(Media::class, 'project_media')->withPivot(['alt_text', 'caption', 'sort_order'])->orderBy('project_media.sort_order'); }
    public function testimonials() { return $this->hasMany(Testimonial::class); }

    public function scopePublished(Builder $query): Builder { return $query->where('status', 'published')->where(fn (Builder $query) => $query->whereNull('published_at')->orWhere('published_at', '<=', now())); }
}
