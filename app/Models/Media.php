<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Media extends Model
{
    protected $table = 'media';
    protected $fillable = ['disk', 'path', 'filename', 'mime_type', 'size', 'width', 'height', 'alt_text', 'title', 'variants'];
    protected function casts(): array { return ['variants' => 'array']; }

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
            + LearningTopic::where('image', $this->path)->count()
            + ProductImage::where('path', $this->path)->count()
            + Page::where('hero_image', $this->path)->count()
            + PageSection::where('image', $this->path)->count()
            + SiteSetting::where('value', $this->path)->count()
        );
    }
}
