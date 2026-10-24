<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use SoftDeletes;

    public const STATUSES = ['completed', 'production_ready', 'in_production', 'active_development', 'internal_project', 'client_project', 'concept', 'archived'];

    protected $fillable = ['title', 'slug', 'client_name', 'industry', 'project_type', 'project_status', 'media_type', 'creative_category', 'photography_type', 'services_provided', 'location', 'event_date', 'short_description', 'full_introduction', 'challenge', 'role', 'approach', 'solution', 'technical_notes', 'timeline', 'platform', 'status', 'project_date', 'live_url', 'repository_url', 'featured_image', 'featured_focal_point', 'technologies', 'features', 'results', 'display_order', 'is_featured', 'published_at', 'seo_title', 'seo_description', 'primary_keyword', 'secondary_keywords', 'canonical_url', 'robots', 'og_title', 'og_description', 'og_image'];

    protected function casts(): array { return ['technologies' => 'array', 'features' => 'array', 'results' => 'array', 'services_provided' => 'array', 'is_featured' => 'boolean', 'published_at' => 'datetime', 'project_date' => 'date', 'event_date' => 'date']; }

    public function services() { return $this->belongsToMany(Service::class)->orderBy('sort_order'); }
    public function gallery() { return $this->belongsToMany(Media::class, 'project_media')->withPivot(['alt_text', 'caption', 'sort_order'])->orderBy('project_media.sort_order'); }
    public function testimonials() { return $this->hasMany(Testimonial::class); }

    public function scopePublished(Builder $query): Builder { return $query->where('status', 'published')->where(fn (Builder $query) => $query->whereNull('published_at')->orWhere('published_at', '<=', now())); }

    public function statusLabel(): string
    {
        return str($this->project_status ?: ($this->status === 'published' ? 'published' : $this->status))->replace('_', ' ')->title()->toString();
    }
}
