<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['title', 'slug', 'subtitle', 'featured_image', 'level', 'duration', 'expected_availability', 'price', 'currency', 'status', 'is_featured', 'short_description', 'full_description', 'learning_outcomes', 'prerequisites', 'instructor', 'cta_label', 'enrollment_available', 'seo_title', 'seo_description', 'og_image', 'sort_order'];

    protected function casts(): array
    {
        return ['price' => 'decimal:2', 'learning_outcomes' => 'array', 'prerequisites' => 'array', 'is_featured' => 'boolean', 'enrollment_available' => 'boolean'];
    }

    public function modules() { return $this->hasMany(CourseModule::class)->orderBy('sort_order'); }
    public function faqs() { return $this->hasMany(CourseFaq::class)->orderBy('sort_order'); }
    public function learningTopics() { return $this->belongsToMany(LearningTopic::class); }
    public function scopePublished($query) { return $query->whereIn('status', ['published', 'coming_soon']); }
}
