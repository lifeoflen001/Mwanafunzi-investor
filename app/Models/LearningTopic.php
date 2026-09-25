<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LearningTopic extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['title', 'slug', 'icon', 'short_description', 'full_description', 'image', 'hero_eyebrow', 'hero_title', 'hero_summary', 'hero_image', 'hero_focal_point', 'hero_overlay', 'hero_alignment', 'seo_title', 'seo_description', 'og_image', 'canonical_url', 'robots', 'og_title', 'og_description', 'learning_outcomes', 'skill_level', 'study_time', 'expected_availability', 'sort_order', 'is_published', 'status'];

    protected function casts(): array
    {
        return ['learning_outcomes' => 'array', 'is_published' => 'boolean'];
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class);
    }

    public function articles()
    {
        return $this->belongsToMany(Article::class, 'article_learning_topic');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'learning_topic_product');
    }

    public function faqs()
    {
        return $this->morphMany(Faq::class, 'faqable')->orderBy('sort_order');
    }
}
