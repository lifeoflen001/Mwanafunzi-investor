<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Article extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['article_category_id', 'user_id', 'title', 'slug', 'excerpt', 'content', 'featured_image', 'hero_focal_point', 'author', 'published_at', 'reading_time', 'status', 'is_featured', 'seo_title', 'seo_description', 'og_image', 'canonical_url', 'robots', 'og_title', 'og_description'];
    protected function casts(): array { return ['published_at' => 'datetime', 'is_featured' => 'boolean']; }
    public function category() { return $this->belongsTo(ArticleCategory::class, 'article_category_id'); }
    public function tags() { return $this->belongsToMany(Tag::class); }
    public function scopePublished($query) { return $query->whereIn('status', ['published', 'scheduled'])->whereNotNull('published_at')->where('published_at', '<=', now()); }
    public function scopeVisible($query) { return $query->whereIn('status', ['published', 'scheduled'])->whereNotNull('published_at')->where('published_at', '<=', now()); }
}
