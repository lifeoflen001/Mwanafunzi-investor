<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Media extends Model
{
    protected $table = 'media';
    protected $fillable = ['disk', 'path', 'filename', 'mime_type', 'size', 'width', 'height', 'alt_text', 'caption', 'title', 'uploaded_by', 'variants'];
    protected function casts(): array { return ['variants' => 'array']; }

    public function uploader() { return $this->belongsTo(User::class, 'uploaded_by'); }

    public function getUrlAttribute(): string
    {
        return asset('storage/'.$this->path);
    }

    public function usageCount(): int
    {
        return (int) (
            Course::where('featured_image', $this->path)->orWhere('og_image', $this->path)->count()
            + Product::where('thumbnail', $this->path)->orWhere('og_image', $this->path)->count()
            + Article::where('featured_image', $this->path)->orWhere('og_image', $this->path)->count()
            + LearningTopic::where(fn ($query) => $query->where('image', $this->path)->orWhere('hero_image', $this->path)->orWhere('og_image', $this->path))->count()
            + ProductImage::where('path', $this->path)->count()
            + Page::where(fn ($query) => $query->where('hero_image', $this->path)->orWhere('og_image', $this->path))->count()
            + PageSection::where('image', $this->path)->count()
            + Project::where(fn ($query) => $query->where('featured_image', $this->path)->orWhere('og_image', $this->path))->count()
            + Project::whereHas('gallery', fn ($query) => $query->where('media.path', $this->path))->count()
            + Service::where(fn ($query) => $query->where('featured_image', $this->path)->orWhere('og_image', $this->path))->count()
            + Testimonial::where(fn ($query) => $query->where('client_photo', $this->path)->orWhere('company_logo', $this->path))->count()
            + TeamMember::where(fn ($query) => $query->where('portrait', $this->path)->orWhere('og_image', $this->path))->count()
            + SiteSetting::where('value', $this->path)->count()
        );
    }
}
