<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['title', 'slug', 'subtitle', 'featured_image', 'hero_focal_point', 'level', 'duration', 'expected_availability', 'price', 'currency', 'status', 'is_featured', 'short_description', 'full_description', 'learning_outcomes', 'prerequisites', 'instructor', 'cta_label', 'enrollment_available', 'seo_title', 'seo_description', 'og_image', 'canonical_url', 'robots', 'og_title', 'og_description', 'sort_order'];

    protected function casts(): array
    {
        return ['price' => 'decimal:2', 'learning_outcomes' => 'array', 'prerequisites' => 'array', 'is_featured' => 'boolean', 'enrollment_available' => 'boolean'];
    }

    public function modules() { return $this->hasMany(CourseModule::class)->orderBy('sort_order'); }
    public function faqs() { return $this->hasMany(CourseFaq::class)->orderBy('sort_order'); }
    public function learningTopics() { return $this->belongsToMany(LearningTopic::class); }
    public function entitlements() { return $this->hasMany(Entitlement::class); }
    public function enrollments() { return $this->hasMany(Enrollment::class); }
    public function waitlists() { return $this->hasMany(CourseWaitlist::class); }
    public function isPublishedForPurchase(): bool { return in_array($this->status, ['published', 'open'], true); }
    public function effectivePrice(): string { return (string) ($this->price ?? '0.00'); }
    public function scopePublished($query) { return $query->whereIn('status', ['published', 'open', 'coming_soon']); }
}
