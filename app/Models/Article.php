<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Article extends Model
{
    use HasFactory, SoftDeletes;

    public const CONTENT_TYPES = [
        'article' => 'Article',
        'news' => 'News',
        'daily_update' => 'Daily update',
        'market_education' => 'Market education',
        'trading_review' => 'Trading review',
        'risk_management' => 'Risk management',
        'student_of_money' => 'Student of Money',
        'platform_update' => 'Platform update',
        'product_update' => 'Product update',
        'announcement' => 'Announcement',
        'mechanical_systems' => 'Mechanical systems',
        'portfolio_thinking' => 'Portfolio thinking',
        'opinion' => 'Opinion / commentary',
    ];

    protected $fillable = ['article_category_id', 'user_id', 'team_member_id', 'title', 'slug', 'excerpt', 'content', 'content_type', 'featured_image', 'hero_focal_point', 'author', 'published_at', 'reading_time', 'status', 'is_featured', 'feature_priority', 'feature_start_at', 'feature_end_at', 'seo_title', 'seo_description', 'og_image', 'canonical_url', 'robots', 'og_title', 'og_description'];
    protected function casts(): array { return ['published_at' => 'datetime', 'feature_start_at' => 'datetime', 'feature_end_at' => 'datetime', 'is_featured' => 'boolean', 'feature_priority' => 'integer']; }
    public function getRouteKeyName() { return 'slug'; }
    public function category() { return $this->belongsTo(ArticleCategory::class, 'article_category_id'); }
    public function tags() { return $this->belongsToMany(Tag::class); }
    public function authorMember() { return $this->belongsTo(TeamMember::class, 'team_member_id'); }
    public function authorUser() { return $this->belongsTo(User::class, 'user_id'); }
    public function socialLinks() { return $this->hasMany(ArticleSocialLink::class)->orderBy('sort_order'); }
    public function reactions() { return $this->hasMany(ArticleReaction::class); }
    public function comments() { return $this->hasMany(Comment::class); }
    public function approvedComments() { return $this->hasMany(Comment::class)->approved(); }
    public function learningTopics() { return $this->belongsToMany(LearningTopic::class, 'article_learning_topic'); }
    public function scopePublished($query) { return $query->whereIn('status', ['published', 'scheduled'])->whereNotNull('published_at')->where('published_at', '<=', now()); }
    public function scopeVisible($query) { return $query->whereIn('status', ['published', 'scheduled'])->whereNotNull('published_at')->where('published_at', '<=', now()); }
    public function scopeFeatured($query) { return $query->published()->where('is_featured', true)->where(fn ($query) => $query->whereNull('feature_start_at')->orWhere('feature_start_at', '<=', now()))->where(fn ($query) => $query->whereNull('feature_end_at')->orWhere('feature_end_at', '>=', now()))->orderByDesc('feature_priority')->latest('published_at'); }

    public function canonicalUrl(): string
    {
        return $this->canonical_url ?: route('journal.show', $this);
    }

    public function displayAuthor(): string
    {
        return $this->authorMember?->name ?: $this->author ?: $this->authorUser?->name ?: 'Mwanafunzi Investor';
    }
}
